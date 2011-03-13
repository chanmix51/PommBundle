===================================
PommBundle a new O(R)M for Symfony2
===================================

What is Pomm ?
--------------

Pomm stands for **Postgresql Object Model Manager**. It turns an existing Postgresql database to collection of coherent objects through an *Object Mapping* (OM). Pomm makes you use SQL to query the database and take advantage of the RDBMS features. 

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
  $ git clone https://github.com/chanmix51/PommBundle src

You might prefer `downloading an archive`__ of the Pomm bundle. Simply unzip it in your *src* directory.

.. __: https://github.com/chanmix51/PommBundle/zipball/master

::

    src$ unzip ~/Downloads/chanmix51-PommBundle-4f2ed43.zip

You have now to tell Symfony2 autoloader where to find the API and the files that will be generated. Fire up your text editor and add the following lines to the *app/autoload.php* file:

::

    #app/autoload.php

        'Pomm'                           => __DIR__.'/../vendor/pomm',
        'GHub'                           => __DIR__.'/../src',
        'Pomm\Model\Map'                 => __DIR__.'/cache',


You can now define your database settings in your main configuration file. The example below uses the yaml format:

::

    # app/config/config.yml
    pomm:
        connections:
            default:
                dsn: pgsql://user:password@host:port/dbname

The *default* here is a name for your database connection. You can define several databases connections using different names on different databases, users etc...

::

    #app/config/config.yml
    pomm:
        connections:
            con1:
                dsn: pgsql://user:password@host:port/dbname
            con2:
                dsn: pgsql://user:password@host:port/dbname


