<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Plugin\AdditionalFee\Creditmemo;

use Magento\Sales\Api\CreditmemoRepositoryInterface;
use Magento\Sales\Api\Data\CreditmemoExtensionInterfaceFactory;
use Magento\Sales\Api\Data\CreditmemoInterface;
use Magento\Sales\Api\Data\CreditmemoSearchResultInterface;
use Magento\Sales\Model\Order\Creditmemo;
use Netresearch\ShippingCore\Model\AdditionalFee\TotalsManager;

/**
 * Add totals to credit memo extension attributes.
 *
 * The additional totals columns in `sales_creditmemo` are necessary
 * for totals calculations but are not part of the `CreditmemoInterface`.
 * In order to make the totals available for reading, this class shifts
 * them to extension attributes.
 */
class AddTotalsAsExtensionAttributes
{
    /**
     * @var CreditmemoExtensionInterfaceFactory
     */
    private $creditmemoExtensionFactory;

    public function __construct(CreditmemoExtensionInterfaceFactory $creditmemoExtensionFactory)
    {
        $this->creditmemoExtensionFactory = $creditmemoExtensionFactory;
    }

    /**
     * Shift totals columns to extension attributes when reading a single credit memo.
     *
     * @param CreditmemoRepositoryInterface $subject
     * @param CreditmemoInterface $creditmemo
     * @return CreditmemoInterface
     */
    public function afterGet(
        CreditmemoRepositoryInterface $subject,
        CreditmemoInterface $creditmemo
    ): CreditmemoInterface {
        $extensionAttributes = $creditmemo->getExtensionAttributes();
        if (!$extensionAttributes) {
            $extensionAttributes = $this->creditmemoExtensionFactory->create();
        }
        /** @var Creditmemo $creditmemo */
        $extensionAttributes->setBaseNrshippingAdditionalFee(
            $creditmemo->getData(TotalsManager::ADDITIONAL_FEE_BASE_FIELD_NAME)
        );
        $extensionAttributes->setNrshippingAdditionalFee(
            $creditmemo->getData(TotalsManager::ADDITIONAL_FEE_FIELD_NAME)
        );
        $extensionAttributes->setBaseNrshippingAdditionalFeeInclTax(
            $creditmemo->getData(TotalsManager::ADDITIONAL_FEE_BASE_INCL_TAX_FIELD_NAME)
        );
        $extensionAttributes->setNrshippingAdditionalFeeInclTax(
            $creditmemo->getData(TotalsManager::ADDITIONAL_FEE_INCL_TAX_FIELD_NAME)
        );
        $creditmemo->setExtensionAttributes($extensionAttributes);

        return $creditmemo;
    }

    /**
     * Shift totals columns to extension attributes when reading a list of credit memos.
     *
     * @param CreditmemoRepositoryInterface $subject
     * @param CreditmemoSearchResultInterface $searchResult
     * @return CreditmemoSearchResultInterface
     */
    public function afterGetList(
        CreditmemoRepositoryInterface $subject,
        CreditmemoSearchResultInterface $searchResult
    ): CreditmemoSearchResultInterface {
        foreach ($searchResult->getItems() as $creditmemo) {
            $this->afterGet($subject, $creditmemo);
        }

        return $searchResult;
    }
}
