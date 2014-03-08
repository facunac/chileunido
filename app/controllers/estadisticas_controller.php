<?php
	class EstadisticasController extends AppController
	{
		var $name = "Estadisticas";
		var $uses = array('Actividad', 'Voluntario', 'Programa', 'Convenio','Caso','Seguimiento','Beneficiario','Persona','Comuna','Respuestaficha','Dimension', 'Turno');
		var $helpers = array('Html', 'Form', 'Time', 'Excel');

		var $tipo_periodo  =array('t'=> "Todo", 'a' => "Anual", 'm' => "Mes", 's' => "Semana", 'd' => "Dia");

		var $criterio_casos=array('abiertos'=> "abiertos en el periodo", 'cerrados' => "cerrados en el periodo", 'activos' => "activos en el periodo");
	

		//ojo que todos los query tienen que incluir a beneficiario por lo del convenio, a casos por las fechas y tipo casos por el cod programa
		//ojo que  <ANDselectconvenio> debe tener como minimo una relacion entre beneficiarios y casos o sino te cuenta todos los beneficiarios
		//ojo que hay post procesamiento de los query en la funcion grafico.

		//Los query, los titulos y los xlabel deben estar en el mismo orden.
		//Son arrays y la posicion se maneja con la variable $grafico.

		var $query = array(

			//$query es una variable con toda la informacion necesaria para identificar a una consulta. Este tiene:
			//key_consulta:	Identificador de las consultas ademÃ¡s de ser nombre en el eje x del grafico generado.
			//titulo:		Para el seleccionador de consultas en la vista, el grafico y la tabla.
			//tipo:			Hay consultas que son un SQL preprogramado y otras, las referentes a la info en fichas dinamicas, que se deben sacar por manejo logico de cake.
			//cod_programa:	programas de chileunido para el cuales es valida la consulta. 1 es Acoge, 2 es Comunicate, 0 es todos.
			//recurso:		informacion que se necesita para ejecutar la consulta.

			'Tipo de ingreso'		=> array('titulo' => "Casos por tipo de ingreso", 				'cod_programa' => "0", 'tipo' => "sql", 	'recurso' =>
																																							"SELECT `tipoingresos`.`nom_medio` AS COLUMNA, COUNT( * ) AS DATO
																																							FROM `casos`, `tipocasos`, `tipoingresos`, `beneficiarios`
																																							WHERE `casos`.`cod_tipocaso`=`tipocasos`.`cod_tipocaso`
																																								AND `casos`.`cod_tipoingreso`=`tipoingresos`.`cod_tipoingreso`
																																								AND `tipocasos`.`cod_programa`=<cod_programa>
																																								<ANDselectperiodo>
																																								<ANDselectconvenio>
																																							GROUP BY `tipoingresos`.`nom_medio`
																																							ORDER BY DATO DESC"
			),

			'Canal de ingreso'		=> array('titulo' => "Casos por tipo de ingreso desglozado",	'cod_programa' => "0", 'tipo' => "sql", 	'recurso' =>
																																							"SELECT CONCAT_WS(', ',`tipoingresos`.`nom_medio`,`tipoingresos`.`nom_tipoingreso`)  AS COLUMNA, COUNT( * ) AS DATO
																																							FROM `casos`, `tipocasos`, `tipoingresos`, `beneficiarios`
																																							WHERE `casos`.`cod_tipocaso`=`tipocasos`.`cod_tipocaso`
																																								AND `casos`.`cod_tipoingreso`=`tipoingresos`.`cod_tipoingreso`
																																								AND `tipocasos`.`cod_programa`=<cod_programa>
																																								<ANDselectperiodo>
																																								<ANDselectconvenio>
																																							GROUP BY `tipoingresos`.`nom_tipoingreso`
																																							ORDER BY DATO DESC"
			),

			'Edad'					=> array('titulo' => "Casos por edad al ingreso", 				'cod_programa' => "0", 'tipo' => "sql", 	'recurso' =>
																																							"SELECT ( IF(`personas`.`ano_nacimiento`<1900, NULL , IF(YEAR( `casos`.`fec_ingreso` ) - `personas`.`ano_nacimiento`=0, ' Cero', YEAR( `casos`.`fec_ingreso` ) - `personas`.`ano_nacimiento`   )  )) AS COLUMNA, COUNT( * ) AS DATO
																																							FROM `casos`, `tipocasos`, `beneficiarios`, `personas`
																																							WHERE `casos`.`cod_tipocaso`=`tipocasos`.`cod_tipocaso`
																																								AND	`casos`.`cod_beneficiario`=`personas`.`cod_persona`
																																								AND `tipocasos`.`cod_programa`=<cod_programa>
																																								<ANDselectperiodo>
																																								<ANDselectconvenio>
																																							GROUP BY COLUMNA
																																							ORDER BY COLUMNA ASC"
			),

			'Grupo Etario'			=> array('titulo' => "Casos por grupos etarios, edad al ingreso",'cod_programa' => "0", 'tipo' => "sql", 	'recurso' =>
																																							"SELECT GE.grupo AS COLUMNA, COUNT(*) AS DATO
																																							FROM
																																							(
																																								SELECT ( IF(`personas`.`ano_nacimiento`<1900 OR ISNULL(`personas`.`ano_nacimiento`), -1 , IF(YEAR( `casos`.`fec_ingreso` ) - `personas`.`ano_nacimiento`<0,-1,YEAR( `casos`.`fec_ingreso` ) - `personas`.`ano_nacimiento` ) )) AS EDADES
																																								FROM `casos`, `tipocasos`, `beneficiarios`, `personas`
																																								WHERE `casos`.`cod_tipocaso`=`tipocasos`.`cod_tipocaso`
																																									AND	`casos`.`cod_beneficiario`=`personas`.`cod_persona`
																																									AND `tipocasos`.`cod_programa`=<cod_programa>
																																									<ANDselectperiodo>
																																									<ANDselectconvenio>
																																							) AS POREDAD LEFT JOIN gruposetarios AS GE
																																							ON POREDAD.edades = GE.edad
																																							GROUP BY GE.grupo
																																							ORDER BY GE.grupo"
			),

			'Zona'					=>array('titulo' => "Casos por zona",							'cod_programa' => "0", 'tipo' => "sql", 	'recurso' =>
																																							"SELECT `comunas`.`nom_zona` AS COLUMNA, COUNT( * ) AS DATO
																																							FROM `casos`, `personas`, `comunas`, `tipocasos`, `beneficiarios`
																																							WHERE `casos`.`cod_tipocaso`=`tipocasos`.`cod_tipocaso`
																																								AND `tipocasos`.`cod_programa`=<cod_programa>
																																								AND	`casos`.`cod_beneficiario`=`personas`.`cod_persona`
																																								AND `personas`.`cod_comuna`=`comunas`.`cod_comuna`
																																								<ANDselectperiodo>
																																								<ANDselectconvenio>
																																							GROUP BY `comunas`.`nom_zona`
																																							ORDER BY DATO DESC"
			),

			'Region'				=> array('titulo' => "Casos por region",						'cod_programa' => "0", 'tipo' => "sql", 	'recurso' =>
																																							"SELECT `comunas`.`nom_region` AS COLUMNA, COUNT( * ) AS DATO
																																							FROM `casos`, `personas`, `comunas`, `tipocasos`, `beneficiarios`
																																							WHERE `casos`.`cod_tipocaso`=`tipocasos`.`cod_tipocaso`
																																								AND `tipocasos`.`cod_programa`=<cod_programa>
																																								AND `casos`.`cod_beneficiario`=`personas`.`cod_persona`
																																								AND `personas`.`cod_comuna`=`comunas`.`cod_comuna`
																																								<ANDselectperiodo>
																																								<ANDselectconvenio>
																																							GROUP BY `comunas`.`nom_region`
																																							ORDER BY DATO DESC"
			),

			'Comuna'				=> array('titulo' => "Casos por comuna",						'cod_programa' => "0", 'tipo' => "sql", 	'recurso' =>
																																							"SELECT `comunas`.`nom_comuna` AS COLUMNA, COUNT( * ) AS DATO
																																							FROM `casos`, `personas`, `comunas`, `tipocasos`, `beneficiarios`
																																							WHERE `casos`.`cod_tipocaso`=`tipocasos`.`cod_tipocaso`
																																								AND `tipocasos`.`cod_programa`=<cod_programa>
																																								AND `casos`.`cod_beneficiario`=`personas`.`cod_persona`
																																								AND `personas`.`cod_comuna`=`comunas`.`cod_comuna`
																																								<ANDselectperiodo>
																																								<ANDselectconvenio>
																																							GROUP BY `comunas`.`nom_comuna`
																																							ORDER BY DATO DESC"
			),

			'NSE'					=> array('titulo' => "Casos por NSE (Gran Santiago)",			'cod_programa' => "0", 'tipo' => "sql", 	'recurso' =>
																																							"SELECT `comunas`.`nom_gse` AS COLUMNA, COUNT( * ) AS DATO
																																							FROM `casos`, `personas`, `comunas`, `tipocasos`, `beneficiarios`
																																							WHERE `casos`.`cod_tipocaso`=`tipocasos`.`cod_tipocaso`
																																								AND `casos`.`cod_beneficiario`=`personas`.`cod_persona`
																																								AND `personas`.`cod_comuna`=`comunas`.`cod_comuna`
																																								AND `comunas`.`nom_gse` IS NOT NULL
																																								AND `tipocasos`.`cod_programa`=<cod_programa>
																																								<ANDselectperiodo>
																																								<ANDselectconvenio>
																																							GROUP BY `comunas`.`nom_gse`
																																							ORDER BY COLUMNA ASC"
			),

		   'A nombre de quien llama'=> array('titulo' => "Casos por a nombre de quien llama",		'cod_programa' => "0", 'tipo' => "sql", 	'recurso' =>
																																							"SELECT `casos`.`est_porquien` AS COLUMNA, COUNT( * ) AS DATO
																																							FROM `casos`, `tipocasos`, `beneficiarios`
																																							WHERE `casos`.`cod_tipocaso`=`tipocasos`.`cod_tipocaso`
																																								AND `tipocasos`.`cod_programa`=<cod_programa>
																																								<ANDselectperiodo>
																																								<ANDselectconvenio>
																																							GROUP BY `casos`.`est_porquien`
																																							ORDER BY DATO DESC"
			),

			'Identificacion'		=> array('titulo' => "Casos por rol familiar",					'cod_programa' => "0", 'tipo' => "sql", 	'recurso' =>
																																							"SELECT `beneficiarios`.`tip_rolfamilia` AS COLUMNA, COUNT( * ) AS DATO
																																							FROM `casos`, `tipocasos`, `beneficiarios`
																																							WHERE `casos`.`cod_tipocaso`=`tipocasos`.`cod_tipocaso`
																																								AND `tipocasos`.`cod_programa`=<cod_programa>
																																								AND `casos`.`cod_beneficiario`=`beneficiarios`.`cod_persona`
																																								<ANDselectperiodo>
																																								<ANDselectconvenio>
																																							GROUP BY `beneficiarios`.`tip_rolfamilia`
																																							ORDER BY DATO DESC"
			),

			'Tipo_acoge'			=> array('titulo' => "Casos por tipo",							'cod_programa' => "1", 'tipo' => "sql", 	'recurso' =>
																																							"SELECT IF(`tipocasos`.`bit_aborto`,'Considera el Aborto','No Considera el Aborto') AS COLUMNA, COUNT( * ) AS DATO
																																							FROM `casos`, `tipocasos`, `beneficiarios`
																																							WHERE `casos`.`cod_tipocaso`=`tipocasos`.`cod_tipocaso`
																																								AND `tipocasos`.`cod_programa`=<cod_programa>
																																								<ANDselectperiodo>
																																								<ANDselectconvenio>
																																							GROUP BY COLUMNA
																																							ORDER BY COLUMNA"
			),

			'Tipo_riesgo'			=> array('titulo' => "Casos por tipo con consideracion de aborto",		'cod_programa' => "1", 'tipo' => "sql", 	'recurso' =>
																																							"SELECT `tipocasos`.`nom_tipocaso` AS COLUMNA, COUNT( * ) AS DATO
																																							FROM `casos`, `tipocasos`, `beneficiarios`
																																							WHERE `casos`.`cod_tipocaso`=`tipocasos`.`cod_tipocaso`
																																								AND `tipocasos`.`cod_programa`=<cod_programa>
																																								<ANDselectperiodo>
																																								<ANDselectconvenio>
																																								AND `tipocasos`.`bit_aborto`=1
																																							GROUP BY `tipocasos`.`nom_tipocaso`
																																							ORDER BY DATO DESC"
			),

			'Tipo_sin_riesgo'		=> array('titulo' => "Casos por tipo sin consideracion de aborto",		'cod_programa' => "1", 'tipo' => "sql", 	'recurso' =>
																																							"SELECT `tipocasos`.`nom_tipocaso` AS COLUMNA, COUNT( * ) AS DATO
																																							FROM `casos`, `tipocasos`, `beneficiarios`
																																							WHERE `casos`.`cod_tipocaso`=`tipocasos`.`cod_tipocaso`
																																								AND `tipocasos`.`cod_programa`=<cod_programa>
																																								<ANDselectperiodo>
																																								<ANDselectconvenio>
																																								AND `tipocasos`.`bit_aborto`=0
																																							GROUP BY `tipocasos`.`nom_tipocaso`
																																							ORDER BY DATO DESC"
			),

			'Tipo_comunicate'		=> array('titulo' => "Casos por tipo",							'cod_programa' => "2", 'tipo' => "sql", 	'recurso' =>
																																							"SELECT `tipocasos`.`nom_tipocaso` AS COLUMNA, COUNT( * ) AS DATO
																																							FROM `casos`, `tipocasos`, `beneficiarios`
																																							WHERE `casos`.`cod_tipocaso`=`tipocasos`.`cod_tipocaso`
																																								AND `tipocasos`.`cod_programa`=<cod_programa>
																																								<ANDselectperiodo>
																																								<ANDselectconvenio>
																																							GROUP BY `tipocasos`.`nom_tipocaso`
																																							ORDER BY DATO DESC"
			),

			'Cantidad de Casos'		=> array('titulo' => "Casos en el periodo",						'cod_programa' => "0", 'tipo' => "sql", 	'recurso' =>
																																							"SELECT 'Cantidad de Casos' AS COLUMNA, COUNT( * ) AS DATO
																																							FROM `casos`, `tipocasos`, `beneficiarios`
																																							WHERE `casos`.`cod_tipocaso`=`tipocasos`.`cod_tipocaso`
																																								AND `tipocasos`.`cod_programa`=<cod_programa>
																																								<ANDselectperiodo>
																																								<ANDselectconvenio>
																																							ORDER BY DATO DESC"
			),

			//Consultas clinicas
			'Tipos de Turno'		=> array('titulo' => "Cantidad de turnos (obviar periodos)",						'cod_programa' => "C", 'tipo' => "cake", 	'sort' => "cat",	'recurso' =>	"cant_turnos"			),	//cuenta la cantidad de turnos asignados de cada tipo
			'Tipo de Turno'			=> array('titulo' => "Cantidad de voluntarios por tipo turno (obviar periodos)",	'cod_programa' => "C", 'tipo' => "cake", 	'sort' => "frec",	'recurso' =>	"cant_voluntarios"   	),	//cuenta la cantidad de voluntarios por tipo de turno
			'Voluntarios'			=> array('titulo' => "Nombre de voluntarios con turnos (obviar periodos)",			'cod_programa' => "C", 'tipo' => "cake", 	'sort' => "cat",	'recurso' =>	"turnos_por_vol"		),	//cuenta la cantidad de turnos asignados a cada voluntario
			'Voluntarios_desg'		=> array('titulo' =>"Programa y nombre de voluntarios con turnos (obviar periodos)",'cod_programa' => "C", 'tipo' => "cake", 	'sort' => "cat",	'recurso' =>	"turnos_por_vol_desg"	),	//cuenta la cantidad de turnos asignados a cada voluntario, detallando programa
			'Derivaciones Solicitadas' => array('titulo' => "Casos por derivaciones solicitadas",	'cod_programa' => "C", 'tipo' => "cake", 	'sort' => "frec",	'recurso' =>	"derivacion_solicitada"	),	//
			'Derivaciones Aceptadas'   => array('titulo' => "Casos por derivaciones aceptadas",		'cod_programa' => "C", 'tipo' => "cake", 	'sort' => "frec",	'recurso' =>	"derivacion_aceptada"	),	//
			'Atenciones'			=> array('titulo' => "Casos con atenciones clinicas",			'cod_programa' => "C", 'tipo' => "cake",	'sort' => "frec",	'recurso' =>	"atenciones"			),	//cuantos pacientes fueron atendidos clinicamente por la fundacion en el periodo
			'Asistencias'			=> array('titulo' => "Promedio asistencias (obviar total)",					'cod_programa' => "C", 'tipo' => "cake", 	'sort' => "cat",	'recurso' =>	"asistencias"		 	),	//
			'Resultado Clinico'		=> array('titulo' => "Casos por resultado clinico",				'cod_programa' => "C", 'tipo' => "cake", 	'sort' => "cat",	'recurso' =>	"resultado_clinico"	 	),	//entre los casos que tienen cierre clinico.

			//Acoge y Comunicate consulta compartida
			'Meses de Permanencia'	=> array('titulo' => "Casos por meses activos",					'cod_programa' => "0", 'tipo' => "cake", 	'sort' => "cat",	'recurso' =>	"meses_activo"			),	//meses entre que se ingresa el caso hasta el ultimo cierre (en caso de varios) o ahsta hoy si sigue activo

			//Acoge y Comunicate consultas individuales
			'Tipo de familia'		=> array('titulo' => "Casos por tipo de familia",				'cod_programa' => "0", 'tipo' => "cake", 	'sort' => "frec",	'recurso' =>	"tipo_flia"				),
			'Estado Civil'			=> array('titulo' => "Casos por estado civil",					'cod_programa' => "0", 'tipo' => "cake", 	'sort' => "frec",	'recurso' =>	"estado_civil"			),
			'Resultado'				=> array('titulo' => "Casos por su resultado",					'cod_programa' => "0", 'tipo' => "cake", 	'sort' => "frec",	'recurso' =>	"resultado"				),	//final del caso (aborto o nacio o no fue empbarazo etc Ã³ cambio en situacion psicologica)

			//Acoge
			'Numero de hijos'		=> array('titulo' => "Casos por numero de hijos", 				'cod_programa' => "1", 'tipo' => "cake",	'sort' => "cat",	'recurso' =>	"num_hijos"				),	//numero de hijos de la beneficiaria
			'Tipo de padre'			=> array('titulo' => "Casos por tipo de padre", 				'cod_programa' => "1", 'tipo' => "cake",	'sort' => "frec",	'recurso' =>	"padre"					),	//Â¿quien es el papa?
			'Meses de Embarazo'		=> array('titulo' => "Casos por meses de embarazo", 			'cod_programa' => "1", 'tipo' => "cake",	'sort' => "cat",	'recurso' =>	"meses"					),	//meses de embarazo al llamar primera vez
			'Metodo anticonceptivo'	=> array('titulo' => "Casos por metodo anticonceptivo usado",	'cod_programa' => "1", 'tipo' => "cake",	'sort' => "frec",	'recurso' =>	"metodo"				),	//metodo anticonceptivo usado
			'Derivacion'			=> array('titulo' => "Casos por donde fueron derivados", 		'cod_programa' => "1", 'tipo' => "cake",	'sort' => "frec",	'recurso' =>	"derivacion" 			),	//a donde fue derivado el caso
			'Trabajo'			 	=> array('titulo' => "Casos por trabajo de beneficiaria", 		'cod_programa' => "1", 'tipo' => "cake",	'sort' => "frec",	'recurso' =>	"trabajo" 				),	//de la beneficiaria
			'Nivel educacional'		=> array('titulo' => "Casos por nivel educacional beneficiaria",'cod_programa' => "1", 'tipo' => "cake",	'sort' => "cat",	'recurso' =>	"nivel_educ" 			),	//de la beneficiaria

			//Comunicate
			'Educacion Padre'		=> array('titulo' => "Casos por educacion padre",				'cod_programa' => "2", 'tipo' => "cake", 	'sort' => "cat",	'recurso' =>	"educ_padre"			),
			'Educacion Madre'		=> array('titulo' => "Casos por educacion madre",				'cod_programa' => "2", 'tipo' => "cake", 	'sort' => "cat",	'recurso' =>	"educ_madre"			),
			'Trabajo Padre'			=> array('titulo' => "Casos por trabajo padre",					'cod_programa' => "2", 'tipo' => "cake", 	'sort' => "cat_r",	'recurso' =>	"trabajo_padre"			),
			'Trabajo Madre'			=> array('titulo' => "Casos por trabajo madre",					'cod_programa' => "2", 'tipo' => "cake", 	'sort' => "cat_r",	'recurso' =>	"trabajo_madre"			),
			'Derivado'				=> array('titulo' => "Casos por si fue derivado a red",			'cod_programa' => "2", 'tipo' => "cake", 	'sort' => "cat_r",	'recurso' =>	"derivado"				),
			'Asistencia'			=> array('titulo' => "Casos por si asistio a derivacion a red",	'cod_programa' => "2", 'tipo' => "cake", 	'sort' => "cat",	'recurso' =>	"asistencia_derivacion"	)	//asistencia a derivacion

		);

		function index()
		{
			// Se mandan algunas variables al layout para que se vea bien COPIAR SIEMPRE Y CAMBIAR NOMBRE PAGINA
			$this->escribirHeader("Estadisticas");

			$agnos=array();
			for($i=date('Y'); $i>=1999; $i--)
				$agnos[$i]=$i;
			$this->set('agnos', $agnos);

			$programas=array(0 => "");
			$programas+=$this->Programa->getAllAsArray();
			$this->set('programas', $programas);

                        $comunas=$this->Comuna->getAllAsArray();
			$this->set('comunas', $comunas);

                        //esto es necesario para el buscador de voluntarios
			$estados=$this->Voluntario->getPossibleValues('est_voluntario');
			$this->set('estados', $estados);

			$si_no=array('0' => 'No', '1' => 'Si');
			$this->set('si_no', $si_no);


			$roles=$this->Voluntario->getPossibleValues('est_rol');
			$this->set('roles', $roles);
			
			$tipos = array("2"=>"Aperturas","4"=>"Seguimientos","6"=>"Fallidos","8"=>"Cierres");
			$this->set('tipos', $tipos);
                        //fin buscador


			$convenios=array('T' => 'Todos los casos');
			$convenios+=$this->Convenio->getAllAsArray();
			$convenios+=array('S' => 'Sin informacion');
			$this->set('convenios', $convenios);

			$this->set('tipo_periodo', $this->tipo_periodo);
			$this->set('selected_tipo_periodo', 't');

			$this->set('criterio_casos', $this->criterio_casos);

			//para las consultas clinicas
			$this->set('criterio_casos2',	"activos");
			$this->set('programas2',		"2");
			$this->set('convenios2',		"T");
			$consultas2=array();
			foreach($this->query as $key => $q){
				if($q['cod_programa']=="C")
					$consultas2 += array($key => $q['titulo']);
			}
			$this->set('consultas2', $consultas2);
		}


		function opcion_consulta($cod_programa){
			$this->layout='vacio';
			$consultas=array();
			$selected="";
			if($cod_programa!="0"){
				foreach($this->query as $key => $q){
					if($q['cod_programa']==$cod_programa||$q['cod_programa']=="0")
					$consultas += array($key => $q['titulo']);
				}
			}
			$this->set('consultas', $consultas);
			$this->set('selected', $selected);
		}


		function opcion_periodo($agno,$tipo_periodo,$numero=null){ //$numero sirve para poder presentar mas de una ventana con periodo.
			$this->layout='vacio';
			$periodos=array();
			$selected="";

			switch($tipo_periodo){
				case 'm':
					$periodos=array('1' => 'Enero', '2' => 'Febrero', '3' => 'Marzo', '4' => 'Abril', '5' => 'Mayo', '6' => 'Junio', '7' => 'Julio', '8' => 'Agosto', '9' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre');
					$selected=date('m');
				break;
				case 's':
					//[Ignacio] primer dï¿½a del aï¿½o
					$iniciodemes=mktime(null, null, null, 1, 1, $agno);
					//[Ignacio] el dï¿½a de la semana (lunes=0... domingo=6) del 1 de enero
					$diadelasemana=date('N', $iniciodemes)-1;
					//[Ignacio] la fecha inicial de la semama con el primer lunes del aï¿½o
					$ahora=mktime(null,null,null,1,1-$diadelasemana+7,$agno);
					$i=1;
					do {
						if(strlen($i)==1) $i="0".$i;
						$dia=date('d', $ahora);
						$mes=date('m', $ahora);
						$ano=date('Y', $ahora);
						$periodos+=array($agno.$i => 'Semana del '.$dia."-".$mes."-".$ano);
						$domingo=mktime(null,null,null,$mes,$dia+6,$ano);

						$hoy=mktime(null,null,null,date('m'),date('d'),$ano);
						if($ahora<=$hoy && $hoy<=$domingo) $selected=$agno.$i;
						$ahora=mktime(null,null,null,$mes,$dia+7,$ano);
						$i++;
					//la semana debe tener un lunes
					} while(date('Y', $ahora)<=$agno);
				break;
				case 'd':
					$selected=date('j,n');
					$dia=1;
					$mes=1;
					$ano=$agno;
					do {
						$fecha_mostrar=date('d-m', mktime(null,null,null,$mes,$dia,$ano));
						$fecha_value=date('j,n', mktime(null,null,null,$mes,$dia,$ano));
						$periodos+=array($fecha_value => $fecha_mostrar);

						$magnana=mktime(null,null,null,$mes,$dia+1,$ano);
						$dia=date('j', $magnana);
						$mes=date('n', $magnana);
						$ano=date('Y', $magnana);
					} while($ano==$agno);

				break;
			}
			$this->set('periodos', $periodos);
			$this->set('selected', $selected);
			$this->set('nom_periodo', $this->tipo_periodo[$tipo_periodo]);
			$this->set('numero', $numero);
		}


/**[Dawes] esta es la funcion que llama a las consultas SQL. Para desplegar abajo de la consulta **/

		function grafico($cod_programa, $cod_convenio, $key_consulta, $crit_casos, $agno, $tipo_periodo, $periodo=""){

			$this->layout='vacio';

			//[Dawes] Se establecen los tÃ­tulos de la tabla y del grÃ¡fico.

			//Nombre Programa
			$programa=$this->Programa->findByCodPrograma($cod_programa);
			$nom_programa=$programa['Programa']['nom_programa'];

			//Nombre de convenio
			if ($cod_convenio == "T"){
				$nom_convenio="Todos";
			} else if ($cod_convenio == "S"){
				$nom_convenio="Sin Informacion";
			} else {
				$convenio=$this->Convenio->findByCodConvenio($cod_convenio);
				$nom_convenio=$convenio['Convenio']['nom_convenio'];
			}

			//Nombre del periodo: se guarda en la parte de subtÃ­tulo.
			$meses=array('1' => 'enero', '2' => 'febrero', '3' => 'marzo', '4' => 'abril', '5' => 'mayo', '6' => 'junio', '7' => 'julio', '8' => 'agosto', '9' => 'septiembre', '10' => 'octubre', '11' => 'noviembre', '12' => 'diciembre');
			$nom_periodo="";
			$inicio_periodo="";
			switch($tipo_periodo){
				case 't':
					$nom_periodo="Todos los aÃ±os";
					break;
				case 'a':
					$nom_periodo="aÃ±o ".$agno;
					$inicio_periodo=$agno."-01-01";
					break;
				case 'm':
					$nom_periodo=$meses[$periodo]." de ".$agno;
					$inicio_periodo=$agno."-".$periodo."-01";
				break;
				case 's':
					$periodo=substr($periodo,4,2);
					//[Ignacio] cualquier dia de la semana escogida
					$diacualquiera=mktime(null, null, null, 1, 7*$periodo+1, $agno);
					//[Ignacio] el dï¿½a de la semana (lunes=0... domingo=6) del 1 de enero
					$diadelasemana=date('N', $diacualquiera)-1;
					//[Ignacio] la fecha inicial de la semama con el primer domingo del aï¿½o
					$iniciodesemana=mktime(null,null,null,1,7*$periodo+1-$diadelasemana,$agno);
					$dia=date('j', $iniciodesemana);
					$mes=date('n', $iniciodesemana);
					$ano=date('Y', $iniciodesemana);
					$nom_periodo="Semana del ".$dia." de ".$meses[$mes]." de ".$ano;
					$inicio_periodo=$ano."-".$mes."-".$dia;
				break;
				case 'd':
					$periodo=explode(",", $periodo);
					$nom_periodo=$periodo[0]." de ".$meses[$periodo[1]]." de ".$agno;
					$inicio_periodo=$agno."-".$periodo[1]."-".$periodo[0];
				break;
			}

			$crit = "";
			if 		($crit_casos=="abiertos" || $crit_casos=="activos")  $crit = "fec_ingreso"; //es igual que en el caso de los casos abiertos
			else if ($crit_casos=="cerrados") $crit = "fec_retiro";
			else {
				echo ("Error en eleccion de caso abierto, cerrado o activo");
				die();
			}
			$nom_criterio = "Casos ".$this->criterio_casos[$crit_casos];

/** De aqui hay dos opciones. O la consulta requerida se saca de un sql, o se hace por las herramientas cake  **/


/***POR SQL, son validos para ambos programas***/
			if($this->query[$key_consulta]['tipo']=="sql"){ // se usa una consulta sql predefinida

				//[Dawes] Se remplazan los facotres variables de las consultas sql.

				//Se agrega seleccion por programa
				$this->query[$key_consulta]['recurso']=str_replace('<cod_programa>', $cod_programa, $this->query[$key_consulta]['recurso']);

				//Se agrega seleccion por convenio: Si no se selecionÃ³ convenio, "todos los convenios" o sin informacion
				if ($cod_convenio == "T"){
					$this->query[$key_consulta]['recurso']=str_replace('<ANDselectconvenio>', "AND `beneficiarios`.`cod_persona` = `casos`.`cod_beneficiario`", $this->query[$key_consulta]['recurso']);
				} else if ($cod_convenio == "S"){
					$this->query[$key_consulta]['recurso']=str_replace('<ANDselectconvenio>', "AND `beneficiarios`.`cod_persona` = `casos`.`cod_beneficiario` AND `beneficiarios`.`cod_convenio` IS NULL", $this->query[$key_consulta]['recurso']);
				} else {
					$this->query[$key_consulta]['recurso']=str_replace('<ANDselectconvenio>', "AND `beneficiarios`.`cod_persona` = `casos`.`cod_beneficiario` AND `beneficiarios`.`cod_convenio` = ".$cod_convenio, $this->query[$key_consulta]['recurso']);
				}

				//Se agrega seleccion por periodo. ver definicion de $crit mas arriba
				$cond_periodo="AND (( ";
				switch($tipo_periodo){
					case 't':
						$cond_periodo.="`casos`.`".$crit."` IS NOT NULL";
						break;
					case 'a':
						$cond_periodo.="YEAR(`casos`.`".$crit."`)=".$agno;
						break;
					case 'm':
						$cond_periodo.="YEAR(`casos`.`".$crit."`)=".$agno." AND MONTH(`casos`.`".$crit."`)=".$periodo;
						break;
					case 's':
						$cond_periodo.="YEAR(`casos`.`".$crit."`)=".$agno." AND YEARWEEK(`casos`.`".$crit."`,5)=".$agno.$periodo;
						break;
					case 'd':
						//$periodo=explode(",", $periodo); ---> [Dawes]Esto ya se hizo arriba definiendo tÃ­tulos.
						$cond_periodo.="YEAR(`casos`.`".$crit."`)=".$agno." AND DAY(`casos`.`".$crit."`)=".$periodo[0]." AND MONTH(`casos`.`".$crit."`)=".$periodo[1];
					break;
				}
				if($crit_casos=="activos" && $tipo_periodo!="t") {
					$cond_periodo.=" ) OR ( `casos`.`fec_ingreso` < CAST('".$inicio_periodo."' AS date) AND ( `casos`.`fec_retiro` > CAST('".$inicio_periodo."' AS date) OR `casos`.`fec_retiro` IS NULL )";
				}
				$cond_periodo.=" ))";

				$this->query[$key_consulta]['recurso']=str_replace('<ANDselectperiodo>', $cond_periodo, $this->query[$key_consulta]['recurso']);


				//*************Se aplica el query SQL
				$res=$this->Voluntario->query($this->query[$key_consulta]['recurso']);
				echo $this->query[$key_consulta]['recurso'];

				//Se elimina el primer indice (Nombre del modelo)
				/*un ejemplo:
					$res:
					array(1) {
						[0]= array(2) {
							["tipoingresos"]=array(1) {
								['COLUMNA']=string(11) "TelevisiÃ³n"
					    	}
					    	[0]= array(1) {
								['DATO']= string(2) "34"
							}
						}
					}
					$dataset:
					array(1) {
						[0]=array(2) {
							['COLUMNA']=string(11) "TelevisiÃ³n"
							['DATO']=string(2) "34"
					  }
					}
				*/
				$dataset=array();
				foreach($res as $ix => $v){
					$dataset+=array($ix => array());
					foreach($v as $v2){
						$dataset[$ix]+=$v2;
					}
					//[DAWES] se le pone nombre a los sin informacion.
					$dataset[$ix]['COLUMNA']=$dataset[$ix]['COLUMNA']?$dataset[$ix]['COLUMNA']:"Sin informacion";
				}


/***POR CODIGO CAKE se hacen las consultas referentes a datos guardados en el caso, no la persona***/
			} else if ($this->query[$key_consulta]['tipo']="cake") {  //se saca la info a traves de herramientas cake

				//seleccionador de estadÃ­stica
				$nom_dato=$this->query[$key_consulta]['recurso'];
				$datos_casos=array();

				if($this->query[$key_consulta]["cod_programa"]!=$cod_programa&&$this->query[$key_consulta]["cod_programa"]!="0"&&$this->query[$key_consulta]["cod_programa"]!="C"){
					//$mensaje=45;
					echo "PROGRAMA VALIDO PARA LA CONSULTA: ".$this->query[$key_consulta]["cod_programa"];
					echo "<BR/>";
					echo "PROGRAMA ELEGIDO: ".$cod_programa;
					echo "<BR/>";
					$this->flash("La consulta requerida no corresponde a el programa seleccionado", "www.google.cl",4);
				}

				/**PRIMER SELECTOR DE CONSULTAS ya que algunas estÃ¡n por separado dado que son independientes de los casos y periodos**/

				switch ($nom_dato){

					case 'cant_turnos': {
						$turnos = $this->Turno->findAll();
						foreach ($turnos as $turno) {
							$datos[$nom_dato] = null;

				        	switch ($turno['Box']['tip_box']) {
				        		case 'Clinico': {
				        			if($turno['Turno']['cod_caso']) $datos[$nom_dato] = "Clinico asignado";
				        			else 							$datos[$nom_dato] = "Clinico libre";
				        		} break;
				        		case 'Telefonico': {
				        			$prog=$this->Programa->findByCodPrograma($turno['Voluntario']['cod_programa']);
				        			$datos[$nom_dato] = "Telefonico ".$prog['Programa']['nom_programa'];
				        		} break;
				        		case 'No Psicologico':{
				        			$datos[$nom_dato] = "`No Psicologico";
				        		} break;
				        	}
							$datos_casos = array_merge ($datos_casos,array($datos)); // se suman los datos de este caso con los anteriores
				        }
				    } break;

				    case 'cant_voluntarios':{
						$turnos = $this->Turno->findAll();
						$nombres_progs = $this->Programa->findall("","Programa.nom_programa","","","",-1);
						$vol_ya_encontrados = array('Otros' => array());
						foreach($nombres_progs as $n){
							$vol_ya_encontrados[ 'Clinico '   .$n['Programa']['nom_programa'] ] = array();
							$vol_ya_encontrados[ 'Telefonico '.$n['Programa']['nom_programa'] ] = array();
						}
						foreach ($turnos as $turno) {
							$datos[$nom_dato] = null;
				        	switch ($turno['Voluntario']['est_rol']) {
				        		case 'Voluntario': { // Turnos clÃ­nicos y telefÃ³nicos por programa
				        			$prog=$this->Programa->findByCodPrograma($turno['Voluntario']['cod_programa'],null,null,-1);
				        	        if ($turno['Box']['tip_box'] == 'Clinico' && $turno['Voluntario']['bit_clinico']) {
				        				$datos[$nom_dato] = 'Clinico '.$prog['Programa']['nom_programa'];
				        			} else if ($turno['Box']['tip_box'] == 'Telefonico') {
				        				$datos[$nom_dato] = 'Telefonico '.$prog['Programa']['nom_programa'];
				        			}
				        		} break;
				        		case 'Voluntario no psicologo':  // Otros turnos
				        		case 'Administrativo': // Nada?
				        			$datos[$nom_dato] = 'Otros';
				        			break;
				        	}
			        		if($datos[$nom_dato]){ // aqui se ve si es primera vez que se cuenta al voluntario y se lleva registro
				        		if(!in_array($turno['Voluntario']['cod_persona'], $vol_ya_encontrados[$datos[$nom_dato]])){
				        			array_push($vol_ya_encontrados[$datos[$nom_dato]], $turno['Voluntario']['cod_persona']);
				        			$datos_casos = array_merge ($datos_casos,array($datos)); // se suman los datos de este caso con los anteriores
				        		}
			        		} else { // se agrega un null si no se tiene informacion del tipo de turno
			        			$datos_casos = array_merge ($datos_casos,array($datos)); // se suman los datos de este caso con los anteriores
			        		}
				        }
				    } break;

				    case 'turnos_por_vol': {
						$turnos = $this->Turno->query("SELECT `Voluntario`.`cod_persona` FROM `turnos` AS `Turno` LEFT JOIN `voluntarios` AS `Voluntario` ON (`Turno`.`cod_voluntario` = `Voluntario`.`cod_persona`)");
						foreach ($turnos as $turno) {
							$datos[$nom_dato] = null;
							$vol=$this->Persona->findByCodPersona($turno['Voluntario']['cod_persona'], null, null, -1);
							$datos[$nom_dato] = $vol['Persona']['nom_nombre']." ".$vol['Persona']['nom_appat']." ".$vol['Persona']['nom_apmat'];
				        	$datos_casos = array_merge ($datos_casos,array($datos)); // se suman los datos de este caso con los anteriores
				        }
					}  break;

					case 'turnos_por_vol_desg': {
						$turnos = $this->Turno->findAll();

						foreach ($turnos as $turno) {
							$datos[$nom_dato] = null;
							$vol=$this->Persona->findByCodPersona($turno['Voluntario']['cod_persona'], null, null, -1);
				        	switch ($turno['Voluntario']['est_rol']) {
				        		case 'Voluntario': { // Turnos clÃ­nicos y telefÃ³nicos por programa
				        			$prog=$this->Programa->findByCodPrograma($turno['Voluntario']['cod_programa'],null,null,-1);
				        	        if ($turno['Box']['tip_box'] == 'Clinico' && $turno['Voluntario']['bit_clinico']) {
				        				$datos[$nom_dato] = 'Clinico'." - ".$vol['Persona']['nom_nombre']." ".$vol['Persona']['nom_appat']." ".$vol['Persona']['nom_apmat'];;
				        			} else if ($turno['Box']['tip_box'] == 'Telefonico') {
				        				$datos[$nom_dato] = 'Telefonico '.$prog['Programa']['nom_programa']." - ".$vol['Persona']['nom_nombre']." ".$vol['Persona']['nom_appat']." ".$vol['Persona']['nom_apmat'];;;
				        			}
				        		} break;
				        		case 'Voluntario no psicologo':  // Otros turnos
				        		case 'Administrativo': // Nada?
				        			$datos[$nom_dato] = 'Otros'." ".$vol['Persona']['nom_nombre']." - ".$vol['Persona']['nom_appat']." ".$vol['Persona']['nom_apmat'];;;
				        			break;
				        	}
							$datos_casos = array_merge ($datos_casos,array($datos)); // se suman los datos de este caso con los anteriores
				        }
					}  break;

					case 'derivacion_solicitada':
					case 'derivacion_aceptada':
					case 'atenciones':  //cuantos beneficiarios tuvieron atenciones clinicas.
					case 'asistencias':
					case 'resultado_clinico':{
						//Se agrega seleccion por periodo.
						switch($tipo_periodo){
							case 't':
								$cond_periodo=null;
								break;
							case 'a':
								$cond_periodo=" AND (( YEAR(`Seguimiento`.`fec_ejecucion`)=".$agno." ))";
								break;
							case 'm':
								$cond_periodo=" AND (( YEAR(`Seguimiento`.`fec_ejecucion`)=".$agno." AND MONTH(`Seguimiento`.`fec_ejecucion`)=".$periodo." ))";
								break;
							case 's':
								$cond_periodo=" AND (( YEAR(`Seguimiento`.`fec_ejecucion`)=".$agno." AND YEARWEEK(`Seguimiento`.`fec_ejecucion`,5)=".$agno.$periodo." ))";
								break;
							case 'd':
								//$periodo=explode(",", $periodo); ---> [Dawes]Esto ya se hizo arriba definiendo tÃ­tulos.
								$cond_periodo=" AND (( YEAR(`Seguimiento`.`fec_ejecucion`)=".$agno." AND DAY(`Seguimiento`.`fec_ejecucion`)=".$periodo[0]." AND MONTH(`Seguimiento`.`fec_ejecucion`)=".$periodo[1]." ))";
							break;
						}
						switch ($nom_dato){

							case 'derivacion_solicitada': {
								//solicitud de derivacion clinica
								$actividad_sol_derivacion_clinica=$this->Actividad->find("Actividad.cod_programa = ".$cod_programa." AND Actividad.tip_actividad LIKE 'Solicitud Derivacion'", array('Actividad.cod_actividad'), "", -1);
								$respuestas = $this->Seguimiento->findall("`Seguimiento`.`cod_actividad`= ".$actividad_sol_derivacion_clinica['Actividad']['cod_actividad'].$cond_periodo, array('Seguimiento.num_evento'),"",null,null,-1);
								$datos[$nom_dato] = "Derivaciones Solicitadas";
								foreach($respuestas as $respuesta){
		        					$datos_casos = array_merge ($datos_casos, array($datos)); // se suman los datos de este caso con los anteriores
								}
							} break;

							case 'derivacion_aceptada': {
								$actividad_derivacion_clinica=$this->Actividad->find("Actividad.cod_programa = ".$cod_programa." AND Actividad.tip_actividad LIKE 'Derivacion'", array('Actividad.cod_actividad'), "", -1);
								$respuestas = $this->Seguimiento->findall("`Seguimiento`.`cod_actividad`= ".$actividad_derivacion_clinica['Actividad']['cod_actividad'].$cond_periodo, array('Seguimiento.num_evento'),"",null,null,-1);
								$datos[$nom_dato] = "Derivacion Aceptada";
								foreach($respuestas as $respuesta){
		        					$datos_casos = array_merge ($datos_casos, array($datos)); // se suman los datos de este caso con los anteriores
								}
							} break;

							case 'atenciones': {
								//Se busca el codigo de actividad de las fichas de atencion clinica, para las consultas que lo requieran.
								$actividad_clinica=$this->Actividad->find("Actividad.cod_programa = ".$cod_programa." AND Actividad.tip_actividad LIKE 'Seguimiento Clinico'", array('Actividad.cod_actividad'), "", -1);
								$respuestas = $this->Seguimiento->findall("`Seguimiento`.`cod_actividad`= ".$actividad_clinica['Actividad']['cod_actividad'].$cond_periodo, array('Seguimiento.cod_caso'), "", null, null, -1);
								$datos[$nom_dato] = "Atenciones";
								foreach($respuestas as $respuesta){
		        					$datos_casos = array_merge ($datos_casos, array($datos)); // se suman los datos de este caso con los anteriores
								}
							} break;

							case 'resultado_clinico':
							case 'asistencias': {

								$actividad_cierre_clinico=$this->Actividad->find("Actividad.cod_programa = ".$cod_programa." AND Actividad.tip_actividad LIKE 'Cierre Clinico'", array('Actividad.cod_actividad'), "", -1);
								$seguimiento_clinico_final  = $this->Seguimiento->findall("`Seguimiento`.`cod_actividad` = ".$actividad_cierre_clinico['Actividad']['cod_actividad'].$cond_periodo, array('Seguimiento.num_evento'), "", null, null, -1);

								switch ($nom_dato){
									case 'resultado_clinico': {
										if($seguimiento_clinico_final){
											foreach($seguimiento_clinico_final as $scf){
												$respuestas = $this->Respuestaficha->findall("Respuestaficha.num_evento = ".$scf['Seguimiento']['num_evento']);
												if($cod_programa==2){
													foreach($respuestas as $respuesta){
														if(	($respuesta['Respuestaficha']['nom_respuesta']==1)&&
															(
																($respuesta['Respuestaficha']['cod_subpregunta']==205)||
																($respuesta['Respuestaficha']['cod_subpregunta']==206)||
																($respuesta['Respuestaficha']['cod_subpregunta']==207)
															)
														  ) $datos['resultado_clinico'] = $respuesta['Subpregunta']['nom_subpregunta'];
													}
												} else $datos['resultado_clinico'] = "Caso cerrado programa no implementado en estadisticas";
												$datos_casos = array_merge ($datos_casos, array($datos)); // se suman los datos de este caso con los anteriores
											}
										} else $this->flash("No hay casos clinicos cerrados", null);
									} break;

									case 'asistencias': {
										//var_dump($seguimiento_clinico_final);
										$i_asist = 0;

										$lista_asist = null;
										$lista_inasist = null;
										$sum['n_asistencias'] = 0;
										$sum['n_inasistencias'] = 0;
										if($seguimiento_clinico_final){
											foreach($seguimiento_clinico_final as $scf){
												$r_as=null;
												$r_in=null;
												$respuestas = $this->Respuestaficha->findall("Respuestaficha.num_evento = ".$scf['Seguimiento']['num_evento']);
												foreach($respuestas as $respuesta){
													if($respuesta['Respuestaficha']['cod_subpregunta']==262){
														$r_as=$respuesta['Respuestaficha']['nom_respuesta'];
													}
													if($respuesta['Respuestaficha']['cod_subpregunta']==263){
														$r_in=$respuesta['Respuestaficha']['nom_respuesta'];
													}
												}
												if($lista_asist) 	$lista_asist .= ", ";
												if($lista_inasist) 	$lista_inasist .= ", ";

												if($r_as||$r_in){
													$i_asist++;
													$sum['n_asistencias']   += $r_as;
													$sum['n_inasistencias'] += $r_in;
													if($r_as) 	$lista_asist = $lista_asist.$r_as;
													else		$lista_asist = $lista_asist."0";
													if($r_in)	$lista_inasist = $lista_inasist.$r_in;
													else		$lista_inasist = $lista_inasist."0";
												} else {
													$lista_asist   = $lista_asist."_";
													$lista_inasist = $lista_inasist."_";
												}
											}
											//AquÃ­ imprimimos los valores para poder ver el valor decimal y poder comprobar que cada caso este bien ingresado.
											print("<pre>");			// ojo!!!  este <pre></pre> hace que se vean los saltos de lÃ­nea y en fixed width font!

												if($i_asist!=0){

													//echo($i_asist."   ".$sum['n_asistencias']."   ".$sum['n_inasistencias']);
													//echo (PHP_EOL);
													//echo (PHP_EOL);

													$prom_a = round($sum['n_asistencias']/$i_asist,1);
													echo ("    Promedio Asistencias:  ".$prom_a); //se echoea dado que el grafico solo mestra numeros enteros...
													echo (PHP_EOL);
													echo ("  Asistencias Ingresadas:  "."</pre>".$lista_asist."<pre>");
													echo (PHP_EOL);

													echo (PHP_EOL);

													$prom_i = round($sum['n_inasistencias']/$i_asist,1);
													echo ("   Promedio Insistencias:  ".$prom_i); //se echoea dado que el grafico solo mestra numeros enteros...
													echo (PHP_EOL);
													echo ("Inasistencias Ingresadas:  "."</pre>".$lista_inasist."<pre>");
													echo (PHP_EOL);

													//se mandan los datos para grÃ¡fico
													$prom = round($prom_a);
													$datos['asistencias']="Asistencias";
													for($prom_a; $prom_a>0; $prom_a--){
														$datos_casos = array_merge ($datos_casos, array($datos)); // se suman los datos de este caso con los anteriores
													}
													$prom = round($prom_i);
													$datos['asistencias']="Inasistencias";
													for($prom_i; $prom_i>0; $prom_i--){
														$datos_casos = array_merge ($datos_casos, array($datos)); // se suman los datos de este caso con los anteriores
													}

												} else {
													echo ("<SPAN>No hay informacion sobre asistencias. </SPAN>");
													echo (PHP_EOL);
													echo ("<SPAN>No hay informacion sobre inasistencias. <SPAN>");
													echo (PHP_EOL);
												}

											print("</pre>");
											$this->flash("", null);	//es para que no aparezca el grÃ¡fico tÃ­pico de las estadÃ­sticas...

										} else $this->flash("No hay casos clinicos cerrados.", null);

									} break;
								}

							} break;

						}
					} break;


					default : { //consultas referentes a casos y periodos

						/**SE REMPLAZAN LOS ESPECIFICADORES DE PERIODO, PROGRAMA Y PERIODO, ADEMAS DE OBTENER ALGUNAS VARIABLES**/

						//Se agrega seleccion por programa
						$consulta = "Tipocaso.cod_programa = ".$cod_programa." ";

						$cond_convenio = "";
						//Se agrega seleccion por convenio: Si no se selecionÃ³ convenio, "todos los convenios" o sin informacion
						if ($cod_convenio == "T"){
							$cond_convenio.="";
						} else if ($cod_convenio == "S"){
							$cond_convenio.=" AND `Beneficiario`.`cod_convenio` IS NULL";
						} else {
							$cond_convenio.=" AND `Beneficiario`.`cod_convenio` = ".$cod_convenio;
						}
						$consulta.=$cond_convenio." ";

						//Se agrega seleccion por periodo.  ver definicion de $crit mas arriba
						$cond_periodo="AND (( ";
						switch($tipo_periodo){
							case 't':
								$cond_periodo.="`Caso`.`".$crit."` IS NOT NULL";
								break;
							case 'a':
								$cond_periodo.="YEAR(`Caso`.`".$crit."`)=".$agno;
								break;
							case 'm':
								$cond_periodo.="YEAR(`Caso`.`".$crit."`)=".$agno." AND MONTH(`Caso`.`".$crit."`)=".$periodo;
								break;
							case 's':
								$cond_periodo.="YEAR(`Caso`.`".$crit."`)=".$agno." AND YEARWEEK(`Caso`.`".$crit."`,5)=".$agno.$periodo;
								break;
							case 'd':
								//$periodo=explode(",", $periodo); ---> [Dawes]Esto ya se hizo arriba definiendo tÃ­tulos.
								$cond_periodo.="YEAR(`Caso`.`".$crit."`)=".$agno." AND DAY(`Caso`.`".$crit."`)=".$periodo[0]." AND MONTH(`Caso`.`".$crit."`)=".$periodo[1];
							break;
						}
						if($crit_casos=="activos" && $tipo_periodo!="t") {
							$cond_periodo.=" ) OR ( `Caso`.`fec_ingreso` < CAST('".$inicio_periodo."' AS date) AND ( `Caso`.`fec_retiro` > CAST('".$inicio_periodo."' AS date) OR `Caso`.`fec_retiro` IS NULL )";
						}
						$cond_periodo.=" ))";

						$consulta.=$cond_periodo." ";

						//var_dump($consulta);

						//[Dawes] Se hace la busqueda. Retorna array, cada item es un array con el caso, su voluntario, su tipocaso, sus seguimientos (como array), su beneficiario y tipo ingreso (todas las tablas con relacion directa)
						$casos=$this->Caso->query("SELECT Caso.cod_caso, Caso.est_caso, Caso.fec_ingreso, Caso.fec_retiro FROM `casos` AS `Caso` LEFT JOIN `beneficiarios` AS `Beneficiario` ON (`Caso`.`cod_beneficiario` = `Beneficiario`.`cod_persona`) LEFT JOIN `tipocasos` AS `Tipocaso` ON (`Caso`.`cod_tipocaso` = `Tipocaso`.`cod_tipocaso`) WHERE ".$consulta);
						//$casos=$this->Caso->findAll( $consulta  ,null,'Caso.fec_ingreso ASC'); //version cake: mas lenta

						//Se busca el codigo de actividad de las fichas de cierre, para las consultas que lo requieran.
						$actividad_cierre			= $this->Actividad->find("Actividad.cod_programa = ".$cod_programa." AND Actividad.tip_actividad LIKE 'Cierre'",       array('Actividad.cod_actividad'), "", -1);
						$cod_actividad_cierre		= $actividad_cierre['Actividad']['cod_actividad'];

						//Se busca el codigo de actividad de las fichas iniciales, para las consultas que lo requieran.
						$actividad_inicial			= $this->Actividad->find("Actividad.cod_programa = ".$cod_programa." AND Actividad.tip_actividad LIKE 'Inicial'",      array('Actividad.cod_actividad'), "", -1);
						$cod_actividad_inicial		= $actividad_inicial['Actividad']['cod_actividad'];

						//Se busca el codigo de actividad de las fichas iniciales, para las consultas que lo requieran.
						$actividad_reactivar		= $this->Actividad->find("Actividad.cod_programa = ".$cod_programa." AND Actividad.tip_actividad LIKE 'Reactivacion'", array('Actividad.cod_actividad'), "", -1);
						$cod_actividad_reactivar	= $actividad_reactivar['Actividad']['cod_actividad'];


						foreach($casos as $caso){

							$datos['cod_caso']=$caso['Caso']['cod_caso']; //para asegurarse que dada linea tenga informacion
							$datos[$nom_dato] = null;  // inicializa el dato a buscar en null por si no se encuentra una respuesta distinta.



							/****DESDE AQUI SE MANEJAN LAS CONSULTAS de casos compratidas por los programas****/

							//consultas comunes
							switch ($nom_dato) {

								case 'meses_activo': {
									$seguimientos_inicial = $this->Seguimiento->query("SELECT fec_ejecucion FROM seguimientos AS Seguimiento WHERE cod_caso = ".$caso['Caso']['cod_caso']." AND ( cod_actividad = ".$cod_actividad_inicial." OR cod_actividad = ".$cod_actividad_reactivar." ) ORDER BY num_evento DESC");
									$seguimientos_final   = $this->Seguimiento->query("SELECT fec_ejecucion FROM seguimientos AS Seguimiento WHERE cod_caso = ".$caso['Caso']['cod_caso']." AND cod_actividad = ".$cod_actividad_cierre." ORDER BY num_evento DESC");

									//desde aqui se toman todas las fechas, se ordenan, y se trata de sumar todos los periodos entre un primer seguimiento de apertura (entre varios consecutivos si hay) y el ultimo seguimiento de cierre entre el proximo bloque de ellos consecutivo (puede ser uno solo).

									$fechas = array();
									foreach($seguimientos_inicial as $si){
										$f = explode(" ", $si['Seguimiento']['fec_ejecucion']);
										$f = explode("-", $f[0]); //$fecha[2]=dia, $fecha[1]=mes, $fecha[0]=aÃ±o
										$f = mktime(0,0,0,$f[1],$f[2],$f[0]); // hora, min, seg, mes, dia, aÃ±o
										$fechas = array_merge($fechas, array($f."_apertura"));
									}
									foreach($seguimientos_final as $sf){
										$f = explode(" ", $sf['Seguimiento']['fec_ejecucion']);
										$f = explode("-", $f[0]); //$fecha[2]=dia, $fecha[1]=mes, $fecha[0]=aÃ±o
										$f = mktime(0,0,0,$f[1],$f[2],$f[0]); // hora, min, seg, mes, dia, aÃ±o
										$fechas = array_merge($fechas, array($f."_cierre"));
									}
									sort($fechas);
									foreach($fechas as $k => $fec){
										$fechas[$k] = explode("_", $fec);
									}

									//para la primera fecha de inicio se toma la de ingreso del caso, esto pq hay veces que cambian la fecha manualmente.
									$fi = explode(" ", $caso['Caso']['fec_ingreso']);
									$fi = explode("-", $fi[0]); //$fecha[2]=dia, $fecha[1]=mes, $fecha[0]=aÃ±o
									$fi = mktime(0,0,0,$fi[1],$fi[2],$fi[0]); // hora, nim, seg, mes, dia, aÃ±o
									$ff = false;

									$i = 0;
									$n = count($fechas);
									$meses  = null;
									while($i<$n){
										//busca la primera fecha de apertura
										while ($i<$n && !$fi) {
											if($fechas[$i][1]=="apertura") {
												$fi = $fechas[$i][0];
												$i++; //queda listo para buscar en proxima etapa
											}
											else $i++;
										}
										if($fi){
											//se busca la proxima fecha de ceirre, luego se sigue avanzando hasta encontrar la ultima fecha de cierre consecutiva.
											while($i<$n && !$ff){
												if($fechas[$i][1]=="cierre") {
													$ff = $fechas[$i][0];
													$i++;
													//ahora buscamos el ultimo cierre consecutivo, si es que hay.
													$ultimo = false;
													while($i<$n && !$ultimo){
														if($fechas[$i][1]=="cierre"){
															$ff = $fechas[$i][0];
															$i++;
														} else $ultimo = true;
													}
												}
												else $i++;
											}
											if($ff) $meses += $ff - $fi;
											else  	$meses += time() - $fi;
										}
										$fi = null;
										$ff = null;
									}

									if($meses === null){ //equivalencia estricta === pq sino toma 0 como null.
										$datos['meses_activo'] = null;
									} else {
										$datos['meses_activo'] =  " ".floor($meses/(60*60*24*30.44)); //el resultado esta en segundos, por lo que se divide en seg por min, min por hora, horas por dia, dias por mes promedio. Se agrega un espacio para diferenciar de null si sale 0.
									}
									unset($seguimientos_inicial);
									unset($seguimientos_final);

									if($datos['meses_activo'] == null){
										var_dump($caso['Caso']['cod_caso']);
										var_dump($caso['Caso']['fec_ingreso']);
										var_dump($fechas);
									}

								} break;

								case 'resultado':
								case 'asistencia_derivacion':
								{
									if($caso['Caso']['est_caso'] == "Retiro"){ //el caso podrÃ­a tener un seguimiento final pero estar reactivado.
										$seguimientofinal  = $this->Seguimiento->query("SELECT num_evento FROM seguimientos AS Seguimiento WHERE cod_caso = ".$caso['Caso']['cod_caso']." AND cod_actividad = ".$cod_actividad_cierre." ORDER BY num_evento DESC");
									} else {
										$seguimientofinal = null;
									}
									switch ($nom_dato) {

										case 'resultado': {
											if($seguimientofinal){ //si el caso esta cerrado
												$respuestas = $this->Respuestaficha->findall("Respuestaficha.num_evento = ".$seguimientofinal[0]['Seguimiento']['num_evento']);
												if($cod_programa==1){
													foreach($respuestas as $respuesta){
														if(	($respuesta['Respuestaficha']['nom_respuesta']==1)&&
															(
																($respuesta['Respuestaficha']['cod_subpregunta']==179)||
																($respuesta['Respuestaficha']['cod_subpregunta']==180)||
																($respuesta['Respuestaficha']['cod_subpregunta']==181)||
																($respuesta['Respuestaficha']['cod_subpregunta']==182)||
																($respuesta['Respuestaficha']['cod_subpregunta']==183)||
																($respuesta['Respuestaficha']['cod_subpregunta']==184)
															)
														  ) $datos['resultado'] = $respuesta['Subpregunta']['nom_subpregunta'];
													}
												} else if($cod_programa==2){
													foreach($respuestas as $respuesta){
														if(	($respuesta['Respuestaficha']['nom_respuesta']==1)&&
															(
																($respuesta['Respuestaficha']['cod_subpregunta']==238)||
																($respuesta['Respuestaficha']['cod_subpregunta']==186)||
																($respuesta['Respuestaficha']['cod_subpregunta']==187)||
																($respuesta['Respuestaficha']['cod_subpregunta']==188)||
																($respuesta['Respuestaficha']['cod_subpregunta']==189)||
																($respuesta['Respuestaficha']['cod_subpregunta']==190)||
																($respuesta['Respuestaficha']['cod_subpregunta']==191)
															)
														  ) $datos['resultado'] = $respuesta['Subpregunta']['nom_subpregunta'];
													}
												} else {
													$datos['resultado'] = "Caso cerrado y programa no implementado en estadisticas";
												}
											} else {
												$datos['resultado'] = "Caso aun no cerrado";
											}
										} break;

										case 'asistencia_derivacion': {
											if($seguimientofinal){ //si el caso esta cerrado
												$respuestas = $this->Respuestaficha->findall("Respuestaficha.num_evento = ".$seguimientofinal[0]['Seguimiento']['num_evento']);
												foreach($respuestas as $respuesta){
													if(	($respuesta['Respuestaficha']['nom_respuesta']==1)&&
														(
															($respuesta['Respuestaficha']['cod_subpregunta']==254)||
															($respuesta['Respuestaficha']['cod_subpregunta']==255)||
															($respuesta['Respuestaficha']['cod_subpregunta']==256)||
															($respuesta['Respuestaficha']['cod_subpregunta']==257)||
															($respuesta['Respuestaficha']['cod_subpregunta']==265)
														)
													  ) $datos['asistencia_derivacion'] = $respuesta['Subpregunta']['nom_subpregunta'];
												}
											} else {
												$datos['asistencia_derivacion'] = "No ha sido cerrado el caso";
											}
										} break;

									} //end switch para consultas con seguimiento final

								} break; //end case para consultas con seguimiento final


								/****DESDE AQUI SE MANEJAN LAS CONSULTAS referentes al seguimiento inicial, que son mas lentas****/

								default: {
									$seguimientoinicial = $this->Seguimiento->find(array('Seguimiento.cod_caso' => $caso['Caso']['cod_caso'], 'Seguimiento.cod_actividad' => $cod_actividad_inicial),null,'num_evento ASC' ,-1);

									if($seguimientoinicial){// hay casos que podrÃ­an estar fallados por razon X.

										$respuestas = $this->Respuestaficha->query("SELECT `Respuestaficha`.`cod_respuesta`, `Respuestaficha`.`cod_subpregunta`, `Respuestaficha`.`nom_respuesta`, `Subpregunta`.`cod_dimension`, `Subpregunta`.`nom_subpregunta`  FROM `respuestafichas` AS `Respuestaficha` LEFT JOIN `subpreguntas` AS `Subpregunta` ON (`Respuestaficha`.`cod_subpregunta` = `Subpregunta`.`cod_subpregunta`)  WHERE `Respuestaficha`.`num_evento` = ".$seguimientoinicial['Seguimiento']['num_evento']);
										//$respuestas = $this->Respuestaficha->findall("Respuestaficha.num_evento = ".$seguimientoinicial['Seguimiento']['num_evento']); //version cake: muuuy lenta

										if($cod_programa==1){//manejo para acoge

											switch($nom_dato){ //Se usa para ejecutar solo la consulta requerida.

												case 'tipo_flia':
													foreach($respuestas as $respuesta){
														if(	($respuesta['Respuestaficha']['nom_respuesta']==1)&&
															(
																($respuesta['Respuestaficha']['cod_subpregunta']==29)||
																($respuesta['Respuestaficha']['cod_subpregunta']==30)||
																($respuesta['Respuestaficha']['cod_subpregunta']==31)||
																($respuesta['Respuestaficha']['cod_subpregunta']==32)
															)
														  )	$datos['tipo_flia'] = $respuesta['Subpregunta']['nom_subpregunta'];
													}
													if($datos['tipo_flia']=="Otros: especificar") $datos['tipo_flia']= "Otros";
													break;


												case 'estado_civil':
													foreach($respuestas as $respuesta){
														if(	($respuesta['Respuestaficha']['nom_respuesta']==1)&&(
																($respuesta['Respuestaficha']['cod_subpregunta']==18004)||
																($respuesta['Respuestaficha']['cod_subpregunta']==18005)||
																($respuesta['Respuestaficha']['cod_subpregunta']==18006)||
																($respuesta['Respuestaficha']['cod_subpregunta']==18007)||
																($respuesta['Respuestaficha']['cod_subpregunta']==18008)
																)
														  )
															$datos['estado_civil'] = $respuesta['Subpregunta']['nom_subpregunta'];
													}
													break;


												case 'num_hijos': //numero de hijos de la beneficiaria
													foreach($respuestas as $respuesta){
														if(	($respuesta['Respuestaficha']['nom_respuesta']==1)&&
															(
																($respuesta['Respuestaficha']['cod_subpregunta']==2000)||
																($respuesta['Respuestaficha']['cod_subpregunta']==2001)||
																($respuesta['Respuestaficha']['cod_subpregunta']==2002)||
																($respuesta['Respuestaficha']['cod_subpregunta']==2003)||
																($respuesta['Respuestaficha']['cod_subpregunta']==2004)||
																($respuesta['Respuestaficha']['cod_subpregunta']==2005)||
																($respuesta['Respuestaficha']['cod_subpregunta']==2006)||
																($respuesta['Respuestaficha']['cod_subpregunta']==2007)||
																($respuesta['Respuestaficha']['cod_subpregunta']==2008)||
																($respuesta['Respuestaficha']['cod_subpregunta']==2009)
														  	)
														  )
															$datos['num_hijos'] = $respuesta['Subpregunta']['nom_subpregunta'];
													}
													break;


												case 'padre': //Â¿quien es el papa?
													foreach($respuestas as $respuesta){
														if(	($respuesta['Respuestaficha']['nom_respuesta']==1)&&
															(
																($respuesta['Respuestaficha']['cod_subpregunta']==59)||
																($respuesta['Respuestaficha']['cod_subpregunta']==60)||
																($respuesta['Respuestaficha']['cod_subpregunta']==61)||
																($respuesta['Respuestaficha']['cod_subpregunta']==62)
															)
														  ) $datos['padre'] = $respuesta['Subpregunta']['nom_subpregunta'];
													}
													break;


												case 'meses': //meses de embarazo al llamar primera vez
													foreach($respuestas as $respuesta){
														if( ($respuesta['Respuestaficha']['nom_respuesta']==1)&&
															(
																($respuesta['Respuestaficha']['cod_subpregunta']==1049)||
																($respuesta['Respuestaficha']['cod_subpregunta']==1050)||
																($respuesta['Respuestaficha']['cod_subpregunta']==1051)||
																($respuesta['Respuestaficha']['cod_subpregunta']==1052)||
																($respuesta['Respuestaficha']['cod_subpregunta']==1053)||
																($respuesta['Respuestaficha']['cod_subpregunta']==1054)||
																($respuesta['Respuestaficha']['cod_subpregunta']==1055)||
																($respuesta['Respuestaficha']['cod_subpregunta']==1056)||
																($respuesta['Respuestaficha']['cod_subpregunta']==1057)||
																($respuesta['Respuestaficha']['cod_subpregunta']==1058)
															)
														  )
															$datos['meses'] = $respuesta['Subpregunta']['nom_subpregunta'];
													}
													break;


												case 'metodo': //metodo anticonceptivo usado
													foreach($respuestas as $respuesta){
														if(	($respuesta['Respuestaficha']['nom_respuesta']==1)&&
															(
																($respuesta['Respuestaficha']['cod_subpregunta']==49)||
																($respuesta['Respuestaficha']['cod_subpregunta']==50)||
																($respuesta['Respuestaficha']['cod_subpregunta']==51)||
																($respuesta['Respuestaficha']['cod_subpregunta']==510)||
																($respuesta['Respuestaficha']['cod_subpregunta']==52)||
																($respuesta['Respuestaficha']['cod_subpregunta']==53)
															)
														  )
														{
															if($datos['metodo']) $datos['metodo'] = "MÃ¡s de un metodo";
															else $datos['metodo'] = $respuesta['Subpregunta']['nom_subpregunta'];
														}
													}
													break;


												case 'derivacion': //a donde fue derivado el caso
													foreach($respuestas as $respuesta){
														if(	($respuesta['Respuestaficha']['nom_respuesta']==1)&&
															(
																($respuesta['Respuestaficha']['cod_subpregunta']==1061)||
																($respuesta['Respuestaficha']['cod_subpregunta']==1062)||
																($respuesta['Respuestaficha']['cod_subpregunta']==1063)||
																($respuesta['Respuestaficha']['cod_subpregunta']==1064)||
																($respuesta['Respuestaficha']['cod_subpregunta']==1065)||
																($respuesta['Respuestaficha']['cod_subpregunta']==1066)||
																($respuesta['Respuestaficha']['cod_subpregunta']==1067)||
																($respuesta['Respuestaficha']['cod_subpregunta']==1068)||
																($respuesta['Respuestaficha']['cod_subpregunta']==1069)||
																($respuesta['Respuestaficha']['cod_subpregunta']==1070)||
																($respuesta['Respuestaficha']['cod_subpregunta']==1071)||
																($respuesta['Respuestaficha']['cod_subpregunta']==1072)||
																($respuesta['Respuestaficha']['cod_subpregunta']==1073)||
																($respuesta['Respuestaficha']['cod_subpregunta']==1074)
															)
														  )
														{
															if($datos['derivacion']) $datos['derivacion'] = "MÃ¡s de un lugar";
															else $datos['derivacion'] = $respuesta['Subpregunta']['nom_subpregunta'];
														}
													}
													break;


												case 'trabajo': //de la beneficiaria
													foreach($respuestas as $respuesta){
														if(	($respuesta['Respuestaficha']['nom_respuesta']==1)&&
															(
																($respuesta['Respuestaficha']['cod_subpregunta']==42)||
																($respuesta['Respuestaficha']['cod_subpregunta']==43)||
																($respuesta['Respuestaficha']['cod_subpregunta']==44)||
																($respuesta['Respuestaficha']['cod_subpregunta']==45)||
																($respuesta['Respuestaficha']['cod_subpregunta']==46)||
																($respuesta['Respuestaficha']['cod_subpregunta']==47)
															)
														  )	$datos['trabajo'] = $respuesta['Subpregunta']['nom_subpregunta'];
													}
													break;


												case 'nivel_educ': //de la beneficiaria
													$resultado['nivel_educ']=null;
													foreach($respuestas as $respuesta){
														if(	($respuesta['Respuestaficha']['nom_respuesta']==1)&&
															(
																($respuesta['Respuestaficha']['cod_subpregunta']==33)||
																($respuesta['Respuestaficha']['cod_subpregunta']==34)||
																($respuesta['Respuestaficha']['cod_subpregunta']==35)||
																($respuesta['Respuestaficha']['cod_subpregunta']==36)||
																($respuesta['Respuestaficha']['cod_subpregunta']==37)||
																($respuesta['Respuestaficha']['cod_subpregunta']==38)||
																($respuesta['Respuestaficha']['cod_subpregunta']==39)||
																($respuesta['Respuestaficha']['cod_subpregunta']==40)
															)
														  )	$resultado['nivel_educ'] = $respuesta;
													}
													if($resultado['nivel_educ']!=null){
														$dimension = $this->Dimension->find('Dimension.cod_dimension = '.$resultado['nivel_educ']['Subpregunta']['cod_dimension'],null,null,-1);
														$tipo = $dimension['Dimension']['nom_dimension'];
														if($tipo == "I") $tipo = " ".$tipo; //chanchullo para que quede bien ordenado en el grafico.
														$datos['nivel_educ'] = $resultado['nivel_educ']['Subpregunta']['nom_subpregunta']." ".$tipo;
													}
													unset($resultado['nivel_educ']);
													unset($dimension);
													unset($tipo);
													break;

											}// end switch


										} else if($cod_programa==2){//manejo para Comnicate


											switch($nom_dato){ //Se usa para ejecutar solo la consulta requerida.

												case 'tipo_flia':
													foreach($respuestas as $respuesta){
														if(	($respuesta['Respuestaficha']['nom_respuesta']==1)&&
															(
																($respuesta['Respuestaficha']['cod_subpregunta']==115)||
																($respuesta['Respuestaficha']['cod_subpregunta']==116)||
																($respuesta['Respuestaficha']['cod_subpregunta']==117)||
																($respuesta['Respuestaficha']['cod_subpregunta']==118)||
																($respuesta['Respuestaficha']['cod_subpregunta']==119)||
																($respuesta['Respuestaficha']['cod_subpregunta']==120)||
																($respuesta['Respuestaficha']['cod_subpregunta']==121)||
																($respuesta['Respuestaficha']['cod_subpregunta']==122)||
																($respuesta['Respuestaficha']['cod_subpregunta']==123)||
																($respuesta['Respuestaficha']['cod_subpregunta']==124)
															)
														  ) $datos['tipo_flia'] = $respuesta['Subpregunta']['nom_subpregunta'];
													}
													if($datos['tipo_flia']=="Otros: especificar") $datos['tipo_flia']= "Otros";
													break;


												case 'estado_civil':
													foreach($respuestas as $respuesta){
														if(	($respuesta['Respuestaficha']['nom_respuesta']==1)&&
															(
																($respuesta['Respuestaficha']['cod_subpregunta']==126)||
																($respuesta['Respuestaficha']['cod_subpregunta']==127)||
																($respuesta['Respuestaficha']['cod_subpregunta']==128)||
																($respuesta['Respuestaficha']['cod_subpregunta']==129)||
																($respuesta['Respuestaficha']['cod_subpregunta']==130)
															)
														  ) $datos['estado_civil'] = $respuesta['Subpregunta']['nom_subpregunta'];
													}
													break;


												case 'educ_padre':
													$resultado['educ_padre'] = null;
													foreach($respuestas as $respuesta){
														if(	($respuesta['Respuestaficha']['nom_respuesta']==1)&&
															(
																($respuesta['Respuestaficha']['cod_subpregunta']==141)||
																($respuesta['Respuestaficha']['cod_subpregunta']==142)||
																($respuesta['Respuestaficha']['cod_subpregunta']==143)||
																($respuesta['Respuestaficha']['cod_subpregunta']==144)||
																($respuesta['Respuestaficha']['cod_subpregunta']==145)||
																($respuesta['Respuestaficha']['cod_subpregunta']==146)||
																($respuesta['Respuestaficha']['cod_subpregunta']==147)||
																($respuesta['Respuestaficha']['cod_subpregunta']==148)
															)
														  ) $resultado['educ_padre'] = $respuesta;
													}
													if($resultado['educ_padre']!=null){
														$dimension = $this->Dimension->find('Dimension.cod_dimension = '.$resultado['educ_padre']['Subpregunta']['cod_dimension'],null,null,-1);
														$tipo = $dimension['Dimension']['nom_dimension'];
														if($tipo == "I") $tipo = " ".$tipo; //chanchullo para que quede bien ordenado en el grafico.
														$datos['educ_padre'] = $resultado['educ_padre']['Subpregunta']['nom_subpregunta']." ".$tipo;
													}
													unset($resultado['educ_padre']);
													unset($dimension);
													unset($tipo);
													break;


												case 'educ_madre':
													$resultado['educ_madre'] = null;
													foreach($respuestas as $respuesta){
														if(	($respuesta['Respuestaficha']['nom_respuesta']==1)&&
															(
																($respuesta['Respuestaficha']['cod_subpregunta']==153)||
																($respuesta['Respuestaficha']['cod_subpregunta']==154)||
																($respuesta['Respuestaficha']['cod_subpregunta']==155)||
																($respuesta['Respuestaficha']['cod_subpregunta']==156)||
																($respuesta['Respuestaficha']['cod_subpregunta']==157)||
																($respuesta['Respuestaficha']['cod_subpregunta']==158)||
																($respuesta['Respuestaficha']['cod_subpregunta']==159)||
																($respuesta['Respuestaficha']['cod_subpregunta']==160)
															)
														  ) $resultado['educ_madre'] = $respuesta;
													}
													if($resultado['educ_madre']!=null){
														$dimension = $this->Dimension->find('Dimension.cod_dimension = '.$resultado['educ_madre']['Subpregunta']['cod_dimension'],null,null,-1);
														$tipo = $dimension['Dimension']['nom_dimension'];
														if($tipo == "I") $tipo = " ".$tipo; //chanchullo para que quede bien ordenado en el grafico.
														$datos['educ_madre'] = $resultado['educ_madre']['Subpregunta']['nom_subpregunta']." ".$tipo;
													}
													unset($resultado['educ_madre']);
													unset($dimension);
													unset($tipo);
													break;


												case 'trabajo_padre':
													foreach($respuestas as $respuesta){
														if(	($respuesta['Respuestaficha']['nom_respuesta']==1)&&
															(
																($respuesta['Respuestaficha']['cod_subpregunta']==149)||
																($respuesta['Respuestaficha']['cod_subpregunta']==150)||
																($respuesta['Respuestaficha']['cod_subpregunta']==151)
															)
														  ) $datos['trabajo_padre'] = $respuesta['Subpregunta']['nom_subpregunta'];
													}
													break;


												case 'trabajo_madre':
													foreach($respuestas as $respuesta){
														if(	($respuesta['Respuestaficha']['nom_respuesta']==1)&&
															(
																($respuesta['Respuestaficha']['cod_subpregunta']==161)||
																($respuesta['Respuestaficha']['cod_subpregunta']==162)||
																($respuesta['Respuestaficha']['cod_subpregunta']==163)||
																($respuesta['Respuestaficha']['cod_subpregunta']==164)
															)
														  ) $datos['trabajo_madre'] = $respuesta['Subpregunta']['nom_subpregunta'];
													}
													break;


												case 'derivado':
													$resultado['derivado'] = null;
													foreach($respuestas as $respuesta){
														if(	($respuesta['Respuestaficha']['nom_respuesta']==1)&&
															(
																($respuesta['Respuestaficha']['cod_subpregunta']==172)||
																($respuesta['Respuestaficha']['cod_subpregunta']==173)
															)
														  ) $resultado['derivado'] = $respuesta;
													}
													if($resultado['derivado']!=null){
														$dimension = $this->Dimension->find('Dimension.cod_dimension = '.$resultado['derivado']['Subpregunta']['cod_dimension'],null,null,-1);
														$datos['derivado'] = $dimension['Dimension']['nom_dimension'];
													}
													unset($resultado['derivado']);
													unset($dimension);
													break;

											} // end switch consultas comunicate
										} // end if segun tipo de programa

									}// end if seguimiento inicial

									unset($seguimientoinicial);

								} break;  //end case para las consultas sobre la ficha inicial

							} //end switch consultas de casos compratidas/no compartidas por los programas

							$datos_casos = array_merge ($datos_casos,array($datos)); // se suman los datos de este caso con los anteriores

							unset($datos);
							unset($respuesta);
							unset($respuestas);
							unset($seguimientofinal);

						} // end foreach casos

					} break; // end case consultas con casos y periodos...
				}

				//var_dump($datos_casos);

				//$datos_casos viene en formato  datos_casos = array()
				//Ahora $data_presort se deja en formato  $data_presort['categoria'] = (int)cantidad_de_casos.

				$data_presort=array('Sin informacion' => 0 );
				//para cada caso, en la variable dataset se cuenta por tipo de ocurrencia para dar valores agregados.

				foreach($datos_casos as $datos_caso){
					if($datos_caso[$nom_dato]==null){//equivalencia estrcta pq sino toma 0 como null
						$data_presort['Sin informacion']++;
					}else{
						$suma=false;
						foreach($data_presort as $key => $ds){
							if($key==$datos_caso[$nom_dato]){
								$data_presort[$key]++;
								$suma=true;
							}
						}
						if ($suma==false) {
							$data_presort[$datos_caso[$nom_dato]] = 1;
							}
					}
				}
				unset($datos_casos);
				unset($suma);

				//Ahora aplico un sort segun si lo quiero de mayor a menor frecuencia u orden alfabetico categorias.

				//sacamos sin informacion para dejarlo siempre al final
				$s_i = $data_presort['Sin informacion'];
				unset($data_presort['Sin informacion']);
				if($this->query[$key_consulta]['sort']=="frec")			arsort($data_presort); //Segun frecuencia de mayor a menor
				else if ($this->query[$key_consulta]['sort']=="cat")	 ksort($data_presort); //Segun categoria  de menor a mayor
				else if ($this->query[$key_consulta]['sort']=="cat_r")	krsort($data_presort); //Segun categoria  de mayor a menor
				if($s_i!=0) $data_presort += array('Sin informacion' => $s_i);


				/*Ahora necesito llegar a formato:	$dataset[i]['COLUMNA'] = 'categoria'
													$dataset[i]['DATO']    = (int)cantidad_de_casos.
				  con i desde 0 hasta cantidad_de_categorias-1.
				*/

				$dataset=array();
				$contador = 0;
				foreach($data_presort as $categoria => $cantidad){
					$dataset[$contador]['COLUMNA'] = $categoria;
					$dataset[$contador]['DATO']    = $cantidad;
					$contador++;
				}
				unset($data_presort);
				unset($contador);

			} //end consultas cake


			$celdas=array("num_query" => $key_consulta,
						'titulo' => htmlentities($this->query[$key_consulta]['titulo']),
						"xlabel" => htmlentities($key_consulta),
						"dataset" => $dataset);
			//[Ignacio] Ahora se genera un arreglo con Columna => Dato
			$valores=array();
			$labels=array();
			$total=0;
			foreach($dataset as $i=>$v){
				$valores+=array($i => $v['DATO']);
				$total+=$v['DATO'];
				$labels+=array($i => $v['COLUMNA']);
			}
			$this->set('celdas', $celdas);
			$this->set('total', $total);
			$this->set('cod_programa', $cod_programa);
			$this->set('nom_programa', $nom_programa);
			$this->set('cod_convenio', $cod_convenio);
			$this->set('nom_convenio', $nom_convenio);
			$this->set('nom_criterio', $nom_criterio);
			$this->set('nom_periodo', $nom_periodo);
			$this->set('grafico', $key_consulta);
			$this->set('valores', $valores);
			$this->set('labels', $labels);
			$this->set('xlabel', $key_consulta);
			$this->set('titulo', $this->query[$key_consulta]['titulo']);
		}
		function excel(){

			$celdas = unserialize($this->data['Excel']['Hoja']);
			$this->set('celdas',$celdas);
			$this->set('type','estadisticas-'.str_replace(" ","_",strtolower($celdas['titulo'])));
			$this->render('excel', 'excel');
		}
				function excel_voluntarios(){
			$personas = unserialize($this->data['Excel']['Hoja']);
			$this->set('personas',$personas);
			$tipo = $this->data['Excel']['tipo'];
			$this->set('tipo',$tipo);
			$this->set('type','voluntarios');
			$this->set('seguimiento',$this->Seguimiento);
			$this->render('excel_voluntarios', 'excel');
		}


		function voluntarios(){
			$this->escribirHeader("Estadisticas Voluntarios");

			$this->set("seguimiento",$this->Seguimiento);

			$this->set("tipo",$this->data['FormBuscar']['tipo']);
			$nom_rut=$this->data['FormBuscar']['nom_rut']."-".$this->data['FormBuscar']['num_rutcodver'];
			$nom_nombre=$this->data['FormBuscar']['nom_nombre'];
			$nom_appat=$this->data['FormBuscar']['nom_appat'];
			$nom_apmat=$this->data['FormBuscar']['nom_apmat'];

			if(isset($this->data['FormBuscar']['cod_programa']))
				$cod_programa=$this->data['FormBuscar']['cod_programa'];
			else $cod_programa="";
			if(isset($this->data['FormBuscar']['est_voluntario']))
				$est_voluntario=$this->data['FormBuscar']['est_voluntario'];
			else $est_voluntario="";
			if(isset($this->data['FormBuscar']['bit_clinico']))
				$bit_clinico=$this->data['FormBuscar']['bit_clinico'];
			else $bit_clinico="";
			if(isset($this->data['FormBuscar']['est_rol']))
				$est_rol=$this->data['FormBuscar']['est_rol'];
			else $est_rol="";


			//[Ignacio] si el estado es no vacï¿½o, se fitra, sino, no
			$filtroestado=$est_voluntario?array("Voluntario.est_voluntario"=>$est_voluntario):array();
			//[Ignacio] si el programa es no vacï¿½o, se filtra, sino, no
			$filtroprograma=$cod_programa?array("Voluntario.cod_programa"=>$cod_programa):array();
			//[Ignacio] si el programa es no vacï¿½o, se filtra, sino, no
			$filtroclinico=($bit_clinico!="")?array("Voluntario.bit_clinico"=>$bit_clinico):array();

			// lo mismo para rol
			$filtrorol=($est_rol!="")?array("Voluntario.est_rol"=>$est_rol):array();

			$personas=$this->Voluntario->findAll(array("Persona.nom_rut" => "like %$nom_rut%",
										"Persona.nom_nombre" => "like %$nom_nombre%",
										"Persona.nom_appat" => "like %$nom_appat%",
										"Persona.nom_apmat" => "like %$nom_apmat%")+
										$filtroestado+$filtroprograma+$filtroclinico+$filtrorol, "",
										array("Persona.nom_nombre" => "asc",
										"Persona.nom_appat" => "asc",
										"Persona.nom_apmat" => "asc"));

			if(count($personas)==0)
			{
				$mensaje="No existen resultados para los criterios ingresados";
				$this->set("mensaje", $mensaje);
			}
			$this->set('personas', $personas);

			$comunas=$this->Comuna->getAllAsArray();
			$this->set('comunas', $comunas);
		}
	}
?>
