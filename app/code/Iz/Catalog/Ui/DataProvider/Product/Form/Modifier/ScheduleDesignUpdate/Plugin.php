<?php


namespace Iz\Catalog\Ui\DataProvider\Product\Form\Modifier\ScheduleDesignUpdate;


class Plugin
{
    public function aroundModifyMeta($subject, callable $proceed, array $meta)
    {
        return $meta;
    }
}
