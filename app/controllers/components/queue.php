<?php
class QueueComponent extends Object
{
	var $elements = Array();
	var $elementCount = 0;
	var $startElement = 0;
	function startup(&$controller)    
	{
	
	}
	function push($element)
	{
		$this->elements[$this->elementCount++] = $element;
	}
	function peek()
	{
		return $this->element[$this->startElement];
	}
	function pop()
	{
		if($this->count() > 0)
			return $this->element[$this->startElement++];
		return null;
	}
	function count()
	{
		return $this->elementCount - $this->startElement;
	}
}
?>