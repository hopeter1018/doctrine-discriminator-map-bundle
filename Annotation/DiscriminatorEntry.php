<?php

declare(strict_types=1);

namespace HoPeter1018\DoctrineDiscriminatorMapBundle\Annotation;

use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Target;

/**
 * @Annotation
 * @Target({"CLASS"})
 */
final class DiscriminatorEntry
{
    // private $value;
    //
    // public function __construct(array $data)
    // {
    //     $this->value = $data['value'];
    // }
    //
    // public function getValue()
    // {
    //     return $this->value;
    // }
}
