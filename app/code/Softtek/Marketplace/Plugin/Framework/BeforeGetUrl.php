<?php
namespace Softtek\Marketplace\Plugin\Framework;

use Softtek\Marketplace\Helper\Data;
use Softtek\Marketplace\Observer\Customer\Account\CouldNotSaveException;
use Softtek\Marketplace\Observer\Customer\Account\Observer;
use Magento\Framework\Url;
use Magento\Framework\App\RequestInterface;

class BeforeGetUrl
{
    /**
     * @var Data
     */
    protected $stmHelper;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * Observer constructor.
     *
     * @param Data $stmHelper
     * @param RequestInterface $request
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Data $stmHelper,
        RequestInterface $request
    )
    {
        $this->stmHelper = $stmHelper;
        $this->request = $request;
    }

    /**
     * Before plugin to redirect user correctly
     *
     * @param Url $subject
     * @param string $routePath
     * @param array $routeParams
     * @return Array
     * @throws CouldNotSaveException
     */
    public function beforeGetUrl(
        Url $subject, $routePath = null, $routeParams = null
    ) {
        if (!$this->stmHelper->isEnabled()) {
            return [$routePath, $routeParams];
        }

        if (!$this->request->getParam('ut')) {
            return [$routePath, $routeParams];
        }
        if ($this->request->getParam('ut') != 'seller') {
            return [$routePath, $routeParams];
        }

        if ($routePath == '*/*/create') {
            $routeParams['ut'] = 'seller';
        }

        return [$routePath, $routeParams];
    }
}
