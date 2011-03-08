<?php

namespace GHub\Bundle\PommBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;

abstract class BaseCreateCommand extends Command
{
    protected function configure()
    {
        parent::configure();

        $dir = sprintf('%s/app/cache/Pomm/Model/Map', '%kernel.root_dir%/app/cache/Pomm');

        $this
            ->addOption('connection', null, InputOption::VALUE_REQUIRED, 'The name of the connection to use (default: the first one)')
            ->addOption('schema', null, InputOption::VALUE_REQUIRED, 'The schema name to scan for tables (default: "public")', 'public')
            ->addOption('path', null, InputOption::VALUE_REQUIRED, sprintf('The directory where the map files are generated (default "%s")', $dir))
            ->addOption('extends', null, InputOption::VALUE_REQUIRED, 'The classe the map file extends (default: "Pomm\Object\BaseObjectMap")', 'BaseObjectMap')
            ->addOption('namespace', null, InputOption::VALUE_REQUIRED, 'The namespace of the generated map file (default: "Pomm\Model\Map")')
            ;
    }
}

