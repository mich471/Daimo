<?php
/**
 * @package     Plumrocket_DataPrivacy
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\DataPrivacy\Observer;

use Plumrocket\DataPrivacy\Model\Consent\Validation\NotAgreedResponseStrategyInterface;
use Plumrocket\DataPrivacy\Model\Consent\Validation\NotAgreedResponseStrategyInterfaceFactory;
use Plumrocket\DataPrivacyApi\Api\ConsentCheckboxesValidatorInterface;
use Plumrocket\GDPR\Model\Config\Source\ConsentLocations;

class ValidateConsentsObserver implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;

    /**
     * @var array
     */
    private $notAgreedResponseStrategies;

    /**
     * @var \Plumrocket\DataPrivacyApi\Api\ConsentCheckboxesValidatorInterface
     */
    private $consentCheckboxesValidator;

    /**
     * @var \Magento\Customer\Helper\Session\CurrentCustomer
     */
    private $currentCustomer;

    /**
     * @var \Plumrocket\DataPrivacy\Model\Consent\Validation\NotAgreedResponseStrategyInterfaceFactory
     */
    private $redirectStrategyFactory;

    /**
     * @var \Plumrocket\DataPrivacy\Helper\Config
     */
    private $config;

    /**
     * ValidateCheckboxesObserver constructor.
     *
     * @param \Magento\Framework\Registry                                        $coreRegistry
     * @param NotAgreedResponseStrategyInterfaceFactory                          $redirectStrategyFactory
     * @param \Plumrocket\DataPrivacyApi\Api\ConsentCheckboxesValidatorInterface $consentCheckboxesValidator
     * @param \Magento\Customer\Helper\Session\CurrentCustomer                   $currentCustomer
     * @param \Plumrocket\DataPrivacy\Helper\Config                              $config
     * @param array                                                              $notAgreedResponseStrategies
     */
    public function __construct(
        \Magento\Framework\Registry $coreRegistry,
        NotAgreedResponseStrategyInterfaceFactory $redirectStrategyFactory,
        ConsentCheckboxesValidatorInterface $consentCheckboxesValidator,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        \Plumrocket\DataPrivacy\Helper\Config $config,
        array $notAgreedResponseStrategies = []
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->redirectStrategyFactory = $redirectStrategyFactory;
        $this->notAgreedResponseStrategies = $notAgreedResponseStrategies;
        $this->consentCheckboxesValidator = $consentCheckboxesValidator;
        $this->currentCustomer = $currentCustomer;
        $this->config = $config;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var null|\Magento\Framework\App\RequestInterface $request */
        $request = $observer->getData('request');
        /** @var null|\Magento\Framework\App\Action\AbstractAction $controllerAction */
        $controllerAction = $observer->getData('controller_action');

        if ($this->config->isModuleEnabled()
            && $request
            && $request->isPost()
            && $controllerAction
        ) {
            $this->validateConsents($request, $controllerAction);
        }
    }

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\App\Action\AbstractAction $controllerAction
     * @return \Plumrocket\GDPR\Observer\ValidateConsentsObserver
     */
    private function validateConsents($request, $controllerAction)
    {
        $isValidRequest = true;
        $errorMessage = __('Please provide your consent to all terms.');
        $location = (string) $request->getParam('prgdpr_location');
        $consents = $request->getParam('prgdpr_consent', []);

        switch ($request->getModuleName()) {
            case 'newsletter':
                $location = ConsentLocations::NEWSLETTER;
                $errorMessage = __('Please provide your consent to all terms before subscribing to our newsletter.');
                break;
            case 'contact':
                $location = ConsentLocations::CONTACT_US;
                $errorMessage = __('Please provide your consent to all terms before submitting this form.');
                break;
        }

        // If there is no location it means that this request can be accepted without consents
        if (empty($location)) {
            return $this;
        }

        $customerId = (int) $this->currentCustomer->getCustomerId();
        try {
            if ($this->consentCheckboxesValidator->isAcceptedAllRequiredCheckboxes($consents, $location, $customerId)) {
                $this->coreRegistry->unregister('prgdpr_skip_save_consents');
                $this->coreRegistry->register('prgdpr_location', $location);
                $this->coreRegistry->register('prgdpr_consent', $consents);
            } else {
                $isValidRequest = false;
            }
        } catch (\InvalidArgumentException $invalidArgumentException) {
            $isValidRequest = false;
            $errorMessage = __('Undefined location for specified consents.');
        }

        if (! $isValidRequest) {
            $this->getStrategy($request->getFullActionName())
                 ->setMessage($errorMessage)
                 ->render($controllerAction->getResponse());
        }

        return $this;
    }

    /**
     * Retrieve response strategy by Full Action Name
     *
     * @param string $fullActionName
     * @return NotAgreedResponseStrategyInterface
     */
    private function getStrategy(string $fullActionName)
    {
        return $this->notAgreedResponseStrategies[$fullActionName] ?? $this->redirectStrategyFactory->create();
    }
}
