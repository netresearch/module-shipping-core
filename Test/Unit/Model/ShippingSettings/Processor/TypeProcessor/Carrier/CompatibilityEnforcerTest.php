<?php

/**
 * See LICENSE.md for license details.
 */

namespace Netresearch\ShippingCore\Test\Unit\Model\ShippingSettings\TypeProcessor\Carrier;

use Netresearch\ShippingCore\Model\ShippingSettings\Data\CarrierData;
use Netresearch\ShippingCore\Model\ShippingSettings\Data\Compatibility;
use Netresearch\ShippingCore\Model\ShippingSettings\Data\Input;
use Netresearch\ShippingCore\Model\ShippingSettings\Data\ShippingOption;
use Magento\Framework\Exception\LocalizedException;
use Netresearch\ShippingCore\Model\ShippingSettings\TypeProcessor\Carrier\CompatibilityEnforcer;
use PHPUnit\Framework\TestCase;

class CompatibilityEnforcerTest extends TestCase
{
    public function testEnforce()
    {
        $subject = new CompatibilityEnforcer();

        $masterInput = new Input();
        $masterInput->setCode('masterInput');
        $masterInput->setDefaultValue('shouldTriggerRule');

        $masterOption = new ShippingOption();
        $masterOption->setCode('masterOption');
        $masterOption->setInputs([$masterInput]);

        $subjectInput = new Input();
        $subjectInput->setCode('subjectInput');
        $subjectInput->setDefaultValue('shouldBeRemovedByEnforcer');

        $subjectOption = new ShippingOption();
        $subjectOption->setCode('subjectOption');
        $subjectOption->setInputs([$subjectInput]);

        $compatibility = new Compatibility();
        $compatibility->setMasters(['masterOption.masterInput']);
        $compatibility->setSubjects(['subjectOption.subjectInput']);
        $compatibility->setAction('disable');
        $compatibility->setTriggerValue('shouldTriggerRule');

        $carrier = new CarrierData();
        $carrier->setPackageOptions([$masterOption]);
        $carrier->setServiceOptions([$subjectOption]);
        $carrier->setCompatibilityData([$compatibility]);

        $result = $subject->process($carrier, 0, 'DE', '04229');

        // The subject input should be disabled and have a value of ""
        self::assertTrue(
            $result->getServiceOptions()[0]->getInputs()[0]->isDisabled(),
            'The subject was not disabled according to the rule action'
        );
        self::assertEmpty(
            $result->getServiceOptions()[0]->getInputs()[0]->getDefaultValue(),
            'The default value of the subject was not reset when it was disabled'
        );

        // The master input should be unchanged
        self::assertSame(
            'shouldTriggerRule',
            $result->getPackageOptions()[0]->getInputs()[0]->getDefaultValue(),
            'The master input was not left unchanged. Master value was changed'
        );
        self::assertFalse(
            $result->getPackageOptions()[0]->getInputs()[0]->isDisabled(),
            'The master input was not left unchanged. Master was disabled'
        );
    }

    public function testEnforceException()
    {
        $subject = new CompatibilityEnforcer();

        $masterInput = new Input();
        $masterInput->setCode('masterInput');
        $masterInput->setDefaultValue('shouldTriggerRule');

        $masterOption = new ShippingOption();
        $masterOption->setCode('masterOption');
        $masterOption->setInputs([$masterInput]);

        $subjectInput = new Input();
        $subjectInput->setCode('subjectInput');
        $subjectInput->setDefaultValue(''); // Should trigger an error since it's required but has no value

        $subjectOption = new ShippingOption();
        $subjectOption->setCode('subjectOption');
        $subjectOption->setInputs([$subjectInput]);

        $compatibility = new Compatibility();
        $compatibility->setMasters(['masterOption.masterInput']);
        $compatibility->setSubjects(['subjectOption.subjectInput']);
        $compatibility->setAction('require');
        $compatibility->setTriggerValue('shouldTriggerRule');
        $compatibility->setErrorMessage('expectedErrorMessage');

        $carrier = new CarrierData();
        $carrier->setPackageOptions([$masterOption]);
        $carrier->setServiceOptions([$subjectOption]);
        $carrier->setCompatibilityData([$compatibility]);

        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('expectedErrorMessage');
        $result = $subject->process($carrier, 0, 'DE', '04229');
    }
}
