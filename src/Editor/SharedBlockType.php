<?php
namespace Adeliom\EasyBlockBundle\Editor;

use Adeliom\EasyEditorBundle\Block\AbstractBlock;
use Adeliom\EasyEditorBundle\Block\BlockInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class SharedBlockType extends AbstractBlock implements BlockInterface
{
    protected $class;
    protected $translator;

    public function __construct(EntityManagerInterface $manager, TranslatorInterface $translator, string $class)
    {
        parent::__construct($manager);
        $this->class = $class;
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder
            ->add("block", EntityType::class, [
                "class" => $this->class,
                "required" => true,
                "attr" => [
                    'data-ea-widget' => 'ea-autocomplete'
                ],
                "constraints" => [
                    new NotBlank()
                ]
            ])
        ;

        $builder->addModelTransformer($this->getTransformer());
    }

    public function getTransformer(){
        return new CallbackTransformer(
            function ($data) {
                if ($data && $data["block"]){
                    $data["block"] = $this->manager->getRepository($data["block"]["class"])->find($data["block"]["id"]);
                }
                return $data;
            },
            function ($data) {
                if ($data["block"]){
                    $data["block"] = ["class" => $this->class, "id" => $data["block"]->getId()];
                }
                return $data;
            }
        );
    }


    public function getName(): string
    {
        return $this->translator->trans("easy.block.editor.shared_block");
    }

    public function getIcon(): string
    {
        return '<span class="fas fa-shapes"></span>';
    }

    public function getTemplate(): string
    {
        return "@EasyBlock/editor/shared_block.html.twig";
    }
}
