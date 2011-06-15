===================================
PommBundle a new O(R)M for Symfony2
===================================

What is Pomm ?
--------------

Pomm stands for **Postgresql/PHP Object Model Manager**. It turns an existing Postgresql database to collection of coherent objects through an *Object Mapping* (OM). Pomm makes you use SQL to query the database and take advantage of the RDBMS features. 

**Pomm is really Fast**
    Pomm is a layer built on top of PDO and that's it. There is no database abstraction layer to slow down your processes: no query parser, the results are simply fed into your structures. By placing in the database processes that never change you greatly increase the performances of your website.

**Pomm is Powerful**
    Pomm it not powerful in itself, it lets you take advantage from the great features of Postgresql (functions, transactions, extra types, table inheritance ... ). Because it does not use an abstraction layer, you can write queries that do the job the most efficient way. Pomm also also benefits from Symfony2 features and PHP5.3 namespaces. 

**Pomm is Efficient**
    Unlike Propel or Doctrine, Pomm does not rely on a schema. It scans the database to generate the PHP structures it needs. This lets you use the specialized and powerful tools that exist for postgresql to design, migrate, save and deploy databases. From a code point of view, you do not have to learn a new language, the query language is pure Postgres'SQL. 

**Pomm is in Beta state**
    Pomm is still in development and should not be used in production. I encourage its use, do not hesitate to send me feedbacks and bugs.

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
        connections:
            cnct_name:
                dsn: pgsql://user:password@host:port/dbname

The *cnct_name* here is a name for your database connection. You can define several databases connections using different names on different databases, users etc...

::

    #app/config/config.yml
    pomm:
        connections:
            con1:
                dsn:       pgsql://user:password@host:port/dbname
            con2:
                dsn:       pgsql://user:password@host:port/dbname
                class:     My/Database    # default: Pomm\Connection\Database
                isolation: SERIALIZABLE


How to generate Map files
-------------------------

A Map file is the way for Pomm to know about your tables structures. Pomm can scan the database to generate these files for you.

::

    $ app/console pomm:mapfile:create my_table

This will create a file *Model/Pomm/Entity/Public/Base/MyTableMap.php* with the class *MyTableMap* in the namespace *Model\\Pomm\\Entity\\Public\\Base* extending Pomm\\Object\\BaseObjectMap that maps to the table *my_table* in the postgresql's schema *public*. You can of course override any of these settings using the command line options:

::

    $ app/console pomm:mapfile:create --connection=foo --prefix-path=other/dir --prefix-namespace="Other\Namespace" --schema="other_schema" --extends="Other\\Parent" my_table

This will create a *other/dir/Model/Pomm/Entity/OtherSchema/Base/MyTableMap.php* file owning the *Other\\Namespace\\Model\\Pomm\\Entity\\OtherSchema\\Base\\MyTableMap* class from the postgres table *other_schema.my_table* according to the connection defined as *foo* in the configuration. This can be useful if you want to store the model files in your bundles instead having them in the project directory. 

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


In your controllers, using the default connection (the first defined):

::

    public function listThingsAction()
    {
        $things = $this->get('pomm')
            ->getConnection()
            ->getMapFor('Model\Pomm\Entity\NssBlog\Article')
            ->findAll();

            ...
    }

You might want to filter things with some conditions:

::

    public function listActiveAndRecentThingsAction()
    {
        $things = $this->get('pomm')
            ->getConnection()
            ->getMapFor('Model\Pomm\Entity\NssBlog\Article')
            ->findWhere('active AND created_at > ?', array(strtotime('one month ago')));

            ...
    }

Another example calling a custom model function from a connection named *foo*:

::

    public function myListStuffAction()
    {
        $stuff = $this->get('pomm')
            ->getConnection('foo')
            ->getMapFor('Model\Pomm\Entity\AdminUser\Group')
            ->myModelMethod();

            ...
    }


Pomm also make you benefit from Postgresql's nice transaction mechanism, see the `Pomm's online documentation`_.

 .. _Pomm's online documentation : http://pomm.coolkeums.org/documentation




