<?php
/**
 * Niteco
 * User: tinh.ngo
 * Date: 3/1/2019
 * Time: 9:58 AM
 */

namespace Niteco\Oracle\Cron;

use Niteco\Oracle\Helper\SentToOracleStatus;

class SendEmail {

    private $sentEmailLogger;
    private $scheduleManager;

    public function __construct(
        \Niteco\Oracle\Common\SentEmailLogger $sentEmailLogger,
        \Niteco\Oracle\Helper\ScheduleManager $scheduleManager
    )
    {
        $this->sentEmailLogger = $sentEmailLogger;
        $this->scheduleManager = $scheduleManager;
    }

    public function execute() {
        $schedules = $this->scheduleManager->getOrdersScheduleByStatus(SentToOracleStatus::SENT_FAIL);

        if ($schedules->count() > 0) {
            $message = 'List orders sent to Oracle error: ';
            $message .= '</br>';

            foreach ($schedules as $schedule) {
                $message .= '</br>';
                $message .= '-Order # '.$schedule->getData('increment_id').'  -Message: '.$schedule->getData('message');
            }
            $this->sentEmailLogger->logArray($message);


        }

    }
}