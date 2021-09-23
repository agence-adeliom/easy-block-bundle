<?php

namespace Adeliom\EasyBlockBundle\Admin\Field;


use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;

final class BlockSettingsField implements FieldInterface
{
    use FieldTrait;

    /**
     * @param string|false|null $label
     */
    public static function new(string $propertyName, $label = false): self
    {
        return (new self())
            ->setProperty($propertyName)
            ->setLabel($label)
            ->hideOnIndex()
            ->setDefaultColumns('') // this is set dynamically in the field configurator
        ;
    }
}
