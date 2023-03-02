<?php


namespace Softtek\MonitorIntegration\Plugin\Block\Widget\Button;

use Magento\Backend\Block\Widget\Button\Toolbar as ToolbarContext;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Backend\Block\Widget\Button\ButtonList;

class Toolbar
{
    /**
     * @param ToolbarContext $toolbar
     * @param AbstractBlock $context
     * @param ButtonList $buttonList
     * @return array
     */
    public function beforePushButtons(
        ToolbarContext $toolbar,
        \Magento\Framework\View\Element\AbstractBlock $context,
        \Magento\Backend\Block\Widget\Button\ButtonList $buttonList
    ) {
        if (!$context instanceof \Magento\Sales\Block\Adminhtml\Order\View) {
            return [$context, $buttonList];
        }
        $statesToBlockCreditMemo = ['readytopickorship', 'shipping', 'retrying', 'hold'];
        $order = $context->getOrder();

        if (in_array($order->getStatus(),$statesToBlockCreditMemo)) {
            $buttonList->remove('order_creditmemo');
        }

        return [$context, $buttonList];
    }
}
