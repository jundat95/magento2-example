<?php

namespace Mastering\SampleModule\Controller\Index;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Action;

class Index extends Action
{
    public function execute()
    {
        return $result = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

    }
}