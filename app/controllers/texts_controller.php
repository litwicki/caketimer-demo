<?php
    

class TextsController extends AppController 
{
    var $name = "Texts";
    var $uses = array();    
    
    function documentation()
    {
        $this->pageTitle = 'Documentation';
    }
    
    function  faq()
    {
        $this->pageTitle = 'FAQ';
    }
    
    function  downloads()
    {
        $this->pageTitle = 'Downloads';
    }
}
?>