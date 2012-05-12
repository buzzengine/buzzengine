<?php

namespace Pkr\BuzzBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class RawFeedType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('category')
                ->add('url');
    }

    public function getName()
    {
        return 'pkr_buzzbundle_rawfeedtype';
    }
}
