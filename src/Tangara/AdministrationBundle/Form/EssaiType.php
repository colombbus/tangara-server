<?php

namespace Tangara\AdministrationBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EssaiType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('listeFichiers')
            ->add('listeSimple')
            ->add('listeJson')
            ->add('connected')
            ->add('nbMember')
            ->add('title')
            ->add('description')
            ->add('dateConnexion')
            ->add('file')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Tangara\AdministrationBundle\Entity\Essai'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'tangara_administrationbundle_essai';
    }
}
