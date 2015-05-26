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

class SearchType extends AbstractType {
    
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('search','text', array(   'label'=> false, 
                                                'attr'=> array('class'=> 'form-control')));
    }
    
    public function getName() {
        return 'search_form';
    }
}