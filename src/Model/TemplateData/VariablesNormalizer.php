<?php

namespace Droid\Model\TemplateData;

use UnexpectedValueException;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * The purpose of this class is to address a seeming limitation with symfony's
 * ObjectNormalizer: it won't normalise (i.e. convert to an array) array type
 * object attributes by itself, requiring an instance of NormalizerInterface
 * to do the job.  This class is that NormalizerInterface.
 *
 * In this particular case, we want to normalise the `variables` attribute of
 * objects which *we* know are always array type - so the normalize method
 * simply returns its argument (the $object parameter).  The other methods
 * of this class are never called by ObjectNormalizer - they are required only
 * to satisfy interfaces.
 */
class VariablesNormalizer implements NormalizerInterface, SerializerInterface
{
    public function normalize($object, $format = null, array $context = array())
    {
        if (!is_array($object)) {
            throw new UnexpectedValueException(
                sprintf(
                    'Expected an array type $object, but got type "%s"',
                    gettype($object)
                )
            );
        }
        return $object;
    }

    public function supportsNormalization($data, $format = null)
    {
        return is_array($data);
    }

    public function serialize($data, $format, array $context = array())
    {
        return $data;
    }

    public function deserialize($data, $type, $format, array $context = array())
    {
        return $data;
    }
}
