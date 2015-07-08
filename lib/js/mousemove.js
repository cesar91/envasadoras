//Si el navegador del cliente es Mozilla la variable siguiente valdr� true
var moz = document.getElementById && !document.all;
//Flag que indica si estamos o no en proceso de arrastrar el rat�n
var estoyArrastrando = false;
//Variable para almacenar un puntero al objeto que estamos moviendo
var dobj;

function presionarBoton(e) {
  //Obtenemos el elemento sobre el que se ha presionado el bot�n del rat�n
  var fobj = moz ? e.target : event.srcElement;

  // Buscamos el primer elemento en la que est� contenido aquel sobre el que se ha pulsado
  // que pertenezca a la clase objMovible. Esto es necesario por si hemos pinchando sobre
  // un elemento contenido dentro de otro pero este �ltimo es el que pertenece a la clase
  // objmovible
  while (fobj.tagName.toLowerCase() != "html" && fobj.className != "objMovible") {
    fobj = moz ? fobj.parentNode : fobj.parentElement;
  }

  // Si hemos obtenido un objeto movible...			
  if (fobj.className == "objMovible") {
    // Activamos el flag para indicar que se empieza a arrastrar
    estoyArrastrando = true;
    // Guardamos un puntero al objeto que se est� moviendo en la variable global
    dobj = fobj;
    // Devolvemos false para no realizar ninguna acci�n posterior
    return false;
  }
}
//Asociamos la funci�n al evento onmousedown
document.onmousedown = presionarBoton;

function arrastrarRaton(e){
  if (estoyArrastrando) {
    // Obtenemos las coordenadas X e Y del rat�n (de forma diferente dependiendo del navegador del cliente)
    newLeft = moz ? e.clientX : event.clientX;
    newTop = moz ? e.clientY : event.clientY;

    // Posicionamos el objeto en las nuevas coordenadas y aplicamos unas desviaciones
    // horizontal y vertical correspondientes a la mitad del ancho y alto del elemento
    // que movemos para colocar el puntero en el centro de la capa movible.
    dobj.style.left = newLeft - parseInt(dobj.style.width)/2;
    dobj.style.top = newTop - parseInt(dobj.style.height)/2;

    // Devolvemos false para no realizar ninguna acci�n posterior
    return false;
  }
}
//Asociamos la funci�n al evento onmousemove
document.onmousemove = arrastrarRaton;

function soltarBoton(e) {	
  estoyArrastrando = false;
}
//Asociamos la funci�n al evento onmouseup
document.onmouseup = soltarBoton;
