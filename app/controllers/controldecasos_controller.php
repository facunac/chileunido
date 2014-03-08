<?php
	class ControlDeCasosController extends AppController
	{
		var $name = "ControlDeCasos";
		var $uses = array("Seguimiento", "Caso", "Programa", "Persona", "Tipocaso", "Voluntario", "Beneficiario");

		function index($msg=null)
		{
			// [Javier] Se mandan algunas variables al layout para que se vea bien COPIAR SIEMPRE Y CAMBIAR NOMBRE PAGINA
			$this->escribirHeader("Control de Casos");
			
			$this->set('msg_for_layout',$msg);
			
			// [Javier] CASOS PARA HOY:
			// [Javier] Se obtiene todo lo necesario, en una sola query, para los eventos de hoy:
			$query_casos_hoy = "
				SELECT 
				s.cod_voluntario AS cod_voluntario, 
				CONCAT( pv.nom_nombre, ' ', pv.nom_appat ) AS nom_voluntario, 
				p.nom_programa as nom_programa_voluntario, 
				a.cod_actividad AS `cod_actividad`, 
				a.nom_actividad AS nom_actividad, 
				a.tip_actividad AS tip_actividad, 
				s.fec_ejecucion AS fec_ejecucion, 
				c.cod_beneficiario AS cod_beneficiario, 
				c.est_caso AS est_caso, 
				CONCAT( pb.nom_nombre, ' ', pb.nom_appat ) AS nom_beneficiario, 
				tc.nom_tipocaso as nom_tipocaso, 
				s.cod_caso AS `cod_caso` , 
				s.num_evento AS cod_evento 

				FROM 
				(`actividades` AS a natural join `seguimientos` AS s 
				inner join `casos` AS c on s.cod_caso=c.cod_caso natural join `tipocasos` AS tc 
				inner join `personas` AS pb on pb.cod_persona=c.cod_beneficiario) 
				inner join
				(`personas` AS pv natural join `voluntarios` AS v natural join `programas` AS p)
				ON v.cod_persona = s.cod_voluntario					
					
				WHERE p.nom_programa = '".$this->Session->read('programa')."'
				AND s.cod_voluntario != '".$this->Session->read('cod_voluntario')."'
				AND s.bit_revisado = 0
				AND a.tip_actividad IN ('Inicial', 'Reactivacion', 'Cierre')				
				AND DATE(s.fec_ejecucion) = CURDATE()				
			";
			
			$casos_hoy = $this->Seguimiento->query($query_casos_hoy);
			
			$this->set('casos_hoy', $casos_hoy);
			
			// [Javier] Para casos anteriores es la misma consulta, pero con un rango de fechas diferente (desde ayer
			// hasta 7 dï¿½as atrï¿½s:
			
			$query_casos_anteriores="
				SELECT 
				s.cod_voluntario AS cod_voluntario, 
				CONCAT( pv.nom_nombre, ' ', pv.nom_appat ) AS nom_voluntario, 
				p.nom_programa as nom_programa_voluntario, 
				a.cod_actividad AS `cod_actividad`, 
				a.nom_actividad AS nom_actividad, 
				a.tip_actividad AS tip_actividad, 
				s.fec_ejecucion AS fec_ejecucion, 
				c.cod_beneficiario AS cod_beneficiario, 
				c.est_caso AS est_caso, 
				CONCAT( pb.nom_nombre, ' ', pb.nom_appat ) AS nom_beneficiario, 
				tc.nom_tipocaso as nom_tipocaso, 
				s.cod_caso AS `cod_caso` , 
				s.num_evento AS cod_evento 

				FROM 
				(`actividades` AS a natural join `seguimientos` AS s 
				inner join `casos` AS c on s.cod_caso=c.cod_caso natural join `tipocasos` AS tc 
				inner join `personas` AS pb on pb.cod_persona=c.cod_beneficiario) 
				inner join
				(`personas` AS pv natural join `voluntarios` AS v natural join `programas` AS p)
				ON v.cod_persona = s.cod_voluntario					
			
				WHERE p.nom_programa = '".$this->Session->read('programa')."'
				AND s.cod_voluntario != '".$this->Session->read('cod_voluntario')."'
				AND s.bit_revisado = 0
				AND a.tip_actividad IN ('Inicial', 'Reactivacion', 'Cierre')
				AND (	DATE(s.fec_ejecucion) <= CURDATE() - INTERVAL 1 DAY 
						AND DATE(s.fec_ejecucion) >= CURDATE() - INTERVAL 8 DAY)			
			";
			$casos_anteriores = $this->Seguimiento->query($query_casos_anteriores);
			
			$this->set('casos_anteriores', $casos_anteriores);
			
			//echo print_r($casos_anteriores);
			
			// [Javier] Guardo los rangos de fechas para ponerlos en la vista:
			$fec_inferior = $this->Seguimiento->query("SELECT CURDATE() - INTERVAL 8 DAY as inf;");
			$this->set('fec_inferior', $fec_inferior);
			
			$fec_superior = $this->Seguimiento->query("SELECT CURDATE() - INTERVAL 1 DAY as sup;");
			$this->set('fec_superior', $fec_superior);

		}		
		
		// [Javier] Acciï¿½n que aprueba el seguimiento realizado por un voluntario. 
		// A su vez (y por motivos de seguridad) activa con bit_revisado = 1 todos los seguimientos anteriores para ese caso,
		// asumiendo que eventos anteriores ya fueron revisados
		function aprobar()
		{
			// [Javier] Se capturan los datos provenientes del formulario:
			$cod_caso = $this->data['Caso']['cod_caso'];
			$num_evento = $this->data['Seguimiento']['cod_seguimiento'];
			$nom_beneficiario = $this->data['Beneficiario']['nom_beneficiario'];
			$nom_voluntario = $this->data['Voluntario']['nom_voluntario'];
			
			// [Javier] Se modifica el valor de bit_revisado a 1 en el seguimiento correspondiente 
			// y todos aquellos que son anteriores a ï¿½l.
			$aprobar_seguimientos = $this->Seguimiento->query("
			
				UPDATE `seguimientos` 
				SET bit_revisado = 1
				WHERE cod_caso = ".$cod_caso." 
		
			");	
	
			if($aprobar_seguimientos==null)
			{
				// [Javier] Se configura el mensaje de ï¿½xito (mientras no exista de manera formal en la BD):
				$msg = 20;				
			}
			
			else
			{
				// [Javier] Se configura el mensaje de falla (mientras no exista de manera formal en la BD):
				$msg = 21;
			}
			
			$this->redirect('/controldecasos/index/'.$msg);
			exit();
		}
	}	
?>