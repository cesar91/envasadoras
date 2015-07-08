<?php

 class InterfazMovil extends conexion {
 	
 	
 	protected $titulo, $menu;
 	protected $seleccionmenu;
 	public  $Mn;
 	
 	public function InterfazMovil($titulo,$menu=true,$extra=""){
 		
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
 		if($menu==true) {
          //  $this->Mn = new Menu();
        }
 	}
 	
 	
 	/**
 	 * Coloca la parte SUperior de la InterfazMovil
 	 *
 	 */
 	private function  Superior($extra=""){
 		global $APP_TITULO;
 		global $RUTA_R; 		
 		?>
 		<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml">
            <link   type="text/css" rel="stylesheet" href="<?php echo $RUTA_R ?>css/estilo_iphone.css">
            <link   type="text/css" rel="stylesheet" href="<?php echo $RUTA_R ?>css/estilo_lista.css">
            <link   type="text/css" rel="stylesheet" href="<?php echo $RUTA_R ?>css/table_style.css">
            <head>
            <title><?php echo $APP_TITULO ?></title>  
            <style type='text/css'>
            <!--
            body {
                background-color: #FFFFFF;
            }
            -->
            </style>
            </head>
            
            <body>
            <div id="error" style="color:#FF0000"><?php if(isset($_GET['error'])){
            echo "Error de usuario y/o contrase&ntilde;a";
            }?></div>
            <center>
            <table width="320">
                <tr valign='top'><td><img src='<?php echo $RUTA_R ?>images/bkheader_small.png'></td><td><a href="<?php echo $RUTA_R; ?>/inicial.php">Inicio</a><td><td><a href="<?php echo $RUTA_R; ?>/index.php">Salir</a><td></tr>	  
            </table>
            <table width="320">
            
                                                      
 		<?php
 	}
 	
 	public function Footer(){
        /*
 		?>
                <tr>
                    <td colspan="2" height="50"><div align="center"><font color="#336699" size="4" face="Verdana, Arial, Helvetica, sans-serif">Versi&oacute;n 2.0 </font></div></td>
                </tr>                
                </table>
			</body>
		</html>
 		<?php
        */
 	}
 }
?>
