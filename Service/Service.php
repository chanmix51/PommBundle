<?php

namespace GHub\Bundle\PommBundle\Service;

use Pomm\Pomm;

class Service
{
    protected $pomm_class;

    public function __construct($pomm_class, array $connections)
    {
        $this->pomm_class = $pomm_class;

        foreach ($connections as $name => $params)
        {
            $pomm_class::setDatabase($name, $params);
        }
    }

    public function getDatabase($name = null)
    {
        $pomm_class = $this->pomm_class;

        return $pomm_class::getDatabase($name);
    }

    public function getConnection($name = null)
    {
        return $this->getDatabase($name)->createConnection();
    }

    public function getTransaction($name = null)
    {
        return $this->getDatabase($name)->createTransaction();
    }
}
