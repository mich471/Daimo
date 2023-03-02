<?php
namespace Softtek\Marketplace\Block\Customer\Account;

use Magento\Framework\View\Element\Html\Link\Current;

/**
 * Class for My Product Reviews Link
 */
class ReviewsLink extends Current
{
    /**
     * Search redundant /index and / in url
     */
    private const REGEX_INDEX_URL_PATTERN = '/(\/index|(\/))+($|\/$)/';

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (false != $this->getTemplate()) {
            return parent::_toHtml();
        }

        $html = '<li class="nav item item-reviews">';
        $html .=  $this->escapeHtml(__('My Evaluations'));
        $html .= '<ul>';
        if (!$this->isCustomCurrent('review/customer')) {
            $html .= '<li class="nav item"><a href="' . $this->escapeHtml($this->getHref()) . '">' . $this->escapeHtml(__('Product Evaluations')) . '</a></li>';
        } else {
            $html .= '<li class="nav item current"><strong>' . $this->escapeHtml(__('Product Evaluations')) . '</strong></li>';
        }
        if (!$this->isCustomCurrent('sellerinfo/index/customerOrderReviews')) {
            $html .= '<li class="nav item"><a href="' . $this->escapeHtml($this->getUrl('sellerinfo/index/customerOrderReviews')) . '">' . $this->escapeHtml(__('Order Evaluations')) . '</a></li>';
        } else {
            $html .= '<li class="nav item current"><strong>' . $this->escapeHtml(__('Order Evaluations')) . '</strong></li>';
        }
        $html .= '</ul>';
        $html .= '</li>';

        return $html;
    }

    /**
     * Check if link leads to URL equivalent to URL of currently displayed page
     *
     * @return bool
     */
    public function isCustomCurrent($path)
    {
        return $this->getCurrent() ||
            preg_replace(self::REGEX_INDEX_URL_PATTERN, '', $this->getUrl($path))
            == preg_replace(self::REGEX_INDEX_URL_PATTERN, '', $this->getUrl($this->getMca()));
    }

    /**
     * Get current mca
     *
     * @return string
     * @SuppressWarnings(PHPMD.RequestAwareBlockMethod)
     */
    private function getMca()
    {
        $routeParts = [
            (string)$this->_request->getModuleName(),
            (string)$this->_request->getControllerName(),
            (string)$this->_request->getActionName(),
        ];

        $parts = [];
        $pathParts = explode('/', trim($this->_request->getPathInfo(), '/'));
        foreach ($routeParts as $key => $value) {
            if (isset($pathParts[$key]) && $pathParts[$key] === $value) {
                $parts[] = $value;
            }
        }
        return implode('/', $parts);
    }
}
