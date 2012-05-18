<?php

namespace Pkr\BuzzBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class TopicFeedType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('topic')
                ->add('url')
                ->add('disabled', null, array ('required' => false));
    }

    public function getName()
    {
        return 'pkr_buzzbundle_topicfeedtype';
    }
}
