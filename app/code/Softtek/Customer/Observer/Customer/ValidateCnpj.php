<?php
namespace Softtek\Customer\Observer\Customer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\App\RequestInterface;

/**
 * Customer Observer Model
 */
class ValidateCnpj implements ObserverInterface
{
    /**
     * @var RequestInterface
     */
    protected $request;

    public function __construct(
        RequestInterface $request
    ){
        $this->request = $request;
    }

    /**
     * Observer execute
     *
     * @param Observer $observer
     * @return Observer
     * @throws CouldNotSaveException
     */
    public function execute(Observer $observer)
    {
        $moduleName     = $this->request->getModuleName();
        $controllerName = $this->request->getControllerName();
        $actionName     = $this->request->getActionName();
        if ($moduleName != 'customer' && $controllerName != 'account' && $actionName != 'createpost') {
            return $this;
        }

        $customer = $observer->getEvent()->getCustomer();
        $currentCnpj = trim($customer->getCnpj());
        if ($currentCnpj == '') {
            return $this;
        }

        $customerCol = $customer->getCollection();
        $customerCol->addFieldToFilter('cnpj', $currentCnpj);

        foreach ($customerCol as $customerItem) {
            if ($customerItem->getId() != $customer->getId()) {
                throw new CouldNotSaveException(
                    __('CNPJ %1 already in use.', $customerItem->getCnpj())
                );
            }
        }

        return $this;
	}
}
