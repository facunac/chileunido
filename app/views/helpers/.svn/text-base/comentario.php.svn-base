<?php 
class ComentarioHelper extends Helper
{
	var $helpers=array('Html','Form');
	function desplegarComentarios($personaId)
	{
		$html_desplegado ="<div ='comments'>";
		$persona = $this->view->controller->Persona->read(null,$personaId);
		
		if(count($persona['Comentario'])== 0)
		{
			$html_desplegado .= "<p>No hay entradas de Historial</p>";
		}
		else
		{
			$html_desplegado .= $this->getComments($persona['Comentario']);
		}
		$meSelf = $this->view->controller->Session->read('cod_voluntario');
		$html_desplegado .= $this->commentAggregator($personaId,$meSelf);
		return $html_desplegado . "</div>";
	}
	function getComments($comments)
	{
		$dhtml = "";
		foreach($comments as $com)
		{
			$dhtml .= "" . $this->displayComment($com) . "";
			
		}
		return $dhtml."";
	}
	function displayComment($com)
	{
		$dhtml = "<div class='comment'>";
		if($com['cod_creador'] > 0)
		{
			$persona = $this->view->controller->Persona->read(null,$com[cod_creador]);
			$nolink = false;
		}	
		else
		{
			$persona = array('Persona' => array ('nom_nombre' => 'Sistema','nom_appat'=> ''));
			$nolink = true;
		}
		$dhtml .= "<p style='font-size:smaller'> El ".$com['fec_creado']. ", <a href='".$com[cod_creador] . "'> " .$persona['Persona']['nom_nombre']." ".$persona['Persona']['nom_appat'] . "</a> dijo:</p>";
		$dhtml .= "<p><q>".$com['nom_comentario']."</q></p>";
		return $dhtml . "</div>";
	}
		
	function commentAggregator($person,$creator)
	{
		$dhtml = "";

		$dhtml .= '<form action="'. $this->Html->url('/comentarios/add').'" method="post">';
		$dhtml .= '	<div class="optional"> ';
		//$dhtml .= 		$form->labelTag('Comentario/nom_comentario', 'Comentario');
		$dhtml .= 	 	$this->Html->textarea('Comentario/nom_comentario', array('size' => '60'));
		$dhtml .= 		$this->Html->tagErrorMsg('Comentario/nom_comentario', 'Please enter the Nom Comentario.');
		$dhtml .= '	</div>';
		$dhtml .= '	<div class="optional"> ';
		$dhtml .= 	 	$this->Html->hidden('Comentario/cod_persona', array('value' => $person));
		$dhtml .= '	</div>';
		$dhtml .= '	<div class="optional"> ';
		$dhtml .= 	 	$this->Html->hidden('Comentario/cod_creador', array('value' => $creator));
		$dhtml .= '	</div>';
		$dhtml .= '	<div class="submit">';
		$dhtml .= 		$this->Html->submit('Agregar Comentario');
		$dhtml .= '	</div>';
		
		return $dhtml;
	}

}
?>
