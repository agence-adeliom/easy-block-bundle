<?php

declare(strict_types=1);

namespace Adeliom\EasyBlockBundle\Tests\Twig;

use Adeliom\EasyBlockBundle\Block\Helper;
use Adeliom\EasyBlockBundle\Twig\EasyBlockExtension;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Twig\TwigFunction;

#[CoversClass(\Adeliom\EasyBlockBundle\Twig\EasyBlockExtension::class)]
final class EasyBlockExtensionTest extends TestCase
{
    public function testExtensionRegistersExpectedTwigFunctions(): void
    {
        $extension = new EasyBlockExtension($this->createMock(Helper::class));
        $functions = $extension->getFunctions();

        self::assertContainsOnlyInstancesOf(TwigFunction::class, $functions);
        self::assertSame(
            ['easy_block', 'easy_block_assets'],
            array_map(static fn (TwigFunction $function): string => $function->getName(), $functions)
        );
    }
}
