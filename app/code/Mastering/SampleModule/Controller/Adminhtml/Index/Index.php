<?php

namespace Mastering\SampleModule\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;

class Index extends Action {

    public function execute() {

        $result = $this->resultFactory->create(ResultFactory::TYPE_RAW);
        $result->setContents("hello world Admin");
        return $result;
    }
}