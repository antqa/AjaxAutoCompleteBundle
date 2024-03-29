<?php

namespace AntQa\AjaxAutoCompleteBundle\Form\Type;

use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Exception\InvalidConfigurationException;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use AntQa\AjaxAutoCompleteBundle\Form\DataTransformer\ObjectToIdTransformer;
use AntQa\AjaxAutoCompleteBundle\Form\DataTransformer\MultipleObjectsToIdsTransformer;

/**
 * Class AjaxAutoCompleteType
 *
 * @author Piotr Antosik <mail@piotrantosik.com>
 */
class AjaxAutoCompleteType extends AbstractType
{
    public function __construct(private ObjectManager $om)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (empty($options['class'])) {
            throw new InvalidConfigurationException('Option "class" must be set.');
        }

        if (false === $options['multiple']) {
            $transformer = new ObjectToIdTransformer($this->om, $options['class']);
            $builder->addModelTransformer($transformer);
        } else {
            $transformer = new MultipleObjectsToIdsTransformer($this->om, $options['class']);
            $builder->addModelTransformer($transformer);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'class'           => '',
            'multiple'        => false,
            'invalid_message' => 'The selected item does not exist',
        ]);
    }

    /**
     * BC for SF < 2.7
     *
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $this->configureOptions($resolver);
    }

    /**
     * BC for SF < 2.8
     *
     * {@inheritdoc}
     */
    public function getParent()
    {
        return method_exists('Symfony\Component\Form\AbstractType', 'getBlockPrefix') ? 'Symfony\Component\Form\Extension\Core\Type\TextType' : 'text';
    }

    /**
     * BC SF < 2.8
     *
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix() {
        return 'ajax_auto_complete';
    }
}
