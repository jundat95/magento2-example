<?php
/**
 * Niteco
 * User: tinh.ngo
 * Date: 2/22/2019
 * Time: 3:04 PM
 */

namespace Niteco\Oracle\Common;

abstract class Logger {

    private $path_file_log = '/var/log/niteco_oracle.log';

    public function __construct($path_file_log)
    {
        $this->path_file_log = $path_file_log;
    }

    public function logText($text) {
        $writer = new \Zend\Log\Writer\Stream(BP . $this->path_file_log);
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info($text);
    }

    public function logArray($array) {
        $writer = new \Zend\Log\Writer\Stream(BP . $this->path_file_log);
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info(print_r($array, true));
    }
}