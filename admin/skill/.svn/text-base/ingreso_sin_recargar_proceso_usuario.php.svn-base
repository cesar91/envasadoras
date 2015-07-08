<?php
session_start();
require_once("../../lib/php/constantes.php");
require_once "../../Connections/fwk_db.php";
require_once "$RUTA_A/functions/utils.php";
require_once("$RUTA_A/lib/php/configuracion.mailgun.php");

header("Cache-Control: no-store, no-cache, must-revalidate");

//Envia informaci�n del itinerario
if(isset($_POST['email']) && $_POST['email']!=""){
			$cnn	= new conexion();
			$empleado =$_SESSION["idusuario"];			
			$email = $_POST['email'];
			$telefono=$_POST['telefono'];
			$jefe    =$_POST['jefe'];
		
			$pas="";
			
			if(isset($_POST['passwd']) && $_POST['passwd']!="")
				$pas=", u_passwd='".$_POST["passwd"]."'";
						
			$query="update usuario set u_email='".$email."' ".$pas." where u_id='".$empleado."'";
			$rst=$cnn->insertar($query);			

			//$query="update empleado set telefono='".$telefono."', jefe='".$jefe."' where numempleado='".$empleado."'";
			//$query="update empleado set telefono='".$telefono."' where idempleado='".$_SESSION["idusuario"]."'";
			$query="update empleado set telefono='".$telefono."' where idfwk_usuario='".$_SESSION["idusuario"]."'";
			$rst=$cnn->insertar($query);		
			
}

else if(isset($_POST["idJefe"])){
			$cnn	= new conexion();
			$jefe=$_POST["idJefe"];
			
			$query="select * from usuario where u_usuario='".$jefe."'";
			$rst=$cnn->consultar($query);
			
			if($cnn->get_rows()>0)
				echo "yes";
				
			else
				echo "no";
}


if(isset($_GET["q"]) && isset($_GET["aux"])){
	
		//obtiene lista de usuarios
		function Get_list_users($dato){
		    $cnn    = new conexion();
			$query = "SELECT nombre FROM empleado where nombre like '%$dato%' and idnivel<=8 order by nombre";
		    error_log($query);
			$rst    = $cnn->consultar($query);
	        while($fila=mysql_fetch_assoc($rst)){
			echo utf8_encode($fila["nombre"]."\n");
	        }
	    }
	
		//obtiene lista de id de usuarios
		function Get_list_id_users($dato){
			$cnn    = new conexion();
		    //$query = "select numempleado from empleado where numempleado like '%$dato%' and idnivel<=8 order by numempleado";
		    $query = "select idempleado from empleado where idempleado like '%$dato%' and idnivel<=8 order by idempleado";
		    $rst    = $cnn->consultar($query);
	        while($fila=mysql_fetch_assoc($rst)){          
				//echo $fila["numempleado"]."\n";
				echo $fila["idempleado"]."\n";
	        }
    	}			
	
		if($_GET["aux"]==1){
			$q=$_GET["q"];
			Get_list_users($q);
		}
		else if($_GET["aux"]==2){
			$q=$_GET["q"];
			Get_list_id_users($q);
		}
}

elseif (isset($_POST["nombre"]) && isset($_POST["aux"])){
		//obtiene el numempleado del usuari seleccionado
		function Get_nombre_users($dato){
		    $cnn    = new conexion();
		    //$query = "select numempleado from empleado where nombre like '%$dato%'" ;
		    $query = "select idempleado from empleado where nombre like '%$dato%'" ;
		    $rst    = $cnn->consultar($query);
			$fila=mysql_fetch_assoc($rst);          
			//echo $fila["numempleado"];        
			echo $fila["idempleado"];        
	    }

		//obtiene el nombre del usuario seleccionado
		function Get_id_users($dato){
		    $cnn    = new conexion();
		    //$query = "select nombre from empleado where numempleado like '%$dato%'" ;
		    $query = "select nombre from empleado where idempleado like '%$dato%'" ;
		    $rst    = $cnn->consultar($query);
	    	$fila=mysql_fetch_assoc($rst);          
			echo $fila["nombre"];        
	    }

		if($_POST["aux"]==1){
			$q=$_POST["nombre"];
			Get_nombre_users($q);
		}
		else if($_POST["aux"]==2){
			$q=$_POST["nombre"];
			Get_id_users($q);
		}

}
//manda mail para notificar delegaci�n
elseif(isset($_POST["id_user"]) && $_POST["id_user"]!=""){
		
		$cnn	= new conexion();
		$id_user=$_POST["id_user"];
		$user_name=$_SESSION["usuario"];
		$coment=$_POST['coment'];
		
		$U=new Usuario();
		$N=new Notificacion();
		//$M		= new Mail();
		$body="";
		
		//if($U->Load_empleado($id_user)>0){	
		if($U->Load_Usuario_By_ID($id_user)>0){	
			
			//$query="update empleado set delegado='".$id_user."' where numempleado='".$_SESSION["idusuario"]."'";
			$query="update empleado set delegado='".$id_user."' where idempleado='".$_SESSION["idusuario"]."'";
			$rst=$cnn->insertar($query);
			
			$cad="Te a sido asignada la delagaci&oacute;n del usuario <strong>".$user_name."</strong> y esta en espera de su aprobaci&oacute;n";
			
			$N->Add($cad,0,$id_user,$coment,1);
			
			/*$U->Load_usuario($id_user);
			
			$body="<p>
				<strong>Estimado {$U->Get_dato("u_nombre")} {$U->Get_dato("u_paterno")} {$U->Get_dato("u_materno")}</strong><br>
				Le informamos que el usuario: <strong>{$user_name}</strong> lo ha seleccionado para delegar mientras se encuentra en ausencia, y esta en espera de su aprobaci&oacute;n. Le solicitamos responda lo antes posible.
				<br><br><br>
				<strong>Observaciones:</strong>{$coment}
				<br><br>
					Para Ingresar al sistema presione:<a href='http://201.159.131.127/eexpenses_danone'><strong>aqu&iacute;</strong></a>.
				</p>";
				
			$N->set_contenido($body);
			$N->set_destinatario($U->Get_dato("u_email"));
			$N->notificaUsu();*/
			//echo "";
		}
		else
			echo "No";
		
}

// manda mail con password
elseif(isset($_POST["recoveryMail"]) && $_POST["recoveryMail"]!=""){
	$U=new Usuario();
	$user=$_POST["recoveryMail"];
	$N=new Notificacion();
	
	$body="";
	$usuarios = array();
	$usuarios = $U->Load_Usuarios_By_Email($user);
	$num_usuarios = sizeof($usuarios);
	if($num_usuarios>0){

		if($num_usuarios == 1 || $num_usuarios == "1"){
			$body="<font style='font-size:11.0pt; font-family: BMWTypeLight, BMWType V2 Bold, Arial, Helvetica, sans-serif'><p>
			<strong>Estimado Asociado,</strong><br>
			La informaci&oacute;n de su cuenta para acceder al Sistema de Gastos de Viaje es:<br><br>";
		}else{
			$body="<font style='font-size:11.0pt; font-family: BMWTypeLight, BMWType V2 Bold, Arial, Helvetica, sans-serif'><p>
			<strong>Estimado Asociado,</strong><br>
			La informaci&oacute;n de sus cuentas para acceder al Sistema de Gastos de Viaje son:<br><br>";
		}
		$aux = 0;
		foreach($usuarios as $usuario){
			$aux++;
			$body .= "
			Usuario: {$usuario['u_usuario']}<br>
			Contrase&ntilde;a: {$usuario['u_passwd']}<br>";
		}
		
		$body .= "</p></font><font style='font-size:11.0pt; font-family: BMWTypeLight, BMWType V2 Bold, Arial, Helvetica, sans-serif'><p>Atentamente,<br>
Sistema de Gastos de Viaje<br>
Bonafont HOD</font><br>
<font style='font-size:11.0pt; font-family: BMWTypeLight, BMWType V2 Bold, Arial, Helvetica, sans-serif'><strong>Este correo es informativo, favor de no responder a esta direcci&oacute;n, ya que no se encuentra habilitada para recibir mensajes</strong></font> 
<br><br>";
		
		$N->set_cabecera("Recuperaci&oacute;n de contrase&oacute;a.");
		$N->set_contenido($body);
		$N->set_destinatario($user);
					
		
		parametroMail(utf8_decode("Recuperación de password"),$user,$body);
		
		echo "<font color='#00b000'>Se enviar&aacute; un mail con su usuario y password, puede tardar algunos minutos, favor de verificarlo.</font><br><br>";
	}
		
	else
		echo "El correo electr&oacute;nico que ingreso es incorrecto; intente de nuevo.<br><br>";

}

//manda mail si se designo usuario
elseif(isset($_POST["id_userUnsigned"]) && $_POST["id_userUnsigned"]!=""){
		
		$cnn	= new conexion();
		$coment="";
		$id_user=$_POST["id_userUnsigned"];			
		$user_name=$_SESSION["usuario"];
		$coment=$_POST['coment'];
		
		$U=new Usuario();
		$N=new Notificacion();
		$M		= new Mail();
		$body="";
		//if($U->Load_empleado($id_user)>0 || $id_user==0){	
		if($U->Load_Usuario_By_ID($id_user)>0 || $id_user==0){
			
			//$query="update empleado set delegado=0 where numempleado='".$_SESSION["idusuario"]."'";
			$query="update empleado set delegado=0 where idempleado='".$_SESSION["idusuario"]."'";
			$rst=$cnn->insertar($query);
			
			$cad="Se te a removido la delagaci&oacute;n del usuario <strong>".$user_name."</strong>";
			
			$N->Add(utf8_encode($cad),0,$id_user,$coment,0);
			
			//$U->Load_usuario($id_user);
			$U->Load_Usuario_By_ID($id_user);
			
			$body="<p>
				<strong>Estimado {$U->Get_dato("u_nombre")} {$U->Get_dato("u_paterno")} {$U->Get_dato("u_materno")}</strong><br>
				Le informamos que el usuario: <strong>{$user_name}</strong> le ha removido la delegaci&oacute;n que le solicito.
				<br>
				Gracias
				<br><br><br>
				<strong>Observaciones:</strong>{$coment}
				<br><br>
					Para Ingresar al sistema presione:<a href='http://201.159.131.127/eexpenses_danone'><strong>aqu&iacute;</strong></a>.
				</p>";
				
			$N->set_contenido($body);
			$N->set_destinatario($U->Get_dato("u_email"));

			//$query=sprintf("update notificaciones set nt_activo=0 where nt_creador ='%s' and nt_asignado_a='%s' and nt_activo =1",$_SESSION["idusuario"],$id_user);
			$query=sprintf("update notificaciones set nt_activo=0 where nt_remitente ='%s' and nt_asignado_a='%s' and nt_activo =1",$_SESSION["idusuario"],$id_user);
			$rst = $cnn	-> insertar($query);			

			//$N->notificaUsu();			

		}
		else
			echo "No";
		
}
// No retorno ninguna respuesta
?>