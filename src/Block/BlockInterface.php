<?php

namespace Adeliom\EasyBlockBundle\Block;

interface BlockInterface
{
    public function getName(): string;

    public function getDescription(): string;

    /**
     * @return string|mixed[]
     */
    public function getIcon(): string|array;

    public function getTemplate(): string;

    /**
     * @return array<string, string[]>
     */
    public static function configureAssets(): array;

    /**
     * @return array<string, mixed>
     */
    public static function getDefaultSettings(): array;

    /**
     * @return array<string, string[]>
     */
    public static function configureAdminAssets(): array;

    /**
     * @return string[]
     */
    public static function configureAdminFormTheme(): array;
}
