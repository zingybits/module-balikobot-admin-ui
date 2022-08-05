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

namespace ZingyBits\BalikobotAdminUi\Block\Adminhtml\Order\View;

use Magento\Sales\Api\OrderRepositoryInterface;

class View extends \Magento\Backend\Block\Template
{

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;
    /*
    public function __construct(
        OrderRepositoryInterface $orderRepository
    ) {
        $this->orderRepository = $orderRepository;
    }

    public function getBalikobotData($orderId)
    {
        $order = $this->orderRepository->get($orderId);
        $data = $order->getBalikobotJson();

        return "Some data: " . print_r($data, true);
    }
    */
}
