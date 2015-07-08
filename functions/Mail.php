<?php
require_once "$RUTA_A/lib/php/mail.php";
class Mail {
	protected $mail;
	protected $nombre;
	protected $mensaje;
	protected $subject;
	
	
	public function Viaje_iniciador($folio,$usuario,$estatus="APROBADA",$inicia=false,$cierra=false){
		
		$this->datos_usuario($usuario);
		if($inicia==true){ //SE ACABA DE CAPTURAR EL TRAMITE
			$this->subject="Solicitud de Viaje $folio";
			$this->mensaje=sprintf("Estimado %s te informamos que la solicitud de Viaje No. %s fue registrada correctamente y ha sido enviada al usuario correspondiente para su aprobacion",$this->nombre,$folio);		
		}
		else{
			if($cierra==false){
				$this->subject="Solicitud de Viaje $folio";
				$this->mensaje=sprintf("Estimado %s te informamos que la solicitud de Viaje No. %s fue %s  ha sido enviada al usuario correspondiente para su aprobacion en la siguiente etapa",$this->nombre,$folio,$estatus);
				
			}
			else{
				$this->subject="Solicitud de Viaje $folio";
				$this->mensaje=sprintf("Estimado %s te informamos que la solicitud de Viaje No. %s fue %s",$this->nombre,$folio,$estatus);
				
			}
		}
		
		$this->enviar();
	}

	
	
	
public function Viaje_aprobador($folio,$usuario){
		$this->datos_usuario($usuario);
		$this->subject="Solicitud de Viaje $folio pendiente por Aprobar";
		$this->mensaje=sprintf("Estimado %s te informamos que la solicitud de Viaje No. %s te ha sido asignada para su aprobacion",$this->nombre,$folio);		
		$this->enviar();
		
	}
	
	
	
	private function enviar(){
		sendMail($this->subject, $this->mensaje,$this->mail,$this->nombre);
	}
	
	
	private function datos_usuario($usuario){
		$U 	= new Usuario();
		$U->Load_usuario($usuario);
		$this->mail=$U->Get_dato("u_email");
		$this->nombre=$U->Get_dato("u_nombre") . " " . $U->Get_dato("u_paterno") . " " . $U->Get_dato("u_materno");
	}
	
	
	public function Envio_normail($uno,$dos,$tres,$cuatro){
		
		error_log("se envio".sendMail($uno, $dos,$tres,$cuatro));
		
	}
}
?>