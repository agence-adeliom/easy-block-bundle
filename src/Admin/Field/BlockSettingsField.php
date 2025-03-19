<?php

namespace Adeliom\EasyBlockBundle\Admin\Field;

use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;

final class BlockSettingsField implements FieldInterface
{
    use FieldTrait;

    public static function new(string $propertyName, string|false|null $label = false): self
    {
        return (new self())
            ->setProperty($propertyName)
            ->setLabel($label)
            ->hideOnIndex()
            ->setDefaultColumns('') // this is set dynamically in the field configurator
        ;
    }
}
