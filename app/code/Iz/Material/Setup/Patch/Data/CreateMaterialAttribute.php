<?php

namespace Iz\Material\Setup\Patch\Data;

use Iz\Material\Model\Product\Attribute\Source\MaterialUnit;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Catalog\Model\ResourceModel\Product;
use Magento\Catalog\Setup\CategorySetup;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class CreateMaterialAttribute implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;
    /**
     * @var CategorySetupFactory
     */
    private $categorySetupFactory;

    /**
     * This should be set explicitly
     */
    const CATEGORY_ENTITY_TYPE_ID = 3;

    /**
     * This should be set explicitly
     */
    const CATALOG_PRODUCT_ENTITY_TYPE_ID = 4;

    /**
     * CreateMaterialAttribute constructor.
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param CategorySetupFactory $categorySetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        CategorySetupFactory $categorySetupFactory
    )
    {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->categorySetupFactory = $categorySetupFactory;
    }

    /**
     * Get array of patches that have to be executed prior to this.
     *
     * Example of implementation:
     *
     * [
     *      \Vendor_Name\Module_Name\Setup\Patch\Patch1::class,
     *      \Vendor_Name\Module_Name\Setup\Patch\Patch2::class
     * ]
     *
     * @return string[]
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * Get aliases (previous names) for the patch.
     *
     * @return string[]
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * Run code inside patch
     * If code fails, patch must be reverted, in case when we are speaking about schema - then under revert
     * means run PatchInterface::revert()
     *
     * If we speak about data, under revert means: $transaction->rollback()
     *
     * @return $this
     */
    public function apply()
    {
        /** @var CategorySetup $categorySetup */
        $categorySetup = $this->categorySetupFactory->create(['setup' => $this->moduleDataSetup]);
        $this->createNewAttributeForMaterial($categorySetup);
        return $this;
    }

    protected function createNewAttributeForMaterial(CategorySetup $categorySetup)
    {
        foreach ($this->getMaterialAttributes() as $entityName => $entity) {
            $frontendPrefix = isset($entity['frontend_prefix']) ? $entity['frontend_prefix'] : '';
            $backendPrefix = isset($entity['backend_prefix']) ? $entity['backend_prefix'] : '';
            $sourcePrefix = isset($entity['source_prefix']) ? $entity['source_prefix'] : '';

            if (is_array($entity['attributes']) && !empty($entity['attributes'])) {
                foreach ($entity['attributes'] as $attrCode => $attr) {
                    if (!empty($attr['backend'])) {
                        if ('_' === $attr['backend']) {
                            $attr['backend'] = $backendPrefix;
                        } elseif ('_' === $attr['backend'][0]) {
                            $attr['backend'] = $backendPrefix . $attr['backend'];
                        }
                    }
                    if (!empty($attr['frontend'])) {
                        if ('_' === $attr['frontend']) {
                            $attr['frontend'] = $frontendPrefix;
                        } elseif ('_' === $attr['frontend'][0]) {
                            $attr['frontend'] = $frontendPrefix . $attr['frontend'];
                        }
                    }
                    if (!empty($attr['source'])) {
                        if ('_' === $attr['source']) {
                            $attr['source'] = $sourcePrefix;
                        } elseif ('_' === $attr['source'][0]) {
                            $attr['source'] = $sourcePrefix . $attr['source'];
                        }
                    }
                    $categorySetup->removeAttribute($entityName, $attrCode);
                    $categorySetup->addAttribute($entityName, $attrCode, $attr);
                }
            }
        }
    }

    public function getMaterialAttributes()
    {
        return [
            'catalog_product' => [
                'entity_type_id' => self::CATALOG_PRODUCT_ENTITY_TYPE_ID,
                'entity_model' => Product::class,
                'attribute_model' => Attribute::class,
                'table' => 'catalog_product_entity',
                'additional_attribute_table' => 'catalog_eav_attribute',
                'entity_attribute_collection' => Product\Attribute\Collection::class,
                'attributes' => [
                    'material_unit' => [
                        'type' => 'varchar',
                        'label' => 'Material unit',
                        'input' => 'select',
                        'source' => MaterialUnit::class,
                        'required' => true,
                        'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                        'visible' => true,
                        'user_defined' => false,
                        'searchable' => false,
                        'filterable' => false,
                        'comparable' => false,
                        'visible_on_front' => false,
                        'unique' => false,
                        'apply_to' => 'material',
                        'group' => 'General',
                        'is_used_in_grid' => true,
                        'is_visible_in_grid' => true,
                        'is_filterable_in_grid' => true,
                    ],
                    'material_specification' => [
                        'type' => 'varchar',
                        'label' => 'Material specification',
                        'input' => 'text',
                        'sort_order' => 3,
                        'global' => ScopedAttributeInterface::SCOPE_STORE,
                        'searchable' => false,
                        'comparable' => false,
                        'required' => true,
                        'group' => 'General',
                        'unique' => false,
                        'visible' => true,
                        'apply_to' => 'material',
                        'user_defined' => false,
                        'is_used_in_grid' => true,
                        'is_visible_in_grid' => true,
                        'is_filterable_in_grid' => true,
                    ],
                    'material_specification_unit' => [
                        'type' => 'varchar',
                        'label' => 'Material specification unit',
                        'input' => 'select',
                        'source' => MaterialUnit::class,
                        'required' => true,
                        'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                        'visible' => true,
                        'user_defined' => false,
                        'searchable' => false,
                        'filterable' => false,
                        'comparable' => false,
                        'visible_on_front' => false,
                        'unique' => false,
                        'apply_to' => 'material',
                        'group' => 'General',
                        'is_used_in_grid' => true,
                        'is_visible_in_grid' => true,
                        'is_filterable_in_grid' => true,
                    ],
                ],
            ]
        ];
    }
}
