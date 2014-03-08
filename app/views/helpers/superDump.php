<?php
class SuperDumpHelper extends Helper
{	
	function dumpIt($dar,$depth=0)
	{
		if($depth == 0)
		{
			echo "<div style='text-align:left;border:1px solid black;'>";
		}
		echo '<ul>';
		foreach($dar as $key => $el)
		{
			if(is_array($el))
			{
				echo '<li>'.$key.'';
				$this->superDump($el,$depth+1);
				echo '</li>';
			}
			else
			{
				echo '<li>'. $key . '>'. $el . '</li>';
			}
		}
		echo '</ul>';
		if($depth == 0)
		{
			echo "</div>";
		}
	}
}
?>