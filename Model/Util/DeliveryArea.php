<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\Util;

use Netresearch\ShippingCore\Api\Util\DeliveryAreaInterface;

class DeliveryArea implements DeliveryAreaInterface
{
    /**
     * @var string[][]
     */
    private $islandPostalCodes = [
        'DE' => [
            '18565', '25849', '25859', '25863', '25867', '25869', '25938', '25946', '25980', '25992', '25996', '25997',
            '25999', '26465', '26474', '26486', '26548', '26571', '26579', '26757', '27498'
        ]
    ];

    #[\Override]
    public function isIsland(string $countryCode, string $postalCode): bool
    {
        return isset($this->islandPostalCodes[$countryCode])
            && \in_array($postalCode, $this->islandPostalCodes[$countryCode], true);
    }
}
