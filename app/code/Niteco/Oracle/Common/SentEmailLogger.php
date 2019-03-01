<?php
/**
 * Niteco
 * User: tinh.ngo
 * Date: 3/1/2019
 * Time: 10:00 AM
 */

namespace Niteco\Oracle\Common;

class SentEmailLogger extends Logger {

    public function __construct()
    {
        parent::__construct('/var/log/niteco_oracle_sent_email.log');
    }
}