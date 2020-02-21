<?php


namespace Iz\Material\Model\Product;


class Type extends \Magento\Catalog\Model\Product\Type\AbstractType
{

    /**
     * Product type
     */
    const TYPE_CODE = 'material';

    /**
     * Delete data specific for this product type
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return void
     */
    public function deleteTypeSpecificData(\Magento\Catalog\Model\Product $product)
    {
        // TODO: Implement deleteTypeSpecificData() method.
    }
}
