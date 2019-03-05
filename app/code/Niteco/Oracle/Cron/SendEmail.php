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
    private $mailManager;

    public function __construct(
        \Niteco\Oracle\Common\SentEmailLogger $sentEmailLogger,
        \Niteco\Oracle\Helper\ScheduleManager $scheduleManager,
        \Niteco\Oracle\Helper\ConfigManager $configManager,
        \Niteco\Oracle\Helper\MailManager $mailManager
    )
    {
        $this->sentEmailLogger = $sentEmailLogger;
        $this->scheduleManager = $scheduleManager;
        $this->configManager = $configManager;
        $this->mailManager = $mailManager;
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
            if ($this->mailManager->sendMail($message)) {
                foreach ($schedules as $schedule) {
                    $this->scheduleManager->changeStatus(SentToOracleStatus::REPORTED, $schedule);
                }
            }
        }

    }
}