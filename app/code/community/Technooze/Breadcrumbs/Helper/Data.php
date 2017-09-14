<?php

/**
 * @category   Technooze/Modules/Magento_Breadcrumbs
 * @package    Technooze_Breadcrumbs
 * @author     Damodar Bashyal (http://dltr.org/)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Technooze_Breadcrumbs_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getCurrentCategory($product)
    {
        $isSchoolWear = $product->getIsSchoolWear(); // Modify this as your need.
        $category = Mage::registry('current_category');

        if(empty($category) || !$category->getId()){
            $id = (int)Mage::app()->getRequest()->getParam('category');

            if($id){
                $category = Mage::getSingleton('catalog/category')->load($id);
                Mage::unregister('current_category');
                Mage::register('current_category', $category);
            }
        }

        if (!$category || $isSchoolWear) {
            if($category && $category->getId()){
                $pathIds = $category->getPathIds();

                if(count($pathIds) >= 4){ // for me minimum depth required is 4 for a school category page.
                    return $category;
                }
            }
            $categoryIds = $product->getCategoryIds();

            if(count($categoryIds)) {
                /* @var $collection Mage_Catalog_Model_Resource_Product_Collection */
                $collection = Mage::getModel('catalog/category')->getCollection();
                $collection->addAttributeToSelect(array('name', 'lowes_category_id'));
                $collection->addAttributeToFilter('is_active', array('eq'=>'1'));
                $collection->addAttributeToFilter('entity_id', array('in' => $categoryIds));
                $collection->addAttributeToSort('level', 'desc');
                $collection->setPageSize(1);

                /* @var $category Mage_Catalog_Model_Category */
                $category = $collection->getFirstItem();

                if ($category->getId()) {
                    $product->setCategory($category);
                    Mage::unregister('current_category'); // unregister existing category
                    Mage::register('current_category', $category);
                }
            }
        }
        return $category;
    }
}