<?php

declare(strict_types=1);

namespace Amasty\CustomerAttributes\Model;

use Magento\Framework\App\Request\Http;

// @TODO move to plugin dir
class Validator
{
    /**
     * @var Http
     */
    private $request;

    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    public function __construct(
        Http $request,
        \Magento\Framework\Registry $registry
    ) {
        $this->request = $request;
        $this->registry = $registry;
    }

    /**
     * Fix for situation, when attribute is required and was hided on account
     * edit page - magento can't validate customer.
     * @TODO: this is a bad way, need to change
     *
     * @param $subject
     * @param \Closure $proceed
     * @param $value
     *
     * @return bool|mixed
     */
    public function aroundIsValid(
        $subject,
        \Closure $proceed,
        $value
    ) {
        if ($value instanceof \Magento\Customer\Model\Customer) {
            $post = $this->request->getServer('REQUEST_URI');
            if ($this->registry->registry('amasty_customerAttributes_validation-failed')) {
                return false;
            }
            if ($post == '/customer/account/editPost/'
                || $post == '/customer/account/createpost/') {
                    return true;
            }
        }

        return $proceed($value);
    }
}
