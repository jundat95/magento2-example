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
    // created order
    const CREATED = 0;
    // sending to Oracle
    const SENDING = 1;
    // send Oracle success
    const SENT_SUCCESS = 2;
    // send Oracle fail
    const SENT_FAIL = 3;
    // send report to admin success
    const REPORTED = 4;

}