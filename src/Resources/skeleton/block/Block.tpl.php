<?php echo "<?php\n"; ?>

namespace <?php echo $namespace; ?>;

use Adeliom\EasyBlockBundle\Block\AbstractBlock;
use Symfony\Component\Form\FormBuilderInterface;

class <?php echo $class_name; ?> extends AbstractBlock<?php echo "\n"; ?>
{
    public function buildBlock(FormBuilderInterface $builder, array $options): void
    {
        // Implement with your fields
    }

    public function getName(): string
    {
        return '<?php echo $class_name; ?>';
    }

    public function getIcon(): string
    {
        return '';
    }

    public function getTemplate(): string
    {
        return "<?php echo $template_name; ?>";
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
