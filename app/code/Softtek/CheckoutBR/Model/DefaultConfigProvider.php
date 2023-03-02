<?php

namespace Softtek\CheckoutBR\Model;

use Magento\Checkout\Model\Session;

class DefaultConfigProvider
{
    protected $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function afterGetConfig(
        \Magento\Checkout\Model\DefaultConfigProvider $subject,
        array $result
    )
    {
        $items = $result['totalsData']['items'];

        for ( $i=0; $i < count($items); $i++) {
            $quoteId = $items[$i]['item_id'];
            $quote = $this->session->getQuote()->getItemById($quoteId);
            $product = $quote->getProduct();

            $items[$i]['weight'] = $product->getWeight();
        }

        $result['totalsData']['items'] = $items;

        return $result;
    }
}
