<?php

namespace Softtek\MatrixRate\Model\Carrier;

use Magento\Catalog\Model\Product;
use Magento\Quote\Model\Quote\Address\RateRequest;

class Matrixrate extends \WebShopApps\MatrixRate\Model\Carrier\Matrixrate
{

    /**
     * @var \Magento\InventoryApi\Model\GetSourceCodesBySkusInterface
     */
    protected $getSourceCodesBySkus;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger, \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $resultMethodFactory,
        \WebShopApps\MatrixRate\Model\ResourceModel\Carrier\MatrixrateFactory $matrixrateFactory,
        \Magento\InventoryApi\Model\GetSourceCodesBySkusInterface $getSourceCodesBySkus,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        array $data = []
    )
    {
        $this->getSourceCodesBySkus = $getSourceCodesBySkus;
        $this->productRepository = $productRepository;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $rateResultFactory, $resultMethodFactory, $matrixrateFactory, $data);
    }

    public function collectRates(RateRequest $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }

        // exclude Virtual products price from Package value if pre-configured
        if (!$this->getConfigFlag('include_virtual_price') && $request->getAllItems()) {
            foreach ($request->getAllItems() as $item) {
                if ($item->getParentItem()) {
                    continue;
                }
                if ($item->getHasChildren() && $item->isShipSeparately()) {
                    foreach ($item->getChildren() as $child) {
                        if ($child->getProduct()->isVirtual()) {
                            $request->setPackageValue($request->getPackageValue() - $child->getBaseRowTotal());
                        }
                    }
                } elseif ($item->getProduct()->isVirtual()) {
                    $request->setPackageValue($request->getPackageValue() - $item->getBaseRowTotal());
                }
            }
        }

        // Free shipping by qty
        $freeQty = 0;
        $sourceCodes = [];
        $sellerId = "";
        if ($request->getAllItems()) {
            $freePackageValue = 0;
            foreach ($request->getAllItems() as $item) {
                if ($item->getProduct()->isVirtual() || $item->getParentItem()) {
                    continue;
                }

                /**
                 * @var Product $currentProduct
                 */
                $currentProduct = $this->productRepository->getById($item->getProduct()->getId());
                $sourceCodes = $this->getSourceCodesBySkus->execute([$item->getSku()]);
                $sellerId = $currentProduct->getCustomAttribute('seller_id')->getValue();

                if ($item->getHasChildren() && $item->isShipSeparately()) {
                    foreach ($item->getChildren() as $child) {
                        if ($child->getFreeShipping() && !$child->getProduct()->isVirtual()) {
                            $freeShipping = is_numeric($child->getFreeShipping()) ? $child->getFreeShipping() : 0;
                            $freeQty += $item->getQty() * ($child->getQty() - $freeShipping);
                        }
                    }
                } elseif ($item->getFreeShipping()) {
                    $freeShipping = is_numeric($item->getFreeShipping()) ? $item->getFreeShipping() : 0;
                    $freeQty += $item->getQty() - $freeShipping;
                    $freePackageValue += $item->getBaseRowTotal();
                }
            }
            $oldValue = $request->getPackageValue();
            $request->setPackageValue($oldValue - $freePackageValue);
        }

        if (!$request->getConditionMRName()) {
            $conditionName = $this->getConfigData('condition_name');
            $request->setConditionMRName($conditionName ? $conditionName : $this->defaultConditionName);
        }

        // Package weight and qty free shipping
        $oldWeight = $request->getPackageWeight();
        $oldQty = $request->getPackageQty();

        $request->setPackageWeight($request->getFreeMethodWeight());
        $request->setPackageQty($oldQty - $freeQty);

        /** @var \Magento\Shipping\Model\Rate\Result $result */
        $result = $this->rateResultFactory->create();
        $zipRange = $this->getConfigData('zip_range');

        $rateArray = $this->getRateMultivendor($request, $sourceCodes, $sellerId, $zipRange);

        $request->setPackageWeight($oldWeight);
        $request->setPackageQty($oldQty);

        $foundRates = false;

        foreach ($rateArray as $rate) {
            if (!empty($rate) && $rate['price'] >= 0) {
                /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
                $method = $this->resultMethodFactory->create();

                $method->setCarrier('matrixrate');
                $method->setCarrierTitle($this->getConfigData('title'));

                $method->setMethod('matrixrate_' . $rate['pk']);
                $method->setMethodTitle(__($rate['shipping_method']));

                if ($request->getFreeShipping() === true || $request->getPackageQty() == $freeQty) {
                    $shippingPrice = 0;
                } else {
                    $shippingPrice = $this->getFinalPriceWithHandlingFee($rate['price']);
                }

                $method->setPrice($shippingPrice);
                $method->setCost($rate['cost']);

                $result->append($method);
                $foundRates = true; // have found some valid rates
            }
        }

        if (!$foundRates) {
            /** @var \Magento\Quote\Model\Quote\Address\RateResult\Error $error */
            $error = $this->_rateErrorFactory->create(
                [
                    'data' => [
                        'carrier' => $this->_code,
                        'carrier_title' => $this->getConfigData('title'),
                        'error_message' => $this->getConfigData('specificerrmsg'),
                    ],
                ]
            );
            $result->append($error);
        }

        return $result;
    }


    public function getRateMultivendor(
        \Magento\Quote\Model\Quote\Address\RateRequest $request,
        $sourceCode,
        $sellerId,
        $zipRange
    )
    {
        return $this->matrixrateFactory->create()->getRateMultivendor($request, $sourceCode, $sellerId, $zipRange);
    }
}
