<?php
require_once("../lib/php/constantes.php");
require_once "$RUTA_A/Connections/fwk_db.php";
require_once "$RUTA_A/functions/utils.php";
$dir = $RUTA_RH;
$ruta = $RUTA_RH;
$rarchivos = $ruta;
$rbackup = $rarchivos."backup/";
$rlog = $rarchivos."logs/";
if (is_dir($dir)) {
		if ($gd = opendir($dir)) {
			while ($archivo = readdir($gd)) {
				$ext = substr($archivo,-3);
				if ($archivo!="." && $archivo!=".." && $ext == "txt"){
					if (!file_exists($rbackup)) {
						echo "No existe";
						mkdir($rbackup, 0777, true);
						chmod($rbackup, 0777);
					}
					if (!copy($rarchivos.$archivo, $rbackup.date("Y-m-d_H_i_s").$archivo.".bak")) {
						echo "Error al copiar $archivo";
					}
					 guarda_datos($rarchivos.$archivo,$rlog.date("Y-m-d_H_i_s").$archivo.".log");
				}
			}
		closedir($gd);
		}
}
function guarda_datos($archivo,$rlog){
$cnn = new conexion();	
$cardelimitador = '	';
$archivo = $archivo;
 
$oa = fopen($archivo, 'r');
 
$c = 1;
$ci = 0;
$file = fopen($rlog, "w");
	while($a = fgetcsv($oa, 1000, $cardelimitador)){
		$tipoRegistro = utf8_encode($a[0]);
		$idEmpresa = utf8_encode($a[1]);
		$nombre = utf8_encode($a[2]);
		$materno = utf8_encode($a[4]);
		$paterno = utf8_encode($a[3]);
		$activo = utf8_encode($a[5]);
		$usuario = utf8_encode($a[6]);
		$contrasena = utf8_encode($a[7]);
		$email = utf8_encode($a[8]);
		$pagarSAP = utf8_encode($a[9]);
		$cobrarSAP = utf8_encode($a[10]);
		$tipo = utf8_encode($a[11]);
		$ceco = utf8_encode($a[12]);
		$puesto = utf8_encode($a[13]);
		$buscaCeco = "SELECT * FROM cat_cecos WHERE cc_centrocostos = '$ceco';";
		$resCeco = $cnn->consultar($buscaCeco);
		$fila=mysql_fetch_assoc($resCeco);
		$idCeco=$fila["cc_id"];
		$buscaEmpresa = "SELECT * FROM empresas WHERE e_codigo = '$idEmpresa';";
		$resBuscaEmpresa = $cnn->consultar($buscaEmpresa);
		$buscaUser = "SELECT * FROM usuario WHERE u_usuario = '$usuario';";
		$resBuscaUser = $cnn->consultar($buscaUser);
		if(mysql_num_rows($resBuscaUser) == 0){
			if(mysql_num_rows($resCeco) > 0){
				if(mysql_num_rows($resBuscaEmpresa) > 0){
					if($tipoRegistro == "A"){
						$insertaUsuario = "INSERT INTO usuario
											(u_empresa, u_nombre, u_paterno, u_materno, u_interno, u_activo, u_usuario, u_passwd, u_email) VALUES
											($idEmpresa,'$nombre','$paterno','$materno',1,1,'$usuario','$contrasena','$email');";
						$idUser = $cnn->insertar($insertaUsuario);
						$insertaEmpleado = "INSERT INTO empleado 
											(nombre, idfwk_usuario, idcentrocosto, npuesto, estatus, b_id, fechacreacion) VALUES
											('$nombre $paterno $materno',$idUser,$idCeco,'$puesto',1, 1, now());";
						$idEmpleado = $cnn->insertar($insertaEmpleado);
						$insertaUTipo = "INSERT INTO usuario_tipo 
											(ut_usuario, ut_tipo) VALUES
											($idUser, $tipo);";
						$idUTipo = $cnn->insertar($insertaUTipo);
						if($idUTipo){
							$RegInsertados = "Total registros insertados: " . $ci . "\n";
							fwrite($file, $RegInsertados);
							$ci++;
						}
					}elseif($tipoRegistro == "M"){
						$buscaEmpleado = "select * from empleado where nombre = '$nombre $paterno $materno'";
						$resEmpleado = $cnn->consultar($buscaEmpleado);
						$filaEmpleado=mysql_fetch_assoc($resEmpleado);
						$idUsuario=$filaEmpleado["idfwk_usuario"];	
						$actualizaUsuario = "UPDATE usuario SET u_usuario='$usuario',u_passwd='$contrasena',u_email='$email' WHERE u_id='$idUsuario'";
						$cnn->ejecutar($actualizaUsuario);
						$actualizaEmpleado = "UPDATE empleado SET idcentrocosto='$idCeco', npuesto='$puesto' WHERE idempleado='$idUsuario'";
						$cnn->ejecutar($actualizaEmpleado);
						$regModificados = "Total de registros modificados: " . $c . "\n";
						fwrite($file, $regModificados);
					}
				}else{
					$sinEmpresa = "\n No existe empresa para linea ". $c. "\n";
					fwrite($file, $sinEmpresa);
				}
			}else{
				$sinCecos = "\n No existe el centro de costos para linea". $c. "\n";
				fwrite($file, $sinCecos);
			}
		}else{
			if($tipoRegistro == "M"){
						$buscaEmpleado = "select * from usuario  where u_usuario = '$usuario'";
						$resEmpleado = $cnn->consultar($buscaEmpleado);
						$filaEmpleado=mysql_fetch_assoc($resEmpleado);
						$idUsuario=$filaEmpleado["u_id"];	
						$actualizaUsuario = "UPDATE usuario SET u_passwd='$contrasena', u_empresa = '$idEmpresa', u_email='$email' WHERE u_id='$idUsuario'";
						$cnn->ejecutar($actualizaUsuario);
						$actualizaEmpleado = "UPDATE empleado SET idcentrocosto='$idCeco', npuesto='$puesto' WHERE idempleado='$idUsuario'";
						$cnn->ejecutar($actualizaEmpleado);
						$regModificados = "Total de registros modificados: " . $c . "\n";
						fwrite($file, $regModificados);
			}
			$userDuplicado = "\n El usuario ".$usuario." ya existe y se encuentra en la linea". $c. "\n";
			fwrite($file, $userDuplicado);
		}
		$c++;
	}
$totalRegistros = 'Total de registros en el archivo: ' . $c . "\n";
fwrite($file, $totalRegistros);
fclose($file);
unlink($archivo);
fclose($oa);
}
?>