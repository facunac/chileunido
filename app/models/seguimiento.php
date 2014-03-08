<?php
	
	class Seguimiento extends AppModel
	{
		var $name='Seguimiento'; // nombre del modelo
		var $primaryKey='num_evento';
		var $belongsTo=array('Voluntario' => array('foreignKey' => 'cod_voluntario'),
							'Caso' => array('foreignKey' => 'cod_caso'));
		var $hasMany=array('Respuestaficha' => array('foreignKey' => 'num_evento', 'dependent' => true));
		
		
		var $validate=array(//'nom_comentario1'=> VALID_NOT_EMPTY,
							//'fec_proxrevision'=> '/[0-9]{4}[\-][0-9]{2}[\-][0-9]{2}$/i',
							'tip_proxrevision'=> VALID_NOT_EMPTY
							);
							
		var $jsFeedback=array(//'nom_comentario1'=> 'Ingrese Descripciï¿½n',
							  //'fec_proxrevision'=> 'Ingrese fecha de la proxixma revision',
							  'tip_proxrevision'=> 'Ingrese tipo de la proxima revision'
							);
		
		
		function beforeSave() 
		{ 
		
			// Get month day and year from date string 
			
			if ($this->data['Seguimiento']['fec_proxrevision']==NULL){
				$month = 1;
				$day = 1;
				$year = 2100;
			}
			
			else {
				$timestamp = explode("-", $this->data['Seguimiento']['fec_proxrevision']);
				$month = $timestamp[1];
				$day = $timestamp[0];
				$year = $timestamp[2];
			}
			
			
			   
			$this->data['Seguimiento']['fec_proxrevision'] = date('Y-m-d', mktime(null, 
																	 		null, 
																			null, 
																			$month, 
																			$day, 
																			$year)); 
			return true; 
		}
		
		//Pasa Time Stamp a Fecha
		function toFecha($fecha)
		{
			if($fecha){
				$fec1=explode(" ", $fecha);
				$fec2=explode("-", $fec1[0]);
				return $fec2[0]."-".$fec2[1]."-".$fec2[2];
			}
			else return "";
		}


        function CuentaLlamadas($cod_voluntario,$tipoLlamada,$tiempo)
        {
            $string="";
            if($cod_voluntario!=-1){
                 $string = $string."Seguimiento.cod_voluntario = ".$cod_voluntario." and ";}

             return $this->findCount( $string."Seguimiento.cod_actividad = ".$tipoLlamada."
                 and Seguimiento.fec_ejecucion > CURRENT_DATE - INTERVAL ".$tiempo." DAY");                   

        }
	   function CuentaLlamadasMesUsuario($cod_voluntario,$tipoLlamada)
       {
            $string="";
            if($cod_voluntario!=-1){
                 $string = $string."Seguimiento.cod_voluntario = ".$cod_voluntario." and ";}
			
             return $this->findCount( $string."Seguimiento.cod_actividad = ".$tipoLlamada."
                 and MONTH(Seguimiento.fec_ejecucion) = MONTH(CURRENT_DATE) and YEAR(Seguimiento.fec_ejecucion) = YEAR(CURRENT_DATE)");
       }

       function CuentaLlamadasAnualUsuario($cod_voluntario,$tipoLlamada)
       {
            $string="";
            if($cod_voluntario!=-1){
                 $string = $string."Seguimiento.cod_voluntario = ".$cod_voluntario." and ";}

             return $this->findCount( $string."Seguimiento.cod_actividad = ".$tipoLlamada."
                 and YEAR(Seguimiento.fec_ejecucion) = YEAR(CURRENT_DATE)");
       }
      
       
       function CuentaLlamadasUsuario($cod_voluntario,$tipoLlamada,$time)
       {
            return  $this->CuentaLlamadas($cod_voluntario,$tipoLlamada,$time);
       }
		
       function CuentaTodasLasLlamadas($tipoLlamada,$time)
       {
         return $this->CuentaLlamadas(-1,$tipoLlamada,$time);
       }

       function CuentaTodasLasLlamadasAnual($tipoLlamada)
       {
       		
           return $this->CuentaLlamadasAnualUsuario(-1,$tipoLlamada);
       }

       function CuentaTodasLasLlamadasMes($tipoLlamada)
       {
           return $this->CuentaLlamadasMesUsuario(-1,$tipoLlamada);
       }
               
	}
?>
