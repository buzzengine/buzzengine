<?php

namespace Pkr\BuzzBundle\Service;

use Doctrine\ORM\EntityManager;
use Pkr\BuzzBundle\Entity\Log;
use Symfony\Component\Validator\Validator;

class Rating
{
    protected $_apiAccessData = null;
    protected $_entityManager = null;
    protected $_validator = null;

    public function __construct(EntityManager $em, Validator $validator, array $apiAccessData)
    {
        $this->_entityManager = $em;
        $this->_validator = $validator;
        $this->_apiAccessData = $apiAccessData;
    }

    protected function _log($message, $level = Log::NOTICE)
    {
        $log = new Log();
        $log->setLevel($level);
        $log->setMessage($message);

        $errors = $this->_validator->validate($log);
        if (count($errors) > 0)
        {
            throw new \Exception('validate entity log failed');
        }

        $this->_entityManager->persist($log);
        $this->_entityManager->flush($log);
    }

    public function fetch()
    {
        $domains = $this->_entityManager
                        ->getRepository('PkrBuzzBundle:Domain')
                        ->findAll();

        foreach ($domains as $domain)
        {
            /**
             * @TODO: build small compete.com library
             */
            $site = preg_replace('~https?:\/\/~', '', $domain->getUrl());

            $url  = 'http://apps.compete.com/sites/' . $site;
            $url .= '/trended/rank/?apikey=' . $this->_apiAccessData['competeCom']['apiKey'];

            $client = new \Zend\Http\Client($url);
            $result = $client->send();

            $data = json_decode($result->getBody());

            //$data = unserialize('O:8:"stdClass":2:{s:6:"status";s:2:"OK";s:4:"data";O:8:"stdClass":4:{s:6:"trends";O:8:"stdClass":1:{s:4:"rank";a:13:{i:0;O:8:"stdClass":2:{s:4:"date";s:6:"201105";s:5:"value";i:30;}i:1;O:8:"stdClass":2:{s:4:"date";s:6:"201106";s:5:"value";i:28;}i:2;O:8:"stdClass":2:{s:4:"date";s:6:"201107";s:5:"value";i:25;}i:3;O:8:"stdClass":2:{s:4:"date";s:6:"201108";s:5:"value";i:22;}i:4;O:8:"stdClass":2:{s:4:"date";s:6:"201109";s:5:"value";i:23;}i:5;O:8:"stdClass":2:{s:4:"date";s:6:"201110";s:5:"value";i:24;}i:6;O:8:"stdClass":2:{s:4:"date";s:6:"201111";s:5:"value";i:23;}i:7;O:8:"stdClass":2:{s:4:"date";s:6:"201112";s:5:"value";i:23;}i:8;O:8:"stdClass":2:{s:4:"date";s:6:"201201";s:5:"value";i:21;}i:9;O:8:"stdClass":2:{s:4:"date";s:6:"201202";s:5:"value";i:20;}i:10;O:8:"stdClass":2:{s:4:"date";s:6:"201203";s:5:"value";i:20;}i:11;O:8:"stdClass":2:{s:4:"date";s:6:"201204";s:5:"value";i:20;}i:12;O:8:"stdClass":2:{s:4:"date";s:6:"201205";s:5:"value";i:20;}}}s:17:"trends_low_sample";b:0;s:10:"query_cost";i:13;s:16:"trends_frequency";s:7:"monthly";}}');

            if ($data->status === 'OK')
            {
                $ranks = $data->data->trends->rank;
                $domain->setCompeteComRank($ranks[count($ranks) - 1]->value);

                $errors = $this->_validator->validate($domain);
                if (count($errors) > 0)
                {
                    throw new \Exception('validate entity domain failed');
                }

                $this->_entityManager->persist($domain);
            }
            else if ($data->status === 'NO_DATA')
            {
                $this->_log('compete.com: ' . $data->status . ': no data available for ' . $site);
            }
            else
            {
                $this->_log('compete.com: ' . $data->status . ': ' . $data->status_message);
            }
        }

        $this->_entityManager->flush();
    }
}
