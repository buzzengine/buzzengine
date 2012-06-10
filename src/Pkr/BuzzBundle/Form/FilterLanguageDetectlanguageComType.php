<?php

namespace Pkr\BuzzBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class FilterLanguageDetectlanguageComType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('topic')
                ->add('allowedLanguages', 'choice', array (
                        'expanded'  => true,
                        'multiple'  => true,
                        'choices'   => array (
                            'af' => 'af (afrikaans)',
                            'ar' => 'ar (arabic)',
                            'be' => 'be (belarusian)',
                            'bg' => 'bg (bulgarian)',
                            'ca' => 'ca (catalan)',
                            'chr' => 'chr (cherokee)',
                            'cs' => 'cs (czech)',
                            'cy' => 'cy (welsh)',
                            'da' => 'da (danish)',
                            'de' => 'de (german)',
                            'dv' => 'dv (dhivehi)',
                            'el' => 'el (greek)',
                            'en' => 'en (english)',
                            'es' => 'es (spanish)',
                            'et' => 'et (estonian)',
                            'fa' => 'fa (persian)',
                            'fi' => 'fi (finnish)',
                            'fil' => 'fil (tagalog)',
                            'fr' => 'fr (french)',
                            'ga' => 'ga (irish)',
                            'gu' => 'gu (gujarati)',
                            'he' => 'he (hebrew)',
                            'hi' => 'hi (hindi)',
                            'hr' => 'hr (croatian)',
                            'hu' => 'hu (hungarian)',
                            'hy' => 'hy (armenian)',
                            'is' => 'is (icelandic)',
                            'it' => 'it (italian)',
                            'iu' => 'iu (inuktitut)',
                            'ja' => 'ja (japanese)',
                            'ka' => 'ka (georgian)',
                            'km' => 'km (khmer)',
                            'kn' => 'kn (kannada)',
                            'ko' => 'ko (korean)',
                            'lo' => 'lo (laothian)',
                            'lt' => 'lt (lithuanian)',
                            'lv' => 'lv (latvian)',
                            'mk' => 'mk (macedonian)',
                            'ml' => 'ml (malayalam)',
                            'ms' => 'ms (malay)',
                            'nb' => 'nb (norwegian)',
                            'nl' => 'nl (dutch)',
                            'or' => 'or (oriya)',
                            'pa' => 'pa (punjabi)',
                            'pl' => 'pl (polish)',
                            'pt' => 'pt (portuguese)',
                            'ro' => 'ro (romanian)',
                            'ru' => 'ru (russian)',
                            'si' => 'si (sinhalese)',
                            'sk' => 'sk (slovak)',
                            'sl' => 'sl (slovenian)',
                            'sr' => 'sr (serbian)',
                            'sv' => 'sv (swedish)',
                            'sw' => 'sw (swahili)',
                            'syr' => 'syr (syriac)',
                            'ta' => 'ta (tamil)',
                            'te' => 'te (telugu)',
                            'th' => 'th (thai)',
                            'tr' => 'tr (turkish)',
                            'uk' => 'uk (ukrainian)',
                            'vi' => 'vi (vietnamese)',
                            'yi' => 'yi (yiddish)',
                            'zh' => 'zh (chinese)',
                            'zh-tw' => 'zh-tw (chineset)'
                        )
                ))
                ->add('disabled', null, array ('required' => false));
    }

    public function getName()
    {
        return 'pkr_buzzbundle_filterlanguagedetectlanguagecomtype';
    }
}
