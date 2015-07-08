<?php

/*
 *  Configuracion global del sistema 
 */
$SERVER = "201.159.131.127";
$RUTA_R = "/eexpenses_marti/";
$RUTA_A = "/usr/local/Zend/apache2/htdocs/eexpenses_marti";
$APP_TITULO = "Sistema Electrónico de Comandas";

/*
 *  Acceso a MySQL
 */
$MYSQL_HOST="172.20.36.173";
$MYSQL_PORT="3306";
$MYSQL_USER="MTI1";
$MYSQL_PASSWORD="M4RT1";
$MYSQL_DATABASE="EXMTIDV1";

/*
 *  Acceso a Oracle
 */
$DISABLE_ORACLE=False;

// Informacion de conexion a Oracle (Para PeopleSoft)
$ORACLE_USER='masngexpenses';
$ORACLE_PASSWORD='expenses01';
$ORACLE_HOST_SID='172.20.36.172:1521/F88MARTI';
#$ORACLE_HOST_SID='192.168.2.102:1522/F88MARTI';       

/*
 *  Configuracion de Correo
 */
$SMTP_HOST="mail.masnegocio.com;"; 
$SMTP_PORT="25";
$SMTP_AUTH=true;
$SMTP_USERNAME="portales@masnegocio.com";
$SMTP_PASSWORD="Ht8Pv12S";
$SMTP_FROM="portales@masnegocio.com";
$SMTP_FROMNAME="Sistema Electrónico de Comandas";
$SMTP_ACTIVAR_CORREO=True;

/*
 *  Parametrizacion del sistema
 */
$BLOQUEA_NUEVAS_SOLICITUDES_SI_HAY_COMPROBACIONES_PENDIENTES=False;

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
define("FLUJO_ANTICIPO", 1);
define("FLUJO_AMEX", 2);
define("FLUJO_COMPROBACION", 3);
define("FLUJO_COMPROBACION_TDC", 4);
define("FLUJO_REEMBOLSO_CAJA_CHICA", 5);

/////////////////////////////////////////////////////////////////
//
//  DEFINICION DE CADA UNA DE LAS ETAPAS DE LOS FLUJOS
//
/////////////////////////////////////////////////////////////////
//
//
//  ANTICIPO
//
//                                                   |----> ETAPA_APROBADA
//     ETAPA_SOLICITUD ----->  ETAPA_APROBACION  ----|
//                                                   |----> ETAPA_RECHAZADA
//
define("ANTICIPO_ETAPA_SOLICITUD", 1);
define("ANTICIPO_ETAPA_APROBACION", 2);
define("ANTICIPO_ETAPA_APROBADA", 3);
define("ANTICIPO_ETAPA_RECHAZADA", 4);


//
//  ANTICIPO AMEX
//
//                                                                       |----> ETAPA_APROBADA
//     ETAPA_SOLICITUD ----->  ETAPA_AGENCIA ----> ETAPA_APROBACION  ----|
//                                                                       |----> ETAPA_RECHAZADA
//
define("ANTICIPO_AMEX_ETAPA_SOLICITUD", 1);
define("ANTICIPO_AMEX_ETAPA_AGENCIA", 2);
define("ANTICIPO_AMEX_ETAPA_APROBACION", 3);
define("ANTICIPO_AMEX_ETAPA_APROBADA", 4);
define("ANTICIPO_AMEX_ETAPA_RECHAZADA", 5);


//
//  COMPROBACION
//
//              |---------------------------------------------
//              |                                            |
//              |                                            V          |----> ETAPA_APROBADA
//     ETAPA_COMPROBACION ----->  ETAPA_APROBACION ----> ETAPA_CXP  ----|
//              A                        |                   |          |----> ETAPA_RECHAZADA
//              |                        |                   |
//              |---------------------------------------------
//
//
define("COMPROBACION_ETAPA_COMPROBACION", 1);
define("COMPROBACION_ETAPA_APROBACION", 2);
define("COMPROBACION_ETAPA_CXP", 3);
define("COMPROBACION_ETAPA_APROBADA", 4);
define("COMPROBACION_ETAPA_RECHAZADA", 5);
define("COMPROBACION_ETAPA_APROBADA_PARCIAL", 6);

//
//  COMPROBACION TDC
//
//
//                                                                      |----> ETAPA_APROBADA
//   ETAPA_COMPROBACION_TDC --->  ETAPA_APROBACION ----> ETAPA_CXP  ----|
//              A                        |                   |          |----> ETAPA_RECHAZADA
//              |                        |                   |
//              |---------------------------------------------
//
//
define("COMPROBACION_TDC_ETAPA_COMPROBACION_TDC", 1);
define("COMPROBACION_TDC_ETAPA_APROBACION", 2);
define("COMPROBACION_TDC_ETAPA_CXP", 3);
define("COMPROBACION_TDC_ETAPA_APROBADA", 4);
define("COMPROBACION_TDC_ETAPA_RECHAZADA", 5);
define("COMPROBACION_TDC_ETAPA_APROBADA_PARCIAL", 6);

//
//  COMPROBACION CAJA CHICA
//
//
//                                                                      |----> ETAPA_APROBADA
//   ETAPA_COMPROBACION_CC ---->  ETAPA_APROBACION ----> ETAPA_CXP  ----|
//              A                        |                   |          |----> ETAPA_RECHAZADA
//              |                        |                   |
//              |---------------------------------------------
//
//
define("COMPROBACION_CAJA_CHICA_ETAPA_COMPROBACION_CAJA_CHICA", 1);
define("COMPROBACION_CAJA_CHICA_ETAPA_APROBACION", 2);
define("COMPROBACION_CAJA_CHICA_ETAPA_CXP", 3);
define("COMPROBACION_CAJA_CHICA_ETAPA_APROBADA", 4);
define("COMPROBACION_CAJA_CHICA_ETAPA_RECHAZADA", 5);
define("COMPROBACION_CAJA_CHICA_ETAPA_APROBADA_PARCIAL", 6);






// Definiciones adicionales
define("PARTIDA_COMPROBACION_POR_APROBAR", 1);
define("PARTIDA_COMPROBACION_APROBADA", 0);



?>
