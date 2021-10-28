<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingCore\Setup\Module;

class Constants
{
    public const CHECKOUT_CONNECTION_NAME = 'checkout';

    public const SALES_CONNECTION_NAME = 'sales';

    public const TABLE_LABEL_STATUS = 'nrshipping_label_status';

    public const TABLE_RECIPIENT_STREET = 'nrshipping_recipient_street';

    public const TABLE_ORDER_ITEM = 'nrshipping_order_item';

    public const TABLE_ORDER_SHIPPING_OPTION_SELECTION = 'nrshipping_order_address_shipping_option_selection';

    public const TABLE_QUOTE_SHIPPING_OPTION_SELECTION = 'nrshipping_quote_address_shipping_option_selection';

    public const TABLE_RETURN_SHIPMENT_TRACK = 'nrshipping_return_shipment_track';

    public const TABLE_RETURN_SHIPMENT_DOCUMENT = 'nrshipping_return_shipment_document';
}
