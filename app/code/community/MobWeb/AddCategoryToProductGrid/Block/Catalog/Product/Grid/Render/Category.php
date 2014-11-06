<?php

// A renderer that is called for every product and tells the system what to output as the grid field value
class MobWeb_AddCategoryToProductGrid_Block_Catalog_Product_Grid_Render_Category extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $newline = "<br />";

        // Get the current product
        $product = Mage::getModel('catalog/product')->load($row->getEntityId());

        // Get the category IDs of the product
        $categoryIds = $product->getCategoryIds();

        // Collect the names of all the categories
        $allCategoryNamesAsString = '';
        foreach($categoryIds as $categoryId)
        {
            // Load the category and get its name
            $categoryName = Mage::getModel('catalog/category')->load($categoryId)->getName();

            // Append the name to the output
            $allCategoryNamesAsString .= $categoryName . $newline;
        }

        // Remove the trailing newline
        $allCategoryNamesAsString = substr($allCategoryNamesAsString, '0', 0-strlen($newline));

        return $allCategoryNamesAsString;
    }
}