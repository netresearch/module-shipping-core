<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ShippingSettings\Config;

use Magento\Framework\App\CacheInterface;
use Magento\Framework\Config\ConverterInterface;
use Magento\Framework\Config\Dom;
use Magento\Framework\Config\FileResolverInterface;
use Magento\Framework\Config\Reader\Filesystem;
use Magento\Framework\Config\SchemaLocatorInterface;
use Magento\Framework\Config\ValidationStateInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Netresearch\ShippingCore\Model\ShippingSettings\ShippingOption\Codes;
use Psr\Log\LoggerInterface;

class Reader extends Filesystem
{
    private const CACHE_KEY_SHIPPING_OPTIONS_CONFIG = 'nrshipping_option_config';

    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        FileResolverInterface $fileResolver,
        ConverterInterface $converter,
        SchemaLocatorInterface $schemaLocator,
        ValidationStateInterface $validationState,
        CacheInterface $cache,
        SerializerInterface $serializer,
        LoggerInterface $logger,
        string $fileName,
        array $idAttributes = [],
        string $domDocumentClass = Dom::class,
        string $defaultScope = 'global'
    ) {
        $this->cache = $cache;
        $this->serializer = $serializer;
        $this->logger = $logger;

        parent::__construct(
            $fileResolver,
            $converter,
            $schemaLocator,
            $validationState,
            $fileName,
            $idAttributes,
            $domDocumentClass,
            $defaultScope
        );
    }

    /**
     * Do base configuration post processing and cache the result. Load from cache on successive requests.
     *
     * You must clear the Magento cache to apply changes to any shipping_settings.xml file.
     *
     * @param string|null $scope
     * @return array
     */
    public function read($scope = null): array
    {
        $data = $this->cache->load(self::CACHE_KEY_SHIPPING_OPTIONS_CONFIG . $scope);

        if ($data) {
            try {
                return $this->serializer->unserialize($data);
            } catch (\InvalidArgumentException $exception) {
                // re-read the files if the cache is corrupted
                $this->logger->debug('Issue with shipping option cache: ' . $exception->getMessage());
            }
        }

        $data = parent::read($scope);
        $data = $this->applyBaseConfiguration($data);
        $this->cache->save(
            $this->serializer->serialize($data),
            self::CACHE_KEY_SHIPPING_OPTIONS_CONFIG . $scope,
            [\Magento\Framework\App\Config::CACHE_TAG]
        );

        return $data;
    }

    /**
     * Merge data from "base" carrier into other carriers and
     * remove "base" carrier from the data.
     *
     * @param array $result
     * @return array
     */
    private function applyBaseConfiguration(array $result): array
    {
        if (!isset($result['carriers'][Codes::CARRIER_BASE])) {
            return $result;
        }

        $baseCarrier = $result['carriers'][Codes::CARRIER_BASE];
        unset($result['carriers'][Codes::CARRIER_BASE]);

        foreach ($result['carriers'] as $carrierCode => $carrier) {
            $result['carriers'][$carrierCode] = $this->extendRecursive($baseCarrier, $carrier);
        }

        return $result;
    }

    /**
     * Recursively extend a nested base array with another array's values.
     * The extension array will override any values already defined in the base array.
     *
     * This is different from array_merge_recursive in that non-array values are actually overwritten by the
     * extension array.
     *
     * @param $baseArray
     * @param $extensionArray
     * @return array
     */
    private function extendRecursive($baseArray, $extensionArray): array
    {
        foreach ($extensionArray as $key => $value) {
            if (!is_array($value) || !isset($baseArray[$key])) {
                $baseArray[$key] = $value;
            } elseif (is_array($value)) {
                $baseArray[$key] = $this->extendRecursive($baseArray[$key], $value);
            }
        }

        return $baseArray;
    }
}
