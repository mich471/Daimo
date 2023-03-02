<?php
namespace Softtek\Marketplace\Block\Order;

use Magento\Customer\Model\Context;
use Magento\Sales\Block\Order\View as OrderView;
use Magento\Framework\View\Element\Template\Context as TemplateContext;
use Magento\Framework\Registry;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Payment\Helper\Data;
use Magento\Sales\Model\ResourceModel\Order\Status\History\CollectionFactory as HistoryCollection;

/**
 * Sales order view block
 *
 * @api
 * @since 100.0.2
 */
class View extends OrderView
{
    /**
     * @var HistoryCollection
     */
    protected $historyCollectionFactory;

    /**
     * @param TemplateContext $context
     * @param Registry $registry
     * @param HttpContext $httpContext
     * @param Data $paymentHelper
     * @param HistoryCollection $historyCollectionFactory
     * @param array $data
     */
    public function __construct(
        TemplateContext $context,
        Registry $registry,
        HttpContext $httpContext,
        Data $paymentHelper,
        HistoryCollection $historyCollectionFactory,
        array $data = []
    ) {
        $this->historyCollectionFactory = $historyCollectionFactory;

        parent::__construct($context, $registry, $httpContext, $paymentHelper, $data);
    }

    /**
     * Return collection of order status history items.
     *
     * @return HistoryCollection
     */
    public function getVisibleStatusHistoryCollection()
    {
        $collection = $this->historyCollectionFactory->create()->setOrderFilter($this->getOrder())
            ->addFieldToFilter('is_visible_on_front', ['eq' => 1])
            ->setOrder('created_at', 'desc')
            ->setOrder('entity_id', 'desc');
        $collection->getSelect()->where("sm_is_message != 1 OR sm_is_message IS NULL");
        foreach ($collection as $status) {
            $status->setOrder($this->getOrder());
        }
        return $collection;
    }

    /**
     * Return collection of order messages.
     *
     * @return HistoryCollection
     */
    public function getStatusMessagesCollection()
    {
        $collection = $this->historyCollectionFactory->create()->setOrderFilter($this->getOrder())
            ->addFieldToFilter('sm_is_message', ['eq'=> 1])
            ->setOrder('created_at', 'desc')
            ->setOrder('entity_id', 'desc');
        foreach ($collection as $status) {
            $status->setOrder($this->getOrder());
        }
        return $collection;
    }
}
