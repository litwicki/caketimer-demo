<?php
/* SVN FILE: $Id: aros_aco.php 2026 2006-02-18 23:42:21Z phpnut $ */

/**
 * Short description for file.
 *
 * Long description for file
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
 * @subpackage   cake.cake.libs.controller.components.dbacl.models
 * @since        CakePHP v 0.10.0.1232
 * @version      $Revision: 2026 $
 * @modifiedby   $LastChangedBy: phpnut $
 * @lastmodified $Date: 2006-02-18 17:42:21 -0600 (Sat, 18 Feb 2006) $
 * @license      http://www.opensource.org/licenses/mit-license.php The MIT License
 */

/**
 * Short description for file.
 *
 * Long description for file
 *
 * @package    cake
 * @subpackage cake.cake.libs.controller.components.dbacl.models
 * @since      CakePHP v 0.10.0.1232
 *
 */

class ArosAco extends AppModel
{

/**
 * Enter description here...
 *
 * @var unknown_type
 */
    var $name = 'ArosAco';
/**
 * Enter description here...
 *
 * @var unknown_type
 */
    var $useTable = 'aros_acos';
/**
 * Enter description here...
 *
 * @var unknown_type
 */
    var $belongsTo = 'Aro,Aco';
}

?>