<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\Util;

use Netresearch\ShippingCore\Api\Config\ShippingConfigInterface;

class CustomsRegulationsProvider
{
    public const DUTIABLE = 'dutiable';
    public const NON_DUTIABLE = 'non_dutiable';

    private $dutiableRoutes = [
        'eu' => [
            'DE' => [
                '^27498$', // Helgoland
                '^78266$', // Büsingen
            ],
            'DK' => [
                '^[1-9]\d{2}$', // Färöer
                '^39\d{2}$' // Grönland
            ],
            'ES' => [
                '^51\d{3}$', // Ceuta
                '^52\d{3}$', // Melilla
                '^3[58]\d{3}$', // Canary Islands
            ],
            'FI' => [
                '^22\d{3}$', // Åland Islands
            ],
            'FR' => [
                '^987\d{2}$', // Französisch-Polynesien
                '^988\d{2}$', // Neukaledonien
                '^971\d{2}$', // Guadeloupe
                '^972\d{2}$', // Martinique
                '^973\d{2}$', // Französisch-Guayana
                '^974\d{2}$', // Réunion
                '^976\d{2}$', // Mayotte
            ],
            'IT' => ['^22060$'], // Campione d'Italia
        ],
    ];

    private $nonDutiableRoutes = [
        'eu' => [
            'GB' => ['^[bB][tT][1-9][0-9]?\s[\w^_]{3}$'], // Northern Ireland
        ],
    ];

    /**
     * @var ShippingConfigInterface
     */
    private $config;

    public function __construct(ShippingConfigInterface $config)
    {
        $this->config = $config;
    }

    /**
     * Obtain the customs regulations for a given destination area.
     *
     * - empty return value means that the general customs rules for the destination country apply.
     * - return value 'dutiable' means that a customs form must be enclosed for the specific destination area.
     * - return value 'non_dutiable' means that a customs form is not needed for the specific destination area
     *
     * @param string $originCountryCode
     * @param string $destinationCountryCode
     * @param string $destinationPostalCode
     * @return string
     */
    public function getCustomsRegulations(
        string $originCountryCode,
        string $destinationCountryCode,
        string $destinationPostalCode
    ): string {
        // search for special rules
        $origin = \in_array($originCountryCode, $this->config->getEuCountries()) ? 'eu' : $originCountryCode;
        $dutiableRoutes = $this->dutiableRoutes[$origin][$destinationCountryCode] ?? [];
        $nonDutiableRoutes = $this->nonDutiableRoutes[$origin][$destinationCountryCode] ?? [];

        if (empty($dutiableRoutes) && empty($nonDutiableRoutes)) {
            // no special handling required between given countries
            return '';
        }

        $pattern = implode('|', $nonDutiableRoutes);
        if ($pattern && preg_match("/$pattern/", $destinationPostalCode)) {
            // given postal code matches a non-dutiable destination area
            return self::NON_DUTIABLE;
        }

        $pattern = implode('|', $dutiableRoutes);
        if ($pattern && preg_match("/$pattern/", $destinationPostalCode)) {
            // given postal code matches a dutiable destination area
            return self::DUTIABLE;
        }

        // no special handling required for given destination area
        return '';
    }
}
