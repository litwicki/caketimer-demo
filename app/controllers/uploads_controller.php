<?php
/**
 * Short description for file.
 *
 * An example usage of the CakeTimer in a simple UploadsController
 * 
 * PHP versions 4 and 5
 *
 * CakeTimer:  
 * Copyright (c) 2006, Felix Geisendörfer (FelixGe@web.de)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @license      http://www.opensource.org/licenses/mit-license.php The MIT License
 */


class UploadsController extends AppController 
{
    var $name = "Uploads";
    var $uses = array();
    var $helpers = array('Javascript');
    var $pageTitle = 'Demo Page';
    
    // Needs to be included to make CakeTimer work
    var $components = array('CakeTimer');
    
    function beforeFilter()
    {
        //  Setting the Config for the CakeTimer component
        $this->CakeTimer->uploadDir = APP.WEBROOT_DIR.DS.'uploads';;
        $this->CakeTimer->tempDir = APP.WEBROOT_DIR.DS.'uploads';;        
    }
    
    function index()
    {
        // The CakeTimer upload forms require a Sid we need to provide
        $this->CakeTimer->provideUploadSid();  
        
        // This tells our layout to include the js libraries (not part of CakeTimer)
        $this->set('jsUseYahoo', true);
    }
    
    function progress()
    {        
       // This does the AJAX communication for the progress updates
       $this->CakeTimer->handleProgress();
    }
    
    function upload()
    {    
        $this->autoRender = false;
        
        // Here we get the data from the form the user submitted
        $data = $this->CakeTimer->getPostData(null, true);
        
         // Delete everything right away
         $counter = 0;
        while (true)
        {
            if (isset($data['upfile_'.$counter]))
                unlink($this->CakeTimer->uploadDir.DS.$data['upfile_'.$counter]);
            else 
                break;
                
            $counter++;
        }        
        
        debug('Upload Complete');
        debug($data);
    }    
}


?>