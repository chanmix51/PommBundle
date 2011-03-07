<?php

namespace GHub\Bundle\PommBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;

use Pomm\Tools\CreateBaseMapTool;

class CreateBaseMapCommand extends BaseCreateCommand
{
    protected function configure()
    {
        parent::configure();

        $dir = sprintf('%s/app/cache/Pomm/Model/Map', '%kernel.root_dir%/app/cache/Pomm');

        $this->setName('pomm:mapfile:create')
            ->setDescription('Generates the Map file from a given table.')
            ->addArgument('table', InputArgument::REQUIRED, 'The table name to generate the map file from')
            ->setHelp(<<<EOT
The <info>pomm:mapfile:create</info> command generates a Map file from a given table 
definition in the database. The map file is created in the cache under the <info>$dir/app/cache/pomm</info> directory.

    <info>app/console pomm:mapfile:create table_name </info>

If no connection name is provided, Pomm takes the first defined in your configuration.

  <info>app/console pomm:mapfile:create table_name --connection=my_connection</info>

You can specify the Postgresql schema to scan tables into (default: public)

  <info>app/console pomm:mapfile:create table_name --schema=production</info>

By default, map files are generated in your cache directory. You can override 
this behavior by providing a path:

  <info>app/console pomm:mapfile:create table_name --path=/my/directory</info>

The Map objects HAVE TO be instances of BaseObjectMap but you might want to 
choose their basefiles to extend other classes that extend BaseObjectMap.

  <info>app/console pomm:mapfile:create table_name --extends="My\\\\Other\\\\Class"</info>
EOT
        );
    }


    public function execute(InputInterface $input, OutputInterface $output)
    {
        $connection = !$input->hasOption('connection') ? $this->container->get('pomm')->getDatabase() : $this->container->get('pomm')->getDatabase($input->getOption('connection'));
        $dir = $input->getOption('path') != '' ? $input->getOption('path') : $this->container->getParameter('kernel.root_dir').'/cache/Pomm';

        if (!is_dir($dir) and !mkdir($dir))
        {
            throw new \RunTimeException(sprintf("Could not create the directory '%s'. Please check the permissions on the disk.\n", $dir));
        }

        $tool = new CreateBaseMapTool(array(
            'dir'   => $dir, 
            'table' => $input->getArgument('table'),
            'connection'   => $connection,
            'schema' => $input->getOption('schema'),
            'extends' => $input->getOption('extends'),
        ));

        $tool->execute();
    }
}
