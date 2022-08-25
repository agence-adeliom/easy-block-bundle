<?= "<?php\n" ?>

namespace <?= $namespace; ?>;

use Adeliom\EasyBlockBundle\Block\AbstractBlock;
use Symfony\Component\Form\FormBuilderInterface;

class <?= $class_name; ?> extends AbstractBlock<?= "\n" ?>
{
    public function buildBlock(FormBuilderInterface $builder, array $options): void
    {
        // Implement with your fields
    }

    public function getName(): string
    {
        return '<?= $class_name; ?>';
    }

    public function getIcon(): string
    {
        return '';
    }

    public function getTemplate(): string
    {
        return "<?= $template_name ?>";
    }

    public function getDescription(): string
    {
        return '';
    }

    public static function getDefaultSettings(): array
    {
        return [];
    }

    public static function configureAdminAssets(): array
    {
        return [
            'js' => [],
            'css' => []
        ];
    }
}
