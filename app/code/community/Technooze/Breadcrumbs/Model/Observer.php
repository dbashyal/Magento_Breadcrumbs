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

    const LAYERED_FILTER_CATEGORY_PARAM = 'cat';

    /**
     * Show proper category breadcrumb for layered navigation
     * When filtering by sub-category
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function catalogControllerCategoryInitAfter(Varien_Event_Observer $observer)
    {
        /* @var $category Mage_Catalog_Model_Category */
        $category = $observer->getCategory();
        $cat = Mage::app()->getRequest()->getParam(self::LAYERED_FILTER_CATEGORY_PARAM, 0);
        if(!$cat){
            return $this;
        }
        $cat = (int)$cat;

        if($cat != $category->getId()){
            $cat = Mage::getSingleton('catalog/category')->setStoreId($category->getStoreId())->load($cat);

            if(!$cat->getId()){
                return $this;
            }

            Mage::unregister('current_category');
            Mage::register('current_category', $cat);
        }

        return $this;
    }

    /**
     * Adds category to breadcrumb for product views
     * originating from outside a category, for instance
     * homepage or navigation links.
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function addCategoryBreadcrumb(Varien_Event_Observer $observer)
	{
        /* @var $product Mage_Catalog_Model_Product */
        $product = $observer->getProduct();
        Mage::helper('tbreadcrumbs')->getCurrentCategory($product);

		return $this;
	}
}