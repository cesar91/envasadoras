<?php
/**
 * Validamos a un Usuario para TAE
 */
	require_once '../Connections/fwk_db.php';
	if(isset($_POST["user"]) && isset($_POST["passwd"])){ //SI NOS ENVIARON CO LAS VARIABLES
		
		$cnn	= new conexion();	
		$user	= $_POST["user"];
		$passwd	= $_POST["passwd"];

		/**	  Verificamos en la base de datos si existe dicho usuario	 **/
		$query	=sprintf("select * from fwk_usuario where usuario='%s' and passwd='%s'",$user,$passwd);
		$rst	=$cnn->consultar($query);
		if($cnn->get_rows()>0){ //El usuario es Valido
			echo "1";	
			/** Aqui iniciamos la Session **/
		}
		else{ //El usuario no es Valido
			echo "-1";
		}
	}
	else{ //NO ENVIARON LAS VARIABLES
		echo "-2";
	}
?>
