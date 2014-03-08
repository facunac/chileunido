var DefaultName = "opcion";
var DefaultNameIncrementNumber = 0;


// No further customizations required.
function GenerateOptions(id, type, name, value, tag){
    var numOp = document.getElementById("PreguntaEncuestaNumOpciones");
    numOp.value=0;
    DefaultNameIncrementNumber = 0;
    var inhere = document.getElementById("inhere");
    inhere.innerHTML = "";
    AddFormField(id, type, name, value, tag);
    var tipo = document.getElementById("PreguntaEncuestaTipo").value;
    var linkMas = document.getElementById("linkMas");
    linkMas.innerHTML = "";
    if(tipo != "text"){

        linkMas.innerHTML = "<a href=\"javascript:AddFormField('inhere','radio','','','div')\"  >[mas]</a>";
    }
    var aElim = document.getElementById("aElim");
    aElim.innerHTML = "";


}

function AddFormField(id,type,name,value,tag) {
    if(! document.getElementById && document.createElement) {
        return;
    }
    var d = document.getElementById("PreguntaEncuestaTipo").value;
    var numOp = document.getElementById("PreguntaEncuestaNumOpciones");

    if(d != "0"){
        var inhere = document.getElementById(id);
        if(name.length < 1) {
            DefaultNameIncrementNumber++;
            numOp.value= DefaultNameIncrementNumber;
            name = String(DefaultName + DefaultNameIncrementNumber);
        }

        if(d == "text"){
            if(tag.length > 0) {
                var thetag = document.createElement(tag);
                thetag.innerHTML = '<textarea name="' + name + '" cols=35 rows=5></textarea><br>'
                inhere.appendChild(thetag);
            }
        }
        else if(d == "checkbox"){
            var formfield = document.createElement("input");
            type = 'checkbox';
            formfield.type = type;
            formfield.name = name;
            formfield.id = name;
            formfield.value = value;
            var formfield2 = document.createElement("input");
            formfield2.name = name+'text';
            formfield2.type = 'text';
            formfield2.value = 'Opcion';
            formfield2.id = name+'text';
            if(tag.length > 0) {
                var thetag = document.createElement(tag);
                thetag.appendChild(formfield);
                thetag.appendChild(formfield2)
                inhere.appendChild(thetag);
            }
            if(numOp.value>1){
                var aElim = document.getElementById("aElim");
                aElim.innerHTML = "<a href=\"javascript:EliminateFormField('"+name+"')\"  >[Eliminar]</a>";
            }
        }

        else if(d == "radio"){
            var formfield = document.createElement("input");
            type = 'radio';
            formfield.type = type;
            formfield.name = name;
            formfield.value = value;
            formfield.id = name;
            var formfield2 = document.createElement("input");
            formfield2.name = name+'text';
            formfield2.type = 'text';
            formfield2.value = 'Opcion';
            formfield2.id = name+'text';
            if(tag.length > 0) {
                var thetag = document.createElement(tag);
                thetag.appendChild(formfield);
                thetag.appendChild(formfield2)
                inhere.appendChild(thetag);
            }
            var aElim = document.getElementById("aElim");
            if(numOp.value!=1){

                aElim.innerHTML = "<a href=\"javascript:EliminateFormField('"+name+"')\"  >[Eliminar]</a>";
            }else{
                aElim.innerHTML = "";
            }
        }
		else if(d == "ranking"){
            var formfield = document.createElement("input");
            type = 'text';
            formfield.type = type;
            formfield.name = name;
            formfield.value = value;
			formfield.size = 2;
            formfield.id = name;
            var formfield2 = document.createElement("input");
            formfield2.name = name+'text';
            formfield2.type = 'text';
            formfield2.value = 'Opcion';
            formfield2.id = name+'text';
            if(tag.length > 0) {
                var thetag = document.createElement(tag);
                thetag.appendChild(formfield2)
				thetag.appendChild(formfield);
                inhere.appendChild(thetag);
            }
            var aElim = document.getElementById("aElim");
            if(numOp.value!=1){

                aElim.innerHTML = "<a href=\"javascript:EliminateFormField('"+name+"')\"  >[Eliminar]</a>";
            }else{
                aElim.innerHTML = "";
            }
        }

    }
} // function AddFormField()

function EliminateFormField(id){
    var numOp = document.getElementById("PreguntaEncuestaNumOpciones");
    var numOpVal = numOp.value;

    if(numOpVal!=1){
        DefaultNameIncrementNumber--;
        var nuevoName = String(DefaultName + DefaultNameIncrementNumber);
        numOp.value = numOpVal-1;
        var aEliminar = document.getElementById(id);
        var padre = aEliminar.parentNode;
        padre.removeChild(aEliminar);
        var aEliminar2 = document.getElementById(id+"text");
        var padre2 = aEliminar2.parentNode;
        padre2.removeChild(aEliminar2);
        if(DefaultNameIncrementNumber>0){
            var divaEliminar = document.getElementById("aElim");
            divaEliminar.innerHTML="<a href=\"javascript:EliminateFormField('"+nuevoName+"')\"  >[Eliminar]</a>";
        }

    }
}

function validarNuevaEncuesta(){
        var pasoFechaInicio = false;
        var pasoFechaFin = false;
        var validos = 0;
        var correctos = 4;
	var titulo = document.getElementById("EncuestaTitulo");
        var fechaInicio = document.getElementById("EncuestaFechaInicio");
        var fechaFin = document.getElementById("EncuestaFechaFin");
        var tituloV = document.getElementById("EncuestaTitulo_v");
        var fechaInicioV = document.getElementById("EncuestaFechaInicio_v");
        var fechaFinV = document.getElementById("EncuestaFechaFin_v");
        fechaInicioV.style.visibility="hidden";
        fechaFinV.style.visibility="hidden";
        tituloV.style.visibility="hidden";
        var regexp;
        if(titulo){
            regexp = /^.+$/;
            if(!regexp.test(titulo.value)){
                 tituloV.innerHTML="El título no puede ser vacio";
                 tituloV.style.visibility="visible";

            } else {
                    validos++;
            }
        }


	if(fechaInicio){
		regexp = /^\d{4}-\d{2}-\d{2}$/;
		if(!regexp.test(fechaInicio.value)){
                    fechaInicioV.innerHTML="La fecha tiene que seguir el formato AAAA-MM-DD (por ejemplo \"2009-10-18\")";
                    fechaInicioV.style.visibility="visible";
		} else {
			if(isDate("EncuestaFechaInicio")){
                            validos++;
                            pasoFechaInicio = true;
                        }
		}
	}

	if(fechaFin){
		regexp = /^\d{4}-\d{2}-\d{2}$/;
		if(!regexp.test(fechaFin.value)){
                    fechaFinV.innerHTML="La fecha tiene que seguir el formato AAAA-MM-DD (por ejemplo \"2009-10-18\")";
                    fechaFinV.style.visibility="visible";
		} else {
			if(isDate("EncuestaFechaFin")){
                            validos++;
                            pasoFechaFin = true;
                        }
		}
	}

        if(pasoFechaInicio && pasoFechaFin){
            if(fechaInicio.value < fechaFin.value ){
                validos++;
            }else{
                fechaFinV.innerHTML = "La fecha de inicio tiene que ser menor a la de término";
                fechaFinV.style.visibility="visible";
            }
        }
	if(validos == correctos) return true;
        else return false;


}

function validarNuevaPregunta(){
        var validos = 0;
        var correctos = 2;
	var titulo = document.getElementById("PreguntaEncuestaTitulo");
        var tipo = document.getElementById("PreguntaEncuestaTipo");
        var tituloV = document.getElementById("PreguntaEncuestaTitulo_v");
        var tipoV = document.getElementById("PreguntaEncuestaTipo_v");
        tituloV.style.visibility="hidden";
        tipoV.style.visibility="hidden";
        var regexp;
        if(titulo){
            regexp = /^.+$/;
            if(!regexp.test(titulo.value)){
                 tituloV.innerHTML="El título no puede ser vacio";
                 tituloV.style.visibility="visible";

            } else {
                    validos++;
            }
        }


	if(tipo){
		if(tipo.value==0){
                    tipoV.innerHTML="Debe seleccionar un tipo de opción";
                    tipoV.style.visibility="visible";
		} else {
                    validos++;

		}
	}

	
	if(validos == correctos) return true;
        else return false;

}

function cambiarAHabilitada(){
	var habilitada = document.getElementById("habilitada");
	habilitada.value = 1;
	
}
function validarRespuestas(){
	
	var preguntas = new Array();
	preguntas = getElementByClass("divPregunta");
	var incorrectos = 0;
	var mostrar = "";
	var pos = 0;
	var tipo ="";
	var id ="";
	var oblig;
	for(i=0; i< preguntas.length; i++){
		pos = preguntas[i].name.indexOf("%");
		tipo = preguntas[i].name.substring(0,pos);
		id = preguntas[i].name.substring(pos+1);
		oblig = document.getElementById("oblig"+id);
		if(oblig.value==1){
			
			if(tipo=="text")
				incorrectos= incorrectos+validarTexto(id);
			else if(tipo=="radio")
				incorrectos+=validarRadio(id);
			else if(tipo=="checkbox")
				incorrectos+=validarCheckBox(id);
			else if(tipo=="ranking")
				incorrectos+=validarRanking(id,1);
				
		}else{
			if(tipo == "ranking")
				incorrectos+=validarRanking(id,0);
		}
		
		
		
		
	}
	
	if(incorrectos>0){
		
		return false;
	}
	else return true;
		
	
}

function validarTexto(id){
	var element = document.getElementsByName("opcion"+id)[0];
	var verific = document.getElementById("verific_"+id);
	verific.style.visibility="hidden";
	if(vacio(element.value)){
		verific.innerHTML = "Pregunta Obligatoria";
		verific.style.visibility="visible";
		
		return 1;
	}
	//Cambiar a return 0
	return 0;
}

function validarRadio(id){
	var element = new Array();
	
	element = document.getElementsByName("opcion"+id);
	var verific = document.getElementById("verific_"+id);
	verific.style.visibility="hidden";
	var i=0;
	
	for(i=0; i<element.length; i++){
		if(element[i].checked){
			//Cambiar a return 0
			return 0;
		}
	}
	verific.innerHTML="Pregunta Obligatoria";
	verific.style.visibility = "visible";
	return 1;
}

function validarCheckBox(id){
	var element = new Array();
	
	element = document.getElementsByName("opcion"+id+"[]");
	var verific = document.getElementById("verific_"+id);
	verific.style.visibility="hidden";
	var i=0;
	
	for(i=0; i<element.length; i++){
		if(element[i].checked){
			//Cambiar a return 0
			return 0;
		}
	}
	verific.innerHTML="Pregunta Obligatoria";
	verific.style.visibility = "visible";
	return 1;
}

function validarRanking(id,oblig){
	
	var element = new Array();
	element = document.getElementsByName("opcion"+id+"[]");
	var verific = document.getElementById("verific_"+id);
	verific.style.visibility="hidden";
	var i=0;
	var j=0;
	var sonVacios = 0;
	var noInteger = 0;
	
	for(i=0; i< element.length-1;i++){
		if(vacio(element[i].value)){
			sonVacios++;
			continue;
		}
		if(!isInteger(element[i].value)){
			noInteger++;
			continue;
		}
	}
	if(sonVacios>0){
		if(oblig==1){
			verific.innerHTML = "Pregunta Obligatoria. Debe asignar un valor distinto a cada opción";
			verific.style.visibility="visible";
			return 1;	
		}else{
			if(sonVacios!= element.length){
				verific.innerHTML = "Debe asignar un valor distinto a cada una de las opciones";
				verific.style.visibility="visible";
				return 1;	
			}
			else
				//Cambiar a return 0
				return 0;
		}
	}else{
		if(noInteger>0){
			verific.innerHTML = "Debe asignar un valor númerico a cada opción";
			verific.style.visibility="visible";
			return 1;	
		}
	}
	for(i=0; i<element.length-1; i++){
				
		var valor = element[i].value * 1;
		if(valor>element.length){
			verific.innerHTML = "No puede asignar un valor fuera del rango 0 - "+element.length;
			verific.style.visibility="visible";
			return 1;	
		}
		for(j=i+1; j<element.length; j++){
			var valorj = element[j].value * 1;
			if(valor==valorj){
				verific.innerHTML = "No puede asignar el mismo valor a dos opciones";
				verific.style.visibility="visible";
				return 1;	
			}
		}
		
	}
	//Cambiar a 0
	return 0;
}




var dtCh = "-";
var minYear = 1900;
var maxYear = 2100;

function stripCharsInBag(s, bag){
	var i;
    var returnString = "";
    // Search through string's characters one by one.
    // If character is not in bag, append to returnString.
    for (i = 0; i < s.length; i++){
        var c = s.charAt(i);
        if (bag.indexOf(c) == -1) returnString += c;
    }
    return returnString;
}

function isInteger(s){
	var i;
    for (i = 0; i < s.length; i++){
        // Check that current character is number.
        var c = s.charAt(i);
        if (((c < "0") || (c > "9"))) return false;
    }
    // All characters are numbers.
    return true;
}

function isDate(fecha){
	var da = document.getElementById(fecha);
	var val = fecha+"_v";
	var verific = document.getElementById(val);
	var dtStr = da.value;
	var daysInMonth = DaysArray(12);
	var pos1=dtStr.indexOf(dtCh);
	var pos2=dtStr.indexOf(dtCh,pos1+1);
	var strMonth=dtStr.substring(pos1+1,pos2);
	var strDay=dtStr.substring(pos2+1);
	var strYear=dtStr.substring(0,pos1);
	strYr=strYear;
	if (strDay.charAt(0)=="0" && strDay.length>1) strDay=strDay.substring(1);
	if (strMonth.charAt(0)=="0" && strMonth.length>1) strMonth=strMonth.substring(1);
	for (var i = 1; i <= 3; i++) {
		if (strYr.charAt(0)=="0" && strYr.length>1) strYr=strYr.substring(1);
	}
	month=parseInt(strMonth);
	day=parseInt(strDay);
	year=parseInt(strYr);
	if (pos1==-1 || pos2==-1){
		verific.innerHTML="Formato: aaaa-mm-dd";
                verific.style.visibility="visible";
		return false;
	}
	if (strMonth.length<1 || month<1 || month>12){
		verific.innerHTML="Mes invalido";
                verific.style.visibility="visible";
		return false;
	}
	if (strDay.length<1 || day<1 || day>31 || (month==2 && day>daysInFebruary(year)) || day > daysInMonth[month]){
		verific.innerHTML="Dia invalido";
                verific.style.visibility="visible";
		return false;
	}
	if (strYear.length != 4 || year==0 || year<minYear || year>maxYear){
		verific.innerHTML ="Año entre "+minYear+" y "+maxYear;
                verific.style.visibility="visible";
		return false;
	}
	if (dtStr.indexOf(dtCh,pos2+1)!=-1 || isInteger(stripCharsInBag(dtStr, dtCh))==false){
		verific.innerHTML="Fecha invalida";
                verific.style.visibility="visible";
		return false;
	}
	return true;
}

function daysInFebruary (year){
	// February has 29 days in any year evenly divisible by four,
    // EXCEPT for centurial years which are not also divisible by 400.
    return (((year % 4 == 0) && ( (!(year % 100 == 0)) || (year % 400 == 0))) ? 29 : 28 );
}

function DaysArray(n) {
	for (var i = 1; i <= n; i++) {
		this[i] = 31;
		if (i==4 || i==6 || i==9 || i==11) {this[i] = 30;}
		if (i==2) {this[i] = 29;}
   }
   return this;
}



function getElementByClass(theClass) {

	var allHTMLTags = new Array();
	var retorno = new Array();
	//Create Array of All HTML Tags
	var j =0;
	var allHTMLTags=document.getElementsByTagName("div");

	//Loop through all tags using a for loop
	for (i=0; i<allHTMLTags.length; i++) {

		//Get all tags with the specified class name.
		if (allHTMLTags[i].className==theClass) {

			retorno[j] = allHTMLTags[i];
			j++;

		}
	}
	return retorno;
}
String.prototype.trim = function () {
   return this.replace(/^\s*/, "").replace(/\s*$/, "");
}
function vacio(valor){
	var nuevoVal = valor.trim();
	regexp = /^.+$/;
	if(!regexp.test(nuevoVal)){
		return true;

	} else {
		return false;
	}
}
