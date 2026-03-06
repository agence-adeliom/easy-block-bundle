<?php

declare(strict_types=1);

namespace Adeliom\EasyBlockBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

final class TraceableBlockSettingsEvent extends Event
{
    /**
     * @param array<string, mixed> $defaultSettings
     * @param array<string, mixed> $settings
     * @param array<string, mixed> $extra
     * @param array<string, mixed> $assets
     */
    public function __construct(
        private array $defaultSettings,
        private array $settings,
        private array $extra,
        private string $type,
        private array $assets,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function getDefaultSettings(): array
    {
        return $this->defaultSettings;
    }

    /**
     * @param array<string, mixed> $defaultSettings
     */
    public function setDefaultSettings(array $defaultSettings): void
    {
        $this->defaultSettings = $defaultSettings;
    }

    /**
     * @return array<string, mixed>
     */
    public function getSettings(): array
    {
        return $this->settings;
    }

    /**
     * @param array<string, mixed> $settings
     */
    public function setSettings(array $settings): void
    {
        $this->settings = $settings;
    }

    /**
     * @return array<string, mixed>
     */
    public function getExtra(): array
    {
        return $this->extra;
    }

    /**
     * @param array<string, mixed> $extra
     */
    public function setExtra(array $extra): void
    {
        $this->extra = $extra;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return array<string, mixed>
     */
    public function getAssets(): array
    {
        return $this->assets;
    }

    /**
     * @param array<string, mixed> $assets
     */
    public function setAssets(array $assets): void
    {
        $this->assets = $assets;
    }
}
