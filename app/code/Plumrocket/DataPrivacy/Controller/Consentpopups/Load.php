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
use Magento\Framework\Controller\Result\JsonFactory as ResultJsonFactory;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Result\PageFactory;
use Plumrocket\GDPR\Helper\Checkboxes;
use Plumrocket\GDPR\Helper\Notifys;
use Plumrocket\GDPR\Model\Config\Source\ConsentLocations;

/**
 * @since 3.1.0
 */
class Load extends Action
{
    /**
     * @var \Plumrocket\GDPR\Helper\Checkboxes
     */
    private $checkboxes;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    private $resultPageFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $session;

    /**
     * @var \Plumrocket\GDPR\Helper\Notifys
     */
    private $notifys;

    /**
     * @param \Magento\Framework\App\Action\Context            $context
     * @param \Plumrocket\GDPR\Helper\Checkboxes               $checkboxes
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Framework\View\Result\PageFactory       $resultPageFactory
     * @param \Plumrocket\GDPR\Helper\Notifys                  $notifys
     * @param \Magento\Customer\Model\Session                  $session
     */
    public function __construct(
        Context $context,
        Checkboxes $checkboxes,
        ResultJsonFactory $resultJsonFactory,
        PageFactory $resultPageFactory,
        Notifys $notifys,
        Session $session
    ) {
        parent::__construct($context);
        $this->checkboxes = $checkboxes;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->session = $session;
        $this->notifys = $notifys;
    }

    public function execute()
    {
        $response = ['html' => ''];
        $resultJson = $this->resultJsonFactory->create();

        if ($this->session->getCustomerId()) {
            $resultPage = $this->resultPageFactory->create();

            $checkboxes = $this->checkboxes->getCheckboxes(
                ConsentLocations::REGISTRATION,
                false,
                false
            );

            $checkboxesPages = [];

            foreach ($checkboxes as $key => $checkbox) {
                if (! $checkbox->isRequiredForValidate()) {
                    unset($checkboxes[$key]);
                    continue;
                }

                if ($checkbox->getCmsPageId()) {
                    $checkboxesPages[] = $checkbox->getCmsPageId();
                }
            }

            $notifys = $this->notifys->getNotifys($checkboxesPages);

            $block = $resultPage->getLayout()
                                ->createBlock(Template::class)
                                ->setData('popups', $checkboxes)
                                ->setData('notifys', $notifys)
                                ->setTemplate('Plumrocket_GDPR::consent-popups-xinit.phtml')
                                ->toHtml();

            $response = ['html' => $block];
        }

        return $resultJson->setData($response);
    }
}
