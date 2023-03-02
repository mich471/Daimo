<?php
namespace Amasty\CustomerAttributes\Block\Data\Form\Element;

use Magento\Framework\UrlFactory;

class File extends \Magento\Customer\Block\Adminhtml\Form\Element\File
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    public function __construct(
        \Magento\Framework\Data\Form\Element\Factory $factoryElement,
        \Magento\Framework\Data\Form\Element\CollectionFactory $factoryCollection,
        \Magento\Framework\Escaper $escaper,
        \Magento\Backend\Helper\Data $adminhtmlData,
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Magento\Framework\Url\EncoderInterface $urlEncoder,
        array $data,
        \Magento\Customer\Model\Session $customerSession,
        UrlFactory $urlFactory
    ) {
        parent::__construct(
            $factoryElement,
            $factoryCollection,
            $escaper,
            $adminhtmlData,
            $assetRepo,
            $urlEncoder,
            $data
        );
        $this->urlModel = $urlFactory->create();
        $this->customerSession = $customerSession;
    }

    public function getElementHtml()
    {
        if (is_array($this->getValue())) {
            $value = $this->getValue()['value'];
        } else {
            $value = $this->getValue();
        }

        if ($value && strpos($value, ".") !== false) {
            return $this->_getPreviewHtml() . '   ' . $this->_getHiddenInput() . $this->_getDeleteCheckboxHtml();
        }

        return parent::getElementHtml();
    }

    /**
     * Return File preview link HTML
     *
     * @return string
     */
    protected function _getPreviewHtml()
    {
        $html = '';
        if ($this->getValue() && !is_array($this->getValue()) && strpos($this->getValue(), ".") !== false) {
            $image = [
                'alt' => __('Download'),
                'title' => __('Download'),
                'src' => $this->_assetRepo->getUrl('Amasty_CustomerAttributes::images/fam_bullet_disk.gif'),
                'class' => 'v-middle'
            ];

            $url = $this->_getPreviewUrl();
            $html .= '<span>';
            $html .= '<a href="' . $url . '">' . $this->_drawElementHtml('img', $image) . '</a> ';
            $html .= '<a href="' . $url . '">' . __('Download') . '</a>';
            $html .= '</span>';
        }
        return $html;
    }

    /**
     * Return Preview/Download URL
     *
     * @return string
     */
    protected function _getPreviewUrl()
    {
        $customerId = $this->customerSession->getCustomer() ? $this->customerSession->getCustomer()->getId() : 0;
        $value = $this->getValue();
        $valueArray = explode('/', $value);
        if ($valueArray) {
            $value = end($valueArray);
        }

        return $this->urlModel->getUrl(
            'amcustomerattr/index/viewfile',
            [
                'file' => $this->urlEncoder->encode($value),
                'customer_id' => $customerId
            ]
        );
    }
}
