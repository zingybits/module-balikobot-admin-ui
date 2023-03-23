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

namespace ZingyBits\BalikobotAdminUi\Block\Adminhtml\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Backend\Block\Template\Context;
use Magento\Shipping\Model\Config as ShippingConfig;

class AllowedShippers extends AbstractFieldArray
{
    /**
     * @var MethodColumn
     */
    private $methodRenderer;

    /**
     * @var BalikobotShippersColumn
     */
    private $balikobotShippersRenderer;

    /**
     * @var ShippingConfig
     */
    private $shippingConfig;

    /**
     * @param ShippingConfig $shippingConfig
     */
    public function __construct(
        Context $context,
        ShippingConfig $shippingConfig,
    )
    {
        $this->shippingConfig = $shippingConfig;
        parent::__construct($context);
    }

    /**
     * Prepare rendering the new field by adding all the needed columns
     *
     * @return void
     */
    protected function _prepareToRender()
    {
        try {
            $this->_template = 'ZingyBits_BalikobotAdminUi::system/config/form/field/array.phtml';

            $this->addColumn('shipper', ['label' => __('Shipper Name'), 'class' => 'required-entry']);
            // $this->addColumn('shipper', [
            //     'label' => __('Shipper Name'),
            //     'renderer' => $this->getShipperRenderer()
            // ]);
            $this->addColumn('balikobot_shippers', [
                'label' => __('Balikobot Shippers'),
                'renderer' => $this->getBalikobotShippersRenderer()
            ]);
            $this->addColumn('method', [
                'label' => __('Method'),
                'renderer' => $this->getMethodRenderer()
            ]);
            $this->_addAfter = false;
            $this->_addButtonLabel = __('Add');
        } catch (\Throwable $th) {
            $this->_template = 'ZingyBits_BalikobotAdminUi::system/config/form/field/error_plug.phtml';
        }
    }

    /**
     * Get the grid and scripts contents
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $activeShippingMethods = array_keys($this->shippingConfig->getActiveCarriers());
        $elements = [];
        foreach ($element->getValue() as $key => $value) {
            if (in_array($key, $activeShippingMethods)) {
                $elements[$key] = $value;
            }
        }
        $element->setValue($elements);

        $this->setElement($element);
        $html = $this->_toHtml();
        $this->_arrayRowsCache = null;
        // doh, the object is used as singleton!
        return $html;
    }

    /**
     * Prepare existing row data object
     *
     * @param DataObject $row
     * @throws LocalizedException
     */
    protected function _prepareArrayRow(DataObject $row): void
    {
        $options = [];

        $method = $row->getMethod();
        if ($method !== null) {
            $options['option_' . $this->getMethodRenderer()->calcOptionHash($method)] = 'selected="selected"';
        }

        $row->setData('option_extra_attrs', $options);
    }

    /**
     * Return balikobot shipper renderer
     *
     * @return BalikobotShippersColumn
     * @throws LocalizedException
     */
    private function getBalikobotShippersRenderer()
    {
        if (!$this->balikobotShippersRenderer) {
            $this->balikobotShippersRenderer = $this->getLayout()->createBlock(
                BalikobotShippersColumn::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->balikobotShippersRenderer;
    }

    /**
     * Return method renderer
     *
     * @return MethodColumn
     * @throws LocalizedException
     */
    private function getMethodRenderer()
    {
        if (!$this->methodRenderer) {
            $this->methodRenderer = $this->getLayout()->createBlock(
                MethodColumn::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->methodRenderer;
    }
}
