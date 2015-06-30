<?php

namespace Tangara\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class StepType extends AbstractType
{
    
    
    public function __construct(){
    }
    
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array('label'=>'step.name', 'required'=>true))
            ->add('title', 'text', array('label'=>'step.title', 'required'=>true))
            ->add('description', 'textarea', array('label'=>'step.description', 'required'=>false))
            ->add('free', 'checkbox', array('label'=>'step.free', 'required'=>false))
            ->add('cancel', 'button', array('label'=>'step.cancel', 'attr'=>array('btn_type'=>'btn-default', 'btn_icon'=>'glyphicon-remove', 'several-buttons', 'first-button')))
            ->add('save', 'submit', array('label'=>'step.save', 'attr'=>array('several-buttons', 'last-button')));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Tangara\CoreBundle\Entity\Step'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'step';
    }
}
