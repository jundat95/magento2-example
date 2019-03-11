<?php
/**
 * Niteco
 * User: tinhngo
 * Date: 3/4/19
 * Time: 11:41 PM
 */

namespace Niteco\Oracle\Console\Command;

use Magento\Framework\Console\Cli;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InputCommand extends Command {

    const INPUT_KEY_NAME = 'CommandName';

    private $state;
    private $queueManager;
    private $sendOrders;
    private $sendEmail;

    public function __construct(
        \Magento\Framework\App\State $state,
        \Niteco\Oracle\Helper\QueueManager $queueManager,
        \Niteco\Oracle\Cron\SendOrders $sendOrders,
        \Niteco\Oracle\Cron\SendEmail $sendEmail
    )
    {
        $this->state = $state;
        $this->queueManager = $queueManager;
        $this->sendOrders = $sendOrders;
        $this->sendEmail = $sendEmail;

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
            $output->writeln('List queues: ');
            $output->writeln($queues);
        } else if ($commandName === 'queues2') {
            $queues2 = $this->queueManager->getAllOrderIdQueue2();
            $output->writeln('List queues2: ');
            $output->writeln($queues2);
        } else if ($commandName === 'queues3') {
            $queues3 = $this->queueManager->getAllOrderIdQueue3();
            $output->writeln('List queues3: ');
            $output->writeln($queues3);
        } else if ($commandName === 'sendmails') {
            $this->sendEmail->execute();
        } else if ($commandName === 'sendorders') {
            $this->sendOrders->execute();
        }  else if ($commandName === 'sendorders2') {
            $this->sendOrders->executeQueue2();
        }  else if ($commandName === 'sendorders3') {
            $this->sendOrders->executeQueue3();
        } else {
            $output->writeln('Niteco Oracle: invalid options');
            $output->writeln('usage:    niteco:oracle [queues] [sendmails] [sendorders]');
            $output->writeln('(default operation is replace)');
            $output->writeln('          sendmails       (execute send email)');
            $output->writeln('          sendorders      (execute send orders)');
            $output->writeln('          sendorders2     (execute send orders2)');
            $output->writeln('          sendorders3     (execute send orders3)');
            $output->writeln('          queues          (list queues)');
            $output->writeln('          queues2         (list queues2)');
            $output->writeln('          queues3         (list queues3)');
        }
        return Cli::RETURN_SUCCESS;
    }

}