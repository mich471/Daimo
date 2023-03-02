<?php

namespace Softtek\MonitorIntegration\Helper;

use Magento\Catalog\Model\ProductFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Helper\AbstractHelper;

class ProductValidations extends AbstractHelper
{
    /**
     * @var Session
     */
    private $customerSession;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * ProductStockValidations constructor.
     * @param Session $customerSession
     * @param \Psr\Log\LoggerInterface $logger
     * @param ProductFactory $_productloader
     */
    public function __construct(
        Session $customerSession,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->customerSession = $customerSession;
        $this->logger = $logger;
    }

    private function getStockInfoFromSession()
    {
        return $this->customerSession->getStockInfo();
    }

    /**
     * @param $productSku
     * @return int|mixed
     *   0 There is no stock for the product
     *   1 The stock is enough to cover this sale
     *  -1 There is some stock, but is not enough to cover the full requirement
     */
    public function isValidStock($productSku)
    {
        $stockInfo = $this->getStockInfoFromSession();
        $item = null;
        if (!$stockInfo) {
            $response = [
                "status" => 1
            ];
            return $response;
        }
        foreach ($stockInfo as $struct) {
            if ($productSku == $struct['product']) {
                $item = $struct;
                break;
            }
        }
        $response = [
            "status" => $item['status'],
            "stock" => $item['availableStock']
        ];
        return $response;
    }
}
