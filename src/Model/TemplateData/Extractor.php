<?php

namespace Droid\Model\TemplateData;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class Extractor
{
    private $normalizer;

    public function __construct()
    {
        AnnotationRegistry::registerLoader('class_exists');
        $this->normalizer = new ObjectNormalizer(
            new ClassMetadataFactory(
                new AnnotationLoader(new AnnotationReader)
            )
        );
        $this->normalizer->setSerializer(new VariablesNormalizer);
    }

    /**
     * Extract attribute names and values from the supplied object.
     *
     * @param object $object
     * @param array $groups
     *
     * @return array
     */
    public function extract($object, $groups = array('TemplateData'))
    {
        $context = array();

        if (!empty($groups)) {
            $context['groups'] = $groups;
        }

        return $this->normalizer->normalize($object, null, $context);
    }
}
