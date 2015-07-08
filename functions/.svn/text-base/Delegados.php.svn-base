<?php
require_once("$RUTA_A/lib/php/constantes.php");
require_once("$RUTA_A/functions/Tramite.php");
require_once("$RUTA_A/functions/RutaAutorizacion.php");

class Delegados extends conexion {

	protected $rstDg;

	/*public function Load_all(){
		$arr= array();
		$query="select div_nombre as nombre, div_tasa as tasa FROM divisa  WHERE div_id>0 order by div_id ";
		$arrDivisa=array();
		$var=parent::consultar($query);
		while($arr=mysql_fetch_assoc($var)){
			array_push($arrDivisa,$arr);
		}
		return $arrDivisa;
	}

	public function Get_dato($nombre){
		return($this->rstDg[$nombre]);
	}*/
	
	public function existenciaDelegado($usuario, $delegado){
		if($delegado != 0 || $delegado != ""){
			return 1;
		}else{
			return 0;
		}
	}
	
	public function obtenerEtapaDelegados($flujo){
		$etapa = "";		
		switch ($flujo){
			case FLUJO_SOLICITUD:
				$etapa = SOLICITUD_ETAPA_EN_APROBACION_POR_DIRECTOR;
				break;
			case FLUJO_SOLICITUD_INVITACION:
				$etapa = SOLICITUD_INVITACION_ETAPA_EN_APROBACION_POR_DIRECTOR;
				break;
			case FLUJO_COMPROBACION:
				$etapa = COMPROBACION_ETAPA_EN_APROBACION_POR_DIRECTOR;
				break;
			case FLUJO_COMPROBACION_INVITACION:
				$etapa = COMPROBACION_INVITACION_ETAPA_EN_APROBACION_POR_DIRECTOR;
				break;				
		}
		
		return $etapa;
	}
	
	public function validaDelegado($user, $delegado, $empresa, $etapa, $flujo, $motivo){
		$tramite = new Tramite();
		$existeDelegado = $this->existenciaDelegado($user, $delegado);
		
		if($existeDelegado){
			$etapaTramite = $this->obtenerEtapaDelegados($flujo);
			$tramiteId = $tramite->Crea_Tramite($user, $empresa, $etapaTramite, $flujo, $motivo, $delegado);			
		}else{
			$tramiteId = $tramite->Crea_Tramite($iduser, $empresa, $etapa, $flujo, $motivo, $delegado);
		}		
		return $tramiteId;
	}
	
	public function ModificaEtapaDelegados($t_id, $etapa, $flujo, $dueno, $ruta, $iduser, $delegado){
		$tramite = new Tramite();
		$existeDelegado = $this->existenciaDelegado($iduser, $delegado);
		$tramite->Load_Tramite($t_id);
		$iniciador = $tramite->Get_dato("t_iniciador");
		
		if($existeDelegado){
			$etapatramite = $this->obtenerEtapaDelegados($flujo);
			$tramite->Modifica_Etapa($t_id, $etapatramite, $flujo, $iniciador, "");
		}else{
			$tramite->Modifica_Etapa($t_id, $etapa, $flujo, $dueno, $ruta);
			
			$ruta_autorizacion = new RutaAutorizacion();
			$ruta_autorizacion->generaRutaAutorizacionSolicitudInvitacion($tramite_editar, $iduser);
			$aprobador = $ruta_autorizacion->getSiguienteAprobador($t_id, $iduser);
			return $aprobador;
			
		}
	}
	
}
?>