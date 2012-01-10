<?php

namespace GHub\PommBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;

use Pomm\Tools\ScanSchemaTool;

class ScanSchemaCommand extends BaseCreateCommand
{
    protected function configure()
    {
        parent::configure();

        $this->setName('pomm:mapfile:scan')
            ->setDescription('Scans and generates the map files from a database schema.')
            ->setHelp(<<<EOT
The <info>pomm:mapfile:scan</info> command scans the tables in a database schema to generate the
Map files.

    <info>app/console pomm:mapfile:scan</info>

If no database name is provided, Pomm takes the first defined in your configuration.

  <info>app/console pomm:mapfile:scan --database=my_database</info>

You can specify the Postgresql schema to scan tables into (default: public)

  <info>app/console pomm:mapfile:scan --schema=pg_schema</info>

By default, map files are generated in your SchemaName directory tree with default's base to the project directory. You can override
this behavior by providing a prefix-path:

  <info>app/console pomm:mapfile:scan --prefix-path=/my/directory</info>

The example above will generate all the files in /my/directory/DatabaseName/SchemaName/*.

The same apply with namespaces. By default the namespaces will be in the form DatabaseName\\SchemaName\\* but you can prefix this by your own prefix-namespace.

  <info>app/console pomm:mapfile:scan --prefix-namespace=My\\Bundle\\Namespace</info>

The classes will be then in the My\\Bundle\\Namespace\\DatabaseName\\SchemaName\\*.

The Map objects HAVE TO be instances of Pomm\\Object\\BaseObjectMap but you might want to
choose their basefiles to extend other classes that extend Pomm\\Object\\BaseObjectMap.

  <info>app/console pomm:mapfile:scan --extends="My\\Other\\Class"</info>
EOT
        );
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $options = $this->getToolOptions($input);

        $tool = new ScanSchemaTool($options);

        $tool->execute();
    }
}
