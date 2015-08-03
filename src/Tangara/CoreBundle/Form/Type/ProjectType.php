<?php

namespace Tangara\CoreBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\SecurityContext;
use Tangara\CoreBundle\Manager\ProjectManager;

class ProjectType extends AbstractType
{
    
    private $projectManager;
    private $securityContext;
    
    public function __construct(ProjectManager $manager, SecurityContext $securityContext)
    {
        $this->projectManager = $manager;
        $this->securityContext = $securityContext;
    }
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $exercise = isset($options) && isset($options['exercise']) && $options['exercise'];
        if ($exercise) {
            $builder->add('name', 'text', array('label'=>'exercise.name', 'required'=>false));        
        } else {
            $builder->add('name', 'text', array('label'=>'project.name', 'required'=>false))
            ->add('published', 'checkbox', array('label'=>'project.published', 'required'=>false))
            ->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'addFormData'))
            ->add('width', 'integer', array('label'=>'project.width', 'required'=>false))
            ->add('height', 'integer', array('label'=>'project.height', 'required'=>false));
        }
        $builder->add('description', 'textarea', array('label'=>'project.description', 'required'=>false));
        if (!$exercise) {
            $builder->add('instructions', 'textarea', array('label'=>'project.instructions', 'required'=>false));
        }
            //->add('cancel', 'button', array('label'=>'project.cancel', 'attr'=>array('btn_type'=>'btn-danger')))
        $builder->add('cancel', 'button', array('label'=>'project.cancel', 'attr'=>array('btn_type'=>'btn-default', 'btn_icon'=>'glyphicon-remove', 'several-buttons', 'first-button')))
        ->add('save', 'submit', array('label'=>'project.save', 'attr'=>array('several-buttons', 'last-button')));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Tangara\CoreBundle\Entity\Project',
            'exercise' => false
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'project';
    }
    
    public function addFormData(FormEvent $event) {
        $this->addReadonly($event);
        $this->addPrograms($event);
    }
    
    public function addPrograms(FormEvent $event) {
        $project = $event->getData();
        $id = $project->getId();
        if (isset($id)) {
            // Project exists
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
        
    public function addReadonly(FormEvent $event) {
        if ($this->securityContext->isGranted("ROLE_ADMIN")) {
            $project = $event->getData();
            $form = $event->getForm();
            $form->add('readonly', 'checkbox', array('label'=>'project.readonly', 'required'=>false));
        }
    }
        
}
