<?php
/**
 * @author Hexamarvel Team
 * @copyright Copyright (c) 2021 Hexamarvel (https://www.hexamarvel.com)
 * @package Hexamarvel_Attachments
 */
namespace Hexamarvel\Attachments\Api;

interface ProductAttachmentInterface
{
     /**
      * Get the attachment by ID.
      * @api
      * @param string $id of product.
      * @throws \Magento\Framework\Exception\NoSuchEntityException
      * @return array
      */
    public function get($id);

     /**
      * Return the collection of attachments.
      * @api
      * @throws \Magento\Framework\Exception\NoSuchEntityException
      * @return array
      */
    public function getList();

     /**
      * Delete product attachment by ID.
      *
      * @param int $id
      * @return bool true on success
      * @throws \Magento\Framework\Exception\NoSuchEntityException
      * @return bool true on success
      */
    public function deleteById($id);

     /**
      * update an attachments
      * @throws \Magento\Framework\Exception\NoSuchEntityException
      * @return bool true on success
      */
    public function save();

     /**
      * update an attachments
      * @param int $id
      * @throws \Magento\Framework\Exception\NoSuchEntityException
      * @return bool true on success
      */
    public function update($id);

     /**
      * Return the attachments for the  product from Product Id.
      * @api
      * @param int $id
      * @throws \Magento\Framework\Exception\NoSuchEntityException
      * @return array
      */
    public function getAttachmentsByProductId($id);
}
