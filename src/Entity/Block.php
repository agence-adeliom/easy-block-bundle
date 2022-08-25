<?php

namespace Adeliom\EasyBlockBundle\Entity;

use Adeliom\EasyCommonBundle\Traits\EntityIdTrait;
use Adeliom\EasyCommonBundle\Traits\EntityNameTrait;
use Adeliom\EasyCommonBundle\Traits\EntityStatusTrait;
use Adeliom\EasyCommonBundle\Traits\EntityTimestampableTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\HasLifecycleCallbacks]
#[ORM\MappedSuperclass(repositoryClass: \Adeliom\EasyBlockBundle\Repository\BlockRepository::class)]
class Block
{
    use EntityIdTrait;
    use EntityTimestampableTrait {
        EntityTimestampableTrait::__construct as private TimestampableConstruct;
    }
    use EntityNameTrait;
    use EntityStatusTrait;

    /**
     * @var string
     */
    #[ORM\Column(name: 'block_key', type: \Doctrine\DBAL\Types\Types::STRING, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    protected ?string $key = null;

    /**
     * @var string
     */
    #[ORM\Column(name: 'type', type: \Doctrine\DBAL\Types\Types::STRING)]
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    protected ?string $type = null;

    /**
     * @var array|null
     */
    #[ORM\Column(name: 'settings', type: \Doctrine\DBAL\Types\Types::JSON)]
    #[Assert\Type('array')]
    protected $settings = [];

    public function __construct()
    {
        $this->TimestampableConstruct();
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function setKey(string $key)
    {
        $this->key = $key;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getSettings(): ?array
    {
        return $this->settings;
    }

    public function setSettings(array $settings): void
    {
        $this->settings = $settings;
    }
}
