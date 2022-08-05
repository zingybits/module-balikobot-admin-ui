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

namespace ZingyBits\BalikobotAdminUi\Plugin\Sales\Block\Adminhtml\Order;

use Magento\Sales\Block\Adminhtml\Order\View as OrderView;

class View
{
   public function beforeSetLayout(OrderView $subject)
   {
       $subject->addButton(
           'order_custom_button',
           [
               'label' => __('Print Label'),
               'class' => __('balikobot-button'),
               'id' => 'order-view-balikobot-button',
               'onclick' => "zingybits_balikobot_add_balik()"
           ]
       );
   }
}
