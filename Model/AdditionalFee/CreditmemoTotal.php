<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\AdditionalFee;

use Magento\Sales\Model\Order\Creditmemo;
use Magento\Sales\Model\Order\Creditmemo\Total\AbstractTotal;

class CreditmemoTotal extends AbstractTotal
{
    /**
     * @var TotalsManager
     */
    private $totalsManager;

    /**
     * CreditmemoTotal constructor.
     *
     * @param TotalsManager $totalsManager
     * @param mixed[] $data
     */
    public function __construct(TotalsManager $totalsManager, array $data = [])
    {
        $this->totalsManager = $totalsManager;

        parent::__construct($data);
    }

    /**
     * @param Creditmemo $creditmemo
     * @return self|AbstractTotal
     */
    #[\Override]
    public function collect(Creditmemo $creditmemo)
    {
        foreach ($creditmemo->getOrder()->getCreditmemosCollection() as $previousCreditmemo) {
            $refundedFee = (float) $previousCreditmemo->getData(TotalsManager::ADDITIONAL_FEE_BASE_FIELD_NAME);
            if (abs($refundedFee) > 0) {
                // in case the additional fee has already been refunded, do not add it to another creditmemo
                return $this;
            }
        }

        $this->totalsManager->transferAdditionalFees(
            $creditmemo->getOrder(),
            $creditmemo
        );

        return $this;
    }
}
