<?php
require_once("../../../lib/php/constantes.php");
require_once "$RUTA_A/Connections/fwk_db.php";
require_once "$RUTA_A/functions/utils.php";

function devuelve_utf8($valor){
	$utf8 = array("á","é","í","ó","ú","ñ","Á","É","Í","Ó","Ú","ñ");
	$iso = array("&aacute;","&eacute;","&iacute;","&oacute;","&uacute;","&ntilde;","&Aacute;","&Eacute;","&Iacute;","&Oacute;","&Uacute;","&Ntilde;");
	$code_utf8 = str_replace($utf8, $iso ,$valor);
	return 	$code_utf8;
}
	
if (isset($_POST['uid'])) {
	$uid = $_POST['uid'];
	$Usu=new Usuario();
	$Usu->Load_Usuario_By_ID($uid);
	$arreglo = array();
	$cnn = new conexion();
	$query = sprintf("SELECT u_id, u_usuario,u_nombre, u_paterno, u_materno,cc_centrocostos, cc_nombre FROM usuario u
			INNER JOIN empleado e ON (u.u_id = e.idfwk_usuario) INNER JOIN cat_cecos c ON (cc_id = idcentrocosto) WHERE u_id='%s' and u_activo = 1", $uid);
	error_log($query);
	$rst2 = $cnn->consultar($query);
	$filaa = mysql_fetch_assoc($rst2);
	error_log($Usu->Get_dato("ut_tipo"));
	$nombre = $filaa["u_nombre"]." ".$filaa["u_paterno"]." ".$filaa["u_materno"];
	$arreglo=array("tipo"=>$Usu->Get_dato("ut_tipo"),"uid"=>trim($filaa["u_id"]),"usuario"=>trim($filaa["u_usuario"]),"nombre"=>htmlentities($nombre),"cc"=>sprintf("%s - %s",$filaa["cc_centrocostos"],htmlentities($filaa["cc_nombre"])));
	echo json_encode($arreglo);
}


if (isset($_POST['uid_tipo_usuario']) && !isset($_POST['uid_delegado'])) {
	$uid = $_POST['uid_tipo_usuario'];
	$Usu=new Usuario();
	$arreglo = array();
	$arreglo= $Usu->find_tipos_usuario($uid);
	echo json_encode($arreglo);
}


if (isset($_POST['uid_tipo_usuario']) && isset($_POST['uid_delegado'])) {
	$uid = $_POST['uid_tipo_usuario'];
	$iddelegado=$_POST['uid_delegado'];
	$Usu=new Usuario();
	$arreglo = array();
	$arreglo= $Usu->find_tipos_usuario_delegacion($uid, $iddelegado);
	echo json_encode($arreglo);
}


if (isset($_POST['uidsel'])) {
	$uid = $_POST['uidsel'];
	$cnn = new conexion();
	$query = sprintf("SELECT COUNT(ua_id) as cuenta FROM usuarios_asignados WHERE id_asignador='%s';", $uid);
	$rst2 = $cnn->consultar($query);
	$dato=mysql_result($rst2,0,"cuenta");
	error_log($dato);
	if($dato>0){
		echo "TRUE";
	}else{
		echo "FALSE";
	}
}

if (isset($_POST['uidselfill'])) {
	$uid = $_POST['uidselfill'];
	$cnn = new conexion();
	$query = sprintf("SELECT id_delegado,privilegios,id_tipo FROM usuarios_asignados WHERE id_asignador='%s';", $uid);
	error_log($query);
	$rst2 = $cnn->consultar($query);
	//$data[][]=array();
	$arreglo[]=array();
	$i=0;
	$j=0;
    while ($filaa = mysql_fetch_assoc($rst2)) {
        $data["delegado"][$i]=sprintf("%s",$filaa['id_delegado']);
        $data["privilegio"][$i]=sprintf("%s", $filaa['privilegios']);
        $data["tipo"][$i]=sprintf("%s", $filaa['id_tipo']);
        $i++;
    }
    
    
    while($j<$i){
    	$Usu=new Usuario();
    	$Usu->Load_Usuario_By_ID($data["delegado"][$j]);
    	$tipo_name="";
    	 $query1 = sprintf("SELECT u_id, u_usuario,u_nombre, u_paterno, u_materno,cc_centrocostos, cc_nombre FROM usuario u
    		INNER JOIN empleado e ON (u.u_id = e.idfwk_usuario) INNER JOIN cat_cecos c ON (cc_id = idcentrocosto) WHERE u_id='%s' and u_activo = 1",  $data["delegado"][$j]);
   	error_log($query1);
    	 $rst2 = $cnn->consultar($query1);
   	$filaa = mysql_fetch_assoc($rst2);

   	$tipo_id=$data["tipo"][$j];
   	$tipo_name=$Usu->getnombretipo($data["tipo"][$j]);

	$nombre = $filaa["u_nombre"]." ".$filaa["u_paterno"]." ".$filaa["u_materno"];
	$arreglo[$j]=array("tipo_id"=>$tipo_id,"tipo"=>$tipo_name,"uid"=>trim($filaa["u_id"]),"usuario"=>trim($filaa["u_usuario"]),"nombre"=>utf8_encode($nombre),"cc"=>sprintf("%s - %s",$filaa["cc_centrocostos"],utf8_encode($filaa["cc_nombre"]))); 
	
    $data["info"][$j]=$arreglo[$j];
    $j++;
    }
   
echo json_encode($data);
}



?>