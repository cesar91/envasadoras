<?php
	/**
	 * Registro & Edicion de Comprobaciones de Viaje => Servicios 
	 * Creado por: IHV 2013-06-13
	 */	 	 
	require_once("lib/php/constantes.php");
	require_once("$RUTA_A/Connections/fwk_db.php");
	require_once("$RUTA_A/functions/utils.php");
	
	$conexion = new conexion();
	$cont = 0;
	
	function codifica($arg){
		foreach($arg as $key => $val)
			//$arg[$key] = utf8_decode(htmlentities($val));
			$arg[$key] = utf8_encode($val);
		return $arg;
	}
	if(isset($_GET['leido'])){
		$user = $_GET["user"];
		$pass = $_GET["pass"];
		$existeUser = false;
		$sql = "SELECT * FROM usuario WHERE u_usuario='$user' AND u_passwd='$pass'";
		$res = $conexion->consultar($sql);
		if(mysql_num_rows($res) >0 ){ 
			$existeUser = true; 
			while($row = codifica(mysql_fetch_assoc($res))){
				$response = array("existeUser" 	=> $existeUser,
								"leido"	=> $row["u_manual_leido"],
								"id"	=> $row["u_id"]);
			}		
		}else{
			$response = array("existeUser" 	=> $existeUser);
		}
		echo json_encode($response);
	}
	if(isset($_GET['updateLeido'])){
		$usuarioId = $_GET["usuarioId"];
		echo $sql = "UPDATE usuario SET u_manual_leido = 1 WHERE u_id = $usuarioId";
		$res = $conexion->consultar($sql);
	}	
?>