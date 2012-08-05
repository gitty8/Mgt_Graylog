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

class Mgt_Graylog_Model_Log
{
    const XML_PATH_GRAYLOG_ENABLED  = 'mgt_graylog/mgt_graylog/active';
    const XML_PATH_GRAYLOG_HOST  = 'mgt_graylog/mgt_graylog/host';
    const XML_PATH_GRAYLOG_PORT  = 'mgt_graylog/mgt_graylog/port';
    const XML_PATH_GRAYLOG_SEVERITIES  = 'mgt_graylog/mgt_graylog/severities';
    
    // levels
    const ALERT = 1;
    const CRITICAL = 2;
    const ERROR = 3;
    const WARNING = 4;
    const NOTICE = 5;
    const INFO = 6;
    const DEBUG = 7;
    const ALL = 31;
     
    /**
     * collection of logger
     *
     * @var array
     */
    protected static $_logger = array();
     
    /**
     * is appending
     *
     * @var boolean
     */
    protected static $_isAppending = false;
     
    /**
     * configuration
     *
     * @var Zend_Config
     */
    protected static $_config;
     
    /**
     * make a log entry with debug namespace
     *
     * @param  string $namespace
     * @param  string $message
     * @param  object $cause
     * @return void
     */
    public static function alert($namespace, $shortMessage, $message, Exception $cause = null)
    {
        self::_add($namespace, self::ALERT, $shortMessage, $message, $cause ? $cause->getTrace():array());
    }
     
    /**
     * make a log entry with debug namespace
     *
     * @param  string $namespace
     * @param  string $message
     * @param  object $cause
     * @return void
     */
    public static function critical($namespace, $shortMessage, $message, Exception $cause = null)
    {
        self::_add($namespace, self::CRITICAL, $shortMessage, $message, $cause ? $cause->getTrace():array());
    }
     
    /**
     * make a log entry with debug namespace
     *
     * @param  string $namespace
     * @param  string $message
     * @param  object $cause
     * @return void
     */
    public static function error($namespace, $shortMessage, $message, Exception $cause = null)
    {
        self::_add($namespace, self::ERROR, $shortMessage, $message, $cause ? $cause->getTrace():array());
    }
     
    /**
     * make a log entry with debug namespace
     *
     * @param  string $namespace
     * @param  string $message
     * @param  object $cause
     * @return void
     */
    public static function warning($namespace, $shortMessage, $message, Exception $cause = null)
    {
        self::_add($namespace, self::WARNING, $shortMessage, $message, $cause ? $cause->getTrace():array());
    }
     
    /**
     * make a log entry with debug namespace
     *
     * @param  string $namespace
     * @param  string $message
     * @param  object $cause
     * @return void
     */
    public static function notice($namespace, $shortMessage, $message, Exception $cause = null)
    {
        self::_add($namespace, self::NOTICE, $shortMessage, $message, $cause ? $cause->getTrace():array());
    }
     
    /**
     * make a log entry with debug namespace
     *
     * @param  string $namespace
     * @param  string $message
     * @param  object $cause
     * @return void
     */
    public static function info($namespace, $shortMessage, $message, Exception $cause = null)
    {
        self::_add($namespace, self::INFO, $shortMessage, $message, $cause ? $cause->getTrace():array());
    }
     
    /**
     * make a log entry with debug namespace
     *
     * @param  string $namespace
     * @param  string $message
     * @param  object $cause
     * @return void
     */
    public static function debug($namespace, $shortMessage, $message, Exception $cause = null)
    {
        self::_add($namespace, self::DEBUG, $shortMessage, $message, $cause ? $cause->getTrace():array());
    }
     
    /**
     * Add a logger
     *
     * @param  object $logger
     * @param  string $message
     * @param  integer $levelMask
     * @return void
     */
    public static function addLogger($logger, $levelMask = self::ALL, $namespace = null)
    {
        if(is_array($namespace)) {
            foreach($namespace as $ns) {
                self::$_logger[] = array($ns, $levelMask, $logger);
            }
        } else {
            self::$_logger[] = array($namespace, $levelMask, $logger);
        }
    }
     
    /**
     * Remove a logger
     *
     * @param  object $logger
     * @return void
     */
    public static function removeLogger(Log_Model_Logger $loggerToRemove)
    {
        $logger = array();
        foreach(self::getLogger() as $logger) {
            if($logger[2] !== $loggerToRemove) {
                $logger[] = $logger;
            }
        }
        self::$_logger = $logger;
    }
     
    public static function getLogger()
    {
        if (!self::$_logger) {
            $isGraylogEnabled = Mage::helper('mgt_graylog')->isEnabled();
            if ($isGraylogEnabled) {
                $graylogOptions = array(
                    'host' => Mage::getStoreConfig(self::XML_PATH_GRAYLOG_HOST),
                    'port' => Mage::getStoreConfig(self::XML_PATH_GRAYLOG_PORT),
                );
                $severities = explode(',', Mage::getStoreConfig(self::XML_PATH_GRAYLOG_SEVERITIES));
                if (count($severities) == 1 && $severities[0] == Mgt_Graylog_Model_Log::ALL) {
                    $severities = Mgt_Graylog_Model_Log::ALL;
                }
                $graylog = new Mgt_Graylog_Model_Graylog($graylogOptions);
                self::addLogger($graylog, $severities);
            }
        }
        return self::$_logger;
    }
     
    /**
     * Returns the level name
     *
     * @param  string $level
     * @return string
     */
    public static function getLevelName($level)
    {
        switch($level) {
            case self::ALERT:
                return 'Alert';
            case self::CRITICAL:
                return 'Critical';
            case self::ERROR:
                return 'Error';
            case self::WARNING:
                return 'Warning';
            case self::NOTICE:
                return 'Notice';
            case self::INFO:
                return 'Info';
            case self::DEBUG:
                return 'Debug';
            default:
                throw new Exception('log', 'Unknown logging level '.$level);
        }
    }
     
    /**
     * add a log message
     *
     * @param  string $namespace
     * @param  integer $level
     * @param  string $message
     * @param  string $trace
     * @return void
     */
    protected static function _add($namespace, $level, $shortMessage, $message, $trace)
    {
        if (self::$_isAppending) {
            return;
        }
        self::$_isAppending = true;
        foreach(self::getLogger() as $logger) {
            $acceptNamespace = !$logger[0] || strpos($namespace, $logger[0]) === 0;
            
            if (is_array($logger[1])) {
                $acceptLevel=($level&&in_array($level, $logger[1]));
            } else {
                $acceptLevel=0!=($level&$logger[1]);
            }

            if ($acceptNamespace && $acceptLevel) {
                $logger[2]->add($namespace, $level, $shortMessage, $message, $trace);
            }
        }
        self::$_isAppending = false;
    }
}