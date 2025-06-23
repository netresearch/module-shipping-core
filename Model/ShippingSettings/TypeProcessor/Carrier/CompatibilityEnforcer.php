<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ShippingSettings\TypeProcessor\Carrier;

use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\ShipmentInterface;
use Netresearch\ShippingCore\Api\Data\ShippingSettings\CarrierDataInterface;
use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\CompatibilityInterface;
use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\InputInterface;
use Netresearch\ShippingCore\Api\ShippingSettings\TypeProcessor\CarrierDataProcessorInterface;

class CompatibilityEnforcer implements CarrierDataProcessorInterface
{
    /**
     * @param string $compoundCode
     * @param CarrierDataInterface $carrierData
     * @return InputInterface|null
     */
    private function getInputByCode(string $compoundCode, CarrierDataInterface $carrierData): ?InputInterface
    {
        $shippingOptions = array_merge(
            $carrierData->getServiceOptions(),
            $carrierData->getPackageOptions()
        );

        [$optionCode, $inputCode] = explode('.', $compoundCode);
        foreach ($shippingOptions as $option) {
            if ($optionCode !== $option->getCode()) {
                continue;
            }
            foreach ($option->getInputs() as $input) {
                if ($input->getCode() === $inputCode) {
                    return $input;
                }
            }
        }

        return null;
    }

    /**
     * Disable and/or clear values of given input list according to the current action
     *
     * @param string $action Current rule action
     * @param InputInterface[] $inputs Subject inputs
     * @param string[] $emptyActions Inputs to clear of any default value
     * @param string[] $disableActions Inputs to set as disabled
     * @return bool
     */
    private function emptyAndDisableInputs(
        string $action,
        array $inputs,
        array $emptyActions,
        array $disableActions
    ): bool {
        $inputModified = false;
        if (in_array($action, $emptyActions, true)) {
            foreach ($inputs as $input) {
                if ($input->getDefaultValue() !== '') {
                    $input->setDefaultValue('');
                    $inputModified = true;
                }
            }
        }
        if (in_array($action, $disableActions, true)) {
            foreach ($inputs as $input) {
                if (!$input->isDisabled()) {
                    $input->setDisabled(true);
                    $inputModified = true;
                }
            }
        }
        return $inputModified;
    }

    /**
     * @param InputInterface[] $masterInputs
     * @param InputInterface[] $subjectInputs Will be mutated according to the rule
     * @param CompatibilityInterface $rule
     * @return bool                             Returns "true" if any subject inputs were modified by applying the rule
     * @throws LocalizedException               Thrown if a required input is missing a value
     */
    private function processRule(
        array $masterInputs,
        array $subjectInputs,
        CompatibilityInterface $rule
    ): bool {
        $inputModified = false;
        foreach ($masterInputs as $masterInput) {
            $valueMatches = false;
            if ($rule->getTriggerValue() === '*' && $masterInput->getDefaultValue() !== '') {
                $valueMatches = true;
            }
            if (str_starts_with($rule->getTriggerValue(), '/') && str_ends_with($rule->getTriggerValue(), '/')) {
                $valueMatches = preg_match($rule->getTriggerValue(), $masterInput->getDefaultValue()) === 1;
            }
            if ($masterInput->getDefaultValue() === $rule->getTriggerValue()) {
                $valueMatches = true;
            }
            if ($valueMatches) {
                $inputModified = $this->emptyAndDisableInputs(
                    $rule->getAction(),
                    $subjectInputs,
                    [CompatibilityInterface::ACTION_DISABLE, CompatibilityInterface::ACTION_HIDE],
                    [CompatibilityInterface::ACTION_DISABLE]
                );

                if ($rule->getAction() === CompatibilityInterface::ACTION_REQUIRE) {
                    foreach ($subjectInputs as $input) {
                        if (!$input->getDefaultValue()) {
                            throw new LocalizedException(__($rule->getErrorMessage()));
                        }
                    }
                }
            } else {
                $inputModified = $this->emptyAndDisableInputs(
                    $rule->getAction(),
                    $subjectInputs,
                    [CompatibilityInterface::ACTION_ENABLE, CompatibilityInterface::ACTION_SHOW],
                    [CompatibilityInterface::ACTION_ENABLE]
                );
            }
        }

        return $inputModified;
    }

    /**
     * @param CarrierDataInterface $carrierData
     * @return bool
     * @throws LocalizedException   Thrown if a required input is missing a value
     */
    private function processRules(CarrierDataInterface $carrierData): bool
    {
        $inputsModified = false;

        $compatibilityRules = $carrierData->getCompatibilityData();
        foreach ($compatibilityRules as $rule) {
            /** @var InputInterface[] $masterInputs */
            $masterInputs = [];
            foreach ($rule->getMasters() as $master) {
                if ($input = $this->getInputByCode($master, $carrierData)) {
                    $masterInputs[] = $input;
                }
            }

            /** @var InputInterface[] $subjectInputs */
            $subjectInputs = [];
            foreach ($rule->getSubjects() as $subject) {
                if ($input = $this->getInputByCode($subject, $carrierData)) {
                    $subjectInputs[] = $input;
                }
            }

            if ($this->processRule($masterInputs, $subjectInputs, $rule)) {
                $inputsModified = true;
            }
        }

        return $inputsModified;
    }

    /**
     * Applies all compatibility rules.
     *
     * It will apply all rules until all inputs are "stable", i.e. until applying the rules does not modify any input.
     * This is necessary because rules can affect each other, the application of one rule triggering another rule
     * and so on.
     *
     * There is an (arbitrary) limit of 5 iterations to avoid infinite loops or overly convoluted rule setups.
     *
     * @param CarrierDataInterface $shippingSettings
     * @param int $storeId
     * @param string $countryCode
     * @param string $postalCode
     * @param ShipmentInterface|null $shipment
     *
     * @return CarrierDataInterface
     * @throws LocalizedException Thrown if a required input is missing a value
     */
    #[\Override]
    public function process(
        CarrierDataInterface $shippingSettings,
        int $storeId,
        string $countryCode,
        string $postalCode,
        ?ShipmentInterface $shipment = null
    ): CarrierDataInterface {
        for ($iteration = 0; $iteration <= 5; $iteration++) {
            $inputsModified = $this->processRules($shippingSettings);
            if (!$inputsModified) {
                return $shippingSettings;
            }
        }

        throw new \InvalidArgumentException(
            'Shipping option compatibility rules could not be resolved to a stable state. ' .
            'You probably configured rules that conflict with each other or are overly complicated.'
        );
    }
}
