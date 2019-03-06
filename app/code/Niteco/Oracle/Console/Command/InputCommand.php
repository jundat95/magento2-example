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

    public function __construct(
        \Magento\Framework\App\State $state,
        \Niteco\Oracle\Helper\QueueManager $queueManager,

        \Niteco\Oracle\Helper\ScheduleManager $scheduleManager,
        \Niteco\Oracle\Helper\ConfigManager $configManager,
        \Niteco\Oracle\Helper\MailManager $mailManager
    )
    {
        $this->state = $state;
        $this->queueManager = $queueManager;

        $this->scheduleManager = $scheduleManager;
        $this->configManager = $configManager;
        $this->mailManager = $mailManager;

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
//        if ($commandName === 'sendorders') {
//        }
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

}