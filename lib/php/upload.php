<?php
/*
 * Con este código se ingresa el presupuesto a la base de datos.
 * Considerando las siguientes premisas:
 * 1. Debe ser un archivo .cvs
 * 2. No debe contener encabezados sólo los datos a cargar.
 * 3. Los valores de izquierda a derecha deben ser :
 *    a) identificador del centro de costos.
 *    b) identificador del concepto.
 *    c) fecha inicio del periodo presupuestal.
 *    d) fecha fin  del periodo presupuestal.
 *    e) monto de presupuesto asignado (sin separador de miles).
 * 4. La combinación id del ctro de costos y id de concepto es unica.
 * 5. Si ya existe dicha combinación en la base de datos el presupuesto será actualizado
 * 6. Si no se encuentra dicha combinación se insertará un nuevo registro.
 */

function cargarPresupuesto($RUTA_A){
	require_once "$RUTA_A/functions/Presupuesto.php";
	// obtenemos los datos del archivo
	$tipo = $_FILES["archivo"]['type'];
	$archivo = $_FILES["archivo"]['name'];
	//valida que sí ingresen un archivo
	if(empty($archivo)){
		$result = "<b>Debe de especificar un archivo con el botón Examinar...<b>";
		return $result;
	}
	// valida que sólo sean archivos .cvs
	if(substr($archivo,-4,4) != ".csv"){
		$result = "<b>Por favor verifique el tipo de archivo. El archivo debe ser guardado con extensión .csv<b>";
		return $result;
	}
	//Aunque el archivo se llame igual que uno previo se carga.
	$prefijo = substr(md5(uniqid(rand())),0,6);

	if ($archivo != "") {
		// guardamos el archivo a la carpeta "datos"
		$destino = $RUTA_A."/datos/".$prefijo."_".$archivo;
		if (copy($_FILES['archivo']['tmp_name'],$destino)) {
			$result = "Archivo colocado: <b>".$archivo."</b>";
		} else {
			return "<b>Error al subir el archivo</b>";
		}
	} else {
		return "<b>Error al subir el archivo</b>";
	}

	$registros=0;
	$actualizaciones=0;
	$no_registrados="";
	$no_actualizados="";
	$i=0;
	$fp = fopen ($destino,"r");
	if($fp !== FALSE){

		$Presupuesto = new Presupuesto();
		$CentroCosto = new CentroCosto();
		$Concepto = new Concepto ();
		$data = fgetcsv ($fp, 0, ",");
		while ($data)			
		{
			$i++;
			$empresa = $data[0];
			$ceco = $data[1];
			$finicial = $data[2];
			$ffinal = $data[3];
			$monto = $data[4];
			$row_resultCC=$CentroCosto->Busca_CeCoXCodigo($ceco,$empresa);			
			if (empty($row_resultCC)){
				echo "El Centro de Costos ".$ceco." para la empresa".$empresa." no existe. Linea no. ".$i." </br>";
			}else{
				$fechaI = explode("/",$finicial);
				$fechaF = explode("/",$ffinal);
				$FI = $fechaI[2]."-".$fechaI[1]."-".$fechaI[0];
				$FF = $fechaF[2]."-".$fechaF[1]."-".$fechaF[0];
				$cc_id = $row_resultCC["cc_id"];
				$row_resultPP=$Presupuesto->Existe_PP($cc_id,$FI,$FF);
				if(empty($row_resultPP)){
					if($Presupuesto->Nuevo_Presupuesto($cc_id,$FI,$FF,$monto) > 0){
					$registros++;
					}else{
						$no_actualizados=$no_actualizados."[".$data[0]."][".$data[1]."],";					}
				}else{
					$pDisponible = $monto - $row_resultPP["pp_presupuesto_utilizado"]; 
					if($Presupuesto->Actualiza_Presupuesto($row_resultPP["pp_id"],$data[4],$pDisponible)){
						$actualizaciones++;
					}else{
						$no_actualizados=$no_actualizados."[".$data[0]."][".$data[1]."],";
					}
				}
			}
			 $data = fgetcsv ($fp, 0, ",");
		}
	}
	fclose ($fp);	
	return mostrarResultados ($registros,$actualizaciones,$no_registrados,$no_actualizados);
}
function mostrarResultados ($registros,$actualizaciones,$no_registrados,$no_actualizados){
	if($registros>0)
	$result=$result." Registros insertados: <b>".$registros."</b></br>";
	if($actualizaciones>0)
	$result=$result."</b> Registros actualizados: <b>".$actualizaciones."</b></br>";
	if($no_registrados!="")
	$result=$result."</b> Sin registrarse [Empresa][Código Ctro de Costos]: <b>".$no_registrados."</b></br>";
	if($no_actualizados!="")
	$result=$result."</b> Sin actualizarse [Empresa][Código Ctro de Costos]:  <b>".$no_actualizados."</b></br>";
	return $result;
}

?>