<?php

namespace GHub\PommBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;

abstract class BaseCreateCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->addOption('connection', null, InputOption::VALUE_REQUIRED, 'The name of the connection to use (default: the first one)')
            ->addOption('schema', null, InputOption::VALUE_REQUIRED, 'The schema name to scan for tables', 'public')
            ->addOption('prefix-path', null, InputOption::VALUE_REQUIRED, 'The directory where the Model tree is located', '')
            ->addOption('extends', null, InputOption::VALUE_OPTIONAL, 'The classe the map file extends (default: "Pomm\Object\BaseObjectMap")')
            ->addOption('prefix-namespace', null, InputOption::VALUE_OPTIONAL, 'The namespace prefix for the model namespace (default: none)')
            ;
    }
}

