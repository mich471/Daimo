<?php
namespace Softtek\Marketplace\Plugin\Customer\Account;

use Magento\Customer\Model\Session;
use Magento\Framework\UrlInterface;
use Softtek\Marketplace\Helper\Data;
use Magento\Customer\Controller\Account\CreatePost;

class CreatePostAfter
{
    /**
     * @var UrlInterface
     */
    protected $urlInterface;

    /**
     * @var Data
     */
    protected $stmHelper;

    /**
     * @var Session
     */
    protected $session;

    /**
     * Plugin constructor.
     *
     * @param UrlInterface $urlInterface
     * @param Data $stmHelper
     * @param Session $session
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        UrlInterface $urlInterface,
        Data $stmHelper,
        Session $session
    )
    {
        $this->urlInterface = $urlInterface;
        $this->stmHelper = $stmHelper;
        $this->session = $session;
    }

    /**
     * After plugin to redirect user if it is logged-in
     *
     * @param Create $subject
     * @return Object
     */
    public function afterExecute(
        CreatePost $subject, $result
    ) {
        if (!$this->stmHelper->isEnabled()) {
            return $result;
        }

        if ($this->session->isLoggedIn()) {
            return $result;
        }

        $params = $this->session->getSellerFormData();
        if (!isset($params['ut'])) {
            return $result;
        }
        if ($params['ut'] != 'seller') {
            return $result;
        }
        if (!isset($params['successful_creation'])) {
            return $result;
        }

        $url = $this->urlInterface->getUrl('sellerinfo/index/registrationstep2');
        $result->setUrl($url);

        $this->session->unsSellerFormData();

        return $result;
    }
}
