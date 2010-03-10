Example application for konstrukt-framework
==

[Konstrukt](http://konstrukt.dk/) is a HTTP-friendly framework of controllers for PHP5. After reading the [Getting Started 1](http://konstrukt.dk/getting-started-part1.html), [2](http://konstrukt.dk/getting-started-part2.html), and [3](http://konstrukt.dk/getting-started-part3.html), we are ready to build our first example application using konstrukt.

We will be making a basic blog-application just to showcase what konstrukt can do. Konstrukt is not a full-featured framework. Konstrukt focuses on the controller layer, and lets the programmer make decisions about implementation.

Requirements
-- 

In our example application we will use the following:

* konstrukt for the controller layer
* the konstrukt default implementation of a php-template engine for the view layer
* pdoext for the database access in the model layer
* Zend_Form for form-handling
* Zend_Validate for validating form inputs
* bucket for dependency injection

Setting up requirements
--

A little voodoo at the command line makes it quite easy to install dependencies, as kontrukt and the requirements can be installed using the pear command line.

    pear channel-discover pear.konstrukt.dk
    pear install konstrukt/konstrukt

    pear channel-discover public.intraface.dk
    pear install intrafacepublic/bucket
    pear install intrafacepublic/pdoext

    pear channel-discover zend.googlecode.com/svn
    pear install zend/zend

Now all the dependencies are installed, and we are ready to start the project.

Getting started
--

We will be using the default starter pack from the konstrukt examples as a starting point for creating our blog. You need to put the code in a directory which can be accessed through a web browser.

    svn export http://konstrukt.googlecode.com/svn/tags/2.3.1/examples/starterpack_default blog
    
After setting it up, you should be able to navigate to:

	http://workspace/blog/www/
	
And you will be greeted with a message: Root. This page has intentionally been left blank. This makes a great starting point for creating your blog.

In the config/global.inc.php we will add the following lines right after the include of konstrukt:

    require_once 'pdoext/query.inc.php';
    require_once 'pdoext/tablegateway.php';
    require_once 'pdoext/connection.inc.php';

Now the default directory structure will be created. We will start setting up a couple of things that we need when developing the application. Konstrukt relies on dependency injection, so we will start setting up the di-container. First we will add some methods in the application factory placed in the lib-directory. We will add these methods at the bottom of the class:

    function new_pdoext_Connection($c) {
      return new pdoext_Connection($this->pdo_dsn, $this->pdo_username, $this->pdo_password);
    }

    function new_model_BlogGateway($c) {
      return new model_BlogGateway("blogentries", $this->new_pdoext_Connection($c));
    }
	
First setup a database in mysql. Now we will edit some of the config-settings in config/development.inc.php. We need to change the database. We will be using a sqlite-file to make it easy. We will set this up in the function create_container(). We will change these settings:

    $factory->pdo_dsn = 'mysql:host=localhost;dbname=blog';
    $factory->pdo_username = 'root';

Make sure that you have write access to the directory where the sqllite file will be put. Now we need to make our first component:

Create the database
--

Create a var directory and make it writable.

	mkdir var
	chmod 777 var

Go to the script directory. Run the following code:

    php generate_migration.php
 
This command will generate a skeleton migration file in the directory migrations. Navigate to that file and edit it. Put in the following code:

	#!/usr/bin/env php
	<?php
	require_once(dirname(__FILE__) . '/../../config/global.inc.php');
	$container = create_container();
	$db = $container->create('pdoext_Connection');
	$db->exec('CREATE TABLE blogentries (
	  id int(11) NOT NULL AUTO_INCREMENT,
	  name varchar(255) NOT NULL,
	  published datetime NOT NULL,
	  title varchar(255) NOT NULL,
	  excerpt text NOT NULL,
	  content longtext NOT NULL,
	  PRIMARY KEY (id)
    );');

Now the database has been setup, and we are ready to create some models.

Adding some models
--

We will add a model for the blog entries in in lib/model. Let's call it bloggateway.php. We will let it extend pdoext_TableGateway.

    class model_BlogGateway extends pdoext_TableGateway {
        function __construct(pdoext_Connection $pdo) {
            return parent::__construct('blogentries', $pdo);
        }
    }

Now we are ready to create our components.

Creating our first component
--

With no further ado we will present our first component:

    <?php
	class components_Blog extends k_Component {
  	    protected $gateway;
  	    protected $templates;
        protected $form;

  	    /**
   	     * Constructor
   	     *
	     * Relies on dependency injection handled by bucket.
   	     * @see www/index.php
   	     * @see lib/applicationfactory.php
   	     *
   	     * @param object $datasource
   	     * @param objct $templates
   	     * 
   	     * @return void
   	     */
        function __construct(model_BlogGateway $datasource, k_TemplateFactory $templates) {
            $this->datasource = $datasource;
            $this->templates = $templates;
        }
        function execute() {
            return $this->wrap(parent::execute());
        }
        function wrapHtml($content) {
            $t = $this->templates->create("wrapper");
            return
      		    $t->render(
                $this,
                array(	
      	  	      'href' => $this->url(),
          	      'title' => "index",
          	      'content' => $content
                ));
        }
        function renderHtml() {
            $resultset = Array();
            $results = $this->datasource->select('published', 'desc');
            $model = Array(
      	     'resultset' => $results
            );
            $tpl = $this->templates->create('blog/index');
    		return $tpl->render($this, $model);
  		}
	}
	
We need to create two templates. One for showing the list of blog entries and a wrapper template. Put them in the templates directory.

	<!-- blog/index.tpl.php -->
	<?php foreach ($resultset as $result) : ?>
	  <h2><a href="<?php e(url($result->name)); ?>"><?php e($result->title); ?></a></h2>
	  <span class="date"><?php e($result->published); ?></span>
	  <p><?php e($result->excerpt); ?></p>
	<?php endforeach; ?>
	
	<p class="pager">
		<a style="float:right" href="<?php e(url(null, array('create'))); ?>">create</a>
	</p>
		
	<!-- wrapper.tpl.php -->
	<a href="<?php e($href); ?>"><?php e($title); ?></a>
	<div style="border:1px solid #ccc;padding:1em">
		<?php echo $content; ?>
	</div>

Now this component is ready to show a list of blog entries. Now all we need to do to make it accessible for the application is map the component from the root controller. This is done differently in Konstrukt than other frameworks. We will add the following method to the root-controller:

  	function map($name) {
      return 'components_Blog';
  	}
  	
Now try to navigate to http://workspace/blog/www/blog/ and you should see a page with two links. An index-link and a create-link. This means that everyhing works as expected.