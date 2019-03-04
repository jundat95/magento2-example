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
    private $configManager;

    public function __construct(
        \Niteco\Oracle\Common\SentEmailLogger $sentEmailLogger,
        \Niteco\Oracle\Helper\ScheduleManager $scheduleManager,
        \Niteco\Oracle\Helper\ConfigManager $configManager
    )
    {
        $this->sentEmailLogger = $sentEmailLogger;
        $this->scheduleManager = $scheduleManager;
        $this->configManager = $configManager;
    }

    public function execute() {

        $isEnableMail = $this->configManager->isSendMailEnable();
        if (empty($isEnableMail) || $isEnableMail === "0") return;

        $schedules = $this->scheduleManager->getOrdersScheduleByStatus(SentToOracleStatus::SENT_FAIL);

        if ($schedules->count() > 0) {
            $message = '';
            foreach ($schedules as $schedule) {
                $message .= ' || Order # '.$schedule->getData('increment_id').' , Message: '.$schedule->getData('message');
            }
            $this->configManager->sendMail($message);
        }

    }
}