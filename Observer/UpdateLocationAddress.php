<?php

namespace Netresearch\ShippingCore\Observer;

use Magento\Directory\Helper\Data;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilderFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\Quote;
use Magento\Sales\Api\Data\OrderAddressInterface;
use Magento\Sales\Api\OrderAddressRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\ResourceModel\GridInterface;
use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\Selection\AssignedSelectionInterface;
use Netresearch\ShippingCore\Api\Data\ShippingSettings\ShippingOption\Selection\SelectionInterface;
use Netresearch\ShippingCore\Model\ShippingSettings\ShippingOption\Codes;
use Netresearch\ShippingCore\Model\ShippingSettings\ShippingOption\Selection\QuoteSelection;
use Netresearch\ShippingCore\Model\ShippingSettings\ShippingOption\Selection\QuoteSelectionRepository;

/**
 * Replace the residential shipping address if a delivery location (e.g. post office) was chosen during checkout.
 */
class UpdateLocationAddress implements ObserverInterface
{
    /**
     * @var SearchCriteriaBuilderFactory
     */
    private $searchCriteriaBuilderFactory;

    /**
     * @var FilterBuilder
     */
    private $filterBuilder;

    /**
     * @var QuoteSelectionRepository
     */
    private $quoteShippingOptionSelectionRepository;

    /**
     * @var OrderAddressRepositoryInterface
     */
    private $orderAddressRepository;

    /**
     * @var GridInterface
     */
    private $orderGrid;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var Data
     */
    private $directoryHelper;

    public function __construct(
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        FilterBuilder $filterBuilder,
        QuoteSelectionRepository $quoteShippingOptionSelectionRepository,
        OrderAddressRepositoryInterface $orderAddressRepository,
        GridInterface $orderGrid,
        ScopeConfigInterface $scopeConfig,
        Data $directoryHelper
    ) {
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
        $this->filterBuilder = $filterBuilder;
        $this->quoteShippingOptionSelectionRepository = $quoteShippingOptionSelectionRepository;
        $this->orderAddressRepository = $orderAddressRepository;
        $this->orderGrid = $orderGrid;
        $this->scopeConfig = $scopeConfig;
        $this->directoryHelper = $directoryHelper;
    }

    public function execute(Observer $observer): void
    {
        /** @var Order $order */
        $order = $observer->getData('order');
        /** @var Quote $quote */
        $quote = $observer->getData('quote');

        if ($order->getIsVirtual()) {
            // virtual orders are not shipped, no shipping address to update.
            return;
        }

        $addressFilter = $this->filterBuilder
            ->setField(AssignedSelectionInterface::PARENT_ID)
            ->setValue($quote->getShippingAddress()->getId())
            ->setConditionType('eq')
            ->create();
        $locationFilter = $this->filterBuilder
            ->setField(SelectionInterface::SHIPPING_OPTION_CODE)
            ->setValue(Codes::SERVICE_OPTION_DELIVERY_LOCATION)
            ->setConditionType('eq')
            ->create();

        $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
        $searchCriteria = $searchCriteriaBuilder
            ->addFilter($addressFilter)
            ->addFilter($locationFilter)
            ->create();

        $locationSettings = $this->quoteShippingOptionSelectionRepository->getList($searchCriteria)->getItems();

        if (empty($locationSettings)) {
            // no delivery location selected during checkout, no need to update shipping address.
            return;
        }

        $location = array_reduce(
            $locationSettings,
            static function (array $carry, QuoteSelection $item) {
                $carry[$item->getInputCode()] = $item->getInputValue();
                return $carry;
            },
            []
        );

        /** @var OrderAddressInterface|Order $orderShippingAddress */
        $orderShippingAddress = $order->getShippingAddress();
        $orderShippingAddress->setCustomerAddressId(null)
            ->setStreet($location[Codes::SERVICE_INPUT_DELIVERY_LOCATION_STREET])
            ->setPostcode($location[Codes::SERVICE_INPUT_DELIVERY_LOCATION_POSTAL_CODE])
            ->setCity($location[Codes::SERVICE_INPUT_DELIVERY_LOCATION_CITY])
            ->setCountryId($location[Codes::SERVICE_INPUT_DELIVERY_LOCATION_COUNTRY_CODE])
            ->setCompany($location[Codes::SERVICE_INPUT_DELIVERY_LOCATION_COMPANY] ?: $location['displayName'] ?: '');

        /**
         * Delivery locations do not have region/state data. If region is not required for the destination
         * country, we remove it. Otherwise, we keep the region of the original shipping address, which should match
         * in most cases.
         */
        if (!array_key_exists(
            $location[Codes::SERVICE_INPUT_DELIVERY_LOCATION_COUNTRY_CODE],
            $this->directoryHelper->getCountriesWithStatesRequired()
        )) {
            $orderShippingAddress->setRegion(null);
            $orderShippingAddress->setRegionId(null);
        }

        $this->orderAddressRepository->save($orderShippingAddress);

        if (!$this->scopeConfig->getValue('dev/grid/async_indexing')) {
            $this->orderGrid->refresh($order->getId());
        }
    }
}
