<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

	class Xml{
		
		var $document;
  		var $filename;
  		var $xml;
		
		function Xml () {
			
  		}
  		
  		function load ($file) {
    
	   		$bad  = array('|//+|', '|\.\./|');
    		$good = array('/', '');
		    $file = APPPATH.preg_replace ($bad, $good, $file).'.xml';
		
		    echo "filepath: ".$file;
		    
		    if (! file_exists ($file)) {
		    	return false;
		    }
		
		    $this->document = utf8_encode(file_get_contents($file));
		    $this->filename = $file;

    		return true;
  		}  
  		
  		function parser(){
  			$this->xml = new SimpleXMLElement($this->document);	
  			return $this->xml;
  		}
	}
?>