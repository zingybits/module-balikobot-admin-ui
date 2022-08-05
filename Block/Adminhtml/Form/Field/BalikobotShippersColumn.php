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

declare(strict_types=1);

namespace ZingyBits\BalikobotAdminUi\Block\Adminhtml\Form\Field;

use Magento\Framework\View\Element\Html\Select;
use Magento\Framework\View\Element\Context;
use ZingyBits\BalikobotCore\Model\BalikobotApiClient;

class BalikobotShippersColumn extends Select
{
    /**
     * @var BalikobotApiClient
     */
    protected $balikobotApiClient;

    /**
     * Constructor
     *
     * @param Context $context
     * @param array $data
     * @param BalikobotApiClient $balikobotApiClient
     */
    public function __construct(
        Context $context,
        array $data = [],
        BalikobotApiClient $balikobotApiClient
    ) {
        $this->balikobotApiClient = $balikobotApiClient;
        parent::__construct($context, $data);
    }



    /**
     * Set "name" for <select> element
     *
     * @param string $value
     * @return $this
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }

    /**
     * Set "id" for <select> element
     *
     * @param $value
     * @return $this
     */
    public function setInputId($value)
    {
        return $this->setId($value);
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml(): string
    {
        if (!$this->getOptions()) {
            $this->setOptions($this->getSourceOptions());
        }
        return parent::_toHtml();
    }

    private function getSourceOptions(): array
    {
        $list = [
            ['label' => 'No', 'value' => '0']
        ];
        try {
            $shippers = $this->balikobotApiClient->getShippers();
            foreach ($shippers as $shipper) {
                $list[] = [
                    'label' => $shipper,
                    'value' => $shipper
                ];
            }
        } catch (\Exception $e) {
            // TODO log Balikobot API error: ' . $e->getMessage()
        }
        return $list;
    }
}
