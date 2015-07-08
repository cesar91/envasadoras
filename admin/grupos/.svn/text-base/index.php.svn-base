<?php
session_start();
require_once("../../lib/php/constantes.php");
require_once "$RUTA_A/Connections/fwk_db.php";
require_once "$RUTA_A/functions/utils.php";

if(isset($_POST["Agregar"]) && isset($_POST['nombre']) && $_POST['nombre']!=""){

	$grupo= new Grupo();
	
	$nombre=$_POST['nombre'];
	
	$grupo->add_Grupo($nombre);	
	header('Location: index.php');
}

else if(isset($_GET["id"]) && $_GET['id']!=""){

	include('edita_grupo.php');

}
else{
	$query=" SELECT 
g_nombre
, CONCAT( COALESCE((SELECT u_nombre from usuario where u_id = usuario1), ''), COALESCE(CONCAT(' / ', (SELECT u_nombre from usuario where u_id = usuario1a)), '')), usuario1_limite
, CONCAT( COALESCE((SELECT u_nombre from usuario where u_id = usuario2), ''), COALESCE(CONCAT(' / ', (SELECT u_nombre from usuario where u_id = usuario2a)), '')), usuario2_limite
, CONCAT( COALESCE((SELECT u_nombre from usuario where u_id = usuario3), ''), COALESCE(CONCAT(' / ', (SELECT u_nombre from usuario where u_id = usuario3a)), '')), usuario3_limite
, CONCAT( COALESCE((SELECT u_nombre from usuario where u_id = usuario4), ''), COALESCE(CONCAT(' / ', (SELECT u_nombre from usuario where u_id = usuario1a)), '')), usuario4_limite
, CONCAT( COALESCE((SELECT u_nombre from usuario where u_id = usuario5), ''), COALESCE(CONCAT(' / ', (SELECT u_nombre from usuario where u_id = usuario1a)), '')), usuario5_limite
, (SELECT u_nombre from usuario where u_id = usuario_excepcion),  usuario_excepcion_limite
, (SELECT u_nombre from usuario where u_id = usuario1_excepcion),  usuario1_excepcion_limite
, (SELECT u_nombre from usuario where u_id = usuario_cxp)
 FROM grupos ORDER BY g_nombre;";
	 $I	= new Interfaz("Grupos:: Lista de Grupos",true);
		$L	= new Lista();
		//$L->Cabeceras("ID");
		$L->Cabeceras("Nombre");
		$L->Cabeceras("Usuario 1");
		$L->Cabeceras("L&iacute;mite");
		$L->Cabeceras("Usuario 2");
		$L->Cabeceras("L&iacute;mite");
		$L->Cabeceras("Usuario 3");
		$L->Cabeceras("L&iacute;mite");
		$L->Cabeceras("Usuario 4");
		$L->Cabeceras("L&iacute;mite");
		$L->Cabeceras("Usuario 5");
		$L->Cabeceras("L&iacute;mite");                
		$L->Cabeceras("Usuario de Excepci&oacute;n 1");
		$L->Cabeceras("L&iacute;mite");
		$L->Cabeceras("Usuario de Excepci&oacute;n 2");
		$L->Cabeceras("L&iacute;mite");
		$L->Cabeceras("Usuario CxP");
//		$L->Herramientas("E","./index.php?id=");
		if(isset($_GET["oksave"])){
			echo "<h3>El grupo de Aprobadores se guard&oacute; correctamente</h3>";
		}
        echo("<table><tr><td><h1>Aprobadores</h1></td></tr></table>");        
		$L->muestra_lista($query,0);

?>
<br />

<!--
<center><h2>Nuevo Grupo</h2></center>
<form action="" method="post">
	<table align="center">
		<tr>
			<td>
				Nombre de grupo:
			</td>
			<td>
				<input type="text" id="nombre" name="nombre" />				
			</td>
		</tr>
		<tr>
			
			<td align="right" colspan="2">
				<input type="submit" id="Agregar" name="Agregar" value="Agregar"/>
				<input type="reset" id="Cancelar" name="Cancelar" value="Cancelar"/>
			</td>
		</tr>


	</table>
</form>
-->
	
<?php
$I->Footer();
}
?>
