<?php

namespace Pkr\BuzzBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Pkr\BuzzBundle\Entity\RawFeed;

class RawFeedType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('category')
                ->add('url')
                ->add('fetchFrequency', 'choice', array (
                    'label'   => 'Fetch frequency',
                    'choices' => array (
                        RawFeed::FETCH_HOURLY  => RawFeed::FETCH_HOURLY,
                        RawFeed::FETCH_DAILY   => RawFeed::FETCH_DAILY,
                        RawFeed::FETCH_WEEKLY  => RawFeed::FETCH_WEEKLY,
                        RawFeed::FETCH_MONTHLY => RawFeed::FETCH_MONTHLY
                    ),
                ));
    }

    public function getName()
    {
        return 'pkr_buzzbundle_rawfeedtype';
    }
}
