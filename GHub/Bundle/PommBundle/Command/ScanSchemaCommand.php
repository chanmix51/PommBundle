<?php

namespace GHub\Bundle\PommBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;

class ScanSchemaCommand extends BaseCreateCommand
{
    protected function configure()
    {
        parent::configure();

        $this->setName('pomm:mapfile:scan')
            ->setDescription('Scans and generates the map files from a database schema.')
            ->setHelp(<<<EOT
The <info>pomm:mapfile:scan</info> command scans the tables in a database schema to generate the 
Map files. They are created in the cache by default.

    <info>app/console pomm:mapfile:scan</info>

If no connection name is provided, Pomm takes the first defined in your configuration.

  <info>app/console pomm:mapfile:scan --connection=my_connection</info>

You can specify the Postgresql schema to scan tables into (default: public)

  <info>app/console pomm:mapfile:scan --schema=production</info>

By default, map files are generated in your cache directory. You can override 
this behavior by providing a path:

  <info>app/console pomm:mapfile:scan --path=/my/directory</info>

The Map objects HAVE TO be instances of BaseObjectMap but you might want to 
choose their basefiles to extend other classes that extend BaseObjectMap.

  <info>app/console pomm:mapfile:scan --extends="My\\\\Other\\\\Class"</info>
EOT
        );
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
    }
}
