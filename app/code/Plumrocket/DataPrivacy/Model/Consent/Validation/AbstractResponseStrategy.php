<?php
/**
 * @package     Plumrocket_DataPrivacy
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Plumrocket\DataPrivacy\Model\Consent\Validation;

use Magento\Framework\App\ActionFlag;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Response\HttpInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Phrase;

/**
 * @since 3.1.0
 */
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
    public function __construct(ActionFlag $actionFlag)
    {
        $this->actionFlag = $actionFlag;
    }

    /**
     * @param ResponseInterface                $response
     * @param \Magento\Framework\Phrase|string $errorMessage
     * @return void
     */
    abstract protected function modifyResponse(ResponseInterface $response, $errorMessage): void;

    /**
     * @param \Magento\Framework\Phrase $errorMessage
     * @return NotAgreedResponseStrategyInterface
     */
    public function setMessage(Phrase $errorMessage): NotAgreedResponseStrategyInterface
    {
        $this->errorMessage = $errorMessage;
        return $this;
    }

    /**
     * @param \Magento\Framework\App\Response\HttpInterface $response
     * @return \Plumrocket\DataPrivacy\Model\Consent\Validation\NotAgreedResponseStrategyInterface
     */
    public function render(HttpInterface $response): NotAgreedResponseStrategyInterface
    {
        $this->actionFlag->set('', ActionInterface::FLAG_NO_DISPATCH, true);
        $this->actionFlag->set('', ActionInterface::FLAG_NO_POST_DISPATCH, true);
        $this->modifyResponse($response, $this->errorMessage);
        return $this;
    }
}
