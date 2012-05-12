<?php

namespace Pkr\BuzzBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class TopicType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('name')
                ->add('description', null, array ('required' => false))
                ->add('categories', 'entity', array (
                    'class'     => 'PkrBuzzBundle:Category',
                    'expanded'  => true,
                    'multiple'  => true
                ));
    }

    public function getName()
    {
        return 'pkr_buzzbundle_topictype';
    }
}
