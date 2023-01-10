<?php

namespace AntQa\AjaxAutoCompleteBundle\Form\DataTransformer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Class MultipleObjectsToIdsTransformer
 *
 * @author Piotr Antosik <mail@piotrantosik.com>
 */
class MultipleObjectsToIdsTransformer implements DataTransformerInterface
{
    public function __construct(private ObjectManager $om, private string $class)
    {
    }

    /**
     * Transforms an objects to a string (ids).
     *
     * @param  array|null $array
     *
     * @return string
     */
    public function transform($array)
    {
        if (false === is_array($array) && !$array instanceof \Traversable) {
            return '';
        }

        $ids = [];

        /** @var Object $el */
        foreach ($array as $el) {
            $ids[] = $el->getId();
        }

        return implode(',', $ids);
    }

    /**
     * Transforms a string (ids) to an object (object).
     *
     * @param  string $ids
     *
     * @return ArrayCollection|null
     * @throws TransformationFailedException if num objects is not equal ids.
     */
    public function reverseTransform($ids)
    {
        if (false === is_array($ids)) {
            $ids = explode(',', $ids);
        }

        $ids = array_filter($ids, 'strlen');

        if (empty($ids)) {
            return null;
        }

        $objects = $this->om->getRepository($this->class)->findBy(['id' => $ids]);
        $objects = new ArrayCollection($objects);

        if (count($ids) !== count($objects)) {
            throw new TransformationFailedException(sprintf('Not found all objects from class "%s": found %d, ids: %d', $this->class, count($objects), count($ids)));
        }

        return $objects;
    }
}
