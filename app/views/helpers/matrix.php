<?php
class MatrixHelper extends Helper
{
	var $header;
	var $content;
	var $rows;
	var $cols;
	var $rowNames;
	var $rowNameCounter = 0;
	
	var $helpers = array('Html');
		
	
	function render($contentData, $headerData = null, $columnData = null,
					$className = "matrixRender",
					$emptyMessage = "No hay datos disponibles", $renderEmpty = false, $id="", $style="")
	{	
		$this->content = $contentData;
		$acumString = "<table class='".$className."' border='1' id='".$id."' style='".$style."'><thead></thead><tbody>";
		if($headerData != null)
		{
			$this->cols = count($headerData);
		}
		else
		{
			$this->cols = count($arreglo);
		}
		$acum = 0;
		foreach($contentData as $row)
		{
			$acum += count($row);
			if(count($row) > $this->rows) $this->rows = count($row);
		}
		if($columnData != null)
		{
			$this->rows = count($columnData); 
			$this->cols += 1;
			$extraColumns = 1;
		}
		$this->rowNames = $columnData;
		
		
		if($acum == 0 && !$renderEmpty)
		{
			$acumString .= $this->renderHeaderColumns($headerData);
			$acumString .= "<tr><td class='emptyField' colspan='".$this->cols."'>".$emptyMessage."</td></tr>";
		}
		else
		{
			$acumString .= $this->renderHeaderColumns($headerData,$extraColumns);
			$acumString .= $this->renderContent();
		}
		
		$acumString .= "</tbody><tfoot></tfoot></table>";
		return $acumString;
	}
	
	function renderContent()
	{
		$cont = array();
		
		if($this->rowNames != null)
		{
			$cont[0] = $this->rowNames;
			
		}
		foreach($this->content as $row)
		{
			$cont[count($cont)] = $row;
		}
		$acumString="";
		
		for($i = 0; $i < $this->rows; $i++)
		{
			$acumString .= "<tr>";
			
			foreach($cont as $row)
			{
				if(!isset($row[$i]))
					$cell = null;
				else $cell = $row[$i];
				
				
				// [Diego Jorquera] Agregada lectura de clases CSS opcionales desde objeto
				/*if (property_exists($cell, 'css_class') && $cell->css_class != null) {
					$acumString .= "<td class='" . $cell->css_class . "'>";
				} else {
					$acumString .= "<td class='cal_vacio'>";
				}
				

				if ($cell != null)
				{
					if(method_exists($cell,'render'))
					{
						$acumString .= $cell->render();
					}
					else $acumString .= $cell;
				}
				$acumString .= "</td>";*/
				
				
				
				
				// INICIO COMENTAR
				$acumString .= "<td>";
				if($cell != null)
				{
					if(method_exists($cell,'render'))
					{
						$acumString .= $cell->render();
					}
					else $acumString .= $cell;
				}
				$acumString .= "</td>";
				// FIN COMENTAR
				
				
			}
			
			$acumString .= "</tr>";	
		}
		
		return $acumString;
		
	}
	
	function renderHeaderColumns($columnNames,$addExtraColum=0)
	{
		$acumString = "<tr>";
		for($i = 0 ; $i < $addExtraColum ; $i++)
		{
			$acumString .= "<th></th>";
		}
		foreach($columnNames as $name)
		{
			$acumString .= "<th>".$name."</th>";
		}
		$acumString .= "</tr>";
		return $acumString;
	}
	
	function matrixWrapper($content)
	{
		$headerData = array('Lunes','Martes','Miercoles','Jueves','Viernes');
		$columnData = array('9:00','10:00','11:00','12:00','13:00','14:00','15:00','16:00','17:00','18:00');
		$this->render($content, $headerData, $columnData);
	}

}
?>