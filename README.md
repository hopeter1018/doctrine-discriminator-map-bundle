# doctrine-discriminator-map-bundle

## Introduction

This bundle aims to simplify doctrine discriminator map config.

By adding **ONLY** one annotation to parent and **EACH** children, the bundle:

-   handle the ` * @ORM\\*` annotation
-   add a Database **index**

## Installation

### Require the package

`composer require hopeter1018/doctrine-discriminator-map-bundle`

### Add to kernel

#### Symfony 4+ or Symfony Flex

Add `/config/bundles.php`

```php
return [
  ...,
  HoPeter1018\DoctrineDiscriminatorMapBundle\HoPeter1018DoctrineDiscriminatorMapBundle::class => ['all' => true],
];
```

#### Symfony 2+

Add `/app/AppKernel.php`

```php
$bundles = [
  ...,
  new HoPeter1018\DoctrineDiscriminatorMapBundle\HoPeter1018DoctrineDiscriminatorMapBundle(),
];
```

### Config

#### thru Doctrine Annotation (Parent + ALL children)

##### Parent

```php
namespace Your\Bundle\Entity;

use HoPeter1018\DoctrineDiscriminatorMapBundle\Annotation\DiscriminatorParent;

/**
 * Class docblock
 *
 * @DiscriminatorParent
 */
class ParentEntityClass {
}
```

##### All Children

```php
namespace Your\Bundle\Entity;

use HoPeter1018\DoctrineDiscriminatorMapBundle\Annotation\DiscriminatorParent;

/**
 * Class docblock
 *
 * @DiscriminatorEntry
 */
class EntryEntityClass {

}
```
