<?php
/**
 * Custom Related Products by Category
 * @author : Tabish Naeem <[<t.naeem@live.com>]>
 */

namespace Tabish\RelatedProductByCategories\Block;

class ProductByCategory extends \Magento\Framework\View\Element\Template
{

    /**
     * \Magento\Framework\Registry
     * @var [object]
     */
    protected $_registry;

    /**
     * \Magento\Framework\Registry Current Category
     * @var [type]
     */
    protected $_currentCategories;


    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ){
        $this->_categoryFactory = $categoryFactory;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_registry = $registry;
        parent::__construct( $context, $data );
        $this->_currentCategories = $this->_registry->registry('current_product')->getCategoryIds();
    }

    /**
     * @return [type]
     */
    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    public function getCategory($categoryId) 
    {
        $category = $this->_categoryFactory->create();
        $category->load($categoryId);
        return $category;
    }
    
    /**
     * Get Current Products by Current Category
     * @return [object]
     */
    public function getCurrentCategoryProducts()
    {
        if( $this->_currentCategories ){

            $product = $this->getCategory( $this->_currentCategories[0] )->getProductCollection();
            $product->addAttributeToSelect('*');
            $product->addAttributeToFilter('visibility', \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH);
            $product->addAttributeToFilter('status',\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
            $product->setPageSize(20);
            $product->setCurPage(1);

            return $product;
        }

        return false;
    }
}
