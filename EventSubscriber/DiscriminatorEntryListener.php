<?php

declare(strict_types=1);

namespace HoPeter1018\DoctrineDiscriminatorMapBundle\EventSubscriber;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\ClassMetadata;
use HoPeter1018\DoctrineDiscriminatorMapBundle\Annotation\DiscriminatorEntry;
use HoPeter1018\DoctrineDiscriminatorMapBundle\Annotation\DiscriminatorParent;
use ReflectionClass;

class DiscriminatorEntryListener implements EventSubscriber
{
    const ANNOTATION_ENTRY = DiscriminatorEntry::class;
    const ANNOTATION_PARENT = DiscriminatorParent::class;

    private $discriminatorMaps = [];
    private $annotations = [];

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [
            Events::loadClassMetadata,
        ];
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $event)
    {
        $class = $event->getClassMetadata()->name;
        $driver = $event->getEntityManager()->getConfiguration()->getMetadataDriverImpl();

        // Is it DiscriminatorMap parent class?
        // DiscriminatorSubscriber::loadClassMetadata processes only parent classes
        //
        if (!$this->isDiscriminatorParent($class)) {
            return;
        }
        //
        // Register our discriminator class
        //
        $this->discriminatorMaps[$class] = [];

        //
        // And find all subclasses for this parent class
        //
        foreach ($driver->getAllClassNames() as $name) {
            if ($this->isDiscriminatorChild($class, $name)) {
                $this->discriminatorMaps[$class][] = $name;
            }
        }
        //
        // Collect $discriminatorMap for ClassMetadata
        //
        $discriminatorMap = [];
        foreach ($this->discriminatorMaps[$class] as $childClass) {
            $annotation = $this->getAnnotation(new ReflectionClass($childClass), self::ANNOTATION_ENTRY);
            $discriminatorMap[$this->transformDiscriminatorMapName($childClass)] = $childClass;
        }

        $parentAnnotation = $this->getAnnotation(new ReflectionClass($class), self::ANNOTATION_ENTRY);

        $discriminatorValue = $class;
        $discriminatorMap[$this->transformDiscriminatorMapName($class)] = $class;

        $event->getClassMetadata()->discriminatorValue = $discriminatorValue;
        $event->getClassMetadata()->discriminatorMap = $discriminatorMap;

        if (ClassMetadata::INHERITANCE_TYPE_NONE === $event->getClassMetadata()->inheritanceType) {
            $event->getClassMetadata()->inheritanceType = ClassMetadata::INHERITANCE_TYPE_SINGLE_TABLE;
        }

        if (!isset($event->getClassMetadata()->table['indexes'])) {
            $event->getClassMetadata()->table['indexes'] = [];
        }
        if (!isset($event->getClassMetadata()->table['indexes']['discriminator_fqcn_index'])) {
            $event->getClassMetadata()->table['indexes']['discriminator_fqcn_index'] = [
              'columns' => ['discriminator_fqcn'],
            ];
        }
        if (null === $event->getClassMetadata()->discriminatorColumn) {
            $event->getClassMetadata()->discriminatorColumn = [
                'name' => 'discriminator_fqcn',
                'type' => 'string',
                'length' => 255,
                'columnDefinition' => null,
                'fieldName' => 'discriminator_fqcn',
            ];
        }
    }

    protected function transformDiscriminatorMapName($class)
    {
        // return implode('-', (explode('\\', strtolower(str_replace(['App\\Entity\\', 'EasternColor\\CoreBundle\\', 'Entity\\', 'Classification\\', 'Options\\'], ['', 'eccore\\', 'orm\\', 'class\\', 'opt\\'], $class)))));
        return strtolower(str_replace('\\', '.', $class));
    }

    /**
     * @param $annotationName
     *
     * @return mixed
     */
    private function getAnnotation(ReflectionClass $class, $annotationName)
    {
        if (isset($this->annotations[$class->getName()][$annotationName])) {
            return $this->annotations[$class->getName()][$annotationName];
        }
        $reader = new AnnotationReader();
        if ($annotation = $reader->getClassAnnotation($class, $annotationName)) {
            $this->annotations[$class->getName()][$annotationName] = $annotation;
        }

        return $annotation;
    }

    /**
     * @param string $class
     *
     * @return bool
     */
    private function isDiscriminatorParent($class)
    {
        $reflectionClass = new ReflectionClass($class);
        if (!$this->getAnnotation($reflectionClass, self::ANNOTATION_PARENT)) {
            return false;
        }

        return true;
    }

    /**
     * @param string $parent
     * @param string $class
     *
     * @return bool
     */
    private function isDiscriminatorChild($parent, $class)
    {
        $reflectionClass = new ReflectionClass($class);
        $parentClass = $reflectionClass->getParentClass();

        if (false === $parentClass) {
            return false;
        } elseif ($parentClass->getName() !== $parent) {
            return $this->isDiscriminatorChild($parentClass->getName(), $class);
        }
        if ($this->getAnnotation($reflectionClass, self::ANNOTATION_ENTRY)) {
            return true;
        }

        return false;
    }
}
