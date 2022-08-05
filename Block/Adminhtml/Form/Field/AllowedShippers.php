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
use ZingyBits\BalikobotAdminUi\Block\Adminhtml\Form\Field\MethodColumn;
use ZingyBits\BalikobotAdminUi\Block\Adminhtml\Form\Field\ShipperColumn;
use ZingyBits\BalikobotAdminUi\Block\Adminhtml\Form\Field\BalikobotShippersColumn;

/**
 * Class Ranges
 */
class AllowedShippers extends AbstractFieldArray
{
    /**
     * @var ShipperColumn
     */
    private $shipperRenderer;

    /**
     * @var MethodColumn
     */
    private $methodRenderer;

    /**
     * @var BalikobotShippersColumn
     */
    private $balikobotShippersRenderer;

    /**
     * Prepare rendering the new field by adding all the needed columns
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
     * @return ShipperColumn
     * @throws LocalizedException
     */
    private function getShipperRenderer()
    {
        if (!$this->shipperRenderer) {
            $this->shipperRenderer = $this->getLayout()->createBlock(
                ShipperColumn::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->shipperRenderer;
    }

    /**
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
