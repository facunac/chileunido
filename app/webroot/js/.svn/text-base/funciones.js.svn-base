var i=0;
var modified=false;

function getTurno(nombrediv, ini, clinico){
	if(ini!=0 && !modified){
		i=ini;
		modified=true;
	}
	document.getElementById(nombrediv).innerHTML=document.getElementById(nombrediv).innerHTML + 
		'<br /><p><label>D&iacute;a</label>' +
		'<select name="data[Dia][' + i + ']"  id="VoluntarioNomTurnoDia1">' +
			'<option value="" ></option>' +
			'<option value="Lunes" >Lunes</option>' +
			'<option value="Martes" >Martes</option>' +
			'<option value="Miercoles" >Miercoles</option>' +
			'<option value="Jueves" >Jueves</option>' +
			'<option value="Viernes" >Viernes</option>' +
			'<option value="Sabado" >Sabado</option>' +
			'<option value="Domingo" >Domingo</option>' +
		'</select><br />' +
		'<label>Hora inicio</label>' +
		'<select name="data[HoraInicio_hora][' + i + ']" class="select_chico">' +
			'<option value="08" >08</option>' +
			'<option value="09" >09</option>' +
			'<option value="10" >10</option>' +
			'<option value="11" >11</option>' +
			'<option value="12" >12</option>' +
			'<option value="13" >13</option>' +
			'<option value="14" >14</option>' +
			'<option value="15" >15</option>' +
			'<option value="16" >16</option>' +
			'<option value="17" >17</option>' +
			'<option value="18" >18</option>' +
			'<option value="19" >19</option>' +
		'</select>' + 
		'<select name="data[HoraInicio_min][' + i + ']" class="select_chico">' +
			'<option value="00" >00</option>' +
			'<option value="30" >30</option>' +
		'</select><br />' + 
		'<label>Hora fin</label>' +
		'<select name="data[HoraFin_hora][' + i + ']" class="select_chico">' +
			'<option value="08" >08</option>' +
			'<option value="09" >09</option>' +
			'<option value="10" >10</option>' +
			'<option value="11" >11</option>' +
			'<option value="12" >12</option>' +
			'<option value="13" >13</option>' +
			'<option value="14" >14</option>' +
			'<option value="15" >15</option>' +
			'<option value="16" >16</option>' +
			'<option value="17" >17</option>' +
			'<option value="18" >18</option>' +
			'<option value="19" >19</option>' +
		'</select>' + 
		'<select name="data[HoraFin_min][' + i + ']" class="select_chico">' +
			'<option value="00" >00</option>' +
			'<option value="30" >30</option>' +
		'</select><br />' + 
		'<input type="hidden" name="data[BitClinico][' + i + ']" value="'+clinico+'" />' +
		'</p>';
	i++;
}

var initAttempts = 0;

function disableDates(fechas) {
        try {
                var dp = datePickerController.getDatePicker("SeguimientoFecProxrevision");
        } catch(err) {
                if(initAttempts++ < 10) setTimeout("disableDates(fechas)", 200);
                return;
        };

       
        dp.setDisabledDates(fechas);
}
datePickerController.addEvent(window, 'load', disableDates);

//verifica si un atributo unique existe en la BD
function isUnique(id_div, modelo, atributo, valor){
	elem = valor;
	new Ajax.Updater(id_div, 'unique/'+modelo+'/'+atributo, {asynchronous:true, evalScripts:true, parameters:Form.Element.serialize(elem)});
}

function Revision(nombrediv, numvisitas)
{
	//alert(numvisitas);
	document.getElementById(nombrediv).innerHTML='<a href="revisar_disponibilidad" onclick="javascript:RevisionMax(numvisitas);">Revisar Disponibilidad</a>';
	/*
	var f = document.forms[0];
	var e = f.elements["data[FormCrear][tip_proxrevision]"];
	if(e.value=='Visita')
		document.getElementById(nombrediv).innerHTML=
	else
		document.getElementById(nombrediv).innerHTML="";
		*/
}

function RevisionMax(numvisitas)
{
	if(numvisitas>0)
	{	
		alert("Existe disponibilidad para derevacion a fundacion");
		return true;
	}
	else
	{
		alert("No existe disponibilidad para derivacion a fundacion");
		return false;
	}
}