<?php

namespace Softtek\Tax\Model;

use Magento\Catalog\Model\ResourceModel\ProductFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Tax\Api\Data\QuoteDetailsItemInterface;
use Magento\Tax\Api\Data\TaxDetailsInterfaceFactory;
use Magento\Tax\Api\Data\TaxDetailsItemInterfaceFactory;
use Magento\Tax\Api\TaxClassManagementInterface;
use Magento\Tax\Model\Calculation;
use Magento\Tax\Model\Calculation\CalculatorFactory;
use Magento\Tax\Model\Config;
use Magento\Tax\Model\TaxDetails\TaxDetails;

class TaxCalculation extends \Magento\Tax\Model\TaxCalculation
{

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    protected $productRepository;

    public function __construct
    (
        Calculation $calculation,
        CalculatorFactory $calculatorFactory,
        \Magento\Tax\Model\Config $config,
        TaxDetailsInterfaceFactory $taxDetailsDataObjectFactory,
        TaxDetailsItemInterfaceFactory $taxDetailsItemDataObjectFactory,
        StoreManagerInterface $storeManager,
        TaxClassManagementInterface $taxClassManagement,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Catalog\Model\ProductRepository $productRepository
    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->checkoutSession = $checkoutSession;
        $this->productRepository = $productRepository;
        parent::__construct($calculation, $calculatorFactory, $config, $taxDetailsDataObjectFactory,
            $taxDetailsItemDataObjectFactory, $storeManager, $taxClassManagement, $dataObjectHelper);
    }

    /**
     * {@inheritdoc}
     */
    public function calculateTax(
        \Magento\Tax\Api\Data\QuoteDetailsInterface $quoteDetails,
                                                    $storeId = null,
                                                    $round = true
    ) {
        if ($storeId === null) {
            $storeId = $this->storeManager->getStore()->getStoreId();
        }

        // initial TaxDetails data
        $taxDetailsData = [
            TaxDetails::KEY_SUBTOTAL => 0.0,
            TaxDetails::KEY_TAX_AMOUNT => 0.0,
            TaxDetails::KEY_DISCOUNT_TAX_COMPENSATION_AMOUNT => 0.0,
            TaxDetails::KEY_APPLIED_TAXES => [],
            TaxDetails::KEY_ITEMS => [],
        ];
        $items = $quoteDetails->getItems();
        if (empty($items)) {
            return $this->taxDetailsDataObjectFactory->create()
                ->setSubtotal(0.0)
                ->setTaxAmount(0.0)
                ->setDiscountTaxCompensationAmount(0.0)
                ->setAppliedTaxes([])
                ->setItems([]);
        }
        $this->computeRelationships($items);

        $calculator = $this->calculatorFactory->create(
            $this->config->getAlgorithm($storeId),
            $storeId,
            $quoteDetails->getBillingAddress(),
            $quoteDetails->getShippingAddress(),
            $this->taxClassManagement->getTaxClassId($quoteDetails->getCustomerTaxClassKey(), 'customer'),
            $quoteDetails->getCustomerId()
        );

        $processedItems = [];
        /** @var QuoteDetailsItemInterface $item */
        foreach ($this->keyedItems as $item) {
            if (isset($this->parentToChildren[$item->getCode()])) {
                $processedChildren = [];
                foreach ($this->parentToChildren[$item->getCode()] as $child) {
                    $processedItem = $this->processItem($child, $calculator, $round);
                    $taxDetailsData = $this->aggregateItemData($taxDetailsData, $processedItem);
                    $processedItems[$processedItem->getCode()] = $processedItem;
                    $processedChildren[] = $processedItem;
                }
                $processedItem = $this->calculateParent($processedChildren, $item->getQuantity());
                $processedItem->setCode($item->getCode());
                $processedItem->setType($item->getType());
            } else {
                $processedItem = $this->processItem($item, $calculator, $round);
                $taxDetailsData = $this->aggregateItemData($taxDetailsData, $processedItem);
            }
            $processedItems[$processedItem->getCode()] = $processedItem;
        }

        //get quote itesm to generate applied_taxed $taxDetailsData['applied_taxes']
        $cofins = $this->scopeConfig->getValue('tax/weee/cofins');
        $ipi = $this->scopeConfig->getValue('tax/weee/ipi');

        $quote = $this->checkoutSession->getQuote();
        $items = $quote->getAllVisibleItems();

        foreach($items as $item) {
            $product = $this->productRepository->getById($item->getProduct()->getId());

            if ($product->getIpi()) {
//                $tax[] = $product->getPrice() * 0.05;
            }

//            $taxDetailsData[TaxDetails::KEY_APPLIED_TAXES] = $product->getPrice() * 0.0925;
        }

        $taxDetailsDataObject = $this->taxDetailsDataObjectFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $taxDetailsDataObject,
            $taxDetailsData,
            \Magento\Tax\Api\Data\TaxDetailsInterface::class
        );
        $taxDetailsDataObject->setItems($processedItems);
        return $taxDetailsDataObject;
    }

    /**
     * Computes relationships between items, primarily the child to parent relationship.
     *
     * @param QuoteDetailsItemInterface[] $items
     * @return void
     */
    private function computeRelationships($items)
    {
        $this->keyedItems = [];
        $this->parentToChildren = [];
        foreach ($items as $item) {
            if ($item->getParentCode() === null) {
                $this->keyedItems[$item->getCode()] = $item;
            } else {
                $this->parentToChildren[$item->getParentCode()][] = $item;
            }
        }
    }
}
