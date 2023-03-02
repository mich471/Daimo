<?php
namespace Softtek\CheckoutBR\Plugin\Quote;

use Psr\Log\LoggerInterface;
use Magento\Quote\Model\BillingAddressManagement as CoreBillingAddressManagement;
use Magento\Quote\Api\Data\AddressInterface;

/**
 * Copy street prefix from checkout billing address to customer/order address  *
 *
 * NOTICE OF LICENSE
 *
 * @category  Softtek
 * @package   Softtek_CheckoutBR
 * @author    www.sofftek.com
 * @copyright Softtek Brasil
 * @license   http://opensource.org/licenses/osl-3.0.php
 */

class BillingAddressManagement
{

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Construct
     *
     * BillingAddressManagement constructor.
     *
     * @param LoggerInterface $logger
     */
    public function __construct(
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
    }

    /**
     * Before Assign
     *
     * @param CoreBillingAddressManagement $subject
     * @param $cartId
     * @param AddressInterface $address
     * @param bool $useForShipping
     */
    public function beforeAssign(
        CoreBillingAddressManagement $subject,
        $cartId,
        AddressInterface $address,
        $useForShipping = false
    ) {
        $extAttributes = $address->getExtensionAttributes();

        if (!empty($extAttributes->getStreetPrefix())) {
            try {
                $address->setStreetPrefix($extAttributes->getStreetPrefix());
            } catch (\Exception $e) {
                $this->logger->critical($e->getMessage());
            }
        }
    }
}