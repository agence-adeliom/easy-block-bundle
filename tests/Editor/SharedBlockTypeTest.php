<?php

declare(strict_types=1);

namespace Adeliom\EasyBlockBundle\Tests\Editor;

use Adeliom\EasyBlockBundle\Editor\SharedBlockType;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Validator\Constraints\NotBlank;

#[CoversClass(\Adeliom\EasyBlockBundle\Editor\SharedBlockType::class)]
final class SharedBlockTypeTest extends TestCase
{
    public function testBuildFormAddsParentFieldsAndTransformer(): void
    {
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder->expects(self::exactly(3))
            ->method('add')
            ->willReturnCallback(function (string $name, string $type, array $options = []) use ($builder): FormBuilderInterface {
                static $calls = 0;
                ++$calls;

                if (1 === $calls) {
                    TestCase::assertSame('block_type', $name);
                    TestCase::assertSame(HiddenType::class, $type);
                    TestCase::assertSame(SharedBlockType::class, $options['data']);
                }

                if (2 === $calls) {
                    TestCase::assertSame('position', $name);
                    TestCase::assertSame(HiddenType::class, $type);
                }

                if (3 === $calls) {
                    TestCase::assertSame('block', $name);
                    TestCase::assertSame(EntityType::class, $type);
                    TestCase::assertSame(\stdClass::class, $options['class']);
                    TestCase::assertTrue($options['required']);
                    TestCase::assertSame('ea-autocomplete', $options['attr']['data-ea-widget']);
                    TestCase::assertCount(1, $options['constraints']);
                    TestCase::assertInstanceOf(NotBlank::class, $options['constraints'][0]);
                }

                return $builder;
            });
        $builder->expects(self::once())
            ->method('addModelTransformer')
            ->with(self::isInstanceOf(CallbackTransformer::class))
            ->willReturnSelf();

        $block = new SharedBlockType(
            $this->createMock(EntityManagerInterface::class),
            new Translator('en'),
            \stdClass::class
        );

        $block->buildForm($builder, []);
    }

    public function testTransformerConvertsStoredArrayToEntityAndBack(): void
    {
        $entity = new class () {
            public function getId(): int
            {
                return 42;
            }
        };

        $repository = $this->getMockBuilder(EntityRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['find'])
            ->getMock();
        $repository->expects(self::once())
            ->method('find')
            ->with(42)
            ->willReturn($entity);

        $manager = $this->createMock(EntityManagerInterface::class);
        $manager->expects(self::once())
            ->method('getRepository')
            ->with(\stdClass::class)
            ->willReturn($repository);

        $block = new SharedBlockType($manager, new Translator('en'), \stdClass::class);
        $transformer = $block->getTransformer();

        self::assertSame(['block' => $entity], $transformer->transform(['block' => ['class' => \stdClass::class, 'id' => 42]]));
        self::assertSame(['block' => ['class' => \stdClass::class, 'id' => 42]], $transformer->reverseTransform(['block' => $entity]));
    }

    public function testMetadataUsesTranslatorIconAndTemplate(): void
    {
        $translator = new Translator('en');
        $translator->addLoader('array', new class () extends \Symfony\Component\Translation\Loader\ArrayLoader {
        });
        $translator->addResource('array', ['easy.block.editor.shared_block' => 'Shared block'], 'en');

        $block = new SharedBlockType(
            $this->createMock(EntityManagerInterface::class),
            $translator,
            \stdClass::class
        );

        self::assertSame('Shared block', $block->getName());
        self::assertSame('<span class="fas fa-shapes"></span>', $block->getIcon());
        self::assertSame('@EasyBlock/editor/shared_block.html.twig', $block->getTemplate());
    }
}
