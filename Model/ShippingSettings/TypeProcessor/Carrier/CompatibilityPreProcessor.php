<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ShippingSettings\TypeProcessor\Carrier;

use Magento\Sales\Api\Data\ShipmentInterface;
use Netresearch\ShippingCore\Api\Data\ShippingSettings\CarrierDataInterface;
use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\CompatibilityInterfaceFactory;
use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOptionInterface;
use Netresearch\ShippingCore\Api\ShippingSettings\TypeProcessor\CarrierDataProcessorInterface;

class CompatibilityPreProcessor implements CarrierDataProcessorInterface
{
    /**
     * @var CompatibilityInterfaceFactory
     */
    private $ruleFactory;

    public function __construct(CompatibilityInterfaceFactory $ruleFactory)
    {
        $this->ruleFactory = $ruleFactory;
    }

    /**
     * Normalize all given codes into "compound code" format.
     *
     * A compound code is a string in the format {optionCode}.{inputCode}.
     * For CompatibilityRule masters and subjects it's also valid to pass only an option code as a shortcut for all
     * of the inputs of that option. This method will expand those into separate compound codes for every input.
     *
     * Codes that are already in compound format remain unchanged. codes for inputs that don't exist (anymore) are
     * stripped.
     *
     * @example ['preferredNeighbour'] will become ['preferredNeighbour.name', 'preferredNeighbour.address']
     * @param string[] $codes                               The list of codes to convert
     * @param ShippingOptionInterface[] $shippingOptions    The list of shipping options matching the codes
     * @return string[]                                     List of compound codes
     *
     */
    private function convertToCompoundCodes(array $codes, array $shippingOptions): array
    {
        $result = [];
        foreach ($codes as $code) {
            if (strpos($code, '.') !== false) {
                $result[] = $code;
                continue;
            }
            foreach ($shippingOptions as $shippingOption) {
                if ($code !== $shippingOption->getCode()) {
                    continue;
                }
                foreach ($shippingOption->getInputs() as $input) {
                    $result[] = $code . '.' . $input->getCode();
                }
            }
        }

        return $result;
    }

    /**
     * Split up masterless rules into separate rules.
     *
     * This is useful for inputs with bidirectional dependency as opposed to a hierarchy.
     * - Hierarchy: If input A checked, then input B requires a value.
     * - Mutual dependency: If input A has a value, then input B requires a value and vice versa.
     *
     * In this example, the mutual dependency will be split into two separate, hierarchical rules,
     * which is much easier to do than to handle master-less rules in the CompatibilityEnforcer.
     *
     * The processor also converts all rule subject identifiers into compound codes before
     * splitting rules, which allows to specify only the option as subject as opposed to
     * all its inputs individually (a catch-all shortcut).
     *
     * @param CarrierDataInterface $carrierSettings
     * @param int $storeId
     * @param string $countryCode
     * @param string $postalCode
     * @param ShipmentInterface|null $shipment
     *
     * @return CarrierDataInterface
     * @throws \InvalidArgumentException
     */
    public function process(
        CarrierDataInterface $carrierSettings,
        int $storeId,
        string $countryCode,
        string $postalCode,
        ShipmentInterface $shipment = null
    ): CarrierDataInterface {
        $processedRules = [];
        $shippingOptions = array_merge($carrierSettings->getServiceOptions(), $carrierSettings->getPackageOptions());

        foreach ($carrierSettings->getCompatibilityData() as $rule) {
            $masters = $this->convertToCompoundCodes($rule->getMasters(), $shippingOptions);
            if (empty($masters) && !empty($rule->getMasters())) {
                // This rule has none of its masters available at runtime. We remove it so it does not get turned
                // into a masterless rule and changes its semantics in unexpected ways.
                continue;
            }
            $subjects = $this->convertToCompoundCodes($rule->getSubjects(), $shippingOptions);
            if (empty($subjects)) {
                // A rule without any available subjects can do nothing. We remove it to improve performance.
                continue;
            }

            foreach ($masters as $master) {
                if (in_array($master, $subjects)) {
                    throw new \InvalidArgumentException(
                        "Invalid compatibility rule {$rule->getId()}: "
                        . 'A "master" input must not be a "subject" of its own rule.'
                    );
                }
            }

            /**
             * We check if there are no masters in the rule.
             * The convertedMasters list is no good indication
             * for a master-less rule since convertToCompoundCodes
             * filters out services unavailable during runtime.
             */
            if (empty($rule->getMasters())) {
                /** Split up master-less rule */
                foreach ($subjects as $subjectCode) {
                    $newRule = $this->ruleFactory->create();
                    $newRule->setId($rule->getId() . '-' . $subjectCode);
                    $newRule->setMasters([$subjectCode]);
                    $subjectDiff = array_diff($subjects, [$subjectCode]);
                    $newRule->setSubjects($subjectDiff);
                    $newRule->setErrorMessage($rule->getErrorMessage());
                    $newRule->setTriggerValue($rule->getTriggerValue());
                    $newRule->setAction($rule->getAction());
                    $processedRules[$newRule->getId()] = $newRule;
                }
            } else {
                $newRule = $this->ruleFactory->create();
                $newRule->setId($rule->getId());
                $newRule->setMasters($masters);
                $newRule->setSubjects($subjects);
                $newRule->setErrorMessage($rule->getErrorMessage());
                $newRule->setTriggerValue($rule->getTriggerValue());
                $newRule->setAction($rule->getAction());
                $processedRules[$newRule->getId()] = $newRule;
            }
        }

        $carrierSettings->setCompatibilityData($processedRules);

        return $carrierSettings;
    }
}
