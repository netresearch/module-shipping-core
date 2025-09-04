<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\Util;

/**
 * Utility for splitting single address line into name, number, supplement
 */
class StreetSplitter
{
    private const OPTION_A_ADDITION_1 = 'A_Addition_to_address_1';
    private const OPTION_A_STREET_NAME = 'A_Street_name_1';
    private const OPTION_A_HOUSE_NUMBER = 'A_House_number_1';
    private const OPTION_A_ADDITION_2 = 'A_Addition_to_address_2';
    private const OPTION_B_ADDITION_1 = 'B_Addition_to_address_1';
    private const OPTION_B_STREET_NAME = 'B_Street_name';
    private const OPTION_B_HOUSE_NUMBER = 'B_House_number';
    private const OPTION_B_ADDITION_2 = 'B_Addition_to_address_2';

    /**
     * Split street into street name, number and additional street information.
     *
     * @param string $street
     * @return string[]
     */
    public function splitStreet(string $street): array
    {
        $result = [
            'street_name'   => $street,
            'street_number' => '',
            'supplement'    => '',
        ];

        // Try simple pattern first for common "Street Number, Floor" format (German addresses)
        // Only match if: 1) exactly one comma, 2) supplement looks like floor indicator
        if (substr_count($street, ',') === 1 &&
            preg_match('/^(?P<street_name>.+?)\s+(?P<street_number>\d+[a-zA-Z]?)\s*,\s*(?P<supplement>\d+\.?\s*(og|ug|eg|dg|stock|floor|etage|obergeschoss|untergeschoss|erdgeschoss|dachgeschoss).*)$/iu', $street, $simpleMatches)) {
            $result = [
                'street_name' => trim($simpleMatches['street_name']),
                'street_number' => trim($simpleMatches['street_number']),
                'supplement' => trim($simpleMatches['supplement']),
            ];
            return $result;
        }

        if (preg_match($this->getRegex(), $street, $matches)) {
            // Pattern A
            if (isset($matches[self::OPTION_A_STREET_NAME]) && !empty($matches[self::OPTION_A_STREET_NAME])) {
                $result['street_name'] = trim($matches[self::OPTION_A_STREET_NAME]);

                if (isset($matches[self::OPTION_A_HOUSE_NUMBER]) && !empty($matches[self::OPTION_A_HOUSE_NUMBER])) {
                    $result['street_number'] = trim($matches[self::OPTION_A_HOUSE_NUMBER]);
                }

                if (isset($matches[self::OPTION_A_ADDITION_1]) && isset($matches[self::OPTION_A_ADDITION_2])) {
                    $result['supplement'] =
                        trim($matches[self::OPTION_A_ADDITION_1] . ' ' . $matches[self::OPTION_A_ADDITION_2]);
                }

                // Pattern B
            } elseif (isset($matches[self::OPTION_B_STREET_NAME]) && !empty($matches[self::OPTION_B_STREET_NAME])) {
                $result['street_name'] = trim($matches[self::OPTION_B_STREET_NAME]);

                if (isset($matches[self::OPTION_B_HOUSE_NUMBER]) && !empty($matches[self::OPTION_B_HOUSE_NUMBER])) {
                    $result['street_number'] = trim($matches[self::OPTION_B_HOUSE_NUMBER]);
                }

                if (isset($matches[self::OPTION_B_ADDITION_1]) && isset($matches[self::OPTION_B_ADDITION_2])) {
                    $result['supplement'] =
                        trim($matches[self::OPTION_B_ADDITION_1] . ' ' . $matches[self::OPTION_B_ADDITION_2]);
                }
            }

            if (stripos($result['street_number'], '/') !== false) {
                list($result['street_number'], $addition) = explode('/', $result['street_number'], 2);
                $result['supplement'] = $addition . $result['supplement'];
                //remove empty spaces if occure
                $result['street_number'] = str_replace(' ', '', $result['street_number']);
                $result['supplement'] = str_replace(' ', '', $result['supplement']);
            }

            // Data integrity validation - prevent silent data loss
            if (!$this->validateDataIntegrity($street, $result)) {
                return [
                    'street_name'   => $street,
                    'street_number' => '',
                    'supplement'    => '',
                ];
            }
        }

        return $result;
    }

    /**
     * Validate data integrity after regex parsing to prevent silent data loss
     *
     * @param string $originalInput
     * @param array $parsedResult
     * @return bool
     */
    private function validateDataIntegrity(string $originalInput, array $parsedResult): bool
    {
        // Skip validation for empty results or when no parsing was attempted
        if (empty(array_filter($parsedResult)) ||
            ($parsedResult['street_name'] === $originalInput && empty($parsedResult['street_number']) && empty($parsedResult['supplement']))) {
            return true;
        }

        // Detect obviously wrong parses - when street name is very short compared to input
        $streetName = trim($parsedResult['street_name']);
        if (strlen($streetName) < 3 && strlen($originalInput) > 10) {
            return false;
        }

        // Detect when street name looks like a supplement (German floor indicators)
        if (preg_match('/^(og|ug|eg|dg|\d+\.\s*og)$/i', $streetName)) {
            return false;
        }

        // Character coverage analysis - only fail on severe data loss
        $originalChars = strlen(preg_replace('/\s+/', '', $originalInput));
        $parsedChars = strlen(preg_replace('/\s+/', '', implode('', array_filter($parsedResult))));

        if ($originalChars > 0) {
            $coverage = $parsedChars / $originalChars;
            if ($coverage < 0.50) { // Only fail on >50% data loss
                return false;
            }
        }

        return true;
    }

    /**
     * Regex to analyze addresses and split them into the groups Street Name, House Number and Additional Information
     * Pattern A is addition number street addition
     * Pattern B is addition street number addition
     *
     * @return string
     */
    private function getRegex(): string
    {
        return "/\\A\\s*
(?:
  #########################################################################
  # Option A: [<Addition to address 1>] <House number> <Street name>      #
  # [<Addition to address 2>]                                             #
  #########################################################################
  (?:
    (?P<A_Addition_to_address_1>.*?)
    ,\\s*
  )?
  # Addition to address 1
  (?:No\\.\\s*)?
  (?P<A_House_number_1>
    \\pN+[a-zA-Z]?
    (?:\\s*[-\\/\\pP]\\s*\\pN+[a-zA-Z]?)*
  )
  # House number
  \\s*,?\\s*
  (?P<A_Street_name_1>
    (?:[a-zA-Z]\\s*|\\pN\\pL{2,}\\s\\pL)
    \\S[^,#]*?
    (?<!\\s)
  )
  # Street name
  \\s*
  (?:
    (?:
      [,\\/]|
      (?=\\#)
    )
    \\s*
    (?!\\s*No\\.)
    (?P<A_Addition_to_address_2>
      (?!\\s)
      .*?
    )
  )?
  # Addition to address 2
  |
  #########################################################################
  # Option B: [<Addition to address 1>] <Street name> <House number>      #
  # [<Addition to address 2>]                                             #
  #########################################################################
  (?:
    (?P<B_Addition_to_address_1>.*?)
    ,\\s*
    (?=.*[,\\/])
  )?
  # Addition to address 1
  (?!\\s*No\\.)
  (?P<B_Street_name>
    \\S\\s*\\S
    (?:
      [^,#]
      (?!\\b\\pN+\\s)
    )*?
    (?<!\\s)
  )
  # Street name
  \\s*[\\/,]?\\s*
  (?:\\sNo\\.)?
  \\s+
  (?P<B_House_number>
    \\pN+\\s*-?[a-zA-Z]?
    (?:
      \\s*[-\\/\\pP]?\\s*\\pN+
      (?:\\s*[\\-a-zA-Z])?
    )*|
    [IVXLCDM]+
    (?!.*\\b\\pN+\\b)
  )
  (?<!\\s)
  # House number
  \\s*
  (?:
    (?:
      [,\\/]|
      (?=\\#)|
      \\s
    )
    \\s*
    (?!\\s*No\\.)
    \\s*
    (?P<B_Addition_to_address_2>
      (?!\\s)
      .*?
    )
  )?
  # Addition to address 2
)
\\s*\\Z/xu";
    }
}
