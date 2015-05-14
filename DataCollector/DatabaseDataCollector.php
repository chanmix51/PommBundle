<?php

namespace Pomm\PommBundle\DataCollector;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Pomm\Connection\FilterChain\FilterInterface;
use Pomm\Connection\FilterChain\QueryFilterChain;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

class DatabaseDataCollector extends DataCollector implements FilterInterface
{
    private $queries;

    public function __construct(\Pomm\Service $pomm)
    {
        $this->queries = array();

        foreach ($pomm->getDatabases() as $database) {
            $database->getConnection()
                ->filter_chain->registerFilter($this);
        }
    }

    public function execute(QueryFilterChain $chain)
    {
        $start = microtime();
        $stmt = $chain->executeNext();
        $end = microtime();
        $this->queries[] = array(
            'sql' => $chain->query->getSql(),
            'values' => $chain->values,
            'time' => $end - $start,
        );

        return $stmt;
    }

    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $time = 0;
        $querycount = 0;
        $queries = $this->queries;

        foreach ($queries as $query) {
            $querycount++;
            $time += $query['time'];
        }

        $this->data = compact('queries', 'querycount', 'time');
    }

    public function getQueries()
    {
        return $this->data['queries'];
    }

    public function getQuerycount()
    {
        return $this->data['querycount'];
    }

    public function getTime()
    {
        return $this->data['time'];
    }

    public function getName()
    {
        return 'db';
    }
}
