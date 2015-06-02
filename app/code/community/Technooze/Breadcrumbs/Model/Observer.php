<?php
/**
* @category   Technooze/Modules/Magento_Breadcrumbs
* @package    Technooze_Breadcrumbs
* @author     Damodar Bashyal (http://dltr.org/)
* @author     Chris Pook (http://j.mp/1FQTjVz)
* @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*/
class Technooze_Breadcrumbs_Model_Observer
{
	/**
	* Adds category to breadcrumb for product views 
	* originating from outside a category, for instance
	* homepage or navigation links.
	*
	* @return Technooze_Breadcrumbs_Model_Observer
	*/
	public function addCategoryBreadcrumb(Varien_Event_Observer $observer)
	{
		if (!Mage::registry('current_category')) {
            /* @var $product Mage_Catalog_Model_Product */
			$product = $observer->getProduct();
			$categoryIds = $product->getCategoryIds();

			if(count($categoryIds)) {
                /* @var $collection Mage_Catalog_Model_Resource_Product_Collection */
                $collection = Mage::getModel('catalog/category')->getCollection();
				$collection->addAttributeToSelect('name');
				$collection->addAttributeToFilter('is_active', array('eq'=>'1'));
				$collection->addAttributeToFilter('entity_id', array('in' => $categoryIds));
				$collection->addAttributeToSort('entity_id', 'desc');
				$collection->setPageSize(1);

                /* @var $category Mage_Catalog_Model_Category */
                $category = $collection->getFirstItem();
				if ($category->getId()) {
					$product->setCategory($category);
					Mage::register('current_category', $category);
				}
			}
		}

		return $this;
	}
}