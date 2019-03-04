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

class Queues extends Command {

    private $queueManager;

    public function __construct(
        \Niteco\Oracle\Helper\QueueManager $queueManager
    )
    {
        $this->queueManager = $queueManager;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('niteco:oracle:queues');
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $queues = $this->queueManager->getAllOrderId();

        $output->writeln('Queues: ');
        $output->writeln($queues);

//        return Cli::RETURN_SUCCESS;
    }

}