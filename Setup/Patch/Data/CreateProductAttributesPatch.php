<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Setup\Patch\Data;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;
use Magento\Framework\Validator\ValidateException;
use Netresearch\ShippingCore\Setup\Module\DataInstaller;
use Netresearch\ShippingCore\Setup\Module\Uninstaller;

class CreateProductAttributesPatch implements PatchRevertableInterface, DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    #[\Override]
    public static function getDependencies(): array
    {
        return [];
    }

    #[\Override]
    public function getAliases(): array
    {
        return [];
    }

    /**
     * Create product attributes
     *
     * @return void
     * @throws LocalizedException
     * @throws LocalizedException|ValidateException
     */
    #[\Override]
    public function apply()
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        DataInstaller::addHsCodeAttribute($eavSetup);
        DataInstaller::addExportDescriptionAttribute($eavSetup);
    }

    #[\Override]
    public function revert()
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        Uninstaller::deleteAttributes($eavSetup);
    }
}
