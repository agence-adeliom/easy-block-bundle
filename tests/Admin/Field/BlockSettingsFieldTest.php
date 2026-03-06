<?php

declare(strict_types=1);

namespace Adeliom\EasyBlockBundle\Tests\Admin\Field;

use Adeliom\EasyBlockBundle\Admin\Field\BlockSettingsField;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(\Adeliom\EasyBlockBundle\Admin\Field\BlockSettingsField::class)]
final class BlockSettingsFieldTest extends TestCase
{
    public function testFieldConfiguresExpectedEasyAdminDto(): void
    {
        $dto = BlockSettingsField::new('settings', 'Settings')->getAsDto();

        self::assertSame('settings', $dto->getProperty());
        self::assertSame('Settings', $dto->getLabel());
        self::assertFalse($dto->isDisplayedOn('index'));
        self::assertSame('', $dto->getDefaultColumns());
    }
}
