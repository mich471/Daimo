<?php
/**
 * Softtek_Marketplace SellerData
 *
 * @category    Softtek
 * @package     Softtek_Marketplace
 * @author      J. Abraham Serena <jorge.serena@softtek.com>
 * @copyright   © Softtek 2022. All rights reserved.
 */
namespace Softtek\Questions\Helper;
use \Datetime;
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * Data Constructor
     *
     * @param \Purpletree\Marketplace\Model\SellerFactory
     * @param \Purpletree\Marketplace\Model\ResourceModel\Seller
     * @param \Magento\Framework\Message\ManagerInterface
     */
    public function __construct(
        \Purpletree\Marketplace\Model\SellerFactory $sellerFactory,
        \Purpletree\Marketplace\Model\ResourceModel\Seller $storeDetails,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        $this->sellerFactory    =   $sellerFactory;
        $this->storeDetails     =   $storeDetails;
        $this->messageManager   =   $messageManager;
    }

    public function getQuestions($id, $limit = false, $p = false){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection();
        $tableName = $resource->getTableName('phpcuong_product_question');
        $sql = "SELECT * FROM phpcuong_product_question where question_status_id = 1 and product_idseller = ". $id ." order by question_created_at desc";
        if (!$limit && $p) {
            $limit = 10;
        }
        if ($limit) {
            $sql .= " LIMIT $limit";
        }
        if ($p) {
            $offset = $limit * $p - $limit;
            $sql .= " OFFSET $offset";
        }
        $sql .= ";";
        $data = $connection->fetchAll($sql);
        return $data;
    }

    public function getQuestion($id){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection();
        $tableName = $resource->getTableName('phpcuong_product_question');
        $sql = "SELECT * FROM phpcuong_product_question WHERE question_id = $id"  ;
        $data = $connection->fetchAll($sql);
        return $data;
    }

    public function saveQuestions($answer, $nameseller, $emailseller, $idQuestion, $userId){
        $now = new DateTime();
        $date = $now->format('Y-m-d H:i:s');
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection();
        $tableName = $resource->getTableName('phpcuong_product_question');
        $sql = "INSERT INTO phpcuong_product_answer (answer_detail, answer_author_name, answer_author_email, question_id, answer_status_id, answer_user_type_id,answer_user_id,answer_created_by, answer_visibility_id, answer_likes, answer_dislikes, answer_created_at) VALUES (" . "'".$answer ."'". "," ."'".$nameseller."'". "," ."'".$emailseller."'".","."'".$idQuestion . "'".",1,1," . "'".$userId ."'".",2,2,0,0,"."'".$date."')" ;
        $data = $connection->fetchAll($sql);
    }

    public function updateQuestions($answer, $Id){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection();
        $tableName = $resource->getTableName('phpcuong_product_question');
        $sql = "UPDATE phpcuong_product_answer SET answer_detail =". "'".$answer."'" ." WHERE answer_id = " . $Id;
        $data = $connection->fetchAll($sql);
    }
}
