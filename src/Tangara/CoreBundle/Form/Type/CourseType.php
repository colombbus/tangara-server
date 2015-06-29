<?php

namespace Tangara\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CourseType extends AbstractType
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
            ->add('name', 'text', array('label'=>'course.name', 'required'=>true))
            ->add('title', 'text', array('label'=>'course.title', 'required'=>true))
            ->add('description', 'textarea', array('label'=>'course.description', 'required'=>false))
            ->add('cancel', 'button', array('label'=>'course.cancel', 'attr'=>array('btn_type'=>'btn-default', 'btn_icon'=>'glyphicon-remove', 'several-buttons', 'first-button')))
            ->add('save', 'submit', array('label'=>'course.save', 'attr'=>array('several-buttons', 'last-button')));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Tangara\CoreBundle\Entity\Course'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'course';
    }
}
