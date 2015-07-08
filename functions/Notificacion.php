<?php
class Notificacion extends conexion {
	
	function __construct(){
		parent::__construct();
	}
	
	protected $cabecera="Danone expenses:";
	protected $cont=" ";
	protected $dest=" ";
	
	//public function Add($comentario,$tramite,$asignado_a,$coment="Ninguno",$activo=1){
	public function Add($comentario,$tramite,$asignado_a,$coment,$activo){
	//session_start();
		$U = new Usuario();		
               	
		if($coment!='Ninguno'){
		
			$U->Load_usuario($_SESSION["idusuario"]);
			$c= $coment;
			$coment="";              
 	        $coment.= $c."    \n\nAprobado por: ".$U->Get_dato("u_nombre")." ".$U->Get_dato("u_paterno")." ".$U->Get_dato("u_materno");
		}

		/*if(!isset($_SESSION["idusuario"])){
			$_SESSION["idusuario"]=$asignado_a;
			
		}*/
		if(trim($comentario)!=""){
				$query=sprintf("insert into notificaciones (
									nt_id,
									nt_descripcion,
									nt_tramite,
									nt_remitente,
									nt_asignado_a,
									nt_fecha,
									nt_comentarios,
									nt_activo,
									nt_aceptado
							)
							values(
									default,
									'%s',
									%s,
									%s,
									'%s',
									now(),
									'%s',
									'%s',
									0
							)
						",
							$comentario,
							$tramite,
							$_SESSION["idusuario"],
							$asignado_a,
							$coment,
							$activo
							
						);
					
				parent::insertar($query);
		}
	}
	
	
	public function Show_list($usuario="",$empleado){
		if($usuario=="")
			$usuario	= $empleado;
	
		$Lista		= new Lista();
		
		if($_SESSION["perfil"]==5){
			$query=" select t_id,t_etiqueta,date(t_fecha_registro), (select concat(u_nombre,' ',u_paterno,' ',u_materno) from usuario where u_id=t_iniciador)  as atiende, (select svi_fecha_salida from sv_itinerario where svi_solicitud = solicitud_viaje.sv_id group by svi_solicitud) as fechaSalida, sv_agente_travel, sv_anticipo from tramites inner join solicitud_viaje on (solicitud_viaje.sv_tramite= tramites.t_id) where t_etapa_actual=2 || t_etapa_actual=3 and t_owner=" . $_SESSION["idusuario"]." and t_cerrado=false and t_cancelado=false and t_flujo=3 order by t_id desc";
			
			//$query=sprintf("select t_id, nt_descripcion,t_etiqueta,nt_fecha from notificaciones left outer join tramites on (notificaciones.nt_tramite=tramites.t_id) where nt_asignado_a='%s' order by nt_fecha desc ;",$usuario);
			$Lista->Herramientas("E","./index.php?id=");
			$Lista->Herramientas("AG","");
			$Lista->Cabeceras("Folio");			
			$Lista->Cabeceras("Referencia");
			$Lista->Cabeceras("Fecha Registro");
			$Lista->Cabeceras("Iniciador");
			$Lista->Cabeceras("Fecha Salida");
			$Lista->Cabeceras("Agente");
			$Lista->Cabeceras("$ Solc.","","number","R");
			
		}
		
		else{

			$query		=sprintf("select nt_descripcion,t_etiqueta,nt_fecha from notificaciones left outer join tramites on (notificaciones.nt_tramite=tramites.t_id) where nt_asignado_a='%s' order by nt_fecha desc ;",$usuario);
			$Lista->Cabeceras("Notificaci&oacute;n");
			$Lista->Cabeceras("Referencia");
			$Lista->Cabeceras("Fecha");				
		}
				
		$Lista->muestra_lista($query,0,false,-1,"",15);
		
	}
	
	public function Show_list_travel($empleado){
		if($_SESSION["perfil"]==5){
			
			$Lista		= new Lista();
			
			$query=" select t_id,t_etiqueta,date(t_fecha_registro), (select concat(u_nombre,' ',u_paterno,' ',u_materno)  from usuario where u_id=t_iniciador)  as atiende, (select svi_fecha_salida from sv_itinerario where svi_solicitud = solicitud_viaje.sv_id group by svi_solicitud) as fechaSalida, sv_agente_travel from tramites inner join solicitud_viaje on (solicitud_viaje.sv_tramite= tramites.t_id)  where t_etapa_actual=4 and t_owner=" . $_SESSION["idusuario"]." and t_cerrado=false and t_cancelado=false and t_flujo=3 order by t_id desc";
			$Lista->Herramientas("E","./index.php?view=");
			$Lista->Cabeceras("Folio");
			$Lista->Cabeceras("Referencia");
			$Lista->Cabeceras("Fecha Registro");
			$Lista->Cabeceras("Iniciador");
			$Lista->Cabeceras("Fecha Salida");
			$Lista->Cabeceras("Agente");
			//$Lista->Cabeceras("$ Solc.","","number");
		}
		$Lista->muestra_lista($query,0,false,-1,"",15);
	}
	
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $tramite
	 */
	public function Notificar($tramite,$comentario="Ninguna",$TipoSolicitud,$etapaComp="",$coTipo=""){ //Se agrego $etapaComp para las comprobaciones ya autorizadas.
		//session_start();
		$cad_iniciador	="";
		$cad_grupo		="";
		$cad_usuario	="";
		
		$U= new Usuario();
		file_put_contents("setsion.txt",$_SESSION["idusuario"]);
		$U->Load_usuario($_SESSION["idusuario"]);
		//echo $_SESSION["idusuario"]." dso";
		$NameUser=$U->Get_dato("u_nombre")." ".$U->Get_dato("u_paterno")." ".$U->Get_dato("u_materno");
		
		
		$T	= new Tramite();
		$F	= new Flujo();
		$C	= new Comprobacion();
		//$E	= new Etapa();
		$T->Load_Tramite($tramite);
		
		
		//$F->Load($T->Get_dato("t_flujo"));
		//$E->Load_Etapa($T->Get_dato("t_etapa_actual"));
		
		//$nombre_tramite=$F->Get_dato("f_nombre");
		$cad_grupo		= "La " . $TipoSolicitud . " No.  <strong>" . sprintf("%05s",$tramite)  . "</strong>";
		$cad_iniciador	= $cad_grupo;
		$cad_usuario	= $cad_grupo;
		$etapa			= "";
				
		
		if($T->Get_dato("t_cerrado")==1){ //SI EL TRAMITE SE CERRO
			
			if($T->Get_dato("t_cancelado")==0){
				$cad_iniciador.=" ha sido <strong> Aprobada </strong> por <strong>{$NameUser}</strong> y  <strong>Cerrada Correctamente</strong> ";
				$etapa			="AUTORIZADA";
				$cad_usuario	="";
				$cad_grupo		="";
			}else{
				$etapa="CANCELADA";
				$cad_iniciador	.=" ha sido <strong>Cancelada</strong> por <strong>{$NameUser}</strong> ";
				$cad_grupo		="";
				$cad_usuario	="";
			}
		}else{ //PASA A UNA ETAPA DETERMINADA
			
			//$et_ant=$this->etapa_anterior($T->Get_dato("t_etapa_actual"));
			
			if($T->Get_dato("t_etapa_actual")==2){ //SE ACABA DE CAPTURAR
				
				$cad_usuario	.=" ha sido <strong>Generada</strong>";
				$cad_iniciador	.=" ha sido <strong>Generada</strong> Correctamente";
				$etapa			 ="GENERADA";
				
			}elseif($T->Get_dato("t_etapa_actual")==3 && $etapaComp == ""){
				$cad_usuario	.=" ha sido <strong>Generada</strong>";
				$cad_iniciador	.=" ha sido <strong>Generada</strong> Correctamente";
				$etapa			 ="GENERADA";
			}elseif($T->Get_dato("t_etapa_actual") == 3 && isset($etapaComp) && !empty($etapaComp)){
				$cad_usuario	.=" ha sido <strong>Generada</strong>";
				$cad_iniciador  .=" ha sido <strong> Aprobada </strong> por <strong>{$NameUser}</strong> y pasa a la siguiente etapa para su aprobaci贸n";
				$etapa			="APROBADA";
			}else{				
				$cad_iniciador	.= " ha sido <strong>APROBADA</strong> por <strong>{$NameUser}</strong> y pasa a la siguiente Etapa para su Aprobaci贸n.";
				$etapa="APROBADA";
			}
			/*if($T->Get_dato("t_grupo")>0){//ES UN GRUPO
				$G	= new Grupo();
				$G->Load_data($T->Get_dato("t_grupo"));
	
				//$cad_grupo		.=" ha sido asignada al Grupo: <strong>" . $G->Get_dato("g_nombre") . "</strong> y queda en espera de su aprobacion";
				$cad_grupo			.=" ha sido asignada a tu Grupo para su Aprobaci贸n";
				//MANDAMOS NOTIFICACION PARA TODO EL GRUPO
				$this->Notifica_grupo($T->Get_dato("t_id"),$cad_grupo,$T->Get_dato("t_grupo"),$comentario);
				$cad_usuario	="";		
			}
			else{*/
			
				$cad_grupo	 ="";
				$cad_usuario.=" y te ha sido Asignada para su Aprobaci贸n.";
				
				//MANDAMOS A INSERTAR LA NOTIFICACION
			//}
			
		}
		$U_Iniciador= new Usuario();
		$U_Iniciador->Load_usuario($T->Get_dato("t_iniciador"));
		
		if($T->Get_dato("t_iniciador") != $T->Get_dato("t_owner")){
			$this->Add($cad_iniciador,$T->Get_dato("t_id"),$U_Iniciador->Get_dato("u_usuario"));
			$this->envia_mail_iniciador($T->Get_dato("t_id"), $T->Get_dato("t_iniciador"),$etapa,$TipoSolicitud,$comentario);
		}
		
		if($TipoSolicitud=="Comprobacion"){
			if($coTipo != "2"){
				$C->Load_Comprobacion($tramite);
				$co_id	= $C->Get_dato("co_id");
				
				$query=sprintf("SELECT * FROM detalle_comprobacion where dc_comprobacion=%s",$co_id);			
				$rst=parent::consultar($query);
				if($fila=mysql_fetch_assoc($rst)){
					if(trim($cad_usuario!="")){
						$U_Iniciador->Load_usuario($fila['dc_responsable']);
						$this->Add($cad_usuario,$T->Get_dato("t_id"),$fila['dc_responsable']);
						$this->envia_mail_usuario($T->Get_dato("t_id"),$U_Iniciador->Get_dato("u_id"),$TipoSolicitud,$T->Get_dato("t_iniciador"),$comentario);
					}
				}
				
			}else{
				$C->Load_Comprobacion_Amex($tramite);
				$co_id	= $C->Get_dato_amx("co_id");
				$query=sprintf("SELECT * FROM detalle_comprobacion_amex where dca_comprobacion=%s",$co_id);			
				$rst=parent::consultar($query);
				if($fila=mysql_fetch_assoc($rst)){
					if(trim($cad_usuario!="")){
						$U_Iniciador->Load_usuario($fila['dca_responsable']);
						$this->Add($cad_usuario,$T->Get_dato("t_id"),$fila['dca_responsable']);
						$this->envia_mail_usuario($T->Get_dato("t_id"),$U_Iniciador->Get_dato("u_id"),$TipoSolicitud,$T->Get_dato("t_iniciador"),$comentario);
					}
				}
			}
			
		}else{
			if(trim($cad_usuario!="")){
				$U_Iniciador->Load_usuario($T->Get_dato("t_owner"));
				$this->Add($cad_usuario,$T->Get_dato("t_id"),$U_Iniciador->Get_dato("u_usuario"));
				$this->envia_mail_usuario($T->Get_dato("t_id"),$T->Get_dato("t_owner"),$TipoSolicitud,$T->Get_dato("t_iniciador"),$comentario);
			}
		}
		
	}
	/*
	$tramite= id del tramite
	$usuario=usuario que inicio el tramite,$etapa, $flujo,$comentario
	$etapa= estado de la solcitud
	$flujo
	*/
	private function envia_mail_iniciador($tramite,$usuario,$etapa, $flujo,$comentario){
		
		$M			= new Mail();
		$U			= new Usuario();
		$extra		= "";
		$tramite	= sprintf("%05s",$tramite);
		$U->Load_usuario($usuario);
		$U2			= new Usuario();
		$U2->Load_usuario($_SESSION["idusuario"]);
		$nombre_actual= " ".strtoupper($U2->Get_dato("u_nombre")). " " .strtoupper($U2->Get_dato("u_paterno")). " " .strtoupper($U2->Get_dato("u_materno")). " ";
		switch($etapa){
			case "AUTORIZADA":	$extra=" satisfactoriamente por el usuario: $nombre_actual";	break;
			case "CANCELADA":	$extra=" por el usuario: $nombre_actual y por lo tanto no podr&aacute; ser tomada como una solicitud v&aacute;lida";	break;
			case "APROBADA":	$extra=" satisfactoriamente por el usuario: $nombre_actual y pasa a la siguiente etapa para su aprobaci&oacute;n";	break;
			case "GENERADA":	$extra=" y pasa a la siguiente etapa para su aprobaci&oacute;n";	break;
			
		}
		$body="<p>
					Estimado: <strong>".strtoupper($U->Get_dato('u_nombre'))." ".strtoupper($U->Get_dato('u_paterno'))." ".strtoupper($U->Get_dato('u_materno'))."</strong><br>
					Te informamos que tu $flujo con No. <strong>$tramite</strong><br> ha sido <strong>$etapa</strong> $extra
					
					<br><br><br>
					<strong>Observaciones:</strong>{$comentario}
					
					<br><br>
					Para Ingresar al sistema presione:<a href='http://201.159.133.44/eexpenses_danone/'><strong>aqui</strong></a>.
				</p>";
	//	mail($U->Get_dato("u_email"),"Danone expenses Tramite: " . $tramite ,$body,"");
		$M->Envio_normail("Danone expenses Tramite: " . $tramite ,$body,$U->Get_dato("u_email"),"");		
	}
	
	private function envia_mail_usuario($tramite,$usuario,$flujo, $iniciador,$comentario){
		$U		= new Usuario();
		$U2		= new Usuario();
		$M		= new Mail();
		$U->Load_usuario($usuario);
		$U2->Load_usuario($iniciador); 
		
		$tramite=sprintf("%05s",$tramite);
		//$U2->Load_usuario($_SESSION["idusuario"]);
		$nombre_actual = " ".strtoupper($U2->Get_dato("u_nombre")). " ".strtoupper($U2->Get_dato("u_paterno")). " " .strtoupper($U2->Get_dato("u_materno")). " ";
		$body="<p>
				Estimado: <strong>".strtoupper($U->Get_dato('u_nombre'))." ".strtoupper($U->Get_dato('u_paterno'))." ".strtoupper($U->Get_dato('u_materno'))."</strong><br>
				Le informamos que el usuario: ";
				if($usuario == $iniciador){
		  			$body .= "<strong>Agencia de Viajes</strong>";	
				}else{
					$body .= "<strong>".$nombre_actual."</strong>"; 
				}
				$body .= " ha registrado la <strong>".utf8_decode($flujo)."</strong> No. <strong>$tramite</strong><br>
				 y esta en espera de su aprobaci&oacute;n. Le solicitamos vertifique los datos del tr&aacute;mite lo antes posible.
				<br><br><br>
				<strong>Observaciones:</strong>{$comentario}
				<br><br>
					Para Ingresar al sistema presione:<a href='http://201.159.133.44/eexpenses_danone'><strong>aqu&iacute;</strong></a>.
				</p>";
//		mail($U->Get_dato("u_email"),"Danone expenses Tramite: " . $tramite ,$body,"");
		$M->Envio_normail("Danone expenses Tramite: " . $tramite ,$body,$U->Get_dato("u_email"),"");
	}
	
	private function etapa_anterior($etapa){
		$query=sprintf("select * from etapas where et_siguiente_etapa =%s;",$etapa);
		$rst=parent::consultar($query);
		$fila=mysql_fetch_assoc($rst);
		return($fila);
	}
	
	
	private function Notifica_grupo($tramite,$comentario,$grupo,$coment){
		$M	= new Mail();
		$G	= new Grupo();
		$G->Load_data($grupo);
		$mails="";
		$query=sprintf(" select detalle_grupo.*, usuario.u_email from detalle_grupo inner join usuario on (detalle_grupo.dg_usuario=usuario.u_id) where dg_grupo =%s",$grupo);
		$rst=parent::consultar($query);
		while($fila=mysql_fetch_assoc($rst)){
			$mails.=($mails=="")?"":",";
			$mails.=$fila["u_email"];
			$this->Add($comentario,$tramite,$fila["dg_usuario"],$coment);
		}
		$body="<p>
						<strong>Estimado Grupo:" . $G->Get_dato("g_nombre") .  "</strong><br>
						Te Informamos que el tramite: <strong>". sprintf("%05s",$tramite) ."</strong> y ha sido asignado al grupo de usuarios que usted pertenece.<br>
						Le solicitamos verifique los datos dentro del sistema lo antes posible. El tramite estar&aacute; relacionado al usuario del grupo que lo atienda primero.
						
						<br><br><br>
						<strong>Observaciones:</strong>{$coment}
						
						<br><br>
					Para Ingresar al sistema presione:<a href='http://201.159.133.44/eexpenses_danone'><strong>aqu&iacute;</strong></a>.
						
		</p>";
		//mail($U->Get_dato("u_email"),"Danone expenses Tramite: " . $tramite ,$body,"");
		$M->Envio_normail("Danone expenses Tramite: " . sprintf("%05s",$tramite) ,$body,$mails,"GRUPO: ". $G->Get_dato("g_nombre"));
	}	
	
	public function notificaUsu(){	
		$M	= new Mail();
		//file_put_contents("antes.txt","antes");
//		$M->Envio_normail($a,$b,$c,$d);		
		//file_put_contents("despues.txt","despues");
		//$M->Envio_normail("Danone expenses:","contenido111","luis@metistd.com","");
		//file_put_contents("tercero.txt","tercero");
		//error_log($this->cabecera." ".$this->cont." ".$this->dest);
		$M->Envio_normail($this->cabecera,$this->cont,$this->dest,"");
//		$this->envia();
//		mail('luis@metistd.com', 'El t铆tulo', 'El mensaje', null,'luis@metistd.com');
	}
	
	
	private function envia()
	{	
		
		$M	= new Mail();
		//$M->Envio_normail("Danone expenses:","contenido22222222","luis@metistd.com","");
		$M->Envio_normail($this->cabecera,$this->cont,$this->dest,"");

	}
	
	public function set_cabecera($var){
		$this->cabecera=$var;
		
	}
	
	public function set_contenido($var){
		$this->cont=$var;
		error_log("");
	}
	
	public function set_destinatario($var){
		$this->dest=$var;
	}
	
	public function anotaObservacion($t_dueno,$HObser,$sObser,$flujo,$etapa, $representante = 0){
		//Tomara el usuario que aprobo,si de controlling o finanzas y si es un gerente/director de area
		$duenoActual = new Usuario();
		
		if($representante != 0){
			$duenoActual->Load_Usuario_By_ID($representante);
			$nombreRepresentante = $duenoActual->Get_dato('nombre');
			$dueno_act_nombre = sprintf(strtoupper($nombreRepresentante." en nombre de: "));
		}
			
		//error_log(">>>>>>>>>>>>>>>>>>>>>Due駉: ".$t_dueno);
		if($duenoActual->Load_Usuario_By_ID($t_dueno)){
			$dueno_act_nombre .= $duenoActual->Get_dato('nombre');
		}else{
			$agrup_usu = new AgrupacionUsuarios();
			$agrup_usu->Load_Grupo_de_Usuario_By_ID($t_dueno);
			$dueno_act_nombre .= $agrup_usu->Get_dato("au_nombre");
		}
		
		if($HObser != ""){
			if(($etapa == SOLICITUD_GASTOS_ETAPA_RECHAZADA && $flujo == FLUJO_SOLICITUD_GASTOS)){
				$txt = $HObser."\n".$sObser;
			}else{
				$txt = $dueno_act_nombre.": ".$sObser."\n".$HObser;
			}
		}else{
			if(($etapa == SOLICITUD_GASTOS_ETAPA_RECHAZADA && $flujo == FLUJO_SOLICITUD_GASTOS)){
				$txt = $sObser;
			}else{
				$txt = $dueno_act_nombre.": ".$sObser;
			}			
		}
		
		return $txt;
	}
		
}
?>