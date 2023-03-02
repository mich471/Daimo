<?php

namespace Softtek\Sales\Model;

use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Magento\Payment\Api\Data\PaymentAdditionalInfoInterfaceFactory;
use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Api\Data\OrderSearchResultInterfaceFactory as SearchResultFactory;
use Magento\Sales\Model\ResourceModel\Metadata;
use Magento\Tax\Api\OrderTaxManagementInterface;

class OrderRepository extends \Magento\Sales\Model\OrderRepository
{

    /**
     * @var \Purpletree\Marketplace\Model\ResourceModel\Sellerorder\CollectionFactory
     */
    protected $sellerorderCollectionFactory;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    public function __construct(
        Metadata $metadata,
        SearchResultFactory $searchResultFactory,
        CollectionProcessorInterface $collectionProcessor = null,
        OrderExtensionFactory $orderExtensionFactory = null,
        OrderTaxManagementInterface $orderTaxManagement = null,
        PaymentAdditionalInfoInterfaceFactory $paymentAdditionalInfoFactory = null,
        JsonSerializer $serializer = null,
        JoinProcessorInterface $extensionAttributesJoinProcessor = null,
        \Purpletree\Marketplace\Model\ResourceModel\Sellerorder\CollectionFactory $sellerorderCollectionFactory,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
    )
    {
        $this->sellerorderCollectionFactory = $sellerorderCollectionFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        parent::__construct($metadata, $searchResultFactory, $collectionProcessor, $orderExtensionFactory,
            $orderTaxManagement, $paymentAdditionalInfoFactory, $serializer, $extensionAttributesJoinProcessor);
    }

    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        $groups = $searchCriteria->getFilterGroups();
        if (is_array($groups)) {
            $group = array_shift($groups);
            $filters = $group->getFilters();
            if (is_array($filters)) {
                $filter = array_shift($filters);
                if ($filter->getField() == 'seller_id') {
                    $collectiossn = $this->sellerorderCollectionFactory->create();
                    $sellerId   = $filter->getValue();
                    $orderids = array();
                    foreach ($collectiossn as $dddd) {
                        if ($sellerId == $dddd->getSellerId()) {
                            $orderids[] = $dddd->getOrderId();
                        }
                    }

                    $searchCriteria = $this->searchCriteriaBuilder->addFilter(
                        'entity_id', $orderids, 'in'
                    )->create();

                    return parent::getList($searchCriteria);
                }
            }
        }

        return parent::getList($searchCriteria);
    }
}
