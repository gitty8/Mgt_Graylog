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

class Mgt_Graylog_Model_Graylog
{  
    const DEFAULT_GRAYLOG_PORT = 12201;

    protected $_gelfMessage;
 
    public function __construct(array $options)
    {
         if (isset($options['port']) && $options['port']) {
             $graylogPort = $options['port'];
         } else {
             $graylogPort = self::DEFAULT_GRAYLOG_PORT;
         }
         
         $url = parse_url($options['host']);
         $this->_gelfMessage = new Mgt_Graylog_Model_Gelf_Message($url['host'], $graylogPort);
    }
     
    public function add($namespace, $level, $shortMessage, $message, $trace) 
    {
         $message = self::_getMessage($message, $trace);
         $this->_gelfMessage->setShortMessage($shortMessage);
         $this->_gelfMessage->setFullMessage($message);
         $this->_gelfMessage->setFacility($namespace);
         $this->_gelfMessage->setHost(self::getHost());
         $this->_gelfMessage->setLevel($level);
         $this->_gelfMessage->send();
    }
     
    static public function getHost()
    {
         return gethostname();
    }
     
    static protected function _getShortMessage($message)
    {
          return mb_substr($message, 0, self::NUMBER_SHORT_MESSAGE_CHARACTER);
    }
     
    static protected function _getMessage($message, $trace)
    {
         return $message;
    }
}