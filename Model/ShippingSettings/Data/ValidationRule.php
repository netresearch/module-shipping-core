<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Model\ShippingSettings\Data;

use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\ValidationRuleInterface;

class ValidationRule implements ValidationRuleInterface
{
    /**
     * @var string
     */
    private $name = '';

    /**
     * @var mixed
     */
    private $param = null;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return mixed|null
     */
    public function getParam()
    {
        return $this->param;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @param mixed $param
     */
    public function setParam($param): void
    {
        $this->param = $param;
    }
}
