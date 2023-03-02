<?php
namespace Softtek\CheckoutBR\Plugin\Quote;

use Psr\Log\LoggerInterface;
use Magento\Quote\Model\ShippingAddressManagement as CoreShippingAddressManagement;
use Magento\Quote\Api\Data\AddressInterface;
/**
 * Copy street prefix from checkout shipping address to customer/order address
 *
 * NOTICE OF LICENSE
 *
 * @category  Softtek
 * @package   Softtek_CheckoutBR
 * @author    www.sofftek.com
 * @copyright Softtek Brasil
 * @license   http://opensource.org/licenses/osl-3.0.php
 */
class ShippingAddressManagement
{

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Construct
     *
     * ShippingAddressManagement constructor.
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
     * @param CoreShippingAddressManagement $subject
     * @param $cartId
     * @param AddressInterface $address
     */
    public function beforeAssign(
        CoreShippingAddressManagement $subject,
        $cartId,
        AddressInterface $address
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