<?php

namespace Tangara\CoreBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tangara\CoreBundle\Manager\ProjectManager;

class ProjectType extends AbstractType
{
    
    private $projectManager;
    
    public function __construct(ProjectManager $manager)
    {
        $this->projectManager = $manager;
    }
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array('label'=>'project.name', 'required'=>false))
            ->add('published', 'checkbox', array('label'=>'project.published', 'required'=>false))
            ->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'addPrograms'))
            ->add('launcher', 'checkbox', array('label'=>'project.published', 'required'=>false))
            ->add('width', 'integer', array('label'=>'project.width', 'required'=>false))
            ->add('height', 'integer', array('label'=>'project.height', 'required'=>false))
            ->add('description', 'textarea', array('label'=>'project.description', 'required'=>false))
            ->add('instructions', 'textarea', array('label'=>'project.instructions', 'required'=>false))
            //->add('cancel', 'button', array('label'=>'project.cancel', 'attr'=>array('btn_type'=>'btn-danger')))
            ->add('cancel', 'button', array('label'=>'project.cancel', 'attr'=>array('btn_type'=>'btn-default', 'btn_icon'=>'glyphicon-remove', 'several-buttons', 'first-button')))
            ->add('save', 'submit', array('label'=>'project.save', 'attr'=>array('several-buttons', 'last-button')));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Tangara\CoreBundle\Entity\Project'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'project';
    }
    
    public function addPrograms(FormEvent $event) {
        $project = $event->getData();
        $form = $event->getForm();
        
        $form->add('launcher', 'entity', array(
            'label' => 'project.launcher',
            'class' => 'TangaraCoreBundle:File',
            'property'=>'name',
            'query_builder' => function(EntityRepository $er) use($project) {
                return $er->createQueryBuilder('f')
                ->where('f.project = :project')
                ->andWhere('f.program = true')
                ->orderBy('f.name', 'ASC')
                ->setParameter('project', $project);
            })
        );
    }
        
        
}
