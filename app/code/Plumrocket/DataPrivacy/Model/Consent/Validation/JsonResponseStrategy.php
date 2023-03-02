<?php
/**
 * @package     Plumrocket_DataPrivacy
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\DataPrivacy\Model\Consent\Validation;

use Magento\Framework\App\ActionFlag;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Serialize\SerializerInterface;

/**
 * @since 3.1.0
 */
class JsonResponseStrategy extends AbstractResponseStrategy
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
     * @param array|null                                    $formatResponseData
     */
    public function __construct(
        ActionFlag $actionFlag,
        SerializerInterface $serializer,
        ObjectManagerInterface $objectManager,
        ?array $formatResponseData = null
    ) {
        parent::__construct($actionFlag);
        $this->serializer = $serializer;

        if (is_array($formatResponseData)) {
            $formatResponseData = $objectManager->get($formatResponseData['instance']);
        }
        $this->formatResponseData = $formatResponseData;
    }

    /**
     * @param \Magento\Framework\App\Response\HttpInterface $response
     * @param \Magento\Framework\Phrase|string              $errorMessage
     */
    protected function modifyResponse(ResponseInterface $response, $errorMessage): void
    {
        if ($this->formatResponseData) {
            $data = $this->formatResponseData->execute($errorMessage);
        } else {
            $data = ['message' => $errorMessage];
        }

        $response->setHttpResponseCode(400);
        $response->setHeader('Content-type', 'application/json');
        $response->setBody($this->serializer->serialize($data));
    }
}
