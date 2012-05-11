<?php

namespace Pkr\BuzzBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class TopicType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('name')
                ->add('description', 'textarea', array ('required' => false));
    }

    public function getName()
    {
        return 'pkr_buzzbundle_topictype';
    }
}
