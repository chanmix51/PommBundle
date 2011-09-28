<?php

namespace GHub\PommBundle\Command;

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

        $dir = sprintf('%s/Model/Pomm/Entity/schema_name/Base', '%kernel.root_dir%');

        $this->setName('pomm:mapfile:create')
            ->setDescription('Generates the Map file from a given table.')
            ->addArgument('table', InputArgument::REQUIRED, 'The table name to generate the map file from')
            ->setHelp(<<<EOT
The <info>pomm:mapfile:create</info> command generates a Map file from a given table 
definition in the database. The map file is created in the model directory tree under the <info>$dir</info> directory.

    <info>app/console pomm:mapfile:create table_name</info>

If no connection name is provided, Pomm takes the first defined in your configuration.

  <info>app/console pomm:mapfile:create --connection=my_connection table_name</info>

You can specify the Postgresql schema to scan tables into (default: public). As the Schema plays the role of database namespace it is used in the Model directory tree under the Entity directory.

  <info>app/console pomm:mapfile:create --schema=production table_name</info>

By default, map files are generated in the main Model directory tree. You can override 
this behavior by providing a prefix-path. This is useful if you want to manage a bundle based Model tree:

  <info>app/console pomm:mapfile:create --prefix-path=/my/directory table_name</info>

This command line above will generate files in the directory /my/directory/Model/Pomm/Entity/Schema.

The Map objects HAVE TO be instances of Pomm\\Object\\BaseObjectMap but you might want to 
choose their basefiles to extend other classes that extend Pomm\\Object\\BaseObjectMap.

  <info>app/console pomm:mapfile:create --extends="My\\Other\\Class" table_name</info>

By default, the classes' namespace will be Model\\Pomm\\Entity\\Schema. It is possible to add a prefix to this namespace.

  <info>app/console pomm:mapfile:create --prefix-namespace="My\\Other\\Namespace" table_name</info>

This will result in model class belonging to namespace My\\Other\\Namespace\\Model\\Pomm\\Entity\\Schema.
EOT
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $options = array();
        $options['connection'] = !$input->hasOption('connection') ? $this->getContainer()->get('pomm')->getDatabase() : $this->getContainer()->get('pomm')->getDatabase($input->getOption('connection'));
        $options['prefix_dir'] = $input->getOption('prefix-path');

        if ($input->getOption('prefix-namespace') != '') {
            $options['prefix_namespace'] = $input->getOption('prefix-namespace');
        }

        $options['prefix_dir'] = $input->getOption('prefix-path') == '' ? $this->getContainer()->getParameter('kernel.root_dir').'/..' : $input->getOption('prefix-path');
        $options['table'] = $input->getArgument('table');
        $options['schema'] = $input->getOption('schema') != '' ? $input->getOption('schema') : 'public';
        $options['extends'] = $input->getOption('extends') != '' ? $input->getOption('extends') : 'BaseObjectMap';

        $tool = new CreateBaseMapTool($options);

        $tool->execute();
    }
}
