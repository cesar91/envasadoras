<?php
require_once("../../../lib/php/constantes.php");
require_once "$RUTA_A/Connections/fwk_db.php";
//require_once "$RUTA_A/functions/utils.php";
	$cnn = new conexion();
	$accion = $_GET['accion'];
	switch ($accion) {
		case "selectcco":
			$sql = "SELECT cc_centrocostos FROM cat_cecos  WHERE cc_centrocostos = '".$_POST['codigo']."'";
			if(isset($_POST['id'])){
				$sql.= " AND cc_id != ".$_POST['id'];
			}
			$res = $cnn->consultar($sql);
			$row = mysql_fetch_assoc($res);
			//echo json_encode($row);
			echo $row['cc_centrocostos'];
        break;
		case "selectresponable":
			$empresa = $_GET['empresa'];
			$query = "SELECT * 
							FROM empleado 
							INNER JOIN usuario_tipo ON empleado.idfwk_usuario=usuario_tipo.ut_usuario
							INNER JOIN usuario on empleado.idfwk_usuario=usuario.u_id							
							WHERE usuario_tipo.ut_tipo = 1
							AND estatus = 1
							AND usuario.u_empresa = ".$empresa."";
			$res = $cnn->consultar($query);		
			while($arr = mysql_fetch_assoc($res)){
				$usuario[] = $arr['idfwk_usuario'];
				$nombre[] = utf8_encode($arr['nombre']);				
			}			
			$array = array ('usuario' => $usuario, 'nombre' => $nombre);
			echo json_encode($array);
        break;
	}
    /*case 2:
        echo "i es igual a 2";
        break;*/
?>
