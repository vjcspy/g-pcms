<?php


namespace Iz\Catalog\Setup\Patch\Data;


use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchInterface;

class AttributeSetUpdate implements DataPatchInterface
{
    /**
     * @var \Magento\Eav\Model\Entity\Attribute\Set
     */
    private $attributeSet;
    /**
     * @var \Magento\Eav\Model\Entity\Type
     */
    private $entityType;
    /**
     * @var \Magento\Eav\Model\AttributeManagement
     */
    private $attributeManagement;
    /**
     * @var \Magento\Catalog\Model\Entity\AttributeFactory
     */
    private $catalogAttributeFactory;
    /**
     * @var array
     */
    private $unassignableAttributes;
    /**
     * @var ModuleDataSetupInterface
     */
    private $setup;

    /**
     * @var \Magento\Eav\Model\Entity\Attribute
     */

    public function __construct(
        \Magento\Eav\Model\Entity\Attribute\Set $attributeSet,
        \Magento\Eav\Model\Entity\Type $entityType,
        \Magento\Eav\Model\AttributeManagement $attributeManagement,
        \Magento\Catalog\Model\Entity\AttributeFactory $catalogAttributeFactory,
        \Magento\Catalog\Model\Attribute\Config $attributeConfig,
        ModuleDataSetupInterface $setup
    )
    {
        $this->attributeSet = $attributeSet;
        $this->entityType = $entityType;
        $this->attributeManagement = $attributeManagement;
        $this->catalogAttributeFactory = $catalogAttributeFactory;
        $this->attributeConfig = $attributeConfig;
        $this->setup = $setup;

        $this->unassignableAttributes = $attributeConfig->getAttributeNames('unassignable');
    }

    /**
     * @inheritDoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function apply()
    {

        $productEntity = $this->entityType->loadByCode("catalog_product");
        if ($productEntity) {
            $defaultSetId = $productEntity->getDefaultAttributeSetId();

            if ($defaultSetId) {
                $attributes = $this->attributeManagement->getAttributes('catalog_product', $defaultSetId);
                foreach ($attributes as $attribute) {
                    /** @var \Magento\Catalog\Model\Entity\Attribute $catalogAttr */
                    $catalogAttr = $this->catalogAttributeFactory->create();
                    $catalogAttr->load($attribute->getAttributeId());
                    $isUnassignable = in_array($attribute->getAttributeCode(), $this->unassignableAttributes);
                    if (!$isUnassignable) {
                        $table = $this->setup->getTable('eav_entity_attribute');
                        $where = [
                            'attribute_id =?' => $attribute->getAttributeId(),
                            'attribute_set_id =?' => $defaultSetId,
                        ];

                        $this->setup->getConnection()->delete($table, $where);
                    }
                }

                return;
            }
        }

        throw new \Error("can_not_update_attribute_set_for_catalog_product");
    }
}
