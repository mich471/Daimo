<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket_GDPR
 * @copyright   Copyright (c) 2018 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\GDPR\Block\Adminhtml\Consent\Checkbox\Edit;

class GenericButton
{
    /**
     * @var \Magento\Backend\Block\Widget\Context
     */
    protected $context;

    /**
     * @var \Plumrocket\DataPrivacyApi\Api\CheckboxRepositoryInterface
     */
    protected $checkboxRepository;

    /**
     * @param \Magento\Backend\Block\Widget\Context                      $context
     * @param \Plumrocket\DataPrivacyApi\Api\CheckboxRepositoryInterface $checkboxRepository
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Plumrocket\DataPrivacyApi\Api\CheckboxRepositoryInterface $checkboxRepository
    ) {
        $this->context = $context;
        $this->checkboxRepository = $checkboxRepository;
    }

    /**
     * @return int
     */
    public function getCheckboxId() : int
    {
        try {
            $id = $this->checkboxRepository->getById($this->context->getRequest()->getParam('id'))->getId();
        } catch (\Magento\Framework\Exception\NoSuchEntityException $noSuchEntityException) {
            $id = 0;
        }

        return $id;
    }

    /**
     * Generate url by route and parameters
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getUrl($route = '', $params = []) : string
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
