<?php

namespace GHub\PommBundle;

use Pomm\Logger as BaseLogger;

class Logger extends BaseLogger
{
    protected $stopwtach;

    public function __construct($stopWatch = null)
    {
        $this->stopWatch = $stopWatch;
    }

    public function startQuery($sql, array $params = null)
    {
        if ($this->stopWatch)
        {
            $this->stopWatch->start('pomm');
        }
        parent::startQuery($sql, $params);
    }

    public function stopQuery()
    {
        parent::stopQuery();
        if ($this->stopWatch)
        {
            $this->stopWatch->stop('pomm');
        }
    }
}
