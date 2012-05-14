<?php

namespace Pkr\BuzzBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class QueryType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('topic')
                ->add('value')
                ->add('disabled', null, array ('required' => false));
    }

    public function getName()
    {
        return 'pkr_buzzbundle_querytype';
    }
}
