<?php
/* SVN FILE: $Id: pages_controller.php 2407 2006-03-26 09:59:40Z phpnut $ */

/**
 * Short description for file.
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
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
 * @subpackage   cake.cake.libs.controller
 * @since        CakePHP v 0.2.9
 * @version      $Revision: 2407 $
 * @modifiedby   $LastChangedBy: phpnut $
 * @lastmodified $Date: 2006-03-26 03:59:40 -0600 (Sun, 26 Mar 2006) $
 * @license      http://www.opensource.org/licenses/mit-license.php The MIT License
 */


/**
 * Short description for class.
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package    cake
 * @subpackage cake.cake.libs.controller
 */
class PagesController extends AppController{

/**
 * Enter description here...
 *
 * @var unknown_type
 */
    var $name = 'Pages';

/**
 * Enter description here...
 *
 * @var unknown_type
 */
    var $helpers = array('Html');

/**
 * This controller does not use a model
 *
 * @var $uses
 */
    var $uses = null;


/**
 * Displays a view
 *
 */
    function display()
    {
        if (!func_num_args())
        {
            $this->redirect('/');
        }
        $path = func_get_args();
        if (!count($path))
        {
            $this->redirect('/');
        }
        $this->set('page', $path[0]);
        $this->set('subpage', empty($path[1])? null: $path[1]);
        $this->set('title', ucfirst($path[count($path)-1]));
        $this->render(join('/', $path));
    }
}
?>