<?php

/**
 * See LICENSE.md for license details.
 */

namespace Netresearch\ShippingCore\Model\ShippingSettings;

use Magento\Framework\Webapi\ServiceOutputProcessor;
use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingDataInterface;
use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingDataInterfaceFactory;
use Netresearch\ShippingCore\Api\ShippingSettings\CheckoutManagementInterface;
use Netresearch\ShippingCore\Model\ShippingSettings\Data\CarrierData;
use Netresearch\ShippingCore\Model\ShippingSettings\Data\Comment;
use Netresearch\ShippingCore\Model\ShippingSettings\Data\Compatibility;
use Netresearch\ShippingCore\Model\ShippingSettings\Data\Input;
use Netresearch\ShippingCore\Model\ShippingSettings\Data\ItemCombinationRule;
use Netresearch\ShippingCore\Model\ShippingSettings\Data\ItemShippingOptions;
use Netresearch\ShippingCore\Model\ShippingSettings\Data\Metadata;
use Netresearch\ShippingCore\Model\ShippingSettings\Data\Option;
use Netresearch\ShippingCore\Model\ShippingSettings\Data\Route;
use Netresearch\ShippingCore\Model\ShippingSettings\Data\ShippingOption;
use Netresearch\ShippingCore\Model\ShippingSettings\Data\ValidationRule;

class ShippingDataHydrator
{
    private const CLASSMAP = [
        'carriers' => [
            'type' => 'array',
            'className' => CarrierData::class,
        ],
        'metadata' => [
            'type' => 'object',
            'className' => Metadata::class,
        ],
        'commentsBefore' => [
            'type' => 'array',
            'className' => Comment::class,
        ],
        'commentsAfter' => [
            'type' => 'array',
            'className' => Comment::class,
        ],
        'comment' => [
            'type' => 'object',
            'className' => Comment::class,
        ],
        'packageOptions' => [
            'type' => 'array',
            'className' => ShippingOption::class,
        ],
        'serviceOptions' => [
            'type' => 'array',
            'className' => ShippingOption::class,
        ],
        'shippingOptions' => [
            'type' => 'array',
            'className' => ShippingOption::class,
        ],
        'itemOptions' => [
            'type' => 'array',
            'className' => ItemShippingOptions::class,
        ],
        'inputs' => [
            'type' => 'array',
            'className' => Input::class,
        ],
        'options' => [
            'type' => 'array',
            'className' => Option::class,
        ],
        'validationRules' => [
            'type' => 'array',
            'className' => ValidationRule::class,
        ],
        'itemCombinationRule' => [
            'type' => 'object',
            'className' => ItemCombinationRule::class,
        ],
        'routes' => [
            'type' => 'array',
            'className' => Route::class,
        ],
        'compatibilityData' => [
            'type' => 'array',
            'className' => Compatibility::class,
        ]
    ];

    /**
     * @var ShippingDataInterfaceFactory
     */
    private $shippingDataFactory;

    /**
     * @var ServiceOutputProcessor
     */
    private $outputProcessor;

    public function __construct(
        ShippingDataInterfaceFactory $shippingDataFactory,
        ServiceOutputProcessor $outputProcessor
    ) {
        $this->shippingDataFactory = $shippingDataFactory;
        $this->outputProcessor = $outputProcessor;
    }

    /**
     * Convert a plain nested array of scalar types into a ShippingDataInterface object.
     *
     * Note: For M2.2 compatibility, created types must not have constructors with required values. Only populate
     * entities through setters.
     *
     * @param mixed[] $data
     * @return ShippingDataInterface
     * @throws \RuntimeException
     */
    public function toObject(array $data): ShippingDataInterface
    {
        return $this->shippingDataFactory->create(
            ['carriers' => $this->recursiveToObject('carriers', $data['carriers'])]
        );
    }

    /**
     * Convert a ShippingDataInterface object into a plain nested array of scalar types.
     *
     * @param ShippingDataInterface $data
     * @return array
     */
    public function toArray(ShippingDataInterface $data): array
    {
        return $this->outputProcessor->process(
            $data,
            CheckoutManagementInterface::class,
            'getCheckoutData'
        );
    }

    /**
     * @param string $key
     * @param mixed $data
     * @return mixed
     */
    private function recursiveToObject(string $key, $data)
    {
        if (array_key_exists($key, self::CLASSMAP)) {
            $className = self::CLASSMAP[$key]['className'];
            $type = self::CLASSMAP[$key]['type'];

            if ($type === 'array') {
                $result = [];
                foreach ($data as $arrayKey => $arrayItem) {
                    $result[$arrayKey] = new $className();
                    foreach ($arrayItem as $property => $value) {
                        $result[$arrayKey]->{'set' . ucfirst($property)}(
                            $this->recursiveToObject($property, $value)
                        );
                    }
                }
            } else {
                $result = new $className();
                foreach ($data as $property => $value) {
                    $result->{'set' . ucfirst($property)}(
                        $this->recursiveToObject($property, $value)
                    );
                }
            }
        } else {
            $result = $data;
        }

        return $result;
    }
}
