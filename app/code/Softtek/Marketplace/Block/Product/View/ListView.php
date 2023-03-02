<?php
namespace Softtek\Marketplace\Block\Product\View;

use PHPCuong\ProductQuestionAndAnswer\Block\Product\View\ListView as ViewListView;

class ListView extends ViewListView
{
    /**
     * Retrieve the question information
     *
     * @param \PHPCuong\ProductQuestionAndAnswer\Model\ResourceModel\Question\CollectionFactory $question
     * @return object
     */
    protected function getQuestionInfo($question)
    {
        /** @var \Magento\Framework\DataObjectFactory $dataObjectFactory */
        $questionData = $this->dataObjectFactory->create()
            ->setId($question->getQuestionId())
            ->setTitle(nl2br($question->getQuestionDetail()))
            ->setAuthorName(ucwords(strtolower($question->getQuestionAuthorName())))
            ->setFirstCharacter(substr($question->getQuestionAuthorName(), 0, 1))
            ->setLikes($question->getQuestionLikes())
            ->setDislikes($question->getQuestionDislikes())
            ->setAskedBy($this->getAddedBy($question->getQuestionUserTypeId()))
            ->setCreatedAt($this->formatDateTime->formatCreatedAt($question->getQuestionCreatedAt()))
            ->setAnswerDetail($question->getAnswerDetail());
        $answers = [];
        foreach ($this->getAnswerList($question) as $answer) {
            $answers[] = $this->getAnswerInfo($answer);
        }
        $questionData->setAnswers($answers);
        return $questionData->getData();
    }
}
