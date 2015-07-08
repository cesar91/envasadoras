<?php
require_once("../../lib/php/constantes.php");
require_once "$RUTA_A/Connections/fwk_db.php";
require_once "$RUTA_A/functions/utils.php";


if (isset($_POST['empresa_id'])) {
	
    $empresaId = $_POST['empresa_id'];
    $empresaId=utf8_decode($empresaId);  
    error_log("ENTRA".$empresaId);
    $cnn = new conexion();
    $_arreglo=array();
    $query_ceco = sprintf("SELECT cc_id,cc_centrocostos,cc_nombre FROM cat_cecos WHERE cc_empresa_id ='%s' ORDER BY cc_centrocostos", $empresaId);
    error_log($query_ceco);
    $rst = $cnn->consultar($query_ceco);
	$i=0;
    while ($fila = mysql_fetch_assoc($rst)) {
        $data=sprintf("%s:%s",$fila['cc_id'], $fila["cc_centrocostos"]."-".$fila['cc_nombre']);
        $_arreglo[$i]=utf8_encode($data);
        $i++;
    }
echo json_encode($_arreglo);
}



if (isset($_POST['cecoId'])) {
	$cecoId=$_POST['cecoId'];
	$cecoId=utf8_decode($cecoId);
	$cnn = new conexion();
    $_arreglo=array();
    $query_empleado=sprintf("SELECT nombre FROM empleado WHERE idcentrocosto='%s'",$cecoId);
    $rst = $cnn->consultar($query_empleado);
    $i=0;
    while ($fila = mysql_fetch_assoc($rst)) {
        $data=sprintf("%s:%s",$fila['nombre'], $fila["nombre"]);
        $_arreglo[$i]=utf8_encode($data);
        $i++;
    }
echo json_encode($_arreglo);	
}

?>