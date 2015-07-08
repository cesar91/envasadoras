<?php
require_once "$RUTA_A/Connections/fwk_db.php";
require_once "$RUTA_A/functions/utils.php";

class Presupuesto extends Conexion {

	public function __construct(){
		parent::__construct();		
	}
    	
	/*
     *  Busca el presupuesto por centro de costos y por concepto
     *  (esta combinación es única en esta tabla)
     */
	public function Existe_CtroCosto_Y_Concepto($id_ctroCosto,$id_concepto,$fechaI,$fechaF){
		$query=sprintf("select * from ceco_concepto pp where pp.ceco = ".$id_ctroCosto." and pp.concepto = ".$id_concepto."");
		$result=parent::consultar($query);
		if(parent::get_rows()>0){
			$row=mysql_fetch_assoc($result);
			return $row;
		}else{
			return NULL;
		}
	}
	
	public function Existe_Periodo_Presupuestas($id_ctroCosto,$id_concepto,$fechaI,$fechaF){
		$query=sprintf("select * from ceco_concepto pp where pp.ceco = ".$id_ctroCosto." and pp.concepto = ".$id_concepto."");
		$result=parent::consultar($query);
		if(parent::get_rows()>0){
			$row=mysql_fetch_assoc($result);
			return $row;
		}else{
			return NULL;
		}
	}
	
	public function Existe_CtroCosto_Y_Concepto_Fecha($id_ctroCosto,$id_concepto,$fechaI,$fechaF){
		$query=sprintf("select * from ceco_concepto cc 
							inner join periodo_presupuestal pp on cc.idceco_concepto = pp.idceco_concepto
							where cc.ceco = ".$id_ctroCosto." and cc.concepto = ".$id_concepto."
							and pp.pp_periodo_inicial = '".$fechaI."'
							and pp.pp_periodo_final = '".$fechaF."'");
		$result=parent::consultar($query);
		if(parent::get_rows()>0){
			$row=mysql_fetch_assoc($result);
			return $row;
		}else{
			return NULL;
		}
	}
	
	public function Existe_PP($cc_id,$finicial,$ffinal){
		$FI=explode("-",$finicial);
		$FF=explode("-",$ffinal);
		$query=sprintf("SELECT * FROM periodo_presupuestal WHERE cc_id = $cc_id AND pp_periodo_inicial = '".$FI[2]."-".$FI[1]."-".$FI[0]."' AND pp_periodo_final = '".$FF[2]."-".$FF[1]."-".$FF[0]."' ");			
		$result=parent::consultar($query);
		if(parent::get_rows()>0){
			$row=mysql_fetch_assoc($result);
			return $row;
		}else{
			return NULL;
		}
	}
	
	public function validarPresupuesto($idTramite){
		$tramites = new Tramite();
		$tramites->Load_Tramite($idTramite);
		$flujoTramite = $tramites->Get_dato('t_flujo');
		
		switch ($flujoTramite){
			case FLUJO_SOLICITUD:
				$campos = 'sv_ceco_paga AS idCeco, sv_total AS total';
				$tabla = 'solicitud_viaje';
				$condicion = sprintf('sv_tramite = %s', $idTramite);
				break;
			case FLUJO_SOLICITUD_GASTOS:
				$campos = 'sg_ceco AS idCeco, sg_monto_pesos AS total';
				$tabla = 'solicitud_gastos';
				$condicion = sprintf('sg_tramite = %s', $idTramite);
				break;
			case FLUJO_COMPROBACION:
				$campos = 'co_cc_clave AS idCeco, co_total AS total';
				$tabla = 'comprobaciones';
				$condicion = sprintf('co_mi_tramite = %s', $idTramite);
				break;
			case FLUJO_COMPROBACION_GASTOS:
				$campos = 'co_cc_clave AS idCeco, co_total AS total';
				$tabla = 'comprobacion_gastos';
				$condicion = sprintf('co_mi_tramite = %s', $idTramite);
				break;
		}
	
		$queryCeco = sprintf("SELECT %s FROM %s WHERE %s", $campos, $tabla, $condicion);
		$rst = parent::consultar($queryCeco);
		$cc_id = mysql_result($rst, 0, "idCeco");
		$total = mysql_result($rst, 0, "total");
		
		// Obtener presupuesto
		$presupuestoCECO = $this->obtenerPresupuestoporCeco($cc_id, date("m"));
		$presupuestoDisponible = $presupuestoCECO['pp_presupuesto_disponible'];
		$excedePresupuesto = ($presupuestoDisponible < $total) ? true : false;
		$diferenciaPresupuesto = (float)$presupuestoDisponible - (float)$total;
		$presupuesto = array('excedePresupuesto' => $excedePresupuesto, 'diferenciaPresupuesto' => $diferenciaPresupuesto, 'presupuestoDisponible' => $presupuestoDisponible);
		return json_encode($presupuesto);
	}
	
	/** 
	 * Validación del presupuesto por CECO
	 **/
	public function obtenerPresupuestoporCeco($cc_id, $mes){
		$query = sprintf("SELECT pp_id, cc_id, pp_periodo_inicial, pp_periodo_final, pp_presupuesto_disponible, pp_presupuesto_inicial, pp_presupuesto_utilizado
				FROM periodo_presupuestal WHERE cc_id = %s AND MONTH(pp_periodo_inicial) = %s", $cc_id, $mes);
		$result = parent::consultar($query);
		$row = mysql_fetch_assoc($result);
		return $row;
	}
	
	/**
     * Crea un nuevo registro en presupuesto
	 */
	public function Nuevo_Presupuesto($idceco_concepto,$FI,$FF,$monto){
		$id=0;	
		$query=	"INSERT INTO periodo_presupuestal 
		 (pp_id, cc_id, pp_periodo_inicial, pp_periodo_final,
		 pp_presupuesto_disponible, pp_presupuesto_inicial, pp_presupuesto_utilizado) 
		 VALUES (default, ".$idceco_concepto.", STR_TO_DATE('".($FI)."', '%d-%m-%Y'), STR_TO_DATE('".($FF)."', '%d-%m-%Y'), ".$monto.", ".$monto.", 0)";			
		$id=parent::insertar($query);
		return $id;
	}
	
	public function Nuevo_CecoConcepto($idCentroCostos,$idConcepto,$data){
		$id=0;		
		$query= "INSERT INTO ceco_concepto 
		 (idceco_concepto, ceco, concepto) 
		 VALUES (default, ".$idCentroCostos.", ".$idConcepto.")";			
		$id=parent::insertar($query);
		return $id;
	}	
		
	/**
     * Actualiza un presupuesto
	 */
	public function Actualiza_Presupuesto($id_presupuesto,$pp_presupuesto_inicial, $pDisponible){
		$query=sprintf("UPDATE periodo_presupuestal SET pp_presupuesto_inicial = %s, pp_presupuesto_disponible= %s WHERE pp_id = %s", $pp_presupuesto_inicial, $pDisponible, $id_presupuesto);
		
		return mysql_query($query); // regresa true si es correcta la actualización false si no lo fué
	}
	
	/**
	 * Funcion que permitira el calculo del presupuesto requerido al realizar una solicitud de viaje
	 */
	public function calculoPresupuestoXCECO($totalSolicitud,$cecoId){
		$cecoNombre="";
		$cecoCantidad=0;		
		$diferencia=0;
		
		
		$query="SELECT SUM(pp_presupuesto_inicial) AS 'presupuesto_inicial' FROM periodo_presupuestal WHERE pp_ceco={$cecoId}";		
		$result=parent::consultar($query);
		while ($fila = mysql_fetch_assoc($result)) {
			$cecoCantidad=$fila['presupuesto_inicial'];
		}		
		
		if($cecoCantidad != ""){
			//se inicia la comparacion de el total de la solicitud
			if(floatval($totalSolicitud) > $cecoCantidad){
				$diferencia=1;				
			}else{
				$diferencia=0;
			}
		}
		return $diferencia;
	}
}
?>
