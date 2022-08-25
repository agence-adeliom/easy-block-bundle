<?php

namespace Adeliom\EasyBlockBundle\Block;

interface BlockInterface
{
    public function getName(): string;

    public function getDescription(): string;

    public function getIcon(): string;

    public function getTemplate(): string;

    public static function configureAssets(): array;

    public static function getDefaultSettings(): array;

    public static function configureAdminAssets(): array;

    public static function configureAdminFormTheme(): array;
}
