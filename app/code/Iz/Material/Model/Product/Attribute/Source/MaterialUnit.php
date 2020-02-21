<?php


namespace Iz\Material\Model\Product\Attribute\Source;


use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Framework\Data\OptionSourceInterface;

class MaterialUnit extends AbstractSource implements OptionSourceInterface
{

    /**
     * Retrieve All options
     *
     * @return array
     */
    public function getAllOptions()
    {
        return [
            ["label" => "gram", "value" => "gr"],
            ["label" => "kg", "value" => "kg"],
            ["label" => __("box"), "value" => "bx"],
            ["label" => __("case"), "value" => "cs"],
            ["label" => __("bottle"), "value" => "cs"],
        ];
    }
}
