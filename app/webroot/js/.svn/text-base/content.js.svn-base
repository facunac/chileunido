function cargar_ubicacion(target,location)
{
  var peticion;
  var myConn = new XHConn();
  if (!myConn) alert("XMLHTTP no esta disponible. Intentalo con un navegador mas nuevo.");
  peticion=function(oXML){document.getElementById(target).innerHTML=oXML.responseText;};
  myConn.connect(location, "GET", "", peticion);
}