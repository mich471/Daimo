<?php
namespace Softtek\Marketplace\Observer\Html;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Data\Tree\Node;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\UrlInterface;
use Softtek\Marketplace\Helper\Data;

class BecomeSellerLink implements ObserverInterface
{
    /**
     * @var UrlInterface
     */
    protected $_urlInterface;

    /**
     * @var Data
     */
    protected $_stmHelper;

    /**
     * Observer constructor.
     *
     * @param UrlInterface $urlInterface
     * @param Data $stmHelper
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        UrlInterface $urlInterface,
        Data $stmHelper
    )
    {
        $this->_urlInterface = $urlInterface;
        $this->_stmHelper = $stmHelper;
    }


    /**
     * Observer execute
     *
     * @param Observer $observer
     * @return Observer
     * @throws CouldNotSaveException
     */
    public function execute(EventObserver $observer)
    {
        if (!$this->_stmHelper->isEnabled()) {
            return $this;
        }

        $menu = $observer->getMenu();
        $tree = $menu->getTree();
        $data = [
            'name'      => __('Vender'),
            'id'        => 'vender',
            'url'       => $this->_urlInterface->getUrl('customer/account/create/ut/seller'),
            'css'       => 'nav-9 last level0 level-top ui-menu-item',
            'is_active' => false
        ];
        $node = new Node($data, 'id', $tree, $menu);
        $menu->addChild($node);

        return $this;
    }
}
