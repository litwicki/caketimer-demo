<?php

/**
 * CakeTimerComponent by Felix Geisenörfer
 *
 * http://www.fg-webdesign.de
 * http://www.thinkingphp.org
 *
 * Licensed under the MIT License: http://www.opensource.org/licenses/mit-license.php* 
 * 
 * Parts of this source are derrived/taken from uber-uploader, which is licensed under the 
 * Mozilla Public License Version 1.1 
 * 
 * see http://www.mozilla.org/MPL/ and https://sourceforge.net/projects/uber-uploader
 * 
 */
class CakeTimerComponent extends Object 
{
    /**
     * Contains the controller this component is intitialized in
     *
     * @var object
     */
    var $controller;
    
    /**
     * Contains the path where the finished uploads are stored
     *
     * @var string
     */
    var $uploadDir;
    
    /**
     * Contains the path where the temporary files are stored
     *
     * @var string
     */
    var $tempDir;
    
    /**
     * The timeout in seconds after a download is considered broken (if not data transfer happens)
     *
     * @var number
     */
    var $timeout = 15;
    
    
    /**
     * The Startup function for our controller, that's where we initiliaze
     *
     * @param object $controller
     */
    function startup(&$controller)
    {
        $this->controller = &$controller;

        if (!$this->uploadDir)
            $this->uploadDir = APP.WEBROOT_DIR.DS.'uploads';
            
        if (!$this->tempDir)
            $this->tempDir = APP.WEBROOT_DIR.DS.'uploads';            
    }
    
    /**
     * Generates a unique upload_sid and set's it as a controller variable
     *
     */
    function provideUploadSid()
    {
        $this->controller->set('upload_sid', md5(uniqid(mt_rand(), true)));
    }
    
    /**
     * Handles the Progress Communication for File Uploads
     *
     */
    function handleProgress()
    {                
        $this->controller->autoRender = false;        
        
        // Catch the parameters
        $upload_sid = @$this->controller->params['url']['upload_sid'];
        $file_type  = @$this->controller->params['url']['type'];
        
        if (empty($upload_sid) || empty($file_type))
            die('Parameter Error');
        
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Cache-Control: post-check=0, pre-check=0', FALSE);
        header('Pragma: no-cache');       
        
        if ($this->__uploadDone($file_type, $upload_sid))
        {
            echo "done";   
            
            for ($t=0; $t<$this->timeout; $t++)
            {
                if (file_exists($this->uploadDir.DS.$upload_sid.'.filesizes'))
                {               
                    echo "\n".file_get_contents($this->uploadDir.DS.$upload_sid.'.filesizes');
                    unlink($this->uploadDir.DS.$upload_sid.'.filesizes');
                    exit;
                }
                else 
                {
                    sleep(1);
                }
            }
            
        }

        $bytesUploaded = $this->__bytesUploaded($upload_sid);
        $bytesTotal    =  $this->__bytesTotal($upload_sid);
        
        $log = explode(":", $bytesTotal);
        
        if ($bytesTotal==0)
        {        
            echo "error\n".
                'progressTracker timeout: max is '.$this->timeout.' seconds';
           
            exit;
        }
        
        if (@$log[0]=='error')
        {
            echo "error\n".
                 $log[1]."\n";
                 
            $this->deleteUploadSid($upload_sid);
                 
            exit;
        }
        
        echo $bytesUploaded."\n".
             $bytesTotal."\n";    
    }
    
    /**
     * Returns true if an upload is done
     *
     * @param string $file_type
     * @param string $upload_sid
     * @return boolean
     */
    function __uploadDone($file_type, $upload_sid)
    {        
        if (file_exists($this->uploadDir.DS.$upload_sid.".filesizes"))
            return true;
    
        if ($file_type=='new')
            return false;
            
        $tmp_dir = $this->tempDir.DS.$upload_sid;                
    	if (!is_dir($tmp_dir))
    	   return true;

    	if (!is_file($tmp_dir.DS.'flength'))
    	   return true;
    	   
    	if ($this->__bytesUploaded($upload_sid) >= $this->__bytesTotal($upload_sid))
    	   return true;  
    	   
    	   
    	if ($file_type=='progress')
    	{
        	if ($this->__bytesUploaded($upload_sid)==0)
        	{
        	   if (@filesize($this->uploadDir.DS.$upload_sid.".params")>0)
        	       return true;
        	}
    	}
    	    	       	   
        return false;
    }   
       
    /**
     * Contains the uploaded Bytes of a given POST with upload_sid
     *
     * @param string $upload_sid
     * @return number
     */
    function __bytesUploaded($upload_sid)
    {
        $tmp_dir = $this->tempDir.DS.$upload_sid;
                
    	$bytesRead = 0;
    	
    	clearstatcache();
    	if(is_dir($tmp_dir))
    	{
    		if($handle = opendir($tmp_dir))
    		{
    			while(false !== ($file = readdir($handle)))
    			{
    				if($file != '.' && $file != '..' && $file != 'flength')
    				{     			
    				    $bytesRead += filesize($tmp_dir.DS.$file); 
    				}
    			}
    			closedir($handle);
    		}
    	}
                                  
    	$bytesRead = trim($bytesRead);
                    
    	return $bytesRead;     
    }

    /**
     * Returns the Total Amount of Bytes a POST (with given upload_sid) contains
     *
     * @param string $upload_sid
     * @return number
     */
    function __bytesTotal($upload_sid)
    {
        $temp_dir_sid = $this->tempDir.DS.$upload_sid;
        $flength_file = $temp_dir_sid.DS."flength";
        $flength_file_exists = false;
        $total_upload_size = 0;
        
        // Keep trying to read the flength file for 15 secs
        for($i = 0; $i < $this->timeout; $i++){
        	if(is_file($flength_file) && $fp = fopen($flength_file, "r")){ 
        		$flength_file_exists = true; 
        	
        		$total_upload_size = @fread($fp, filesize($flength_file)); //Read the size of the upload from the flength file
        		fclose($fp);
        		
        		clearstatcache();
        		break;
        	}
        	else{ sleep(1); } //Couldn't find the flength file so wait 1 second and try again
        	
        	clearstatcache(); 
        }    	
        
        return $total_upload_size;
    }    
    

    /**
     * Encrypts the file where the original Post Data is stored and returns it as an array
     * 
     * $deletePost: Determines weather the Post Data should be deleted or not, useful for having several upload handlers
     *
     * @param boolean $deletePost
     * @return array
     */
    function getPostData($sid = null, $deletePost = true)
    {
        $up_dir = $this->uploadDir.DS;

        if (empty($sid))               
            $tmp_sid = $this->controller->params['url']['tmp_sid'];
        else 
            $tmp_sid = $sid;
    
    	$param_array = array();
    	$buffer = "";
    	$key = "";
    	$value = "";
    	$paramFileName = $up_dir . $tmp_sid . ".params";
    	$fh = @fopen($paramFileName, 'r');
    	
    	if (!$fh)
    	   return false;
    	
    	while(!feof($fh))
    	{
    		$buffer = fgets($fh, 4096);
    		@list($key, $value) = @explode('=', trim($buffer));
    		$value = str_replace("~EQLS~", "=", $value);
    		$value = str_replace("~NWLN~", "\r\n", $value);
    		
    		if((!empty($key)) && (!empty($value)))
    		{
	            $param_array[$key] = $value;
    		}
    	}
    
    	fclose($fh);
    	
    	if ($deletePost)    	
    	   unlink($paramFileName);
    	   
    	if (isset($this->controller->params['url']['close_pop']))
    	   $this->__createFileSizesFile($tmp_sid, $param_array);
    	
    	return $param_array;
    }    
    
    /**
     * This function creates a file with the information about the uploaded files
     * Necessary because the temporary .params file might gets deleted by the
     * controller before the user client (browser) will get the async request in
     * for asking about the uploaded files.
     *
     * @param string $sid
     * @param array $files
     */
    function __createFileSizesFile($sid, $files)
    {
        if (file_exists($this->uploadDir.DS.$sid.'.filesizes'))   
            return true;
            
            $data = array();
            $fileCounter = 0;            
            while (true)
            {            
                if (isset($files['upfile_'.$fileCounter]))
                {                
                    $fn = $files['upfile_'.$fileCounter];       
                    $data[] = $fn;
                    $data[] = @filesize($this->uploadDir.DS.$fn);
                }
                else 
                    break;
                    
                $fileCounter++;
            }
            
            file_put_contents($this->uploadDir.DS.$sid.'.filesizes', join("\n", $data));
    }
    
    /**
     * Deletes a temporary upload session
     *
     * @param string $sid
     */
    function deleteUploadSid($sid)
    {        
       $tmp_dir = $this->tempDir.DS.$sid;
       
	   if(is_dir($tmp_dir))
	   {
	       if($handle = opendir($tmp_dir))
	       {
		        while(false !== ($file_name = readdir($handle)))
		        {
				    if($file_name != "." && $file_name != "..")
				        unlink($tmp_dir . '/' . $file_name); 
		        }
			}
			closedir($handle);
			
			rmdir($tmp_dir);
		}        
    }
}

?>