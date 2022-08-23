<?php

namespace Adeliom\EasyBlockBundle\Entity;

use Adeliom\EasyCommonBundle\Traits\EntityIdTrait;
use Adeliom\EasyCommonBundle\Traits\EntityNameTrait;
use Adeliom\EasyCommonBundle\Traits\EntityStatusTrait;
use Adeliom\EasyCommonBundle\Traits\EntityTimestampableTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\HasLifecycleCallbacks]
#[ORM\MappedSuperclass(repositoryClass: 'Adeliom\EasyBlockBundle\Repository\BlockRepository')]
class Block
{
    use EntityIdTrait;
    use EntityTimestampableTrait {
        EntityTimestampableTrait::__construct as private __TimestampableConstruct;
    }
    use EntityNameTrait;
    use EntityStatusTrait;
    /**
     * @var string
     */
    #[ORM\Column(name: 'block_key', type: 'string', nullable: false, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    protected $key;
    /**
     * @var string
     */
    #[ORM\Column(name: 'type', type: 'string', nullable: false)]
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    protected $type;
    /**
     * @var array|null
     */
    #[ORM\Column(name: 'settings', type: 'json')]
    #[Assert\Type('array')]
    protected $settings;
    public function __construct()
    {
        $this->__TimestampableConstruct();
        $this->settings = [];
    }
    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }
    /**
     * @param string $key
     */
    public function setKey(string $key)
    {
        $this->key = $key;
    }
    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }
    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }
    /**
     * @return array|null
     */
    public function getSettings(): ?array
    {
        return $this->settings;
    }
    /**
     * @param array $settings
     */
    public function setSettings(array $settings): void
    {
        $this->settings = $settings;
    }
}
