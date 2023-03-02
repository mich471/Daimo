<?php
/**
 * @author Hexamarvel Team
 * @copyright Copyright (c) 2021 Hexamarvel (https://www.hexamarvel.com)
 * @package Hexamarvel_Attachments
 */
namespace Hexamarvel\Attachments\Block\Adminhtml\Attachments\Edit;

use Magento\Framework\Exception\NoSuchEntityException;

class GenericButton
{
    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Hexamarvel\Attachments\Model\AttachmentsFactory
     */
    protected $attachmentsFactory;

    /**
     * Constructor
     *
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Hexamarvel\Attachments\Model\AttachmentsFactory $attachmentsFactory
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Hexamarvel\Attachments\Model\AttachmentsFactory $attachmentsFactory
    ) {
        $this->context = $context;
        $this->urlBuilder = $context->getUrlBuilder();
        $this->attachmentsFactory = $attachmentsFactory;
    }

    /**
     * Return the synonyms group Id.
     *
     * @return int|null
     */
    public function getId()
    {
        try {
            $attachmentId = $this->attachmentsFactory->create()->load(
                $this->context->getRequest()->getParam('id')
            )->getId();
        } catch (NoSuchEntityException $e) {
            $attachmentId = null;
        }
        return $attachmentId;
    }

    /**
     * Generate url by route and parameters
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->urlBuilder->getUrl($route, $params);
    }
}
