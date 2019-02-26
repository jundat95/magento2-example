<?php
/**
 * Niteco
 * User: tinh.ngo
 * Date: 2/22/2019
 * Time: 4:06 PM
 */

namespace Niteco\Oracle\Helper;

abstract class SentToOracleStatus {

    /*
     * Define status: sent_to_oracle
     */
    const CREATED = 0;
    const SENDING = 1;
    const SENT_SUCCESS = 2;
    const SENT_FAIL = 3;

}