<?php

namespace Pkr\BuzzBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Pkr\BuzzBundle\Entity\AbstractFeed;

class FeedType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('category')
                ->add('url')
                ->add('fetchFrequency', 'choice', array (
                    'label'   => 'Fetch frequency',
                    'choices' => array (
                        AbstractFeed::FETCH_HOURLY  => AbstractFeed::FETCH_HOURLY,
                        AbstractFeed::FETCH_DAILY   => AbstractFeed::FETCH_DAILY,
                        AbstractFeed::FETCH_WEEKLY  => AbstractFeed::FETCH_WEEKLY,
                        AbstractFeed::FETCH_MONTHLY => AbstractFeed::FETCH_MONTHLY
                    ),
                ))
                ->add('disabled', null, array ('required' => false));
    }

    public function getName()
    {
        return 'pkr_buzzbundle_feedtype';
    }
}
