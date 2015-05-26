<?php
namespace Tangara\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class SearchType extends AbstractType {
    
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('search','text', array(   'label'=> false, 
                                                'attr'=> array('class'=> 'form-control')));
    }
    
    public function getName() {
        return 'search_form';
    }
}