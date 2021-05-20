<?php

namespace Desafio\Utils;


use ReflectionObject;
use ReflectionClass;

Class EntityHydrator {

    public static function hydrate($entityClass, array $data) {
        $object = new $entityClass;
        $refObj = new ReflectionObject($object);
        $reflectionClass = new ReflectionClass($entityClass);

        foreach ($refObj->getProperties() as $key => $property) {
            if (isset($data[$property->getName()])) {
                $reflectionProperty = $reflectionClass->getProperty($property->getName());
                $reflectionProperty->setAccessible(true);
                $reflectionProperty->setValue($object, $data[$property->getName()]);
            }
        }

        return $object;
    }

}
