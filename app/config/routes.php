<?php
/* SVN FILE: $Id: routes.php 2134 2006-02-25 19:20:18Z phpnut $ */

/**
 * Short description for file.
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different urls to chosen controllers and their actions (functions).
 *
 * PHP versions 4 and 5
 *
 * CakePHP :  Rapid Development Framework <http://www.cakephp.org/>
 * Copyright (c) 2006, Cake Software Foundation, Inc.
 *                     1785 E. Sahara Avenue, Suite 490-204
 *                     Las Vegas, Nevada 89104
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright    Copyright (c) 2006, Cake Software Foundation, Inc.
 * @link         http://www.cakefoundation.org/projects/info/cakephp CakePHP Project
 * @package      cake
 * @subpackage   cake.app.config
 * @since        CakePHP v 0.2.9
 * @version      $Revision: 2134 $
 * @modifiedby   $LastChangedBy: phpnut $
 * @lastmodified $Date: 2006-02-25 13:20:18 -0600 (Sat, 25 Feb 2006) $
 * @license      http://www.opensource.org/licenses/mit-license.php The MIT License
 */

/**
 * Here, we are connecting '/' (base path) to controller called 'Pages',
 * its action called 'display', and we pass a param to select the view file
 * to use (in this case, /app/views/pages/home.thtml)...
 */
$Route->connect ('/?', array('controller'=>'uploads', 'action'=>'index'));
$Route->connect ('/downloads/*', array('controller'=>'texts', 'action'=>'downloads'));

/**
 * ...and connect the rest of 'Pages' controller's urls.
 */
$Route->connect ('/pages/*', array('controller'=>'pages', 'action'=>'display'));

/**
 * Then we connect url '/test' to our test controller. This is helpfull in
 * developement.
 */
$Route->connect ('/tests', array('controller'=>'tests', 'action'=>'index'));

?>
