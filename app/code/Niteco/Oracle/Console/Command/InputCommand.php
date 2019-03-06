<?php
/**
 * Niteco
 * User: tinhngo
 * Date: 3/4/19
 * Time: 11:41 PM
 */

namespace Niteco\Oracle\Console\Command;

use Magento\Framework\Console\Cli;
use Niteco\Oracle\Helper\SentToOracleStatus;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InputCommand extends Command {

    const INPUT_KEY_NAME = 'CommandName';

    private $state;

    private $queueManager;

    private $scheduleManager;
    private $configManager;
    private $mailManager;

    private $sentOracleLogger;
    private $orderManager;
    private $oracleManager;
    private $timezoneInterface;

    public function __construct(
        \Magento\Framework\App\State $state,
        \Niteco\Oracle\Helper\QueueManager $queueManager,

        \Niteco\Oracle\Helper\ScheduleManager $scheduleManager,
        \Niteco\Oracle\Helper\ConfigManager $configManager,
        \Niteco\Oracle\Helper\MailManager $mailManager,

        \Niteco\Oracle\Common\SentOracleLogger $sentOracleLogger,
        \Niteco\Oracle\Helper\OrderManager $orderManager,
        \Niteco\Oracle\Helper\OracleManager $oracleManager,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezoneInterface
    )
    {
        $this->state = $state;
        $this->queueManager = $queueManager;

        $this->scheduleManager = $scheduleManager;
        $this->configManager = $configManager;
        $this->mailManager = $mailManager;

        $this->sentOracleLogger = $sentOracleLogger;
        $this->orderManager = $orderManager;
        $this->oracleManager = $oracleManager;
        $this->queueManager = $queueManager;
        $this->timezoneInterface = $timezoneInterface;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('niteco:oracle')
            ->addArgument(
                self::INPUT_KEY_NAME,
                InputArgument::REQUIRED,
                'Command name'
            );
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_FRONTEND);

        $commandName = $input->getArgument(self::INPUT_KEY_NAME);
        if ($commandName === 'queues') {
            $queues = $this->queueManager->getAllOrderId();
            $output->writeln('Niteco: ');
            $output->writeln($queues);
        }
        if ($commandName === 'sendmails') {
            $output->writeln('sending email');
            $this->sendEmails();
        }
        if ($commandName === 'sendorders') {
            $output->writeln('sending order');
            $this->sendOrdersWithRedis();
        }
        return Cli::RETURN_SUCCESS;
    }

    public function sendEmails() {
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

    public function sendOrdersWithRedis() {

        $orderId = $this->queueManager->popOrderId();
        if ($orderId) {

            // get order
            $order = $this->orderManager->getOrderById($orderId);
            $orderData = $this->orderManager->getOrderData($order);

            // get order schedule
            $schedules = $this->scheduleManager->getOrdersScheduleById($orderId);
            $schedule = $schedules->getFirstItem();

            $this->scheduleManager->changeStatus(SentToOracleStatus::SENDING, $schedule);
            $currentTime = $this->timezoneInterface->date()->getTimestamp();
            $this->scheduleManager->changeTimeExecute($currentTime, $schedule);

            if ($this->oracleManager->pushOrderToOracle($orderData)) {
                $this->sentOracleLogger->logText('Sent order '.$orderId.' to Oracle is success');

                $this->orderManager->addOrderComment('Transferred to Oracle', $order);
                $this->scheduleManager->changeStatus(SentToOracleStatus::SENT_SUCCESS, $schedule);
                $currentTime = $this->timezoneInterface->date()->getTimestamp();
                $this->scheduleManager->changeTimeFinished($currentTime, $schedule);
            } else {
                $this->sentOracleLogger->logText('Sent order '.$orderId.' to Oracle is fail');

                $this->scheduleManager->changeStatus(SentToOracleStatus::SENT_FAIL, $schedule);
                $this->scheduleManager->setMessage('Sent order '.$orderId.' to Oracle is fail', $schedule);

                // add orderId to queue
                $this->queueManager->pushOrderId($orderId);
            }

        } else {
            $this->sentOracleLogger->logText('queue is empty');
        }

    }

}