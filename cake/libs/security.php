<?php
/* SVN FILE: $Id: security.php 2195 2006-03-08 03:13:32Z phpnut $ */

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
 * @subpackage   cake.cake.libs
 * @since        CakePHP v .0.10.0.1233
 * @version      $Revision: 2195 $
 * @modifiedby   $LastChangedBy: phpnut $
 * @lastmodified $Date: 2006-03-07 21:13:32 -0600 (Tue, 07 Mar 2006) $
 * @license      http://www.opensource.org/licenses/mit-license.php The MIT License
 */

/**
 * Short description for file.
 *
 * Long description for file
 *
 * @package    cake
 * @subpackage cake.cake.libs
 * @since      CakePHP v .0.10.0.1233
 */
class Security extends Object
{

    function &getInstance()
    {
        static $instance = array();

        if (!$instance)
        {
            $instance[0] =& new Security;
        }
        return $instance[0];
    }

    function inactiveMins()
    {
        //$security =& Security::getInstance();
        switch (CAKE_SECURITY)
        {
            case 'high':
                return 10;
            break;
            case 'medium':
                return 100;
            break;
            case 'low':
            default :
                return 300;
            break;
        }
    }

    function generateAuthKey()
    {
        return Security::hash(uniqid(rand(), true));
    }

    function validateAuthKey($authKey)
    {
        return true;
    }


    function hash($string, $type='sha1')
    {
        $type = strtolower($type);
        if ($type == 'sha1')
        {
            if (function_exists('sha1'))
            {
                return sha1($string);
            }
            else
            {
                $type = 'sha256';
            }
        }
        if ($type == 'sha256')
        {
            if (function_exists('mhash'))
            {
                return bin2hex(mhash(MHASH_SHA256, $string));
            }
            else
            {
                 $type = 'md5';
            }
        }
        if ($type == 'md5')
        {
            return md5($string);
        }
    }

    function cipher($text, $key)
    {
        if (!defined('CIPHER_SEED'))
        {
//This is temporary will change later
            define('CIPHER_SEED', 'mKEZGy8AB8FErX4t');
        }
        srand(CIPHER_SEED);

        $out = '';
        for($i = 0; $i < strlen($text); $i++)
        {
            for($j = 0; $j < ord(substr($key, $i % strlen($key), 1)); $j++)
            {
                $toss = rand(0, 255);
            }

            $mask = rand(0, 255);
            $out .= chr(ord(substr($text, $i, 1)) ^ $mask);
        }
        return $out;
    }
}
?>