<?php
/**
 * Page class
 *
 * @package guestbook
 * @author Alexander Belyaev
 **/
class Page {
    
    /**
     * Page title, content of the title tags
     *
     * @var string
     **/
    public $title;
    
    private $template_name;
    private $values;
    private $stylesheets;
    private $scripts;
    
    /**
     * Page
     *
     * @param string Template filename
     * @param array Data that we want to insert to template
     * @return void
     **/
    public function __construct($template_name, $values) {            
        if (file_exists($template_name)) {
            $this->template_name = $template_name;
        } else throw new Exception("Couldn't find $template_name template");
        $this->values = $values;        
    }
    
    /**
     * Adding CSS for page
     *
     * @param string CSS filename
     * @return void
     **/
    public function addStylesheet($css_file) {
    	if (file_exists($css_file)) {
    		$this->stylesheets[] = $css_file;	
    	}        
    }

    /**
     * Adding JavaScript to page
     *
     * @param string JS filename
     * @return void
     **/
    public function addJavascript($js_file) {
    	if (file_exists($js_file)) {
        	$this->scripts[] = $js_file;
    	}
    }
    
    /**
     * Getting page CSS
     *
     * @return void
     **/    
    public function getStylesheets() {
        if (!empty($this->stylesheets)) {
            return $this->stylesheets;
        } else {            
            return null;
        }
    }

    /**
     * Getting page oj JS.
     *
     * @return void
     **/
    public function getJavascripts() {
        if (!empty($this->scripts)) {
            return $this->scripts;
        } else {            
            return null;
        }        
    }
    
    /**
     * Process the template and write result to $html
     *
     * @return void
     **/
    public function render() {
        ob_start();
        include $this->template_name;
        $html = ob_get_contents();
        ob_end_clean();
        return $html;        
    }
    
}

?>