<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ShippingSettings\Config;

use Magento\Framework\Config\FileIterator;
use Magento\Framework\Config\FileIteratorFactory;
use Magento\Framework\Config\FileResolverInterface;
use Magento\Framework\Module\Dir\Reader as DirReader;

class FileResolver implements FileResolverInterface
{
    /**
     * Module configuration file reader
     *
     * @var DirReader
     */
    private $moduleReader;

    /**
     * File iterator factory
     *
     * @var FileIteratorFactory
     */
    private $iteratorFactory;

    public function __construct(DirReader $moduleReader, FileIteratorFactory $iteratorFactory)
    {
        $this->moduleReader = $moduleReader;
        $this->iteratorFactory = $iteratorFactory;
    }

    /**
     * Retrieve the list of configuration files with given name that relate to specified scope
     *
     * @param string $filename
     * @param string $scope
     * @return FileIterator
     */
    #[\Override]
    public function get($filename, $scope): FileIterator
    {
        $paths = $this->moduleReader->getConfigurationFiles($filename)->toArray();
        if ($scope !== 'global') {
            $paths += $this->moduleReader->getConfigurationFiles($scope . '/' . $filename)->toArray();
        }

        return $this->iteratorFactory->create(array_keys($paths));
    }
}
