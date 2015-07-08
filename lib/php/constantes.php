<?php


/* Rutas para la carga de
 * archivos AMEX
*/
//$RUTA_AMEX_FB = "C:/wamp/www/amex";
$RUTA_AMEX_FB = "C:/Program Files (x86)/Zend/Apache2/htdocs/envasadoras/amex";

/**
 *  Configuracion global del sistema
 */
$SERVER = $_SERVER["SERVER_NAME"];//"201.159.131.127";
$RUTA_R = "/envasadoras/";//"/eexpenses_marti_dev/";
$RUTA_A = $_SERVER["DOCUMENT_ROOT"]."/envasadoras"; // C:/Program Files (x86)/Zend/Apache2/htdocs
$RUTA_SAP = $_SERVER["DOCUMENT_ROOT"]."/envasadoras/datos/InterfazSAP/"; //Con diagonal invertida al final
$RUTA_AMEX = $_SERVER["DOCUMENT_ROOT"]."/InterfazEnvasadorasAMEX/entrada/"; //Ruta donde se depositaran los archivos de AMEX
$RUTA_RH = $_SERVER["DOCUMENT_ROOT"]."/InterfazEnvasadorasSAP/RH/"; //Con diagonal invertida al final
$RUTA_POLIZA = $_SERVER["DOCUMENT_ROOT"]."/InterfazEnvasadorasSAP/Polizas/"; //Con diagonal invertida al final

//Ruta para direccionar la consulta de Solicitudes con las Notificaciones
$RUTA_CONSULTA_NOTIFICACIONES = "../solicitudes/index.php?edit_view=edit_view&noti=noti&id=";
//$RUTA_SAP = "C:/Archivos de programa/pix/project/x/"; //Con diagonal invertida al final
$APP_TITULO = utf8_decode("Sistema Electr&oacute;nico de Gastos");
define("FLUJO_AMEX", 2);
define("ANTICIPO_AMEX_ETAPA_AGENCIA", 2);
define("ANTICIPO_AMEX_ETAPA_APROBACION", 3);
define("ANTICIPO_AMEX_ETAPA_APROBADA", 4);
define("ANTICIPO_AMEX_ETAPA_RECHAZADA", 5);

/*
 *  Acceso a MySQL
 */
$MYSQL_HOST="127.0.0.1";
$MYSQL_PORT="3306";
$MYSQL_USER="root";
$MYSQL_PASSWORD="";
$MYSQL_DATABASE="EXPENVKIODES";

/*
 *  Acceso a Oracle
 */
$DISABLE_ORACLE=false;

// Informacion de conexion a Oracle (Para PeopleSoft)
$ORACLE_USER='masngexpenses';
$ORACLE_PASSWORD='expenses01';
$ORACLE_HOST_SID='';

/* 
 *  Configuracion de Correo
 */
/*$SMTP_HOST="mail.masnegocio.com;"; 
$SMTP_PORT="25";
$SMTP_AUTH=true;
$SMTP_USERNAME="portales@masnegocio.com";
$SMTP_PASSWORD="Ht8Pv12S";
$SMTP_FROM="portales@masnegocio.com";
$SMTP_FROMNAME="eexpenses cloudserv01";
$SMTP_ACTIVAR_CORREO=false;*/

/* 
 *  Configuracion de Correo
 */
/*$SMTP_HOST="mail.inso-mexico.net;"; 
$SMTP_PORT="2525";
$SMTP_PORT="25";
$SMTP_AUTH=true;
$SMTP_USERNAME="portales@masnegocio.com";
$SMTP_USERNAME="masnegocio@inso-mexico.net";
$SMTP_PASSWORD="masnegocio";
$SMTP_FROM="masnegocio@inso-mexico.net";
$SMTP_FROMNAME="Sistema Electrï¿½nico de Gastos";
$SMTP_ACTIVAR_CORREO=true;*/

$SMTP_HOST=".com.mx";
$SMTP_PORT="25";
$SMTP_AUTH=true;
$SMTP_USERNAME="@.com.mx";
$SMTP_PASSWORD=""; 
$SMTP_FROM="Envasadoras.com.mx";
$SMTP_FROMNAME="Sistema de Gastos de Viaje";
$SMTP_ACTIVAR_CORREO=true;


/*
 *  Parametrizacion del sistema
 */
$BLOQUEA_NUEVAS_SOLICITUDES_SI_HAY_COMPROBACIONES_PENDIENTES=false;

/*
 *  Definiciones globales. - A partir de este punto no es necesario modificar nada
 */

//DIRECTORIO DONDE SE ENCUENTRAN LOS FLUJOS
$DIR_FLUJOS = $RUTA_A . "flujos/";

//ESTATUS PARA EL ERP
$ESTATUS_ERP_PENDIENTE = 0;
$ESTATUS_ERP_AUTORIZADO = 1;
$ESTATUS_ERP_ENVIADO = 2;
$ESTATUS_ERP_EXITOSO = 3;
$ESTATUS_ERP_FALLIDO = 4;

//
// DEFINICION DE FLUJOS Y ETAPAS
//
define("FLUJO_SOLICITUD", 1);
define("FLUJO_SOLICITUD_GASTOS", 2);
define("FLUJO_COMPROBACION", 3);
define("FLUJO_COMPROBACION_GASTOS", 4);

//
// CONSTANTES RUTA AUTORIZACION (Niveles)
//
define("EMPLEADO",0);
define("GERENTE_AREA",1);
define("DIRECTOR_AREA",2);
define("DIRECTOR_GENERAL",3);
define("CONTRALORIA",4); // Pendiente de definición

//
// CONSTANTES TIPOS DE USUARIO SUPERVISOR Y GERENTE DE FINANZAS
//
define("FINANZAS", 6);
define("SUPERVISOR_FINANZAS", 7);
define("GERENTE_FINANZAS", 8);
define("CONTROL_INTERNO", 10);
define("CONTROLLER", 5);

/////////////////////////////////////////////////////////////////
//
//  DEFINICION DE CADA UNA DE LAS ETAPAS DE LOS FLUJOS
//
/////////////////////////////////////////////////////////////////
//
//
//  SOLICITUD
//
//                                                   |----> ETAPA_APROBADA
//     ETAPA_SOLICITUD ----->  ETAPA_APROBACION  ----|
//                                                   |----> ETAPA_RECHAZADA
//
/* ANTERIORES
define("SOLICITUD_ETAPA_SOLICITUD", 1);
define("SOLICITUD_ETAPA_APROBACION", 2);
define("SOLICITUD_ETAPA_APROBADA", 3);
define("SOLICITUD_ETAPA_RECHAZADA", 4);
define("SOLICITUD_ETAPA_COTIZADA", 5);
define("SOLICITUD_ETAPA_APROBADA_PARCIAL", 6);
define("SOLICITUD_ETAPA_COMPRADA", 7);
*/
/*
define("SOLICITUD_ETAPA_ENVIADA", 1);
define("SOLICITUD_ETAPA_EN_REVISION", 2);
define("SOLICITUD_ETAPA_REVISADA", 3);
*/
define("SOLICITUD_ETAPA_AGENCIA", 1);
define("SOLICITUD_ETAPA_EN_COTIZACION", 2);
define("SOLICITUD_ETAPA_COTIZADA", 3);
define("SOLICITUD_ETAPA_CANCELADA", 4);
define("SOLICITUD_ETAPA_EN_APROBACION", 5);
define("SOLICITUD_ETAPA_RECHAZADA", 6);
define("SOLICITUD_ETAPA_APROBADA", 7);
define("SOLICITUD_ETAPA_SIN_ENVIAR", 8);
define("SOLICITUD_ETAPA_COMPRADA", 9);
define("SOLICITUD_ETAPA_SEGUNDA_APROBACION", 10);
define("SOLICITUD_ETAPA_EN_APROBACION_POR_DIRECTOR", 11);
define("SOLICITUD_ETAPA_RECHAZADA_POR_DIRECTOR", 12);

//
//
//  SOLICITUD INVITACION
//
//                                                   |----> ETAPA_APROBADA
//     ETAPA_SOLICITUD ----->  ETAPA_APROBACION  ----|
//                                                   |----> ETAPA_RECHAZADA
//
define("SOLICITUD_GASTOS_ETAPA_SIN_ENVIAR", 1);
define("SOLICITUD_GASTOS_ETAPA_APROBACION", 2);
define("SOLICITUD_GASTOS_ETAPA_APROBADA", 3);
define("SOLICITUD_GASTOS_ETAPA_RECHAZADA", 4);
define("SOLICITUD_GASTOS_ETAPA_EN_APROBACION_POR_DIRECTOR", 5);
define("SOLICITUD_GASTOS_ETAPA_RECHAZADA_POR_DIRECTOR", 6);
//
//
//  COMPROBACION DE GASTOS
//
//                                                   |----> ETAPA_APROBADA
//     ETAPA_SOLICITUD ----->  ETAPA_APROBACION  ----|
//                                                   |----> ETAPA_RECHAZADA
//
/* ANTERIORES
define("COMPROBACION_ETAPA_SOLICITUD", 1);
define("COMPROBACION_ETAPA_APROBACION", 2);
define("COMPROBACION_ETAPA_COMPROBACION", 2);
define("COMPROBACION_ETAPA_APROBADA", 3);
define("COMPROBACION_ETAPA_RECHAZADA", 4);
*/
/*
define("COMPROBACION_ETAPA_SIN_ENVIAR", 1);
define("COMPROBACION_ETAPA_SOLICITUD", 2);
define("COMPROBACION_ETAPA_EN_APROBACION", 3);
define("COMPROBACION_ETAPA_APROBADA", 4);
define("COMPROBACION_ETAPA_RECHAZADA", 5);
*/
define("COMPROBACION_ETAPA_SIN_ENVIAR", 1);
define("COMPROBACION_ETAPA_EN_APROBACION", 2);
define("COMPROBACION_ETAPA_APROBADA", 3);
define("COMPROBACION_ETAPA_RECHAZADA", 4);
define("COMPROBACION_ETAPA_DEVUELTO_CON_OBSERVACIONES", 5);
define("COMPROBACION_ETAPA_EN_APROBACION_POR_SF", 6);
define("COMPROBACION_ETAPA_APROBADA_POR_SF", 7);
define("COMPROBACION_ETAPA_RECHAZADA_POR_SF", 8);
define("COMPROBACION_ETAPA_EN_APROBACION_POR_DIRECTOR", 9);
define("COMPROBACION_ETAPA_RECHAZADA_POR_DIRECTOR", 10);
//
//
//  COMPROBACION INVITACION
//
//                                                   |----> ETAPA_APROBADA
//     ETAPA_SOLICITUD ----->  ETAPA_APROBACION  ----|
//                                                   |----> ETAPA_RECHAZADA
//
define("COMPROBACION_GASTOS_ETAPA_SIN_ENVIAR", 1);
define("COMPROBACION_GASTOS_ETAPA_APROBACION", 2);
define("COMPROBACION_GASTOS_ETAPA_APROBADA", 3);
define("COMPROBACION_GASTOS_ETAPA_RECHAZADA", 4);
define("COMPROBACION_GASTOS_ETAPA_DEVUELTA_CON_OBSERVACIONES", 5);
define("COMPROBACION_GASTOS_ETAPA_APROBACION_SUPERVISOR_FINANZAS", 6);
define("COMPROBACION_GASTOS_ETAPA_APROBADA_POR_SUPERVISOR_FINANZAS", 7);
define("COMPROBACION_GASTOS_ETAPA_RECHAZADA_POR_SUPERVISOR_FINANZAS", 8);
define("COMPROBACION_GASTOS_ETAPA_EN_APROBACION_POR_DIRECTOR", 9);
define("COMPROBACION_GASTOS_ETAPA_RECHAZADA_POR_DIRECTOR", 10);


// Definiciï¿½n Monto excedente de Vuelo
define("MONTO_EXCEDENTE_VUELO", 0);
define("MONTO_EXCEDENTE_VUELO_DIRECTOR", 1000);
define("CECO_510", 510);

// Definiciones adicionales
define("PARTIDA_COMPROBACION_POR_APROBAR", 1);
define("PARTIDA_COMPROBACION_APROBADA", 0);
define("ULT_APROBACION", 10000);//eamg


define ("FLUJO_SOL",2);
define ("SOL_INV_APROB",2);

// Definiciï¿½n de correo para recepciï¿½n de reportes de Intefaces AMEX - SAP
define("EMAIL_RESPONSABLE_SAP_BMW", "notificaciones.mn@gmail.com");
define("EMAIL_SOPORTE_MN", "notificaciones.mn@gmail.com");
define("EMAIL_TECNOLOGIA_MN", "notificaciones.mn@gmail.com");

// Definiciï¿½n de Interfaces
define("COMPROBACIONES", 1);
define("ANTICIPOS", 2);
define("VUELOS", 3);
define("AMEX", 4);

// Definicion de conceptos
define("COMIDAS_REPRESENTACION", 1);
define("CONCEPTO_AVION", 4);
define("CONCEPTO_HOTEL", 5);
define("CONCEPTO_RENTA_AUTO", 10);

/**
 * Utils Manejador de Errores
 */
define("ERROR_REPORT",E_ALL); //E_ALL, E_USER_NOTICE, E_USER_WARNIGN, E_USER_ERROR
define("ERROR_ACTION",3); //0 = predeterminada, 1 = envia correo si hubiera un 4to parametro, 3 = Se añade al archivo log
define("ERROR_LOG_FILE","errorLog.log"); //Agregar correo en PHP.ini al parametro sendmail_from
define("ERROR_MAIL", "notificaciones.mn@gmail.com");
set_error_handler("__exeptionHandler");
error_reporting(ERROR_REPORT);

// Definimos la Zona Horaria a utilizar
date_default_timezone_set("America/Mexico_City");

function __exeptionHandler($error,$mensaje,$archivo,$linea){
	$usuario = null ;
	$errorArray = array("titulo" => $error, "mensaje" => $mensaje, "archivo" => $archivo, "linea" => $linea, "timeStamp" => date("Y-m-d H:i:s"),"usuario" => $usuario,);
	 
	$errorMensaje = "Error:    ".$errorArray['titulo']."\r\n";
	$errorMensaje.= "Mensaje:  ".$errorArray['mensaje']."\r\n";
	$errorMensaje.= "Archivo:  ".$errorArray['archivo']."\r\n";
	$errorMensaje.= "Linea:    ".$errorArray['linea']."\r\n";
	$errorMensaje.= "Hora:     ".$errorArray['timeStamp']."\r\n";
	//$errorMensaje.= "Usuario:  ".$errorArray['usuario']."\r\n\r\n";

	//error_log($errorMensaje, ERROR_ACTION, ERROR_LOG_FILE, ERROR_MAIL);
	//echo json_encode($errorArray);
}

?>
