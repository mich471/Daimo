<?php
/**
 * Purpletree_Marketplace SellerInformation
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Purpletree License that is bundled with this package in the file license.txt.
 * It is also available through online at this URL: https://www.purpletreesoftware.com/license.html
 *
 * @category    Purpletree
 * @package     Purpletree_Marketplace
 * @author      Purpletree Software
 * @copyright   Copyright (c) 2017
 * @license     https://www.purpletreesoftware.com/license.html
 */

namespace Softtek\Marketplace\Block\Adminhtml\Customer\Edit;

use Purpletree\Marketplace\Block\Adminhtml\Customer\Edit\SellerInformation as MarketplaceSellerInformation;
use Magento\Customer\Controller\RegistryConstants;
use Magento\Ui\Component\Layout\Tabs\TabInterface;
use Magento\Backend\Block\Widget\Form;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Customer\Api\CustomerRepositoryInterface;

/**
 * Customer account form block
 */
class SellerInformation extends MarketplaceSellerInformation
{
    /**
     * @return bool
     */
    public function canShowTab()
    {
        if ($this->getCustomerId()) {
            if ($this->getIsSeller() != 0) {
                return true;
            }
        }
        return false;
    }

    public function initForm()
    {
        if (!$this->canShowTab()) {
            return $this;
        }
        $form = $this->_formFactory->create();
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Seller Information')]);
        $entityId  = '';
        if ($this->getsellerEntityId() != '') {
            $entityId = $this->getsellerEntityId();
            $model = $this->_initSeller($entityId);

            $fieldset->addField(
                'entity_idpts',
                'hidden',
                [
                    'name' => 'seller[entity_idpts]',
                    'data-form-part' => $this->getData('target_form'),
                    'class' => 'seller',
                    'value' => $entityId
                ]
            );
        }
        $fieldset->addField(
            'status_id',
            'select',
            [
                'label' => __('Store Status'),
                'title' => __('Store Status'),
                'name' => 'seller[status_id]',
                'required' => false,
                'class' => 'seller',
                'data-form-part' => $this->getData('target_form'),
                'options' => ['2' => __('Waiting approval to receive data'), '3' => __('Approved to receive data'), '4' => __('Waiting approval to sell'), '1' => __('Enabled'), '0' => __('Disabled')]
            ]
        );
        $fieldset->addField(
            'store_name',
            'text',
            [
                'name' => 'seller[store_name]',
                'label' => __('Store Name'),
                'title' => __('Store Name'),
                'required' => false,
                'data-form-part' => $this->getData('target_form'),
                'class' => 'seller'
            ]
        );
         $fieldset->addField(
             'store_logo',
             'image',
             [
                'name' => 'seller[store_logo]',
                'label' => __('Store Logo'),
                'title' => __('Store Logo'),
                'class' => 'seller',
                'data-form-part' => $this->getData('target_form'),
                'form' => 'customer_form',
                'note' => __('Allowed image types: jpg,png')
             ]
         );
        $fieldset->addField(
            'store_banner',
            'image',
            [
                'name' => 'seller[store_banner]',
                'label' => __('Store Banner'),
                'title' => __('Store Banner'),
                'class' => 'seller',
                'data-form-part' => $this->getData('target_form'),
                'form' => 'customer_form',
                'note' => __('Allowed image types: jpg,png')
              ]
        );
        $fieldset->addField(
            'store_url',
            'text',
            [
                'name' => 'seller[store_url]',
                'class' => 'seller validate-code',
                'label' => __('Store Url'),
                'title' => __('Store Url'),
                'data-form-part' => $this->getData('target_form'),
                'required' => false
            ]
        );

        $fieldset->addField(
            'store_phone',
            'text',
            [
                'name' => 'seller[store_phone]',
                'class' => 'seller validate-number',
                'label' => __('Store Phone'),
                'title' => __('Store Phone'),
                'data-form-part' => $this->getData('target_form'),
                'required' => false
            ]
        );
        $afterElementHtml = '<p class="nm"><small>' . ' In percent per product sale ' . '</small></p>';
                $fieldset->addField(
                    'store_commission',
                    'text',
                    [
                    'name' => 'seller[store_commission]',
                    'class' => 'seller validate-number',
                    'label' => __('Store Commission'),
                    'title' => __('Store Commission'),
                    'data-form-part' => $this->getData('target_form'),
                    'after_element_html' => $afterElementHtml
                    ]
                );
        $fieldset->addField(
            'store_description',
            'editor',
            [
                'name' => 'seller[store_description]',
                'label' => __('Store Description'),
                'title' => __('Store Description'),
                'style' => 'height:15em',
                'config' => $this->_wysiwygConfig->getConfig(),
                'class' => 'seller',
                'data-form-part' => $this->getData('target_form'),
                'required' => false
            ]
        );

        $fieldset->addField(
            'store_address',
            'textarea',
            [
                'name' => 'seller[store_address]',
                'class' => 'seller',
                'label' => __('Store Address'),
                'title' => __('Store Address'),
                'data-form-part' => $this->getData('target_form'),
                'required' => false
            ]
        );
        $fieldset->addField(
            'store_city',
            'text',
            [
                'name' => 'seller[store_city]',
                'class' => 'seller',
                'label' => __('Store City'),
                'title' => __('Store City'),
                'data-form-part' => $this->getData('target_form'),
                'required' => false
            ]
        );
        $optionsc=$this->_countryFactory->toOptionArray();

        $country = $fieldset->addField(
            'store_country',
            'select',
            [
                    'name' => 'seller[store_country]',
                    'label' => __('Store Country'),
                    'title' => __('Store Country'),
                    'class' => 'seller' ,
                    'values' => $optionsc,
                    'data-form-part' => $this->getData('target_form'),
                    'required' => false
                ]
        );

        if ($entityId != '') {
            if ($model->getData('store_region_id') != 0) {
                $states = [];
                if ($model->getData('store_country')) {
                    $states = $this->getStates->toOptionArray($model->getData('store_country'));
                }
                 $fieldset->addField(
                     'store_region_id',
                     'select',
                     [
                     'name' => 'seller[store_region_id]',
                     'label' => __('Store State'),
                     'title' => __('Store State'),
                     'class' => 'seller' ,
                     'values' =>  ['--Please Select Country--'],
                     'data-form-part' => $this->getData('target_form'),
                     'values' => $states,
                     'required' => false
                     ]
                 );
            } else {
                 $fieldset->addField(
                     'store_region',
                     'text',
                     [
                     'name' => 'seller[store_region]',
                     'label' => __('Store State'),
                     'title' => __('Store State'),
                     'class' => 'seller',
                     'data-form-part' => $this->getData('target_form'),
                     'required' => false
                     ]
                 );
            }
        } else {
             $fieldset->addField(
                 'store_region_id',
                 'select',
                 [
                    'name' => 'seller[store_region_id]',
                    'label' => __('Store State'),
                    'title' => __('Store State'),
                    'class' => 'seller' ,
                    'values' =>  ['--Please Select Country--'],
                    'data-form-part' => $this->getData('target_form'),
                    'required' => false
                 ]
             );
        }
         $country->setAfterElementHtml("
            <script type=\"text/javascript\">
                    require([
                    'jquery',
                    'mage/template',
                    'jquery/ui',
                    'mage/translate'
                ],
                function($, mageTemplate) {
                   $(document).on('change', '.seller#store_country', function(event){
                            var country_name = $('.seller#store_country').val();
                        $.ajax({
								url: '". $this->getUrl('purpletree_marketplace/lists/regionlist')."',
                                data: 'country='+country_name,
                                type: 'post',
                                dataType: 'json',
                               showLoader:true,
                               success: function(data){
                                   if(data.success == 'true') {
                                        $('.seller#store_region_id').remove();
                                       $('.seller#store_region').css('display','none');
                                       if($('.seller#store_region_id').length) {
                                       } else {
                                       $('.seller#store_region').after('<select id =\"store_region_id\" data-form-part=\"customer_form\" data-ui-id=\"custom-edit-tab-become-remove-seller-fieldset-element-select-seller-region-id\" name=\"seller[store_region_id]\" title=\"Store State\" class=\"seller input-text admin__control-text _required\"></select>');
                                       }

                                    $('.seller#store_region_id').empty();
                                    $('.seller#store_region').remove();
                                    $('.seller#store_region_id').css('display','block');
                                    $('.seller#store_region_id').append(data.htmlconent);
                                   } else {
                                       $('.seller#store_region_id').css('display','none');
                                       if($('.seller#store_region').length) {
                                       }
                                       else {
                                       $('.seller#store_region_id').after('<input type=\"text\" id =\"store_region\" data-form-part=\"customer_form\" data-ui-id=\"custom-edit-tab-become-remove-seller-fieldset-element-select-seller-region-name\" name=\"seller[store_region]\" title=\"Store State\" class=\"seller input-text admin__control-text _required\" />');
                                       }
                                        $('.seller#store_region_id').remove();
                                   }
                               }
                            });
                   })
                }

            );
            </script>");
        $fieldset->addField(
            'store_zipcode',
            'text',
            [
                'name' => 'seller[store_zipcode]',
                'class' => 'seller',
                'label' => __('Store Zipcode'),
                'title' => __('Store Zipcode'),
                'data-form-part' => $this->getData('target_form'),
                'required' => false
            ]
        );
        $fieldset->addField(
            'store_tin_number',
            'text',
            [
                'name' => 'seller[store_tin_number]',
                'class' => 'seller',
                'label' => __('Store TIN Number'),
                'title' => __('Store TIN Number'),
                'data-form-part' => $this->getData('target_form'),
                'required' => false
            ]
        );
        $fieldset->addField(
            'store_bank_account',
            'textarea',
            [
                'name' => 'seller[store_bank_account]',
                'class' => 'seller',
                'label' => __('Store Bank Details'),
                'title' => __('Store Bank Details'),
                'data-form-part' => $this->getData('target_form'),
                'required' => false
            ]
        );

        $fieldset->addField(
            'store_shipping_policy',
            'editor',
            [
                'name' => 'seller[store_shipping_policy]',
                'label' => __('Store Shipping Policy'),
                'class' => 'seller',
                'title' => __('Store Shipping Policy'),
                'style' => 'height:15em',
                'config' => $this->_wysiwygConfig->getConfig(),
                'data-form-part' => $this->getData('target_form'),
                'required' => false
            ]
        );
        $fieldset->addField(
            'store_return_policy',
            'editor',
            [
                'name' => 'seller[store_return_policy]',
                    'class' => 'seller',
                'label' => __('Store Return Policy'),
                'title' => __('Store Return Policy'),
                'style' => 'height:15em',
                'config' => $this->_wysiwygConfig->getConfig(),
                'data-form-part' => $this->getData('target_form'),
                'required' => false
            ]
        );
        $fieldset->addField(
            'store_meta_keywords',
            'textarea',
            [
                'name' => 'seller[store_meta_keywords]',
                    'class' => 'seller',
                'label' => __('Store Meta Keywords'),
                'title' => __('Store Meta Keywords'),
                'data-form-part' => $this->getData('target_form'),
                'required' => false
            ]
        );
        $fieldset->addField(
            'store_meta_descriptions',
            'textarea',
            [
                'name' => 'seller[store_meta_descriptions]',
                'label' => __('Store Meta Descriptions'),
                    'class' => 'seller',
                'title' => __('Store Meta Descriptions'),
                'data-form-part' => $this->getData('target_form'),
                'required' => false
            ]
        );

        if ($entityId != '') {
            $form->setValues($model->getData());
            $form->setUseContainer(true);
        }
        $this->setForm($form);
        return $this;
    }
}
