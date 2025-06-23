<?php

namespace Netresearch\ShippingCore\Test\Integration\TestCase\Observer;

use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;
use Magento\TestFramework\Helper\Bootstrap;
use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\Selection\AssignedSelectionInterface;
use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\Selection\SelectionInterface;
use Netresearch\ShippingCore\Model\ShippingSettings\ShippingOption\Codes;
use Netresearch\ShippingCore\Model\ShippingSettings\ShippingOption\Selection\QuoteSelection;
use Netresearch\ShippingCore\Model\ShippingSettings\ShippingOption\Selection\QuoteSelectionFactory;
use Netresearch\ShippingCore\Model\ShippingSettings\ShippingOption\Selection\QuoteSelectionRepository;
use PHPUnit\Framework\TestCase;
use TddWizard\Fixtures\Catalog\ProductBuilder;
use TddWizard\Fixtures\Checkout\CartBuilder;
use TddWizard\Fixtures\Checkout\CustomerCheckout;
use TddWizard\Fixtures\Customer\AddressBuilder;
use TddWizard\Fixtures\Customer\CustomerBuilder;
use TddWizard\Fixtures\Customer\CustomerFixture;
use TddWizard\Fixtures\Sales\OrderFixture;
use TddWizard\Fixtures\Sales\OrderFixtureRollback;

class UpdateLocationAddressTest extends TestCase
{
    /**
     * @var OrderInterface|Order
     */
    private static $order;

    private static $shippingOptionInputValues = [
        Codes::SERVICE_INPUT_DELIVERY_LOCATION_STREET => 'Test Str. 42',
        Codes::SERVICE_INPUT_DELIVERY_LOCATION_POSTAL_CODE => '04200',
        Codes::SERVICE_INPUT_DELIVERY_LOCATION_CITY => 'TestCity',
        Codes::SERVICE_INPUT_DELIVERY_LOCATION_COUNTRY_CODE => 'DE',
        Codes::SERVICE_INPUT_DELIVERY_LOCATION_COMPANY => 'Testfiliale',
        'displayName' => 'Packstation 420',
    ];

    /**
     * @throws \Exception
     */
    public static function createOrderWithServices()
    {
        $productBuilders = [];
        // init simple products
        for ($i = 0; $i < 3; $i++) {
            $productBuilders[] = ProductBuilder::aSimpleProduct();
        }

        // create products
        $products = array_map(
            static function (ProductBuilder $productBuilder) {
                return $productBuilder->build();
            },
            $productBuilders
        );

        //create customer
        $customerBuilder = CustomerBuilder::aCustomer()
            ->withAddresses(AddressBuilder::anAddress()->asDefaultBilling()->asDefaultShipping());
        $customer = $customerBuilder->build();
        $customerFixture = new CustomerFixture($customer);
        $customerFixture->login();

        $cartBuilder = CartBuilder::forCurrentSession();
        foreach ($products as $product) {
            $qty = 1;
            $cartBuilder = $cartBuilder->withSimpleProduct($product->getSku(), $qty);
        }

        // create cart and checkout
        $cart = $cartBuilder->build();
        $checkout = CustomerCheckout::fromCart($cart);
        $checkout = $checkout->withShippingMethodCode('flatrate_flatrate');

        // create delivery location service selection for quote
        $selectionFactory = Bootstrap::getObjectManager()->create(QuoteSelectionFactory::class);
        $selectionRepository = Bootstrap::getObjectManager()->create(QuoteSelectionRepository::class);
        foreach (self::$shippingOptionInputValues as $inputCode => $inputValue) {
            /** @var QuoteSelection $selection */
            $selection = $selectionFactory->create();
            $selection->setData(
                [
                    AssignedSelectionInterface::PARENT_ID => $cart->getQuote()->getShippingAddress()->getId(),
                    SelectionInterface::SHIPPING_OPTION_CODE => Codes::SERVICE_OPTION_DELIVERY_LOCATION,
                    SelectionInterface::INPUT_CODE => $inputCode,
                    SelectionInterface::INPUT_VALUE => $inputValue,
                ]
            );
            $selectionRepository->save($selection);
        }

        self::$order = $checkout->placeOrder();

        $customerFixture->logout();
    }

    /**
     * Roll back fixture.
     *
     * @throws LocalizedException
     */
    public static function createOrderWithServicesRollback()
    {
        try {
            OrderFixtureRollback::create()->execute(new OrderFixture(self::$order));
        } catch (\Exception $exception) {
            $argv = $_SERVER['argv'] ?? [];
            if (in_array('--verbose', $argv, true)) {
                $message = sprintf("Error during rollback: %s%s", $exception->getMessage(), PHP_EOL);
                register_shutdown_function('fwrite', STDERR, $message);
            }
        }
    }


    /**
     * @magentoDataFixture createOrderWithServices
     */
    #[\PHPUnit\Framework\Attributes\Test]
    public function updateAddress()
    {
        $this->assertEquals(
            self::$shippingOptionInputValues[Codes::SERVICE_INPUT_DELIVERY_LOCATION_STREET],
            self::$order->getShippingAddress()->getStreetLine(1)
        );
        $this->assertEquals(
            self::$shippingOptionInputValues[Codes::SERVICE_INPUT_DELIVERY_LOCATION_COUNTRY_CODE],
            self::$order->getShippingAddress()->getCountryId()
        );
        $this->assertEquals(
            self::$shippingOptionInputValues[Codes::SERVICE_INPUT_DELIVERY_LOCATION_POSTAL_CODE],
            self::$order->getShippingAddress()->getPostcode()
        );
        $this->assertEquals(
            self::$shippingOptionInputValues[Codes::SERVICE_INPUT_DELIVERY_LOCATION_CITY],
            self::$order->getShippingAddress()->getCity()
        );
        $this->assertNull(
            self::$order->getShippingAddress()->getCustomerAddressId()
        );
    }
}
