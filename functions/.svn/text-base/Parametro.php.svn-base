<?php
$root  = $_SERVER['DOCUMENT_ROOT'];
$dir  = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
$uri = explode("/", $dir);
$base = $root . "/" . $uri[1] . "/";

require_once($base."lib/php/constantes.php");
require_once($RUTA_A."/Connections/fwk_db.php");
require_once($RUTA_A."/functions/ParametrosSistemas.php");

class Parametro{
	
	protected  $cnn;
	protected $vt_parametros;
	protected $p_rst;
	protected $rst_parametro;
	
	public function __construct(){
		$this->cnn= new conexion();	
	}
		
	
	public function Load($id){
		//echo("idFUnLoad:");	
		//echo $id;
		$query	=sprintf("select * from parametro_regionbmw where pr_id=%s",$id);

		$this->rst_parametro	= $this->cnn->consultar($query);
		if($this->cnn->get_rows()>0){
			$this->vt_parametros=$this->Get_dato("pr_id");			
		}	
		return $this->cnn->get_rows();	
	}
	
	public function Load_all_Parametros($id){
		$arr= array();
		$query=sprintf("select * from parametro_regionbmw where p_concepto=%s",$id);
		$arrParametros=array();
		$var=$this->cnn->consultar($query);
		while($arr=mysql_fetch_assoc($var)){
			array_push($arrParametros,$arr);
		}
		return $arrParametros;
		}

	
	public function  Get_dato($nombre){
		return(mysql_result($this->rst_parametro,0,$nombre));
		}
	
	public function Modifica_Parametro($cantidad, $id){
		$query=sprintf("update parametro_regionbmw set pr_cantidad=%s where pr_id=%s",$cantidad, $id);
			$this->cnn->insertar($query);			
	}
	
	public function insertar_Parametro($cantidad, $idConcepto,$tipo,$divisa){
		$query=sprintf("insert parametro_regionbmw set 
				pr_id=default,
				pr_cantidad=%s,
				p_concepto=%s,
				p_nivel_usuario=%s,
				p_divisa='%s'
				",
				$cantidad,
				$idConcepto,
				$tipo,
				$divisa
				);
			return($this->cnn->insertar($query));
	}
//Esta función es la que modifica el valor de los parámetros	
public function Modifica_Concepto($valor_parametro, $idCat){
		$query=sprintf("update parametro_regionbmw set pr_cantidad=%s where pr_id=%s",$valor_parametro,$idCat);
			$this->cnn->insertar($query);			
	}
	
	//funcion que permitira hacer el cambio del campo p_nombre de la tabla parametrosbmw sea internacional o nacional
	public function Modifica_Parametro_TNI_Alimento($idComida,$tazaAlimento,$tazaAlimentoId){

		//funcion que reiniciara el valor de la variable p_nombre.
		if(strstr($tazaAlimento,'TasaAlimentosDiariaNacional')){
			$tazaAlimeto="";
			$tazaAlimento='TasaAlimentosDiariaNacional';
		}else{
			$tazaAlimeto="";
			$tazaAlimento='TasaAlimentosDiariaInternacional';
		}		
				
		//se realiza la actualizacion del campo p_nombre
		$ParametroOriginal=str_replace("Diaria","",$tazaAlimento);
		error_log(".......".$ParametroOriginal);
		if($idComida == 'Desayuno'){			
			$ParametroOriginal.=$idComida;
			error_log("taza alimento----".$tazaAlimento);
		}else if($idComida == 'Comida'){
			$ParametroOriginal.=$idComida;
			error_log("taza alimento----".$tazaAlimento);
		}else if($idComida == 'Cena'){
			$ParametroOriginal.=$idComida;
			error_log("taza alimento----".$tazaAlimento);
		}		
		//$query=sprintf("UPDATE parametrosbmw SET p_nombre= '%s' WHERE p_id=%s",$tazaAlimento,$tazaAlimentoId);
		return $ParametroOriginal;	
		
	}
	
	
	/*
	 * Funciones para el calculo de montos maximos Alimentos , Hotel y Lavanderia
	 */
	
	//Funcion que obtiene el id de la tasa de los conceptos Alimentos, Hotel y Lavanderia (Nacional/Internacional)
	public function calculoTasaConceptos($id,$p_nombre){
		$idConceptoNI=0;
		
		$queryConceptoNI="SELECT p_id FROM parametrosbmw
		INNER JOIN cat_conceptosbmw
		ON parametrosbmw.p_concepto=cat_conceptosbmw.dc_id
		WHERE p_nombre ='{$p_nombre}'
		AND cat_conceptosbmw.dc_id={$id}";
		error_log($queryConceptoNI);
		$parametroConceptoNI = $this->cnn->consultar($queryConceptoNI);
		while ($fila = mysql_fetch_assoc($parametroConceptoNI)) {
		$idConceptoNI=$fila['p_id'];
		}		
		return $idConceptoNI;		
	}	
	
	//funcion que permitira obtener los datos de viaje de tipo sencillo
	public function datosViajeSencilloRedondo($tramiteId){
		$tipoViaje=0;
		$region=0;
		$diasViaje=0;
		
		$query_TV="SELECT svi_tipo_viaje,svi_region,svi_dias_viaje FROM sv_itinerario WHERE svi_solicitud =(SELECT sv_id FROM solicitud_viaje WHERE sv_tramite = {$tramiteId})";
		$rst=$this->cnn->consultar($query_TV);
		$aux = array();
		while ($fila = mysql_fetch_assoc($rst)) {
			array_push($aux, $fila);
		}
		foreach($aux as $datoAux){
			$tipoViaje = $datoAux['svi_tipo_viaje'];
			$region = $datoAux['svi_region'];			
		}
		
		$tramiteDias=new Tramite();
		$diasViaje = $tramiteDias->calculoDias($tramiteId);
		
		$matriz=array($tipoViaje,$region,$diasViaje);
		return $matriz;
	}
	
	//funcion que permitira obtener los datos del viaje  de tipo multidestinos
	public function datosViajeMultidestino($tramiteId){
		$tipoViaje=0;
		$region=0;
		$diasViaje=0;
		
		$query_TV="SELECT svi_tipo_viaje,svi_region FROM sv_itinerario WHERE svi_solicitud =(SELECT sv_id FROM solicitud_viaje WHERE sv_tramite = {$tramiteId})";		
		$rst=$this->cnn->consultar($query_TV);		
		$aux = array();		
		
		while ($fila = mysql_fetch_assoc($rst)) {
			array_push($aux,$fila);
		}
		foreach($aux as $datosAux){
			//se obtienen los datos de los ultimos itinerarios
			$tipoViaje = $datosAux['svi_tipo_viaje'];
			$region = $datosAux['svi_region'];
		}
 		//se obtiene la suma de los dias de cada uno de los itinerarios que poseea
		$tramiteDias=new Tramite();
		$diasViaje = $tramiteDias->calculoDias($tramiteId);
// 		$queryMultiDias="SELECT SUM(svi_dias_viaje) as svi_dias_viaje FROM sv_itinerario WHERE svi_solicitud =(SELECT sv_id FROM solicitud_viaje WHERE sv_tramite = {$tramiteId})";
// 		error_log($queryMultiDias);
// 		$rstMultiDias=$this->cnn->consultar($queryMultiDias);
// 		while ($fila = mysql_fetch_assoc($rstMultiDias)) {
// 			$diasViaje=$fila['svi_dias_viaje'];
// 		}
		$matriz=array($tipoViaje,$region,$diasViaje);
		return $matriz;		
	}
	//funcion que permitira realizar la conversion de tipo de moneda a pesos
	public function calculoMontoDivisa($divisa,$parametroCantidad){
		$parametroTransformacionPesos=0;
		$queryDivisa=sprintf("SELECT div_tasa FROM divisa WHERE div_id ='%s'",$divisa);
		$rstDivisa=$this->cnn->consultar($queryDivisa);
		while ($fila = mysql_fetch_assoc($rstDivisa)) {
			$tasa=$fila['div_tasa'];
		}
		$parametroTransformacionPesos=$parametroCantidad*$tasa;
		return $parametroTransformacionPesos;
	}
	
	/*
	 * FIN
	 */
	
// 	//funcion que nos permite calcular el monto max. de un viaje tipo nacional
// 	public function montoMaxToleranciaNacional($montoVueloTotal,$montoVueloCotizacion){
// 		$p_id=0;
// 		$montoTolerancia=0;
// 		$bandera=0;
// 		$query=sprintf("SELECT p_id FROM parametrosbmw WHERE p_nombre='MontoToleranciaVariacionAgenciaNacional'");
		
// 		$var=$this->cnn->consultar($query);
// 		while ($fila = mysql_fetch_assoc($var)) {			
// 			$p_id=$fila['p_id'];
// 		}
// 		$query_Region=sprintf("SELECT pr_cantidad FROM parametro_regionbmw WHERE p_id=%s",$p_id);
		
// 		$rst=$this->cnn->consultar($query_Region);
// 		while ($fila = mysql_fetch_assoc($rst)) {			
// 				$montoTolerancia=$fila['pr_cantidad'];
// 			}		
// 		//realizamos la operacion para el monto calculando la diferencia
// 		$diferencia=$montoVueloTotal-$montoVueloCotizacion;
		
// 		//enviara un valor 1 si se excedio el monto, de lo contrario solo enviara un 0
// 		if($diferencia > $montoTolerancia){
// 			$bandera=1;
// 			return $bandera;
// 		}else{
// 			return $bandera;
// 		}	
// 	}
	
// 	//funcion que nos permite calcular el monto max. de un viaje tipo Internacional
// 	public function montoMaxToleranciaInter($montoVueloTotal,$montoVueloCotizacion){
// 		//error_log("aqui entra validacion internacional");
// 		$p_id=0;
// 		$montoTolerancia=0;
// 		$bandera=0;
// 		$query=sprintf("SELECT p_id FROM parametrosbmw WHERE p_nombre='MontoToleranciaVariacionAgenciaInternacional'");
// 		$var=$this->cnn->consultar($query);
// 		while ($fila = mysql_fetch_assoc($var)) {			
// 			$p_id=$fila['p_id'];
// 		}
// 		$query_Region=sprintf("SELECT pr_cantidad FROM parametro_regionbmw WHERE p_id=%s",$p_id);
// 		$rst=$this->cnn->consultar($query_Region);
// 		while ($fila = mysql_fetch_assoc($rst)) {			
// 				$montoTolerancia=$fila['pr_cantidad'];
// 			}
// 		// Realizamos la operacion para el monto calculando la diferencia
// 		$diferencia=floatval($montoVueloTotal)-floatval($montoVueloCotizacion);

// 		// Enviará un valor 1 si se excedió el monto, de lo contrario enviara un 0		
// 		if($diferencia > $montoTolerancia){
// 			$bandera=1;
// 			return $bandera;
// 		}else{
// 			return $bandera;
// 		}
// 	}
	
	// Función que nos permite calcular el monto máximo de un viaje
	public function montoMaxTolerancia($tipoViaje, $montoVueloTotal, $montoVueloCotizacion){
		$p_id = 0;
		$montoTolerancia = 0;
		$bandera = 0;
		$diferencia = 0;
		$codigo = 'MTVGNA'; // "MontoToleranciaVariacionAgenciaNacional"
		
		if($tipoViaje == 1){ // Tipo de viaje Internacional
			$codigo = 'MTVAIN'; // "MontoToleranciaVariacionAgenciaInternacional"
		}
		
		$parametroSistema = new ParametrosSistema();
		$parametroSistema->set("codigo", $codigo);
		$parametroSistema->obtenerParametro();
		$montoToler = $parametroSistema->get('valor');
				
		// Realizamos la operacion para el monto calculando la diferencia
		$diferencia = $montoVueloTotal - $montoVueloCotizacion;
		
		error_log("------------->>>>>>>>Diferencia entre los montos: ".$diferencia.", monto del vuelo(ultima cotizacion): ".$montoVueloTotal.", monto previa cotizacion: ".$montoVueloCotizacion." PARAMETRO.");
		
		// Enviará un valor 1 si se excedió el monto, de lo contrario enviara un 0
		if($diferencia > $montoToler){
			$bandera = 1;
			return $bandera;
		}else{
			return $bandera;
		}
	}
	
}
?>
