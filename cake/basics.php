<?php
/* SVN FILE: $Id: basics.php 2477 2006-04-11 15:46:29Z phpnut $ */

/**
 * Basic Cake functionality.
 *
 * Core functions for including other source files, loading models and so forth.
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
 * @subpackage   cake.cake
 * @since        CakePHP v 0.2.9
 * @version      $Revision: 2477 $
 * @modifiedby   $LastChangedBy: phpnut $
 * @lastmodified $Date: 2006-04-11 10:46:29 -0500 (Tue, 11 Apr 2006) $
 * @license      http://www.opensource.org/licenses/mit-license.php The MIT License
 */


/**
 * Basic defines for timing functions.
 */
define('SECOND',  1);
define('MINUTE', 60 * SECOND);
define('HOUR',    60 * MINUTE);
define('DAY',    24 * HOUR);
define('WEEK',    7 * DAY);
define('MONTH',  30 * DAY);
define('YEAR',  365 * DAY);

/**
 * Patch for PHP < 4.3
 */
if (!function_exists("ob_get_clean"))
{
    function ob_get_clean()
    {
        $ob_contents = ob_get_contents();
        ob_end_clean();
        return $ob_contents;
    }
}

/**
 * Loads all models.
 */
function loadModels()
{
    $path = Configure::getInstance();
    if(!class_exists('AppModel'))
    {
        if(file_exists(APP.'app_model.php'))
        {
            require(APP.'app_model.php');
        }
        else
        {
            require(CAKE.'app_model.php');
        }
    }
    if (phpversion() < 5 && function_exists("overload"))
    {
        overload('AppModel');
    }
    $loadedModels = array();
    foreach ($path->modelPaths as $path)
    {
        foreach (listClasses($path) as $model_fn)
        {
            if (!key_exists($model_fn, $loadedModels))
            {
                require ($path.$model_fn);
                if (phpversion() < 5 && function_exists("overload"))
                {
                    list($name) = explode('.', $model_fn);
                    overload(Inflector::camelize($name));
                }
                $loadedModels[$model_fn] = $model_fn;
            }
        }
    }
}

/**
 * Loads a loadPluginController.
 *
 * @param  string  $plugin Name of plugin
 * @return
 */
function loadPluginModels ($plugin)
{
    $pluginAppModel = Inflector::camelize($plugin.'_app_model');
    $pluginAppModelFile = APP.'plugins'.DS.$plugin.DS.$plugin.'_app_model.php';

    if(!class_exists($pluginAppModel))
    {
        if(file_exists($pluginAppModelFile))
        {
            require($pluginAppModelFile);
        }
        else
        {
            die('Plugins must have a class named '. $pluginAppModel);
        }
    }

    if (phpversion() < 5 && function_exists("overload"))
    {
        overload($pluginAppModel);
    }

    $pluginModelDir = APP.'plugins'.DS.$plugin.DS.'models'.DS;

    foreach (listClasses($pluginModelDir) as $modelFileName)
    {
        require ($pluginModelDir.$modelFileName);
        if (phpversion() < 5 && function_exists("overload"))
        {
            list($name) = explode('.', $modelFileName);
            overload(Inflector::camelize($name));
        }
    }
}

/**
 * Loads custom view class.
 *
 */
function loadView ($viewClass)
{
    if(!class_exists($viewClass))
    {
        $paths = Configure::getInstance();
        $file = Inflector::underscore($viewClass).'.php';
        foreach ($paths->viewPaths as $path)
        {
            if(file_exists($path.$file))
            {
                return require($path.$file);
            }
        }
        if(file_exists(LIBS.'view'.DS.$file))
        {
            return require(LIBS.'view'.DS.$file);
        }
        else
        {
            return false;
        }
    }
}

/**
 * Loads a model by CamelCase name.
 */
function loadModel($name)
{
    $name = Inflector::underscore($name);
    $paths = Configure::getInstance();

    if(!class_exists('AppModel'))
    {
        if(file_exists(APP.'app_model.php'))
        {
            require(APP.'app_model.php');
        }
        else
        {
            require(CAKE.'app_model.php');
        }
    }

    foreach ($paths->modelPaths as $path)
    {
        if(file_exists($path.$name.'.php'))
        {
            require ($path.$name.'.php');
            return true;
        }
    }

    return false;
}

/**
 * Loads all controllers.
 */
function loadControllers ()
{
    $paths = Configure::getInstance();
    if(!class_exists('AppController'))
    {
        if(file_exists(APP.'app_controller.php'))
        {
            require(APP.'app_controller.php');
        }
        else
        {
            require(CAKE.'app_controller.php');
        }
    }
    $loadedControllers = array();
    foreach ($paths->controllerPaths as $path)
    {
        foreach (listClasses($path) as $controller)
        {
            if(file_exists($path.$controller.'.php'))
            {
                if (!key_exists($controller, $loadedControllers))
                {
                    require ($path.$controller.'.php');
                    $loadedControllers[$controller] = $controller;
                }
            }
        }
    }
}

/**
 * Loads a controller and its helper libraries.
 *
 * @param  string  $name Name of controller
 * @return boolean Success
 */
function loadController ($name)
{
    $paths = Configure::getInstance();
    if(!class_exists('AppController'))
    {
        if(file_exists(APP.'app_controller.php'))
        {
            require(APP.'app_controller.php');
        }
        else
        {
            require(CAKE.'app_controller.php');
        }
    }
    if($name === null)
    {
        return true;
    }

    if(!class_exists($name.'Controller'))
    {
        $name = Inflector::underscore($name);
        foreach ($paths->controllerPaths as $path)
        {
            if(file_exists($path.$name.'_controller.php'))
            {
                require($path.$name.'_controller.php');
                return true;
            }
        }
        if($controller_fn = fileExistsInPath(LIBS.'controller'.DS.$name.'_controller.php'))
        {
            if(file_exists($controller_fn))
            {
                require($controller_fn);
                return true;
            }
            else
            {
                return false;
            }
        }
    }
    else
    {
        return true;
    }
}

/**
 * Loads a loadPluginController.
 *
 * @param  string  $plugin Name of plugin
 * @param  string  $controller Name of controller to load
 * @return boolean Success
 */
function loadPluginController ($plugin, $controller)
{

    $pluginAppController = Inflector::camelize($plugin.'_app_controller');
    $pluginAppControllerFile = APP.'plugins'.DS.$plugin.DS.$plugin.'_app_controller.php';

    if(!class_exists($pluginAppController))
    {
        if(file_exists($pluginAppControllerFile))
        {
            require($pluginAppControllerFile);
        }
        else
        {
            return false;
        }
    }

    if(empty($controller))
    {
        if(file_exists(APP.'plugins'.DS.$plugin.DS.'controllers'.DS.$plugin.'_controller.php'))
        {
            require(APP.'plugins'.DS.$plugin.DS.'controllers'.DS.$plugin.'_controller.php');
            return true;
        }
    }

    if(!class_exists($controller.'Controller'))
    {
        $controller = Inflector::underscore($controller);
        $file = APP.'plugins'.DS.$plugin.DS.'controllers'.DS.$controller.'_controller.php';
        if(file_exists($file))
        {
            require($file);
            return true;
        }
        elseif(file_exists(APP.'plugins'.DS.$plugin.DS.'controllers'.DS.$plugin.'_controller.php'))
        {
            require(APP.'plugins'.DS.$plugin.DS.'controllers'.DS.$plugin.'_controller.php');
            return true;
        }
        else
        {
            return false;
        }
    }
}

/**
 * Returns an array of filenames of PHP files in given directory.
 *
 * @param  string $path Path to scan for files
 * @return array  List of files in directory
 */
function listClasses($path)
{
    $dir = opendir($path);
    $classes = array();
    while (false !== ($file = readdir($dir)))
    {
        if ((substr($file, -3, 3) == 'php') && substr($file, 0, 1) != '.')
        {
            $classes[] = $file;
        }
    }
    closedir($dir);
	return $classes;
}

/**
 * Loads configuration files
 *
 * @return boolean Success
 */
function config()
{
    $args = func_get_args();
    foreach ($args as $arg)
    {
        if (('database' == $arg) && file_exists(CONFIGS.$arg.'.php'))
        {
            include_once(CONFIGS.$arg.'.php');
        }
        elseif (file_exists(CONFIGS.$arg.'.php'))
        {
            include_once (CONFIGS.$arg.'.php');
            if (count($args) == 1)
            {
                return true;
            }
        }
        else
        {
            if (count($args) == 1)
            {
                return false;
            }
        }
    }

    return true;
}

/**
 * Loads component/components from LIBS.
 *
 * Example:
 * <code>
 * uses('flay', 'time');
 * </code>
 *
 * @uses LIBS
 */
function uses ()
{
    $args = func_get_args();
    foreach ($args as $arg)
    {
        require_once(LIBS.strtolower($arg).'.php');
    }
}

/**
 * Require given files in the VENDORS directory. Takes optional number of parameters.
 *
 * @param string $name Filename without the .php part.
 *
 */
function vendor($name)
{
    $args = func_get_args();
    foreach ($args as $arg)
    {
        if(file_exists(APP.'vendors'.DS.$arg.'.php'))
        {
            require_once(APP.'vendors'.DS.$arg.'.php');
        }
        else
        {
            require_once(VENDORS.$arg.'.php');
        }
    }
}

/**
 * Prints out debug information about given variable.
 *
 * Only runs if DEBUG level is non-zero.
 *
 * @param boolean $var        Variable to show debug information for.
 * @param boolean $show_html    If set to true, the method prints the debug data in a screen-friendly way.
 */
function debug($var = false, $showHtml = false)
{
    if (DEBUG)
    {
        print "\n<pre>\n";
        ob_start();
        print_r($var);
        $var = ob_get_clean();

        if ($showHtml)
        {
            $var = str_replace('<', '&lt;', str_replace('>', '&gt;', $var));
        }

        print "{$var}\n</pre>\n";
    }
}

if (!function_exists('getMicrotime'))
{
/**
 * Returns microtime for execution time checking.
 *
 * @return integer
 */
    function getMicrotime()
    {
      list($usec, $sec) = explode(" ", microtime());
      return ((float)$usec + (float)$sec);
    }
}

if (!function_exists('sortByKey'))
{
/**
 * Sorts given $array by key $sortby.
 *
 * @param  array    $array
 * @param  string  $sortby
 * @param  string  $order  Sort order asc/desc (ascending or descending).
 * @param  integer $type
 * @return mixed
 */
    function sortByKey(&$array, $sortby, $order='asc', $type=SORT_NUMERIC)
    {
        if (!is_array($array))
        {
            return null;
        }

        foreach ($array as $key => $val)
        {
            $sa[$key] = $val[$sortby];
        }

        if ($order == 'asc')
        {
        	asort($sa, $type);
        }
        else
        {
        	arsort($sa, $type);
        }

        foreach ($sa as $key=>$val)
        {
            $out[] = $array[$key];
        }

        return $out;
    }
}

if (!function_exists('array_combine'))
{
/**
 * Combines given identical arrays by using the first array's values as keys,
 * and the second one's values as values. (Implemented for back-compatibility
 * with PHP4)
 *
 * @param  array $a1
 * @param  array $a2
 * @return mixed Outputs either combined array or false.
 */
    function array_combine($a1, $a2)
    {
        $a1 = array_values($a1);
        $a2 = array_values($a2);
        $c1 = count($a1);
        $c2 = count($a2);

        if ($c1 != $c2)
        {
            return false;// different lenghts
        }
        if ($c1 <= 0)
        {
            return false;// arrays are the same and both are empty
        }

        $output = array();

        for ($i = 0; $i < $c1; $i++)
        {
            $output[$a1[$i]] = $a2[$i];
        }

        return $output;
    }
}

/**
 * Convenience method for htmlspecialchars.
 *
 * @param string $text
 * @return string
 */
function h($text)
{
    return htmlspecialchars($text);
}

/**
 * Returns an array of all the given parameters.
 *
 * Example:
 * <code>
 * a('a', 'b')
 * </code>
 *
 * Would return:
 * <code>
 * array('a', 'b')
 * </code>
 *
 * @return array
 */
function a()
{
    $args = func_get_args();
    return $args;
}

/**
 * Constructs associative array from pairs of arguments.
 *
 * Example:
 * <code>
 * aa('a','b')
 * </code>
 *
 * Would return:
 * <code>
 * array('a'=>'b')
 * </code>
 *
 * @return array
 */
function aa()
{
    $args = func_get_args();

    for ($l = 0, $c = count($args); $l < $c; $l++)
    {
        if ($l+1 < count($args))
        {
        	$a[$args[$l]] = $args[$l+1];
        }
        else
        {
        	$a[$args[$l]] = null;
        }
        $l++;
    }
    return $a;
}

/**
 * @deprecated Renamed to aa(). Version 0.10.
 */
function ha()
{
    $args = func_get_args();
    return call_user_func_array('aa', $args);
}

/**
 * Convenience method for echo().
 *
 * @param string $text String to echo
 */
function e($text)
{
    echo $text;
}

/**
 * Convenience method for strtolower().
 *
 * @param string $str String to lowercase
 */
function low($str)
{
    return strtolower($str);
}

/**
 * Convenience method for strtoupper().
 *
 * @param string $str String to uppercase
 */
function up($str)
{
    return strtoupper($str);
}

/**
 * Convenience method for str_replace().
 *
 * @param string $search String to be replaced
 * @param string $replace String to insert
 * @param string $subject String to search
 */
function r($search, $replace, $subject)
{
    return str_replace($search, $replace, $subject);
}

/**
 * Print_r convenience function, which prints out <PRE> tags around
 * the output of given array. Similar to debug().
 *
 * @see    debug()
 * @param array    $var
 */
function pr($var)
{
    if(DEBUG > 0)
    {
        echo "<pre>";
        print_r($var);
        echo "</pre>";
    }
}

/**
 * Display parameter
 *
 * @param  mixed  $p Parameter as string or array
 * @return string
 */
function params($p)
{
    if (!is_array($p) || count($p) == 0)
    {
        return null;
    }
    else
    {
        if (is_array($p[0]) && count($p) == 1)
        {
            return $p[0];
        }
        else
        {
            return $p;
        }
    }
}


/**
 * Merge a group of arrays
 *
 * @param array First array
 * @param array Second array
 * @param array Third array
 * @param array Etc...
 * @return array All array parameters merged into one
 */
function am ()
{
    $r = array();
    foreach (func_get_args() as $a)
    {
        if (!is_array($a))
        {
        	$a = array($a);
        }
        $r = array_merge($r, $a);
    }
    return $r;
}

/**
 * Returns the REQUEST_URI from the server environment, or, failing that,
 * constructs a new one, using the PHP_SELF constant and other variables.
 *
 * @return string URI
 */
function setUri()
{
    if (env('HTTP_X_REWRITE_URL'))
    {
        $uri = env('HTTP_X_REWRITE_URL');
    }
    elseif (env('REQUEST_URI'))
    {
        $uri = env('REQUEST_URI');
    }
    else
    {
        if (env('argv'))
        {
            $uri = env('argv');
            if (defined('SERVER_IIS'))
            {
                $uri = BASE_URL.$uri[0];
            }
            else
            {
                $uri = env('PHP_SELF') .'/'. $uri[0];
            }
        }
        else
        {
            $uri = env('PHP_SELF') .'/'. env('QUERY_STRING');
        }
    }
    return $uri;
}

/**
 * Gets an environment variable from available sources.
 * Used as a backup if $_SERVER/$_ENV are disabled.
 *
 * @param  string $key Environment variable name.
 * @return string Environment variable setting.
 */
function env($key)
{
    if (isset($_SERVER[$key]))
    {
        return $_SERVER[$key];
    }
    elseif (isset($_ENV[$key]))
    {
        return $_ENV[$key];
    }
    elseif (getenv($key) !== false)
    {
        return getenv($key);
    }
    if ($key == 'DOCUMENT_ROOT')
    {
        $offset = 0;
        if (!strpos(env('SCRIPT_NAME'), '.php'))
        {
            $offset = 4;
        }
        return substr(env('SCRIPT_FILENAME'), 0, strlen(env('SCRIPT_FILENAME')) - (strlen(env('SCRIPT_NAME')) + $offset));
    }
    if ($key == 'PHP_SELF')
    {
        return r(env('DOCUMENT_ROOT'), '', env('SCRIPT_FILENAME'));
    }
    return null;
}

if (!function_exists('file_get_contents'))
{
/**
 * Returns contents of a file as a string.
 *
 * @param  string  $fileName        Name of the file.
 * @param  boolean $useIncludePath Wheter the function should use the
 *                                 include path or not.
 * @return mixed    Boolean false or contents of required file.
 */
    function file_get_contents($fileName, $useIncludePath = false)
    {
        $res = fopen($fileName, 'rb', $useIncludePath);
        if ($res === false)
        {
            trigger_error('file_get_contents() failed to open stream: No such file or directory', E_USER_WARNING);
            return false;
        }

        clearstatcache();

        if ($fileSize = @filesize($fileName))
        {
            $data = fread($res, $fileSize);
        }
        else
        {
            $data = '';
            while (!feof($res))
            {
                $data .= fread($res, 8192);
            }
        }

        return "$data\n";
    }
}

if (!function_exists('file_put_contents'))
{
/**
 * Writes data into file.
 *
 * If file exists, it will be overwritten. If data is an array, it will be
 * join()ed with an empty string.
 *
 * @param string $fileName File name.
 * @param mixed  $data     String or array.
 */
    function file_put_contents($fileName, $data)
    {
        if (is_array($data))
        {
            $data = join('', $data);
        }
        $res = @fopen($fileName, 'w+b');
        if ($res)
        {
            @fwrite($res, $data);
        }
    }
}

/**
 * Reads/writes temporary data to cache files or session.
 *
 * @param  string $path    File path within /tmp to save the file.
 * @param  mixed  $data    The data to save to the temporary file.
 * @param  mixed  $expires A valid strtotime string when the data expires.
 * @param  string $target  The target of the cached data; either 'cache' or 'public'.
 * @return mixed  The contents of the temporary file.
 */
function cache($path, $data = null, $expires = '+1 day', $target = 'cache')
{
    if (!is_numeric($expires))
    {
        $expires = strtotime($expires);
    }

    switch (strtolower($target))
    {
        case 'cache':
            $filename = CACHE . $path;
        break;
        case 'public':
            $filename = WWW_ROOT . $path;
        break;
    }

    $now      = time();
    $timediff = $expires - $now;
    $filetime = @filemtime($filename);

    if ($data == null)
    {
// Read data from file
        if (file_exists($filename) && $filetime !== false)
        {
            if ($filetime + $timediff < $now)
            {
// File has expired
                @unlink($filename);
            }
            else
            {
                $data = file_get_contents($filename);
            }
        }
    }
    else
    {
        file_put_contents($filename, $data);
    }
    return $data;
}

/**
 * Used to delete files in the cache directories, or clear contents of cache directories
 *
 * @param mixed $params As String name to be searched for deletion, if name is a directory all files in directory will be deleted.
 *                      If array, names to be searched for deletion.
 *                      If clearCache() without params, all files in app/tmp/cache/views will be deleted
 *
 * @param string $type Directory in tmp/cache defaults to view directory
 * @param string $ext The file extension you are deleting
 * @return true if files found and deleted false otherwise
 */
function clearCache($params = null, $type = 'views', $ext = '.php')
{
    if(is_string($params) || $params === null)
    {
        $params = preg_replace('/\/\//', '/', $params);
        $cache = CACHE.$type.DS.$params;
        if(is_file($cache.$ext))
        {
            @unlink($cache.$ext);
            return true;
        }
        else if(is_dir($cache))
        {
            $files = glob("$cache*");
            if ($files === false)
            {
                return false;
            }

            foreach($files as $file)
            {
                if(is_file($file))
                {
                    @unlink($file);
                }
            }
            return true;
        }
        else
        {
            $cache = CACHE.$type.DS.'*'.$params.'*'.$ext;
            $files = glob($cache);
            if ($files === false)
            {
                return false;
            }
            foreach($files as $file)
            {
                if(is_file($file))
                {
                    @unlink($file);
                }
            }
            return true;
        }
    }
    else if(is_array($params))
    {
        foreach ($params as $key => $file)
        {
            $file = preg_replace('/\/\//', '/', $file);
            $cache = CACHE.$type.DS.'*'.$file.'*'.$ext;
            $files[] = glob($cache);
        }
        if(!empty($files))
        {
            foreach ($files as $key => $delete)
            {
                if(is_array($delete))
                {
                    foreach($delete as $file)
                    {
                        if(is_file($file))
                        {
                            @unlink($file);
                        }
                    }
                }
            }
            return true;
        }
        else
        {
            return false;
        }
    }
    else
    {
        return false;
    }
}

/**
 * Recursively strips slashes from all values in an array
 *
 * @param unknown_type $value
 * @return unknown
 */
function stripslashes_deep($value)
{
    if (is_array($value))
    {
        return array_map('stripslashes_deep', $value);
    }
    else
    {
        return stripslashes($value);
    }
}

/**
 * Returns a translated string if one is found,
 * or the submitted message if not found.
 *
 * @param  unknown_type $msg
 * @param  unknown_type $return
 * @return unknown
 * @todo Not implemented in 0.10.x.x
 */
    function __($msg, $return = null)
    {
        if(is_null($return))
        {
            echo ($msg);
        }
        else
        {
            return $msg;
        }
    }

/**
 * Counts the dimensions of an array
 *
 * @param array $array
 * @return int The number of dimensions in $array
 */
function countdim($array)
{
    if (is_array(reset($array)))
    {
        $return = countdim(reset($array)) + 1;
    }
    else
    {
        $return = 1;
    }
    return $return;
}

/**
 * Shortcut to Log::write.
 */
function LogError ($message)
{
    if(!class_exists('CakeLog'))
    {
        uses('cake_log');
    }

    $bad = array("\n", "\r", "\t");
    $good = ' ';
    CakeLog::write('error', str_replace($bad, $good, $message));
}

/**
 * Searches include path for files
 *
 * @param string $file
 * @return Full path to file if exists, otherwise false
 */
function fileExistsInPath ($file)
{
    $paths = explode(PATH_SEPARATOR, ini_get('include_path'));
    foreach ($paths as $path)
    {
        $fullPath = $path . DIRECTORY_SEPARATOR . $file;
        if (file_exists($fullPath))
        {
            return $fullPath;
        }
        elseif (file_exists($file))
        {
            return $file;
        }
    }
    return false;
}

/**
 * Convert forward slashes to underscores and removes first and last underscores in a string
 *
 * @param string
 * @return string with underscore remove from start and end of string
 */
function convertSlash($string)
{
    $string = preg_replace('/\/\//', '/', $string);
    $string = str_replace('/', '_', $string);
    $pos = strpos($string, '_');
    $pos1 = strrpos($string, '_');
    $char = strlen($string) -1;
    if($pos1 == $char)
    {
        $string = substr($string, 0, $char);
    }
    if ($pos === 0)
    {
        $string = substr($string, 1);
    }
    return $string;
}

?>