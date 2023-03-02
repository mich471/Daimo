<?php

namespace Softtek\Customerlogin\Plugin;

use Magento\Customer\Model\Account\Redirect as AccountRedirect;

class LoginPost {

    protected $scopeConfig;
    protected $customerSession;
    /**
     * @var AccountRedirect
     */
    private $accountRedirect;

    public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
                                \Magento\Customer\Model\Session $customerSession) {
        $this->scopeConfig = $scopeConfig;
        $this->customerSession = $customerSession;
    }

    public function afterExecute(
        \Magento\Customer\Controller\Account\LoginPost $subject, $result
    ) {
        //new \Exception(''.$this->customerSession->getCustomer()->getEmail());
        $configValue = $this->scopeConfig->getValue('guest_wishlist/general/enabled', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if ($configValue == 1 ) {
            $result->setPath('customer/account'); // Change this to what you want
        } else if($this->customerSession->getCustomer()->getEmail()){
            $redirectUrl = $this->customerSession->getBeforeLoginUrl();
            $this->customerSession->unsBeforeLoginUrl();
            $result->setPath($redirectUrl); // Change this to what you want
        }
        return $result;
    }
}
