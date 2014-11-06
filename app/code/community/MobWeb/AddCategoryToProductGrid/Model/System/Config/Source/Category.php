<?php
class MobWeb_AddCategoryToProductGrid_Model_System_Config_Source_Category
{
    // A recursive function that loops through all the categories, starting from $node, and returns
    // the categories in an array
    public function getCategoryFilterOptions(Varien_Data_Tree_Node $categoryNode, $filterOptions, $treeLevel = 0)
    {
        // Tree level, for intendation
        $treeLevel++;

        // Get the value and label for the current option, intend the label according to the current level
        $filterOptions[$categoryNode->getId()]['value'] =  $categoryNode->getId();
        $filterOptions[$categoryNode->getId()]['label'] = str_repeat("--", $treeLevel) . $categoryNode->getName();

        // Also get the options for the current category's child categories, recursively
        foreach ($categoryNode->getChildren() as $childCategoryNode) {
            $filterOptions = $this->getCategoryFilterOptions($childCategoryNode, $filterOptions, $treeLevel);
        }

        return $filterOptions;
    }

    // This method returns all the options for the filter on our custom column
    public function toOptionArray($addEmpty = true)
    {
        // Get the root category ID of the current store
        $store = Mage::app()->getFrontController()->getRequest()->getParam('store', 0);
        $rootCategoryId = $store ? Mage::app()->getStore($store)->getRootCategoryId() : 1;

        // Load the category tree
        $tree = Mage::getResourceSingleton('catalog/category_tree')->load();

        // Get the root category node in the tree
        $rootCategoryNode = $tree->getNodeById($rootCategoryId);

        // Set the output name for the root category, seems to be empty otherwise
        if($rootCategoryNode && $rootCategoryNode->getId() == 1) {
            $rootCategoryNode->setName(Mage::helper('catalog')->__('Root'));
        }

        // Add all the active categories to the category tree (which is required later on in the recursive function)
        $tree->addCollectionData(Mage::getModel('catalog/category')->getCollection()->setStoreId($store)->addAttributeToSelect('name')->addAttributeToSelect('is_active'), true);

        // Get all categories (filter options), using a recursive function
        $categories = $this->getCategoryFilterOptions($rootCategoryNode, array());

        // Create the array that contains all the options with their values and labels
        $optionArray = array();
        foreach ($categories as $category) {
            $optionArray[$category['value']] =  $category['label'];
        }

        // Return the array
        return $optionArray;
    }
}