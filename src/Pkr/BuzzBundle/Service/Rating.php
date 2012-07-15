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
            if (!empty($this->_apiAccessData['competeCom']['apiKey']))
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
        }

        $this->_entityManager->flush();
    }
}
