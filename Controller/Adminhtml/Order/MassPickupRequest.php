<?php

/**
 * Gopay payment gateway by ZingyBits - Magento 2 extension
 *
 * NOTICE OF LICENSE
 *
 * Unauthorized copying of this file, via any medium, is strictly prohibited
 * Proprietary and confidential
 *
 * @category ZingyBits
 * @package ZingyBits_BalikobotAdminUi
 * @copyright Copyright (c) 2022 ZingyBits s.r.o.
 * @license http://www.zingybits.com/business-license
 * @author ZingyBits s.r.o. <support@zingybits.com>
 */

namespace ZingyBits\BalikobotAdminUi\Controller\Adminhtml\Order;

use ZingyBits\BalikobotCore\Api\Status;
use ZingyBits\BalikobotCore\Model\BalikobotApiClient;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Sales\Controller\Adminhtml\Order\AbstractMassAction;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Sales\Model\Order;
use Psr\Log\LoggerInterface;

/**
 * Class MassPickupRequest
 */
class MassPickupRequest extends AbstractMassAction
{
    /**
     * @var OrderManagementInterface
     */
    protected $orderManagement;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var BalikobotApiClient
     */
    protected $balikobotApiClient;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Order
     */
    protected $order;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param OrderManagementInterface $orderManagement
     * @param ScopeConfigInterface $scopeConfig
     * @param BalikobotApiClient $balikobotApiClient
     * @param LoggerInterface $logger
     * @param Order $order
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        OrderManagementInterface $orderManagement,
        ScopeConfigInterface $scopeConfig,
        BalikobotApiClient $balikobotApiClient,
        LoggerInterface $logger,
        Order $order
    ) {
        parent::__construct($context, $filter);
        $this->collectionFactory = $collectionFactory;
        $this->orderManagement = $orderManagement;
        $this->scopeConfig = $scopeConfig;
        $this->balikobotApiClient = $balikobotApiClient;
        $this->logger = $logger;
        $this->order = $order;
    }

    /**
     * Hold selected orders
     *
     * @param AbstractCollection $collection
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    protected function massAction(AbstractCollection $collection)
    {
        $countProcessed = 0;

        $packageIds = [];
        $entityIds = [];
        $firstShippingMethod = null;
        $shippingMethodOnly = null;

        foreach ($collection->getItems() as $item) {
            // leave the loop if no ID
            if (! $item->getEntityId()) {
                continue;
            }
            $loadedOrder = $this->order->load($item->getEntityId());

            // leave the loop if no stored json (from previous api call)
            $balikobotJson = $loadedOrder->getBalikobotJson();
            if (! $balikobotJson) {
                continue;
            }

            $shippingMethod = $item->getShippingMethod();

            // detect what shipping method has been selected and prevent several shipping methods in a single request
            if (is_null($firstShippingMethod)) {
                $firstShippingMethod = $shippingMethod;
            } elseif ($shippingMethod !== $firstShippingMethod) {
                $this->messageManager->addError(__('Only one carrier must be assigned for selected orders'));
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath($this->getComponentRefererUrl());
                return $resultRedirect;
            }

            $balikobotData = json_decode($balikobotJson);
            if (! $balikobotData->package_id) {
                continue;
            }

            $tmp = explode('_', $shippingMethod);
            $shippingMethodOnly = end($tmp);

            $packageIds[] = $balikobotData->package_id;
            $entityIds[] = $item->getEntityId();
        }

        // mapping vendor shipping name to bbot name
        if ($shippingMethodOnly !== null) {

            $map = json_decode($this->scopeConfig->getValue('balikobot/allowed_shippers/shippers') ?: '[]', true);
            foreach ($map as $shipperCode => $info) {
                if (strpos($shippingMethodOnly, (string)$shipperCode) !== false) {
                    $shippingMethodOnly = $info['balikobot_shippers'];
                    break;
                }
            }
        }

        // sending API call - ORDER - to Bbot
        try {
            $bbResponse = $this->balikobotApiClient->order($shippingMethodOnly, $packageIds);
            $countProcessed += count($packageIds);

            if ($bbResponse['status'] == 200) {
                $this->messageManager->addSuccess(__('Order list <a target="_blank" href=%1>%1</a>',
                    $bbResponse['handover_url']));

                // changing order status
                foreach ($entityIds as $id) {
                    $loadedOrder = $this->order->load($id);
                    $this->order->addCommentToStatusHistory(__('The order has been set for pickup'));
                    $loadedOrder->setState(status::STATUS_BBOT_PICKUP, true);
                    $loadedOrder->setStatus(status::STATUS_BBOT_PICKUP);
                    $loadedOrder->addStatusToHistory($loadedOrder->getStatus(),
                        __('new order status - ') . __(status::LABEL_STATUS_BBOT_PICKUP));
                    $loadedOrder->save();
                }
            }

            $countNonProcessed = $collection->count() - $countProcessed;

            if ($countNonProcessed && $countProcessed) {
                $this->messageManager->addError(__('%1 not processed order(s).', $countNonProcessed));
            } elseif ($countNonProcessed) {
                $this->messageManager->addError(__('No processed order(s) .'));
            }

            if ($countProcessed) {
                $this->messageManager->addSuccess(__('%1 order(s) will be picked up.', $countProcessed));
            }
        } catch (\Exception $e) {
            $this->messageManager->addError(__('Balikobot API returned error: ') . $e->getMessage());
        }

        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath($this->getComponentRefererUrl());
        return $resultRedirect;
    }
}
