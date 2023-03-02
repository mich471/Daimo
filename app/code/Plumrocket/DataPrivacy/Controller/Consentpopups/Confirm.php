<?php
/**
 * @package     Plumrocket_magento_2_3_6__1
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\DataPrivacy\Controller\Consentpopups;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Store\Model\StoreManagerInterface;
use Plumrocket\GDPR\Model\ConsentsLogFactory;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Filter\FilterManager;
use Plumrocket\GDPR\Model\Config\Source\ConsentLocations;
use Plumrocket\GDPR\Model\Config\Source\ConsentAction;

/**
 * @since 3.1.0
 */
class Confirm extends Action
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    private $jsonHelper;

    /**
     * @var \Plumrocket\GDPR\Model\ConsentsLogFactory
     */
    private $consentsLogFactory;

    /**
     * @var \Plumrocket\GDPR\Model\ResourceModel\ConsentsLogFactory
     */
    private $consentsLogResourceFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $dateTime;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $session;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress
     */
    private $remoteAddress;

    /**
     * @var \Magento\Framework\Filter\FilterManager
     */
    private $filterManager;

    /**
     * @param \Magento\Framework\App\Action\Context                   $context
     * @param \Magento\Framework\Controller\Result\JsonFactory        $resultJsonFactory
     * @param \Magento\Framework\Json\Helper\Data                     $jsonHelper
     * @param \Plumrocket\GDPR\Model\ConsentsLogFactory               $consentsLogFactory
     * @param \Plumrocket\GDPR\Model\ResourceModel\ConsentsLogFactory $consentsLogResourceFactory
     * @param \Magento\Framework\Stdlib\DateTime\DateTime             $dateTime
     * @param \Magento\Customer\Model\Session                         $session
     * @param \Magento\Store\Model\StoreManagerInterface              $storeManager
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        JsonHelper $jsonHelper,
        ConsentsLogFactory $consentsLogFactory,
        \Plumrocket\GDPR\Model\ResourceModel\ConsentsLogFactory $consentsLogResourceFactory,
        DateTime $dateTime,
        Session $session,
        StoreManagerInterface $storeManager,
        RemoteAddress $remoteAddress,
        FilterManager $filterManager
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->jsonHelper = $jsonHelper;
        $this->consentsLogFactory = $consentsLogFactory;
        $this->consentsLogResourceFactory = $consentsLogResourceFactory;
        $this->dateTime = $dateTime;
        $this->session = $session;
        $this->storeManager = $storeManager;
        $this->remoteAddress = $remoteAddress;
        $this->filterManager = $filterManager;
    }

    public function execute()
    {
        $httpBadRequestCode = 400;

        $resultJson = $this->resultJsonFactory->create();

        try {
            $requestData = $this->jsonHelper->jsonDecode($this->getRequest()->getContent());

            if ($consent = $requestData['consent']) {
                $consentsLog = $this->consentsLogFactory->create()
                                                        ->setData(
                                                            [
                                                                'checkbox_id' => (int) ($consent['consentId'] ?? 0),
                                                                'created_at'  => date(
                                                                    'Y-m-d H:i:s', $this->dateTime->gmtTimestamp()),
                                                                'customer_id' => $this->session->getCustomerId(),
                                                                'website_id'  => $this->storeManager->getStore()
                                                                                                    ->getWebsiteId(),
                                                                'customer_ip' => $this->remoteAddress->getRemoteAddress(),
                                                                'location'    => ConsentLocations::REGISTRATION,
                                                                'label'       => $this->filterManager->stripTags(
                                                                    $consent['checkboxLabel']),
                                                                'cms_page_id' => $consent['page_id'],
                                                                'version'     => isset($consent['cms_page']['version']) ? $consent['cms_page']['version'] : null,
                                                                'action'      => ConsentAction::ACTION_ACCEPT_VALUE,
                                                                'email'       => (string) $this->session->getCustomer()
                                                                                                        ->getEmail(),
                                                            ]);
                $this->consentsLogResourceFactory->create()->save($consentsLog);
            }

            $response = ['error' => false];
        } catch (\Exception $e) {
            return $resultJson->setData([$e->getMessage()])->setHttpResponseCode($httpBadRequestCode);
        }

        return $resultJson->setData($response);
    }
}
