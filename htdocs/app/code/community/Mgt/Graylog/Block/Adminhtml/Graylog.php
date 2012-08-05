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

class Mgt_Graylog_Block_Adminhtml_Graylog extends Mage_Core_Block_Template
{
    protected function _toHtml()
    {
        $output = $this->_getStyles();
        $output .= "<iframe id=\"mgtGraylog\" scrolling=\"auto\"  src=\"{$this->_getIframeUrl()}\" style=\"width: 100%; height:100%;\"></iframe>";
        return $output;
    }

    protected function _getIframeUrl()
    {
        $iframeUrl = 'http://'.Mage::getStoreConfig(Mgt_Graylog_Model_Log::XML_PATH_GRAYLOG_HOST);;
        return $iframeUrl;
    }

    protected function _getStyles()
    {
        $styles = '
        <style type="text/css" media="screen">
        body, html {width: 100%;height: 100%;overflow-x:hidden;}
        .middle {padding:0px; }
        iframe {display:block; width:100%;height:100%; height:800px !important;border-bottom:0px;border-top:0px;border-left:0px;}
        .footer, #loading-mask {display:none;}
        </style>
        ';
        return $styles;
    }
}