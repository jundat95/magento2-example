<?php
/**
 * Niteco
 * User: tinh.ngo
 * Date: 2/22/2019
 * Time: 3:30 PM
 */

namespace Niteco\Oracle\Common;

class SentOracleLogger extends Logger {

    public function __construct()
    {
        parent::__construct('/var/log/niteco_oracle_sent_oracle.log');
    }
}