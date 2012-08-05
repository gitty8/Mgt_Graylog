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

class Mgt_Graylog_Model_Gelf_Message 
{
    const VERSION = '1.0';
 
    protected $_graylogHostname;
    protected $_graylogPort;
    protected $_maxChunkSize;
    protected $_data = array();
 
    public function  __construct($graylogHostname, $graylogPort = 12201, $maxChunkSize = 'LAN')
    {
         if (!is_numeric($graylogPort)) {
             throw new InvalidArgumentException("Port must be numeric");
         }
         $this->_graylogHostname = $graylogHostname;
         $this->_graylogPort = $graylogPort;
         switch ($maxChunkSize) {
             case 'WAN':
                 $this->_maxChunkSize = 1420;
                 break;
             case 'LAN':
                 $this->_maxChunkSize = 8154;
                 break;
             default:
                 $this->_maxChunkSize = $maxChunkSize;
         }
         $this->_setVersion(self::VERSION);
    }
 
    protected function _dataParamSet($dataType) 
    {
         if (isset($this->_data[$dataType]) && strlen($this->_data[$dataType]) > 0) {
             return true;
         }
 
         return false;
    }
 
    protected function _setVersion($version) 
    {
         $this->_data['version'] = $version;
    }
 
    public function send()
    {
         // Check if all required parameters are set.
         if (!$this->_dataParamSet('version') || !$this->_dataParamSet('short_message') || !$this->_dataParamSet('host')) {
             throw new Exception('Missing required data parameter: "version", "short_message" and "host" are required.');
         }
 
         // Convert data array to JSON and GZIP.
         $gzippedJsonData = gzcompress(json_encode($this->_data));
 
         $sock = stream_socket_client('udp://' . gethostbyname($this->_graylogHostname) .':' . $this->_graylogPort, $errno, $errstr);
 
         // Maximum size is 8192 byte. Split to chunks. (GELFv2 supports chunking)
         if (strlen($gzippedJsonData) > $this->_maxChunkSize) {
             // Too big for one datagram. Send in chunks.
             $msgId = microtime(true) . rand(0,10000);
 
             $parts = str_split($gzippedJsonData, $this->_maxChunkSize);
             $i = 0;
             foreach($parts as $part) {
                 fwrite($sock, $this->_prependChunkData($part, $msgId, $i, count($parts)));
                 $i++;
             }
 
         } else {
             // Send in one datagram.
             fwrite($sock, $gzippedJsonData);
         }
    }
 
    protected function _prependChunkData($data, $msgId, $seqNum, $seqCnt)
    {
         if (!is_string($data) || $data === '') {
             throw new Exception('Data must be a string and not be empty');
         }
 
         if (!is_integer($seqNum) || !is_integer($seqCnt) || $seqCnt <= 0) {
             throw new Exception('Sequence number and count must be integer. Sequence count must be bigger than 0.');
         }
 
         if ($seqNum > $seqCnt) {
             throw new Exception('Sequence number must be bigger than sequence count');
         }
 
         return pack('CC', 30, 15) . hash('sha256', $msgId, true) . pack('nn', $seqNum, $seqCnt) . $data;
    }
 
    // Setters / Getters.- Nothing to see here.
 
    public function setShortMessage($message)
    {
         $this->_data['short_message'] = $message;
    }
 
    public function setFullMessage($message)
    {
         $this->_data['full_message'] = $message;
    }
 
    public function setHost($host)
    {
         $this->_data['host'] = $host;
    }
 
    public function setLevel($level)
    {
         $this->_data['level'] = $level;
    }
 
    public function setType($type)
    {
         $this->_data['type'] = $type;
    }
 
    public function setFile($file)
    {
         $this->_data['file'] = $file;
    }
 
    public function setLine($line)
    {
         $this->_data['line'] = $line;
    }
 
    public function setFacility($facility)
    {
         $this->_data['facility'] = $facility;
    }
 
    public function setTimestamp($timestamp)
    {
       $this->_data['timestamp'] = $timestamp;
    }
 
    public function setAdditional($key, $value)
    {
       $key = str_replace (" ", "", $key);
       $this->_data["_" . $key] = $value;
    }
 
    public function getShortMessage()
    {
         return isset($this->_data['short_message']) ? $this->_data['short_message'] : null;
    }
 
    public function getFullMessage()
    {
         return isset($this->_data['full_message']) ? $this->_data['full_message'] : null;
    }
 
    public function getHost()
    {
         return isset($this->_data['host']) ? $this->_data['host'] : null;
    }
 
    public function getLevel()
    {
         return isset($this->_data['level']) ? $this->_data['level'] : null;
    }
 
    public function getType()
    {
         return isset($this->_data['type']) ? $this->_data['type'] : null;
    }
 
    public function getFile()
    {
         return isset($this->_data['file']) ? $this->_data['file'] : null;
    }
 
    public function getLine()
    {
         return isset($this->_data['line']) ? $this->_data['line'] : null;
    }
}
