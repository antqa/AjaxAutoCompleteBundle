<?php

namespace AntQa\Bundle\AjaxAutoCompleteBundle\Form\DataTransformer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Class MultipleObjectsToIdsTransformer
 *
 * @author Piotr Antosik <mail@piotrantosik.com>
 */
class MultipleObjectsToIdsTransformer implements DataTransformerInterface
{
    private $om, $class;

    /**
     * @param ObjectManager $om
     * @param string        $class
     */
    public function __construct(ObjectManager $om, $class)
    {
        $this->om = $om;
        $this->class = $class;
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
        if (false === is_array($array)) {
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
        $ids = explode(',', $ids);
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
