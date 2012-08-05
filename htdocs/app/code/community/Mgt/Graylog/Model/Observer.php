<?php
/**
 * MGT-Commerce GmbH
 * http://www.mgt-commerce.com
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@mgt-commerce.com so we can send you a copy immediately.
 *
 * @category    Mgt
 * @package     Mgt_Graylog
 * @author      Stefan Wieczorek <stefan.wieczorek@mgt-commerce.com>
 * @copyright   Copyright (c) 2012 (http://www.mgt-commerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mgt_GrayLog_Model_Observer
{
    public function addAdminhtmlDashboardTab(Varien_Event_Observer $observer)
    {
       $block = $observer->getEvent()->getBlock();
       $isGraylogEnabled = Mage::helper('mgt_graylog')->isEnabled();
       if ($block && ($block instanceof Mage_Adminhtml_Block_Dashboard_Grids) && $isGraylogEnabled) {
           $dashboardTab = array(
               'label'     => Mage::helper('core')->__('Graylog Dashboard'),
               'content'   => Mage::app()->getLayout()->createBlock('mgt_graylog_adminhtml/dashboard_tab_graylog_dashboard')->toHtml(),
           );
           $block->addTab('graylog_dashboard', $dashboardTab);
       }
    }
    
    public function sendLog(Varien_Event_Observer $observer)
    {
        $exception = $observer->getException();
        if ($exception) {
            $logMessage = '';
            $logData = array(
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString()
            );
            foreach ($logData as $key => $value) {
                if ($value) {
                    $logMessage .= $key.PHP_EOL.$value.PHP_EOL.PHP_EOL;
                }
            }
            Mgt_Graylog_Model_Log::error('magento/error', $exception->getMessage(), $logMessage);
        }
    }
}