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

namespace Plumrocket\GDPR\Model\Consent\Validation;

use Magento\Framework\App\ActionFlag;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Serialize\SerializerInterface;

/**
 * @deprecated since 3.0.0
 * @see \Plumrocket\DataPrivacy\Model\Consent\Validation\JsonResponseStrategy
 * TODO: remove in next major release
 */
class JsonResponseStrategy extends \Plumrocket\GDPR\Model\Consent\Validation\AbstractResponseStrategy
{
    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    private $serializer;

    /**
     * @var callable|null
     */
    private $formatResponseData;

    /**
     * JsonResponseStrategy constructor.
     *
     * @param \Magento\Framework\App\ActionFlag                $actionFlag
     * @param \Magento\Framework\Serialize\SerializerInterface $serializer
     * @param \Magento\Framework\ObjectManagerInterface        $objectManager
     * @param callable|null                                    $formatResponseData
     */
    public function __construct(
        ActionFlag $actionFlag,
        SerializerInterface $serializer,
        ObjectManagerInterface $objectManager,
        $formatResponseData = null
    ) {
        parent::__construct($actionFlag);
        $this->serializer = $serializer;

        if (is_array($formatResponseData)) {
            $formatResponseData = $objectManager->get($formatResponseData['instance']);
        }

        $this->formatResponseData = $formatResponseData;
    }

    /**
     * @param ResponseInterface                $response
     * @param \Magento\Framework\Phrase|string $errorMessage
     */
    protected function modifyResponse(ResponseInterface $response, $errorMessage)
    {
        if ($this->formatResponseData) {
            $data = $this->formatResponseData->execute($errorMessage);
        } else {
            $data = [
                'success' => false
            ];

            if ($errorMessage) {
                $data['message'] = $errorMessage;
            }
        }

        $response->setBody($this->serializer->serialize($data));
    }
}
