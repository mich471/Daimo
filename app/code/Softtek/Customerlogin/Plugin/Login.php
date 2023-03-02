<?php

namespace Softtek\Customerlogin\Plugin;

class Login {
    protected $redirect;
    protected $customerSession;

    public function __construct(\Magento\Framework\App\Response\RedirectInterface $redirect, \Magento\Customer\Model\Session $customerSession) {
        $this->redirect = $redirect;
        $this->customerSession = $customerSession;
    }

    public function beforeExecute(
        \Magento\Customer\Controller\Account\Login $subject
    ) {
        $redirectUrl = $this->redirect->getRefererUrl();
        $this->customerSession->setBeforeLoginUrl($redirectUrl);
    }
}
