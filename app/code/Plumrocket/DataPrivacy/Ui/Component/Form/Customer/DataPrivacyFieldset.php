<?php
/**
 * @package     Plumrocket_DataPrivacy
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\DataPrivacy\Ui\Component\Form\Customer;

use Magento\Framework\View\Element\ComponentVisibilityInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Form\Fieldset;

/**
 * Customer data privacy fieldset class
 *
 * @since 3.2.0
 */
class DataPrivacyFieldset extends Fieldset implements ComponentVisibilityInterface
{
    /**
     * @param ContextInterface $context
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        array $components = [],
        array $data = []
    ) {
        $this->context = $context;

        parent::__construct($context, $components, $data);
    }

    /**
     * Can show data privacy tab in tabs or not
     *
     * Will return false for not registered customer in a case when admin user created new customer account.
     *
     * @return boolean
     */
    public function isComponentVisible(): bool
    {
        $customerId = $this->context->getRequestParam('id');
        return (bool) $customerId;
    }
}
