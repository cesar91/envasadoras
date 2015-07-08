<?php
	/**
	 * Servicios Proveedores
	 * Creado por: IHV 2013-06-20
	 */	 	 
	require_once("../../../lib/php/constantes.php");
	require_once("$RUTA_A/Connections/fwk_db.php");
	require_once("$RUTA_A/functions/utils.php");

	$cnn = new conexion();	
	/**
	 * Busqueda el listado de Proveedores/RFC
	 * 
	 * @param request GET => q
	 * @param request GET => tip
	 */
	if(isset($_GET["q"]) && isset($_GET["tip"])){	
		$q = $_GET["q"];
		$tipo = $_GET["tip"];
		
		if($tipo == 1)
			$sql = "SELECT pro_proveedor AS result
					FROM proveedores 
					WHERE pro_proveedor LIKE '%$q%' 
					ORDER BY pro_proveedor";
		else
			$sql = "SELECT pro_rfc AS result
					FROM proveedores 
					WHERE pro_rfc LIKE '%$q%' 
					ORDER BY pro_rfc" ;
		$res = $cnn->consultar($sql);
		while($row = mysql_fetch_assoc($res)){
			echo $row["result"]."\n";
		}	
	}else
	/**
	 * Busqueda de Proveedor/RFC
	 * 
	 * @param request POST => q
	 * @param request POST=> nombre
	 */		 
	if(isset($_REQUEST["nombre"]) && isset($_REQUEST["tip"])){
		$nombre = $_REQUEST["nombre"];
		$tip = $_REQUEST["tip"];
		
		if($tip == 1){
			$sql = "SELECT pro_rfc AS result
					FROM proveedores 
					WHERE pro_proveedor = '$nombre'";
		}elseif($tip == 2){
			$sql = "SELECT UCASE(pro_proveedor) AS result
					FROM proveedores 
					WHERE pro_rfc = '$nombre'";			
		}
		$res = $cnn->consultar($sql);
		$row = mysql_fetch_assoc($res);          
		echo json_encode($row["result"]);	
	}else
	/** 
	 * Servicio, registro de nuevo proveedor, antes de insertar verifica si no existe un proveedor
	 * dado de alta con el mismo RFC.
	 *
	 * @param request POST => nuevoProveedor
	 * @param proveedor 
	 * @param domicilio 
	 * @param rfc
	 */
	if(isset($_POST['nuevoProveedor'])){
		$proveedor = $_POST["proveedor"];
		$rfc = $_POST["rfc"];
		$domicilio = $_POST['domicilio'];
		
		$sql = "SELECT pro_id
				FROM proveedores 
				WHERE pro_rfc = '$rfc'";
		$res = $cnn->consultar($sql);
		$tot = mysql_num_rows($res);			
		if($tot > 0)
			echo json_encode(array("mensaje" => "El proveedor que intenta ingresar ya fue registrado previamente."));
		else{
			$sql = "INSERT INTO proveedores 
					VALUES(DEFAULT, UCASE('$proveedor'), UCASE('$rfc'), UCASE('$domicilio'), 1)";      
			$res = $cnn->insertar($sql);		
			echo json_encode(array("mensaje" => "Su registro ha sido exitoso."));
		}
	}
	/**
	 * Valida si el proveedor existe
	 * 
	 * @param request GET => busca
	 * @param nameprov
	 * @param rfcprov
	 */
	if(isset($_POST['busca'])){		
		$proveedor = $_POST["nameprov"];
		$rfc = $_POST["rfcprov"];
		$sql = "SELECT pro_proveedor 
				FROM proveedores 
				WHERE pro_proveedor = '$proveedor' 
				AND pro_rfc ='$rfc'";
		$res = $cnn->consultar($sql);
		$row = mysql_num_rows($res);
		if($row > 0)
			echo "El proveedor ya se encuentra registrado";						
	}else
	/**
	 * Valida el folio de la factura
	 * 
	 * @param request POST => find
	 * @param folio
	 * @param rfcprov
	 */
	if(isset($_POST['find'])){
		$folio = $_POST["folio"];
		$rfc = $_POST["rfcprov"];
		$sql = "SELECT dc_id 
				FROM detalle_comprobacion_invitacion 
				WHERE dc_folio_factura = '$folio' 
				AND dc_rfc = '$rfc'";
		$res = $cnn->consultar($sql);
		$row = mysql_num_rows($res);
		if($row > 0)
			echo "El folio de esta factura ya está ocupado favor de verificar";
	}
?>