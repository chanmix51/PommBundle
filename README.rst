===================================
PommBundle a non ORM for Symfony2
===================================

What is PommBundle ?
--------------------

PommBundle makes you able to benefit from `Pomm <http://pomm.coolkeums.org>` and `Postgres <http://postgresql.org>` features from your `Symfony2 <http://www.symfony.com>` development. 

Installation and setup
----------------------

There are several ways to install PommBundle:

 - Use `Composer <http://www.packagist.org>`.
 - Clone github repo or download PommBundle's files in a directory.

The composer way
================

Just add the following line to your `composer.json` file::

    {
        "minimum-stability": "dev",
        "require": {
            "pomm/pomm-bundle": "dev-master"
        }
    }


And launch `composer.phar install` to get the bundle in the vendor directory with the autoloader set. If you are using Symfony 2.0.x, you may still be using sf2 autoloader. Update your `app/autoload.php` file::

    $loader->registerNamespaces(array(
        'Symfony'          => array(__DIR__.'/../vendor/symfony/src', __DIR__.'/../vendor/bundles'),
        ...

        'Pomm'             => __DIR__.'/../vendor/pomm/pomm',
        'Pomm\\PommBundle' => __DIR__.'/../vendor/pomm/pomm-bundle',

Download the files
==================

To use PommBundle, you can clone or download the bundle_ and the Pomm_ API in the *vendor* directory.

.. _bundle: https://github.com/chanmix51/PommBundle
.. _Pomm: https://github.com/chanmix51/Pomm

::

  $ mkdir -p vendor/pomm/{pomm,pomm-bundle/Pomm/PommBundle}
  $ git clone https://github.com/chanmix51/Pomm vendor/pomm/pomm
  ...
  $ git clone https://github.com/chanmix51/PommBundle vendor/pomm/pomm-bundle/Pomm/PommBundle

You have now to tell Symfony2 autoloader where to find the API and the files that will be generated. Fire up your text editor and add the following lines to the *app/autoload.php* file:

::

    #app/autoload.php

        'Pomm/PommBundle'                => __DIR__.'/../vendor/bundles/Pomm',
        'Pomm'                           => __DIR__.'/../vendor/pomm',
    # This is the default namespace for the model
    # But it can be changed see the command line tools
        'Model'                          => __DIR__.'/..',

Let's register the PommBundle in the application kernel:

::

    #app/AppKernel.php
            // register your bundles
            new Pomm\PommBundle\PommBundle(),

You can now define your database settings in your main configuration file. The example below uses the yaml format:

::

    # app/config/config.yml
    pomm:
        databases:
            cnct_name:
                dsn: pgsql://user:password@host:port/dbname

The *cnct_name* here is a name for your database. You can define several databases using different dsn or options.

::

    #app/config/config.yml
    pomm:
        databases:
            con1:
                dsn:       pgsql://user:password@host:port/dbname
            con2:
                dsn:       pgsql://user:password@host:port/dbname
                class:     My/Database    # default: Pomm\Connection\Database
                isolation: SERIALIZABLE

How to register converters
--------------------------

You can define global converter definitions for all databases, and/or per database:

::

    #app/config/config.yml
    pomm:
        converters:
            year: 
                class: My\Pomm\Converter\Year
                types: [year]
            month: 
                class: My\Pomm\Converter\Month
                types: [month]
        databases:
            con1:
                dsn:       pgsql://user:password@host:port/dbname
                converters:
                    day: 
                        class: My\Pomm\Converter\Day
                        types: [day]
            con2:
                dsn:       pgsql://user:password@host:port/dbname
                class:     My/Database    # default: Pomm\Connection\Database
                isolation: SERIALIZABLE

The con1 database will have the year, month and day converters.
The con2 database will have the year and month converters.

How to generate Map files
-------------------------

A Map file is the way for Pomm to know about your tables structures. Pomm can scan the database to generate these files for you.

::

    $ app/console pomm:mapfile:create my_table

This will create a file *Model/Pomm/Entity/Public/Base/MyTableMap.php* with the class *MyTableMap* in the namespace *Model\\Pomm\\Entity\\Public\\Base* extending Pomm\\Object\\BaseObjectMap that maps to the table *my_table* in the postgresql's schema *public*. You can of course override any of these settings using the command line options:

::

    $ app/console pomm:mapfile:create --database=foo --prefix-path=other/dir --prefix-namespace="Other\Namespace" --schema="other_schema" --extends="Other\\Parent" my_table

This will create a *other/dir/Model/Pomm/Entity/OtherSchema/Base/MyTableMap.php* file owning the *Other\\Namespace\\Model\\Pomm\\Entity\\OtherSchema\\Base\\MyTableMap* class from the postgres table *other_schema.my_table* according to the database defined as *foo* in the configuration. This can be useful if you want to store the model files in your bundles instead having them in the project directory. 

Of course a 

::

    $ app/console help pomm:mapfile:create

will help you :)

Real life projects have dozens (sometimes hundreds) tables and it could be tiedous to generate map files one by one. Pomm has a command to scan Postgresql'schemas for tables and generate all the corresponding Map files.

::

    $ app/console pomm:mapfile:scan

All previous options also apply for this command.

Examples
--------


In your controllers, using the default database (the first defined):

::

    public function listThingsAction()
    {
        $things = $this->get('pomm')
            ->getDatabase()
            ->createConnection()
            ->getMapFor('Model\Pomm\Entity\NssBlog\Article')
            ->findAll();

            ...
    }

You might want to filter things with some conditions:

::

    public function listActiveAndRecentThingsAction()
    {
        $things = $this->get('pomm')
            ->getDatabase()
            ->createConnection()
            ->getMapFor('Model\Pomm\Entity\NssBlog\Article')
            ->findWhere('active AND created_at > ?', array(strtotime('one month ago')));

            ...
    }

Another example calling a custom model function from a database named *foo*:

::

    public function myListStuffAction()
    {
        $stuff = $this->get('pomm')
            ->getDatabase('foo')
            ->createConnection()
            ->getMapFor('Model\Pomm\Entity\AdminUser\Group')
            ->myModelMethod();

            ...
    }


Pomm also make you benefit from Postgresql's nice transaction mechanism, see the `Pomm's online documentation`_.

 .. _Pomm's online documentation : http://pomm.coolkeums.org/documentation
