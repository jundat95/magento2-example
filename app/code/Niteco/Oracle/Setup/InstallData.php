<?php
/**
 * Niteco Co Ltd.
 * User: tinh.ngo
 * Date: 2/20/2019
 * Time: 3:43 PM
 */

namespace Niteco\Oracle\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface {

    /**
     * Installs data for a module
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $setup->getConnection()->insert(
            $setup->getTable('sales_order'),
            [
                'sent_to_oracle' => 0
            ]
        );

        $setup->endSetup();
    }
}