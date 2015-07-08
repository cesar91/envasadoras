<?PHP 

 class Interfaz extends conexion {
 	
 	
 	protected $titulo, $menu;
 	protected $seleccionmenu;
 	public  $Mn;
 	
 	public function Interfaz($titulo,$menu=true,$extra=""){
 		if(!isset($_SESSION["idusuario"])){
 			global $RUTA_R;
 			
 			header("Location: {$RUTA_R}");
 			exit();
 			
 		}
 		$this->seleccionmenu=$menu;
 		parent::__construct();
 		$this->titulo	=$titulo;
 		$this->menu		=$menu;
 		$this->Superior($extra);
 		if($menu==true)
 		$this->Mn		= new Menu();
 	}
 	
 	
 	/**
 	 * Coloca la parte SUperior de la Interfaz
 	 *
 	 */
 	private function  Superior($extra=""){
 		global $APP_TITULO;
 		global $RUTA_R;
 		if($this->seleccionmenu==true){
 		$this->Mn = new Menu();
 		$this->Mn->Busca_Menu();
 		}
 		
 		
 		
 		?>
 		<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
			<html lang="es-MX">
				<head>
					<meta http-equiv="Content-type" content="text/html; charset=ISO-8859-1">
					
					<title><?PHP echo $APP_TITULO?></title>
					<link   rel="stylesheet" type="text/css" media="all" href="<?PHP echo $RUTA_R;?>lib/js/jscalendar-1.0/calendar-blue2.css" title="win2k-cold-1" />	
					<script type="text/javascript" src="<?PHP echo $RUTA_R;?>lib/js/jscalendar-1.0/calendar.js"></script>			
					<script type="text/javascript" src="<?PHP echo $RUTA_R;?>lib/js/jscalendar-1.0/lang/calendar-es.js"></script>
					<script type="text/javascript" src="<?PHP echo $RUTA_R;?>lib/js/jscalendar-1.0/calendar-setup.js"></script>
					<script type="text/javascript" src="<?PHP echo $RUTA_R;?>lib/js/swfobject/swfobject.js"></script>
					<link   type="text/css" rel="stylesheet" href="<?PHP echo $RUTA_R ?>css/estilo.css">
					<link   type="text/css" rel="stylesheet" href="<?PHP echo $RUTA_R ?>css/estilo_lista.css">
					<script type="text/javascript" src="<?PHP echo $RUTA_R;?>lib/js/jquery/jquery-1.3.2.js"></script>
					<script type='text/javascript' src="<?PHP echo $RUTA_R;?>lib/js/jquery/json2.js"></script>
					<script type="text/javascript" src="<?PHP echo $RUTA_R;?>lib/js/jquery/jquery.date_input.js"></script>
					<script type="text/javascript" src="<?PHP echo $RUTA_R;?>lib/js/bloqueaRetroceso.js"></script>
					<script type="text/javascript" src="<?PHP echo $RUTA_R;?>lib/js/utilerias.js"></script>
					
					<meta http-equiv="Pragma" content="no-cache">
					
				</head>
				
				<body <?PHP echo $extra; ?>>
				
				
				<div align="center" style="background: #a2a996; padding-top: 10px; min-height: 550px;" id="Interfaz">
					<table bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" width="89%" height="90%">
				    	<tbody valign="top">
				      		<tr>
				        		<td colspan="2" valign="top" height="40px">
				        			<table width="100%" border="0" cellspacing="0" cellpadding="0" style="padding-left: 30px; padding-right: 30px;" background="<?PHP echo $RUTA_R; ?>images/bannerBmw.png" border="0">
				          				<tr>
				            				<td width="50%" valign="top" style="border: 0px solid;"><span  style="position:absolute; left:84px; top:90px; text-transform: capitalize; font-size: 19px; color: #F8F8F8   ; font-variant: small-caps; font-family: sans-serif; font-weight: bold;" >&nbsp; </span> </td>
				            				<td width="50%" align="right" style="padding-right: 40">
				            					<table cellpadding="2" height=110 cellspacing="2">
				            						<tr>				            							
				            							<td>
				            							<?PHP if($this->seleccionmenu==true){ ?>
															<br /><br />
				            								<table style="text-transform:capitalize;  color:#ffc525; font-weight:bold; vertical-align: middle;">
				            									<tr valign="middle">
				            										<td><strong>Usuario:</strong>&nbsp;&nbsp;</td>
				            										<td><?PHP 
				            										if(isset($_SESSION['idrepresentante'])){
				            								 			echo $_SESSION["representante"]."<br /><strong> A NOMBRE DE: </strong><br />".$_SESSION['usuario'];
				            										}else{
				            											echo $_SESSION["usuario"];
				            										}
																	?></td>
				            									</tr>				            									
				            									<tr>
				            										<td><strong>Empresa:</strong>&nbsp;&nbsp;</td>
				            										<td><?PHP echo $_SESSION["nombreempresa"]; ?></td>
				            									</tr>
                                                                <!--
				            									<tr><td colspan='2'><strong><a href="<?PHP echo $RUTA_R; ?>tuto/intro/intro.htm">Te invitamos a consultar el manual de usuario:</a></strong>&nbsp;&nbsp;</td></tr>-->
				            								</table>
				            									
				            							<?PHP } ?>
				            									
				            							
				            							</td>
				            							<td>
															<!-- <img src="<?PHP echo $RUTA_R; ?>images/<?PHP if($_SESSION['nombreempresa']=='Marti'){?>LogoParaEntradaDelSistemaBMW.png<?PHP } else {?>LogoParaEntradaDelSistemaBMW.png<?PHP } ?>" alt="Grupo BMW"> -->
				            								
				            							</td>
				            							
				            						</tr>
				            						<tr></tr>
				            										            						
				            						
				            					</table>				            								            				
				            				</td>
				          				</tr>
				        			</table>
				        		</td>
				      		</tr>		
				      		<tr>
        						
        						<td valign="top" width="93%"> 
        					
									<table width="100%"  border="0" cellspacing="0" cellpadding="0"  >
										
										<tr>
											<td align="center" valign="top">
												<?PHP 
													if($this->seleccionmenu==true){
												 		$this->Mn->Imprime_Papas();
													} 
												 ?>
											</td>
										</tr>
									
            							
          							</table>
          							
          							<table width="100%" cellpadding="0" cellspacing="0" style="padding-left: 10px; padding-right: 10px;">
          							
          								<tr>
          								<?PHP 
          									//if($this->seleccionmenu==true && count($this->Mn->hijos)>0){ ?>
          									
          								<!--	<td valign="top"  width="13%" style="padding-right: 20px;"><?PHP //$this->Genera_Menu(); ?></td>-->
          								<?PHP //} ?>
          									
          								<td valign="top">
          							
          							<table width="100%" border="0" cellspacing="0" cellpadding="0" class="inicio">
          							
            							<tr>
              								<td>
	              								<?PHP if(isset($_GET['loginErr'])){ echo displayMessage($FWK_MSG_LOGINERROR,$FWK_MSG_ERROR);} ?>
	                							<?PHP if(isset($_GET['notLogged'])){ echo displayMessage($FWK_MSG_NOTLOGGED,$FWK_MSG_WARN);} ?>
	                							<?PHP if(isset($_GET['errormsg'])){ echo displayMessage(base64_decode($_GET['errormsg']),$FWK_MSG_ERROR);} ?>
              								</td>
            							</tr>
            							<!--<tr>
              								<td bgcolor="#E5EAEA"><h3><img src="<?PHP //echo $RUTA_R ?>images/bullet_tit_interiores.gif">&nbsp; <?PHP //echo $this->titulo ?> </h3></td>
            							</tr>-->
            							<tr>
              								<td   valign="top">              								
 		<?PHP 
 		
 	}
 	
 	public function Footer(){
 		?>
 												
 											</td>
            							</tr>
          							</table>
          							</tr>
          						</table>
          							<p>&nbsp;</p>
          						</td>
      						</tr>
      						<tr>
        						<td colspan="2" height="50"><div align="center"><font color="#336699" size="1" face="Verdana, Arial, Helvetica, sans-serif">Versi&oacute;n 2.5 </font></div></td>
      						</tr>
      						<tr bgcolor="#e4e4e4">
        						<td colspan="2" class="txtGreyVer9" bgcolor="#e4e4e4" height="50"><div align="center"> </div></td>
      						</tr>
    					</tbody>
  					</table>
  					<p>&nbsp;</p>
				</div>
			</body>
		</html>
 		<?PHP 
 	}
 	
 	private function Genera_Menu(){
 		global $RUTA_R;
 		$this->Mn	= new Menu();
 		$this->Mn->Busca_Menu();
 		
 		?>
 		<table width="100%" border="0" cellpadding="0" cellspacing="0" height="100%" >	
          			<tr>
          				<td bgcolor="#E5EAEA" align="center"><strong>Opciones</strong></td>
          			</tr>
          			
          			<tr>
          			
            			<td valign="top" bgcolor="#fcfffc">
            				<?PHP 
            					
            					$this->Mn->Imprime_Hijos();
            				?>
            			</td>
            		
          			</tr>
        		
      		</table>
 		<?PHP 
 	}
 }
?>
