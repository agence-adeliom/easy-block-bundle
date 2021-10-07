
![Adeliom](https://adeliom.com/public/uploads/2017/09/Adeliom_logo.png)
[![Quality gate](https://sonarcloud.io/api/project_badges/quality_gate?project=agence-adeliom_easy-block-bundle)](https://sonarcloud.io/dashboard?id=agence-adeliom_easy-block-bundle)

# Easy Block Bundle

Provide a basic block component for Easyadmin.


## Features

- A Easyadmin CRUD interface to manage block
- Twig function to render block


## Installation

Install with composer

```bash
composer require agence-adeliom/easy-block-bundle
```

### Setup database

#### Using doctrine migrations

```bash
php bin/console doctrine:migration:diff
php bin/console doctrine:migration:migrate
```

#### Without

```bash
php bin/console doctrine:schema:update --force
```

## Documentation

### Integration into EasyAdmin

Go to your dashboard controller, example : `src/Controller/Admin/DashboardController.php`

```php
<?php

namespace App\Controller\Admin;

...
use App\Entity\EasyFaq\Entry;
use App\Entity\EasyFaq\Category;

class DashboardController extends AbstractDashboardController
{
    ...
    public function configureMenuItems(): iterable
    {
        ...
        yield MenuItem::linkToCrud('easy.block.admin.menu.shared_blocks', 'fa fa-file-alt', Block::class);

        ...
```

### Usage

#### Entity

```php
class Article
{
    /**
     * @ORM\ManyToOne(targetEntity=Block::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $block;
}
```

#### CRUD Controller
```php
class ArticleCrudController extends AbstractCrudController
{
    public function configureFields(string $pageName): iterable
    {
        yield AssociationField::new('block');
    }
}
```

#### Twig template

```twig
# block is an entity object
{{ easy_block(block) }}

# render with extra data
{{ easy_block(block, extra) }}

# render by key
{{ easy_block(key, extra) }}
```

### Create a new type

```bash
bin/console make:block:shared
```

# Events

#### easy_block.render_block
```php
use Symfony\Contracts\EventDispatcher\Event;

$dispatcher->addListener('easy_block.render_block', function (Event $event) {
    // will be executed when the easy_block.render_block event is dispatched
    
    // Get
    $block = $event->getArgument('block');
    $blockType = $event->getArgument('blockType');
    $settings = $event->getArgument('settings');
    
    // Set
    $event->setArgument("block", $block);
    $event->setArgument("blockType", $blockType);
    $event->setArgument("settings", $settings);
});
```

## License

[MIT](https://choosealicense.com/licenses/mit/)


## Authors

- [@arnaud-ritti](https://github.com/arnaud-ritti)

  
