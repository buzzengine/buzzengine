<?php

namespace Pkr\BuzzBundle\Filter;

use Zend\Feed\Reader\Entry;
use Zend\Http\Client;

class Language implements FilterInterface
{
    protected $_apiKey = '';
    protected $_allowedLanguages = null;

    public function __construct(array $allowedLanguages)
    {
        $this->_allowedLanguages = $allowedLanguages;
    }

    public function isAccepted(Entry $entry)
    {
        $client = new Client();
        $client->setUri('http://ws.detectlanguage.com/0.2/detect');
        $client->setMethod('POST');
        $client->setParameterPost(array (
            'key' => $this->_apiKey,
            'q'   => strip_tags($entry->getTitle() . ' ' . $entry->getContent())
        ));
        $response = $client->send();
        $response = json_decode($response->getBody());

        foreach ($response->data->detections as $detection)
        {
            if (in_array($detection->language, $this->_allowedLanguages))
            {
                return true;
            }
        }

        return false;
    }
}
