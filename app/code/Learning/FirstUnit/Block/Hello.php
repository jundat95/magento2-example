<?php

namespace Learning\FirstUnit\Block;

use Magento\Framework\View\Element\Template;

class Hello extends Template {

    public function __construct(
        Template\Context $content,
        array $data = []
    )
    {
        parent::__construct($content, $data);
    }


}