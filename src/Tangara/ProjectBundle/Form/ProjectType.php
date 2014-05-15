<?php

namespace Tangara\ProjectBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProjectType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('projectManager')
            ->add('projectOwnerGroup')
            ->add('contributors')
            ->add('filesRights')
            ->add('files')
            ->add('referenceWidth')
            ->add('referenceHeight')
            ->add('referenceFont')
            ->add('dateCreation')
            ->add('designers')
            ->add('designersButton')
            ->add('designersLogo')
            ->add('designersCredits')
            ->add('designersCharacters')
            ->add('designersBadGuys')
            ->add('designersGraphicalElements')
            ->add('designersDecor')
            ->add('soundDesigners')
            ->add('soundRecorders')
            ->add('scenario')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Tangara\ProjectBundle\Entity\Project'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'tangara_projectbundle_project';
    }
}
