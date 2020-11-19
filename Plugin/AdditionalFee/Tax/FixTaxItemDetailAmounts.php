<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Plugin\AdditionalFee\Tax;

use Magento\Tax\Api\Data\TaxDetailsItemInterface;
use Magento\Tax\Model\Calculation\AbstractCalculator;
use Netresearch\ShippingCore\Model\AdditionalFee\Total;

class FixTaxItemDetailAmounts
{
    public function afterCalculate(
        AbstractCalculator $subject,
        TaxDetailsItemInterface $result
    ): TaxDetailsItemInterface {
        if ($result->getType() !== Total::NRSHIPPING_FEE_TAX_TYPE) {
            return $result;
        }

        /**
         * In case of negative service charges (read "discounts"), the
         * tax calculation drops amount less than 0 for purposes of hidden tax
         * amount calculation. This is detrimental to our cause and we therefore
         * set the tax amount manually again at the tax details item.
         */
        if ($result->getRowTotal() < 0) {
            $result->setRowTax($result->getRowTotalInclTax() - $result->getRowTotal());
            // usually only one tax rule is applied
            foreach ($result->getAppliedTaxes() as $appliedTax) {
                $appliedTax->setAmount($result->getRowTotalInclTax() - $result->getRowTotal());
            }
        }

        return $result;
    }
}
