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
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\GDPR\Model\Consent\Validation;

use Magento\Framework\App\ResponseInterface;

abstract class AbstractResponseStrategy implements NotAgreedResponseStrategyInterface
{
    /**
     * @var \Magento\Framework\Phrase|string
     */
    private $errorMessage = '';

    /**
     * @var \Magento\Framework\App\ActionFlag
     */
    private $actionFlag;

    /**
     * AbstractResponseStrategy constructor.
     *
     * @param \Magento\Framework\App\ActionFlag $actionFlag
     */
    public function __construct(
        \Magento\Framework\App\ActionFlag $actionFlag
    ) {
        $this->actionFlag = $actionFlag;
    }

    /**
     * @param ResponseInterface                $response
     * @param \Magento\Framework\Phrase|string $errorMessage
     * @return void
     */
    abstract protected function modifyResponse(ResponseInterface $response, $errorMessage); //@codingStandardsIgnoreLine

    /**
     * @param \Magento\Framework\Phrase $errorMessage
     * @return NotAgreedResponseStrategyInterface
     */
    public function setMessage(\Magento\Framework\Phrase $errorMessage) : NotAgreedResponseStrategyInterface
    {
        $this->errorMessage = $errorMessage;

        return $this;
    }

    /**
     * @param ResponseInterface $response
     * @return NotAgreedResponseStrategyInterface
     */
    public function render(ResponseInterface $response) : NotAgreedResponseStrategyInterface
    {
        $this->actionFlag->set('', \Magento\Framework\App\ActionInterface::FLAG_NO_DISPATCH, true);
        $this->actionFlag->set('', \Magento\Framework\App\ActionInterface::FLAG_NO_POST_DISPATCH, true);

        $this->modifyResponse($response, $this->errorMessage);

        return $this;
    }
}
