<?php

namespace AntQa\AjaxAutoCompleteBundle\Form\DataTransformer;

use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Class ObjectToIdTransformer
 *
 * @author Piotr Antosik <mail@piotrantosik.com>
 */
class ObjectToIdTransformer implements DataTransformerInterface
{

    public function __construct(private ObjectManager $om, private string $class)
    {
    }

    /**
     * Transforms an object  to a string (id).
     *
     * @param  Object|null $object
     * @return string
     */
    public function transform($object)
    {
        if (null === $object) {
            return '';
        }

        return $object->getId();
    }

    /**
     * Transforms a string (id) to an object (object).
     *
     * @param  string $id
     * @return Object|null
     *
     * @throws TransformationFailedException if object (object) is not found.
     */
    public function reverseTransform($id)
    {
        if (empty($id)) {
            return null;
        }

        $object = $this->om->getRepository($this->class)->find($id);

        if (null === $object) {
            throw new TransformationFailedException(sprintf('Object from class "%s" with id %d not found', $this->class, $id));
        }

        return $object;
    }
}
