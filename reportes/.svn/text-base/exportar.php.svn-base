<?php
	session_start();
	require_once("../lib/php/constantes.php");
	require_once "$RUTA_A/Connections/fwk_db.php";
	require_once "$RUTA_A/functions/utils.php";
	require_once('../lib/php/utils.php');

	$nombre = $_SESSION['nombreReporte'];
	$head = $_SESSION['head'];

	$cnn	= new conexion();
	$sql="drop table if exists resultado;";
	$cnn->consultar($sql);
	
	$sql = $_SESSION['query'];
	$cnn->consultar($sql);
	$sql="select * from resultado;";
	$res=$cnn->consultar($sql);
	
	exportxls($res,$nombre,$head);
	die();

	$sql="drop table if exists resultado;";
	$cnn->consultar($sql);
?>
