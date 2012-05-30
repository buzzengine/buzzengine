<?php

namespace Pkr\BuzzBundle\Filter\Language;

use Pkr\BuzzBundle\Filter\FilterInterface;
use Zend\Feed\Reader\Entry;
use Zend\Http\Client;

class DetectlanguageCom implements FilterInterface
{
    protected $_apiKey = null;
    protected $_allowedLanguages = null;

    public function __construct($apiKey, array $allowedLanguages)
    {
        $this->_apiKey = $apiKey;
        $this->_allowedLanguages = $allowedLanguages;
    }

    public function setAllowedLanguages(array $allowedLanguages)
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
