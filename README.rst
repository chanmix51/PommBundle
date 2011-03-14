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

Let's register the PommBundle in the application kernel:

::

    #app/AppKernel.php
            // register your bundles
            new GHub\Bundle\PommBundle\PommBundle(),

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


How to generate Map files
-------------------------

A Map file is the way for Pomm to know about your tables structures. Pomm can scan the database to generate these files for you.

::

    $ app/console pomm:mapfile:create my_table

This will create a file *app/cache/Pomm/Model/Map/BaseMyTableMap.php* with the class *BaseMyTableMap* in the namespace *Pomm\\Model\\Map* extending Pomm\Object\BaseObjectMap that maps to the table *my_table* in the postgresql's schema *public*. You can of course override any of these settings using the command line options:

::

    $ app/console pomm:mapfile:create --connection=foo --path=other/dir --namespace="Other\\\\Namespace" --schema="other_schema" --extends="Other\\Parent" my_table

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
            ->getMapFor('MyBundle\Model\Thing')
            ->findAll();

            ...
    }

You might want to filter things with some conditions:

::

    public function listActiveAndRecentThingsAction()
    {
        $things = $this->get('pomm')
            ->getConnection()
            ->getMapFor('MyBundle\Model\Thing')
            ->findWhere('active AND created_at > ?', array(strtotime('one month ago')));

            ...
    }

Another example calling a custom model function from a connection named *foo*:

::

    public function myListStuffAction()
    {
        $stuff = $this->get('pomm')
            ->getConnection('foo')
            ->getMapFor('MyBundle\Model\Stuff')
            ->myModelMethod();

            ...
    }

******************
Using transactions
******************

Let's say we want to change the karma of the author of a post on a blog and the karma of the comment author when a comment is added:

::

    public function addCommentAction()
    {
        // ... get the $comment from a form here
        // retreive $blogAuthor and $commentAuthor

        $tr = $this->get('pomm')
            ->getTransaction()
            ->begin();

        try {
            $tr->getMapFor('MyBundle\Model\Comment')
                ->saveOne($comment);    // builtin method

            $tr->getMapFor('MyBundle\Model\CommentStatistic')
                ->updateFor($comment);  // custom method

        } catch (MyBundle\Model\Exception $e) {
            $tr->rollback();

            // note the transaction is over but you can use it
            // as a normal connection.
            $tr->getMapFor('MyBundle\Model\AdminTask')
                ->haveALookAt($comment);

            throw $e;
        }

        $tr->setSavePoint('comment');

        try {
                $tr->getMapFor('MyBundle\Model\Author)
                    ->addBlogAuthorKarmaForComment($blogAuthor, $comment);

            $tr->getMapFor('MyBundle\Model\Author)
                ->addCommentAuthorKarmaForComment($commentAuthor, $comment);

            $message = "Your comment has been saved and your karma has been updated.";

        } catch (MyBundle\Model\Exception $e) {
            $tr->rollback('comment');
            $message = "Your comment has been saved but your karma cannot be changed with this action.";
        }

        $tr->commit();

        $this->redirect(@anotherAction);
    }

******************
In the model layer
******************

::

    #MyBundle/Model/BlogPost.php

    // Returns the most commented blog posts since $date
    public function getMostActiveBlogPosts($date, $limit = 10)
    {
        $sql = sprintf(<<<SQLEND
    SELECT
        post.*,
        COUNT(comment.id) AS comment_count
    FROM
        blog_post post
            JOIN blog_comment comment ON post.id = comment.post_id
    WHERE
            post.active
        AND
            comment.created_at > ?
    GROUP BY %s
    HAVING comment_count > 0
    ORDER BY comment_count DESC
    LIMIT %d
    SQLEND
            , $this->getGroupByFields('post'), $limit);

        return $this->query($sql, array($date));
    }

Accessing the comment count in twig template will be as easy as:

::

    <ul>
    {{ for post in posts }}
        <li>rank {% loop.index %} - {% post.title %} with {% post.getCommentCount %} comments posted last week.</li>
    {{ endfor }}
    </ul>

**Important Note** on the query above. Only the date is passed as parameter and will be escaped by the database. The *$limit* parameter here is voluntarily hard coded in the query using sprintf and thus will **NOT** be escaped. Be aware of that if you want to ovoid SQL injection attacks.

***************
The Where class
***************

Sometimes, it may be useful to build dynamically the where clause of a query. The Where class has been written for that purpose: 

::

    public function selectPeople()
    {
        $where = Where::create("name ~ ?", array('^A'))
            ->andWhere("age > ?", array('35')
            ->orWhere('gender = ?', array('female'))
            ;
        // (name ~ '^A' AND age > 35) OR gender = 'female'

        return $this->findWhere($where, $where->getValues());
    }

    public function cannonFodder()
    {
        $where = Where::create("gender = ?", array('male'))
            ->andWhere(Where::create("age BETWEEN ? AND ?", array(18, 35))->orWhere("registration IS NOT NULL"))
            ;
        // gender = 'male' AND (age BETWEEN 18 AND 35 OR registration IS NOT NULL)

        return $this->findWhere($where);
    }

Send questions, notes, postcards, vacuum tubes to hubert DOT greg AT gmail DOT com.
