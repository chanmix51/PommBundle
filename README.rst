===================================
PommBundle a non ORM for Symfony2
===================================

What is Pomm ?
--------------

Pomm stands for **Postgresql/PHP Object Model Manager**. It turns an existing Postgresql database to collection of coherent objects through an *Object Mapping* (OM). Pomm makes you use SQL to query the database and take advantage of the RDBMS features. 

**Pomm is really Fast**
    Pomm is a layer built on top of PDO and that's it. There is no database abstraction layer to slow down your processes: no query parser, the results are simply fed into your structures. By placing in the database processes that never change you greatly increase the performances of your website.

**Pomm is Powerful**
    Pomm makes you able to take advantage from the great features of Postgresql (functions, transactions, extra types, table inheritance ... ). Because it does not use an abstraction layer, you can write queries that do the job the most efficient way. Pomm also also benefits from Symfony2 features and PHP5.3 namespaces. 

**Pomm is Efficient**
    Unlike Propel or Doctrine, Pomm does not rely on a schema. It scans the database to generate the PHP structures it needs. This lets you use the specialized and powerful tools that exist for postgresql to design, migrate, save and deploy databases. From a code point of view, you do not have to learn a new language, the query language is pure Postgres'SQL. 

Installation and setup
----------------------

To use PommBundle, you must clone the bundle_ in the *src* directory of your sf2 project and the Pomm_ API in the *vendor* directory.

.. _bundle: https://github.com/chanmix51/PommBundle
.. _Pomm: https://github.com/chanmix51/Pomm

::

  $ git clone https://github.com/chanmix51/Pomm vendor/pomm
  ...
  $ git clone https://github.com/chanmix51/PommBundle vendor/bundles/GHub

You might prefer `downloading an archive`__ of the Pomm bundle. Simply unzip it in your *src* directory.

.. __: http://pomm.coolkeums.org/downloads/PommBundle.latest.tar.gz

::

    src$ cd src/bundles && tar xzf ~/Downloads/PommBundle.latest.tar.gz

If you are using the deps file to manage your project's dependencies, you must add the following lines to it:

::

  [Pomm]
    git=https://github.com/chanmix51/Pomm.git
    target=/pomm
  
  [GHubPommBundle]
    git=https://github.com/chanmix51/PommBundle.git
    target=/bundles/GHub/PommBundle

You have now to tell Symfony2 autoloader where to find the API and the files that will be generated. Fire up your text editor and add the following lines to the *app/autoload.php* file:

::

    #app/autoload.php

        'Pomm'                           => __DIR__.'/../vendor/pomm',
        'GHub'                           => __DIR__.'/../vendor/bundles',
    # This is the default namespace for the model
    # But it can be changed see the command line tools
        'Model'                          => __DIR__.'/..',

Let's register the PommBundle in the application kernel:

::

    #app/AppKernel.php
            // register your bundles
            new GHub\PommBundle\GHubPommBundle(),

You can now define your database settings in your main configuration file. The example below uses the yaml format:

::

    # app/config/config.yml
    g_hub_pomm:
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




