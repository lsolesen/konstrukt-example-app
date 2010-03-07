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