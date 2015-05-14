<?php

namespace Pomm\PommBundle\Command;

use Pomm\Tools\OutputLine;
use Pomm\Tools\OutputLineStack;
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
            ->addOption('database', null, InputOption::VALUE_REQUIRED, 'The name of the database to use (default: the first one)')
            ->addOption('schema', null, InputOption::VALUE_REQUIRED, 'The schema name to scan for tables', 'public')
            ->addOption('prefix-path', null, InputOption::VALUE_REQUIRED, 'The directory where the Model tree is located', '')
            ->addOption('extends', null, InputOption::VALUE_OPTIONAL, 'The class the map file extends (default: "Pomm\Object\BaseObjectMap")')
            ->addOption('output-level', null, InputOption::VALUE_OPTIONAL, 'The minimum log output level: DEBUG, INFO, WARNING, ERROR, CRITICAL (default: INFO)')
            ->addOption('prefix-namespace', null, InputOption::VALUE_OPTIONAL, 'The namespace prefix for the model namespace (default: none)')
            ;
    }

    protected function getToolOptions(InputInterface $input)
    {
        $options = array();

        $options['database'] = !$input->hasOption('database') ? $this->getContainer()->get('pomm')->getDatabase() : $this->getContainer()->get('pomm')->getDatabase($input->getOption('database'));

        if ($input->getOption('prefix-namespace') != '') {
            $options['namespace'] = $input->getOption('prefix-namespace');
        }

        $options['prefix_dir'] = $input->getOption('prefix-path') == '' ? $this->getContainer()->getParameter('kernel.root_dir').'/..' : $input->getOption('prefix-path');
        $options['schema'] = $input->getOption('schema') != '' ? $input->getOption('schema') : 'public';
        $options['extends'] = $input->getOption('extends') != '' ? $input->getOption('extends') : 'BaseObjectMap';

        $outputLevel = $input->getOption('output-level');
        if (in_array(
            strtoupper($outputLevel),
            array('', 'DEBUG', 'INFO', 'WARNING', 'ERROR', 'CRITICAL')
        )) {
            $options['output_level'] = $outputLevel != ''
                ? constant('\Pomm\Tools\OutputLine::LEVEL_'.strtoupper($outputLevel))
                : OutputLine::LEVEL_INFO;
        } else {
            throw new \Exception(
                "Invalid log output level: {$input->getOption('output-level')}"
                ."\nAvailable levels: DEBUG, INFO (default), WARNING, ERROR, CRITICAL"
            );
        }

        return $options;
    }

    protected function outputStack(OutputLineStack $stack, OutputInterface $output)
    {
        foreach ($stack as $outputLine) {
            $output->writeln((string) $outputLine);
        }
    }
}
