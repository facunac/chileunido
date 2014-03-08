<?php
class RendereableComponent extends Object
{
	var $rendereableComponents = array();
	var $components = array('Queue');
	function startup(&$controller)    
	{
	
	}
	function render()
	{	
		$returnString = "";
		foreach($this->rendereableComponents as $comp)
		{
			if(method_exists($comp,'render'))
			{
				$returnString .= $comp->render();
			}
			else
			{
				$returnString .= $comp;
			}
		}
		return $returnString;
	}
	
	function add_rendereable($rendereable)
	{
		$this->rendereableComponents[count($this->rendereableComponents)] = $rendereable; 
	}
	
	function obliterate() {
		$this->rendereableComponents = array();
	}
	
}
?>