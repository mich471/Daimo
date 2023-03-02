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
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\GDPR\Model\Account\Processors;

use Magento\Checkout\Model\Session;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Pricing\Helper\Data;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartInterface;
use Plumrocket\GDPR\Api\DataExportProcessorInterface;
use Plumrocket\GDPR\Api\DataProcessorInterface;
use Plumrocket\GDPR\Api\DataRemovalProcessorInterface;

/**
 * Processor customer quote data.
 *
 * Export and delete customer quotes.
 * Export and delete guest current quote.
 */
class CustomerQuote implements DataProcessorInterface, DataRemovalProcessorInterface, DataExportProcessorInterface
{
    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    private $priceHelper;

    /**
     * @var array
     */
    private $dataExport;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $session;

    /**
     * CustomerQuote constructor.
     *
     * @param \Magento\Framework\Pricing\Helper\Data       $priceHelper
     * @param \Magento\Quote\Api\CartRepositoryInterface   $quoteRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Checkout\Model\Session              $session
     * @param array                                        $dataExport
     */
    public function __construct(
        Data $priceHelper,
        CartRepositoryInterface $quoteRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Session $session,
        array $dataExport = []
    ) {
        $this->priceHelper = $priceHelper;
        $this->dataExport = $dataExport;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->quoteRepository = $quoteRepository;
        $this->session = $session;
    }

    /**
     * Executed upon exporting customer data.
     *
     * Expected return structure:
     *      array(
     *          array('HEADER1', 'HEADER2', 'HEADER3', ...),
     *          array('VALUE1', 'VALUE2', 'VALUE3', ...),
     *          ...
     *      )
     *
     * @param CustomerInterface $customer
     * @return array
     */
    public function export(CustomerInterface $customer)
    {
        if ($customer->getId()) {
            return $this->exportCustomerData($customer);
        }

        return $this->exportGuestData($customer->getEmail());
    }

    /**
     * Executed upon customer data deletion.
     *
     * @param CustomerInterface $customer
     * @return void|bool
     */
    public function delete(CustomerInterface $customer)
    {
        if ($customer->getId()) {
            return $this->deleteCustomerData($customer);
        }

        return $this->deleteGuestData($customer->getEmail());
    }

    /**
     * Executed upon customer data anonymization.
     *
     * @param CustomerInterface $customer
     * @return void|bool
     */
    public function anonymize(CustomerInterface $customer)
    {
        return $this->processCustomerQuote($customer);
    }

    /**
     * Process Customer quote.
     *
     * @param $customer
     * @return bool
     */
    public function processCustomerQuote($customer)
    {
        $quotes = $this->getCustomerQuotes($customer);

        foreach ($quotes as $quote) {
            $this->quoteRepository->delete($quote);
        }

        return true;
    }

    /**
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @return \Magento\Quote\Api\Data\CartInterface[]
     */
    public function getCustomerQuotes(CustomerInterface $customer)
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter(
            'customer_id',
            $customer->getId(),
            'eq'
        )->create();
        $cartSearchResults = $this->quoteRepository->getList($searchCriteria);

        return $cartSearchResults->getItems();
    }

    /**
     * Returns quote items data.
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @return array|null
     */
    public function exportCustomerData(CustomerInterface $customer): ?array
    {
        $quotes = $this->getCustomerQuotes($customer);

        $quotes = array_filter($quotes, static function (CartInterface $cart) {
            return $cart->getItemsQty() > 0;
        });

        if (! $quotes) {
            return null;
        }

        $returnData = [];
        $i=0;

        foreach ($this->dataExport as $title) {
            $returnData[$i][] = $title;
        }

        $i++;

        foreach ($quotes as $quote) {
            foreach ($quote->getAllVisibleItems() as $cartItem) {
                $cartItem->setData('price', $this->priceHelper
                    ->currency((float)$cartItem->getPrice(), true, false));
                $cartItemData = $cartItem->getData();
                foreach ($this->dataExport as $key => $title) {
                    $returnData[$i][] = ($cartItemData[$key] ?? '');
                }

                $i++;
            }
        }

        return $returnData;
    }

    /**
     * @param string $email
     * @return array|null
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function exportGuestData(string $email): ?array
    {
        $cartItems = $this->session->getQuote()->getItems();
        if (! $cartItems) {
            return null;
        }

        $returnData = [];

        $i=0;

        foreach ($this->dataExport as $key => $title) {
            $returnData[$i][] = $title;
        }

        $i++;

        foreach ($cartItems as $cartItem) {
            $cartItem->setData('price', $this->priceHelper
                ->currency((float)$cartItem->getPrice(), true, false));
            $cartItemData = $cartItem->getData();
            foreach ($this->dataExport as $key => $title) {
                $returnData[$i][] = ($cartItemData[$key] ?? '');
            }

            $i++;
        }

        return $returnData;
    }

    /**
     * @inheritDoc
     */
    public function deleteCustomerData(CustomerInterface $customer): bool
    {
        return $this->processCustomerQuote($customer);
    }

    /**
     * @inheritDoc
     */
    public function deleteGuestData(string $email): bool
    {
        $this->session->clearQuote();
        return true;
    }

    /**
     * @inheritDoc
     */
    public function getFileName(string $dateTime): string
    {
        return "Cart_Information_$dateTime";
    }
}
