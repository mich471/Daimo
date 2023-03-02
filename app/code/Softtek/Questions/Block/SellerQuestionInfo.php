<?php
/**
 * Softtek_Marketplace SellerData
 *
 * @category    Softtek
 * @package     Softtek_Marketplace
 * @author      J. Abraham Serena <jorge.serena@softtek.com>
 * @copyright   © Softtek 2022. All rights reserved.
 */
namespace Softtek\Questions\Block;

class SellerQuestionInfo extends \Magento\Framework\View\Element\Template
{
    /**
     * SellerPaymentInfo Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context
     * @param \Magento\Customer\Model\Session
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        array $data = []
    ) {
        $this->customerSession = $customerSession;
        parent::__construct($context, $data);
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getProductCollection()) {
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'seller.products.pager'
            )->setCollection(
                $this->getProductCollection()
            );
            $this->setChild('pager', $pager);
            $this->getProductCollection();
        }
        return $this;
    }
    
    /**
     * Pager Html
     *
     * @return Pager Html
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * Get Customer Id
     *
     * @return Customer Id
     */
    public function getCustomerId()
    {
        $customerId = $this->customerSession->getCustomerId();
        return $customerId;
    }
}
