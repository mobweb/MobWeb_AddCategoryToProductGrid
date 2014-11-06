<?php
class MobWeb_AddCategoryToProductGrid_Model_Observer
{
    // This observer checks if the current block is for the catalog product grid, and if yes, inserts our custom column into the block
    public function addCategoryFilterToProductGrid(Varien_Event_Observer $observer)
    {
        // Get the block from the observer
        $block = $observer->getEvent()->getBlock();

        // If no block can be retreived, we can't modify it
        if(!isset($block)) {
            return;
        }

        // Check if it belongs to the grid we're interested in
        if($block instanceof Mage_Adminhtml_Block_Catalog_Product_Grid) {

            // Add our column to the grid
            $block->addColumnAfter('mobweb_addcategorytoproductgrid', array(
                    'header'    => Mage::helper('addcategorytoproductgrid')->__('Category'),
                    'index'     => 'mobweb_addcategorytoproductgrid', // Can be anything, since we use a custom renderer
                    'renderer'  => 'addcategorytoproductgrid/catalog_product_grid_render_category', // Our custom renderer
                    'sortable'  => false,
                    'type'  => 'options', // Add an option filter
                    'options'   => Mage::getSingleton('addcategorytoproductgrid/system_config_source_category')->toOptionArray(), // Provides the options
                    'filter_condition_callback' => array($this, 'filterConditionCallback'), // What to do when the filter is applied
            ), 'qty');
        }
    }

    public function filterConditionCallback($collection, $column)
    {
        // Get the ID of the category that was selected in the filter
        $categoryId = $column->getFilter()->getValue();

        // Add a category filter for the category that was selected to the collection
        $collection->addCategoryFilter(Mage::getModel('catalog/category')->load($categoryId));

        // Return the updated collection
        return $collection;
    }
}