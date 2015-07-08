<?php
//Solución al borrado de cache
//En IE cuando se carga un dato y se edita, al volverlo querer editar,
//el primero queda en cache y no vuelve a llamar las funciones en js y php.
header("Cache-Control: no-store, no-cache, must-revalidate");
session_start();

require_once "../../../lib/php/constantes.php";
require_once "$RUTA_A/Connections/fwk_db.php";
require_once "utils.php";

//Envia información del itinerario
if(isset($_GET['idTramite'])){
			$IdItinerario   = $_GET['idTramite'];
			$cnn			= new conexion();
			$auxDatos= array();

			$query="SELECT * FROM sv_itinerario where svi_id={$IdItinerario} ";

			$rst=$cnn->consultar($query);				
			$datos=mysql_fetch_assoc($rst);
			
			$numeroTransporte = $datos['svi_numero_tranporte'];
			$costoPeaje		  = $datos['svi_monto_peaje'];
			$iva			  = $datos['svi_iva'];
			$tua			  = $datos['svi_tua'];
			$hotel			  = $datos['svi_nombre_hotel'];
			$habitacion		  = $datos['svi_habitacion'];
			$costohotel		  = $datos['svi_costo_hotel'];
			$otros			  = $datos['svi_otrocargo'];			
			
			$auxDatos=array('','');
			
			echo $numeroTransporte."&".$costoPeaje."&".$iva."&".$tua."&".$hotel."&".$habitacion."&".$costohotel."&".$otros;
}


//Actualiza itinerario
else if (isset($_POST['id']) && isset($_POST['costoViaje']) && isset($_POST['iva']) && isset($_POST['tua']) && isset($_POST['costoHotel']) && isset($_POST['otrocargo'])){

			$id		   = $_POST['id'];
			$costoViaje = $_POST['costoViaje'];
			$iva		   = $_POST['iva'];
			$tua		   = $_POST['tua'];
			$costoHotel = $_POST['costoHotel'];
			$otrocargo  = $_POST['otrocargo'];
			
			$cnn	 = new conexion();

			$query=sprintf("update sv_itinerario set svi_monto_peaje=%s, svi_iva=%s, svi_tua=%s, svi_costo_hotel=%s, svi_otrocargo=%s where svi_id=%s",$costoViaje,$iva,$tua,$costoHotel,$otrocargo,$id);
			$rst=$cnn->insertar($query);						
}

//Recalcula después de actualizar itinerarios
elseif(isset($_POST["idTramiteX"])){

		$cnn	 = new conexion();
		$aux= array();
		
		$costoViajeT=0;
		$ivaT=0;
		$tuaT=0;
		$costoHotelT=0;
		$otrocargoT=0;
		$totalT=0;
		$GetCadena="";
		$totalC=0;
		
		$query="SELECT sv_id,t_iniciador, t_id,  DATE_FORMAT(t_fecha_registro,'%d/%m/%Y %H:%i:%S'), t_etiqueta, sv_anticipo, sv_monto_maximo_disponible, sv_dias_viaje, svi_id, svi_tipo_viaje, svi_origen, svi_destino,  svi_medio, svi_kilometraje,  svi_horario, DATE_FORMAT(svi_fecha_salida,'%d/%m/%Y') as fecha_salida, svi_noches_hospedaje, svi_monto_peaje, svi_iva, svi_tua, svi_nombre_hotel, svi_costo_hotel, svi_otrocargo, svi_hotel FROM sv_itinerario inner join solicitud_viaje on (sv_itinerario.svi_solicitud=solicitud_viaje.sv_id)  inner join tramites  on (tramites.t_id=solicitud_viaje.sv_tramite) where t_id={$_POST['idTramite']}";

		$rst=$cnn->consultar($query);
		
		while($datos=mysql_fetch_assoc($rst)){
			array_push($aux,$datos);
		}

		foreach ($aux as $datosAux){
			/*$costoViaje=$datosAux["svi_monto_peaje"];
			$iva=($datosAux["svi_monto_peaje"]* $datosAux["svi_iva"])/100;
			$tua= $datosAux["svi_tua"];
			$costoHotel=$datosAux["svi_costo_hotel"];
			$otrocargo=$datosAux["svi_otrocargo"];
							
			$costoViajeT+=$costoViaje;
			$ivaT+=$iva;
			$tuaT+=$tua;
			$costoHotelT+=$costoHotel;
			$otrocargoT+=$otrocargo;
			$totalT=+$costoViajeT+$ivaT+$tuaT+$costoHotelT+$otrocargoT;
							
			$Total=$costoViaje+$iva+$tua+$costoHotel+$otrocargo;
			$GetCadena.=$Total."&";*/
			$GetCadena=$datosAux["sv_anticipo"]."&";
			$costoViajeT=$datosAux["sv_anticipo"];
			$totalT=$datosAux["sv_anticipo"];
		}		
		//$costoViajeT+=$tuaT+$costoHotelT+$otrocargoT;
		$totalC=$datosAux['sv_monto_maximo_disponible'];
		echo $GetCadena.$costoViajeT."&".$ivaT."&".$totalT."&".$totalC;
}

//calcula despues de actualizar itinerarios para Amex
elseif(isset($_POST["id_tramite_amex"])){

		$cnn= new conexion();
		$aux= array();

		$costoViajeT=0;
		$ivaT=0;
		$tuaT=0;
		$costoHotelT=0;
		$otrocargoT=0;
		$totalT=0;
		$GetCadena="";
		
		$query="SELECT sa_id,t_iniciador, t_id,  DATE_FORMAT(t_fecha_registro,'%d/%m/%Y %H:%i:%S'), t_etiqueta, sai_id, sai_tipo_viaje, sai_origen, sai_destino, sai_horario, DATE_FORMAT(sai_fecha_salida,'%d/%m/%Y') as fecha_salida, sai_monto_vuelo, sai_iva, sai_tua FROM sa_itinerario inner join solicitud_amex on (sa_itinerario.sai_solicitud=solicitud_amex.sa_id)  inner join tramites  on (tramites.t_id=solicitud_amex.sa_tramite) where t_id={$_POST['id_tramite_amex']}";
		//file_put_contents($query,"query_ingreso.txt");

		$rst=$cnn->consultar($query);

		while($datos=mysql_fetch_assoc($rst)){
			array_push($aux,$datos);
		}

		foreach ($aux as $datosAux){
			$costoViaje=$datosAux["sai_monto_vuelo"];
			$iva=($datosAux["sai_monto_vuelo"]* $datosAux["sai_iva"])/100;
			$tua= $datosAux["sai_tua"];
			//$costoHotel=$datosAux["svi_costo_hotel"];
			//$otrocargo=$datosAux["svi_otrocargo"];

			$costoViajeT+=$costoViaje;
			$ivaT+=$iva;
			$tuaT+=$tua;
			//$costoHotelT+=$costoHotel;
			//$otrocargoT+=$otrocargo;
			//$totalT=+$costoViajeT+$ivaT+$tuaT+$costoHotelT+$otrocargoT;
			$totalT=+$costoViajeT+$ivaT+$tuaT;

			//$Total=$costoViaje+$iva+$tua+$costoHotel+$otrocargo;
			$Total=$costoViaje+$iva+$tua;
			$GetCadena.=$Total."&";

		}

		$costoViajeT+=$tuaT;

		echo $GetCadena.$costoViajeT."&".$ivaT."&".$totalT;
}

// carga combo de hoteles en ciudades
else if(isset($_POST['idCd']) && $_POST['idCd']!=""){

		$cnn	 = new conexion();
		$aux= array();
		$idCd=$_POST['idCd'];
		
		$query=sprintf("select * from proveedores where pro_tipo=1 and pro_ciudad='".$idCd."'");
		$rst=$cnn->consultar($query);
		?>
		<select id="selectHotel" name="selectHotel" onchange="cargaPrecioHotel();">
		<?php		
		while($fila=mysql_fetch_assoc($rst)){
			$cad="__";
			
			for($i=strlen ($fila['pro_proveedor']); $i<22 ; $i++){
				$cad.="_";
			}
			?>
			<option value="<?php echo $fila['pro_id'];?>"><?php echo $fila['pro_proveedor'].$cad."$".$fila['pro_costo']?></option>
			<?php
		}
		?>
		</select>		
		<?php		
}
//despliega hoteles
else if(isset($_POST['idHotel']) && $_POST['idHotel']!=""){
		
		$campo="";
		
		if(isset($_POST['aux']) && $_POST['aux']==1)
			$campo="pro_costo";
			
		elseif(isset($_POST['aux']) && $_POST['aux']==2)
			$campo="pro_ciudad";
		
		$cnn	 = new conexion();
		$aux= array();
		
		$idHotel=$_POST['idHotel'];
		
		$query=sprintf("select * from proveedores where pro_tipo=1 and pro_id='".$idHotel."'");
		$rst=$cnn->consultar($query);
		$fila=mysql_fetch_assoc($rst);
		
		echo $fila[$campo];
}
//autocomplete para ciudades
else if(isset($_GET['q']) && isset($_GET['varaux'])){
		
		$dato= $_GET['q'];
	
 		$cnn    = new conexion();
		$query = "SELECT pro_ciudad FROM proveedores where pro_ciudad like '%$dato%' and pro_tipo=1 group by pro_ciudad";
	    $rst    = $cnn->consultar($query);
        while($fila=mysql_fetch_assoc($rst)){
			echo utf8_encode($fila["pro_ciudad"]."\n");
        }
}
//inserta nuevo hotel y ciudad
else if(isset($_POST['cdSelect']) && $_POST['cdSelect']!="" && isset($_POST['selectHotel']) && $_POST['selectHotel']!="" && isset($_POST['costoHotel'])&& $_POST['costoHotel']!="" ){

	$cdSelect=$_POST['cdSelect'];
	$selectHotel=$_POST['selectHotel'];
	$costoHotel=$_POST['costoHotel'];
	
	
	$cnn    = new conexion();	
    $query = "insert into proveedores (pro_proveedor,pro_tipo,pro_costo,pro_ciudad) values ('$selectHotel',1,'$costoHotel','$cdSelect')";
    $rst   = $cnn->insertar($query);	
}

else if(isset($_POST['idConcepto']) && $_POST['idConcepto']!="" && isset($_POST['idnivelUser']) && $_POST['idnivelUser']!="" && isset($_POST['tipoDivisa'])&& $_POST['tipoDivisa']!="" ){

	$idConcepto=$_POST['idConcepto'];
	$idNivelUser=$_POST['idnivelUser'];
	$tipoDivisa=utf8_decode($_POST['tipoDivisa']);
	
	
	$cnn    = new conexion();	
    $query = "select p_cantidad from parametros where p_concepto =$idConcepto and p_nivel_usuario=$idNivelUser and p_divisa= '$tipoDivisa'";

    $rst   = $cnn->consultar($query);	
	$d=mysql_fetch_assoc($rst);
	echo ($d['p_cantidad']);
}

// Recarga el combo de conceptos en base a la divisa
else if(isset($_POST['tipoDivisa'])&& $_POST['tipoDivisa']!="" && isset($_POST['tipoCatalogo']) && $_POST['tipoCatalogo']){
	$tipoDivisa = utf8_decode($_POST['tipoDivisa']);
?>
    <select name="select_concepto" id="select_concepto" onchange="">
        <?php echo get_select_conceptos_viaje($tipoDivisa); ?>
    </select>
	
<?php

}
// No retorno ninguna respuesta
?>
