<?php
session_start();
require_once("../../lib/php/constantes.php");
require_once "$RUTA_A/Connections/fwk_db.php";
require_once "$RUTA_A/functions/utils.php";


if(isset($_GET["elimina"])){
	
	$I	= new Interfaz("Administraci&oacute;n :: Empresas",true,"onload=\"elimina({$_GET['id']});\"");
	?>
		
		<div id="empresas">Si no puedes ver el contenido por favor descarga el plug-in de flash </div>
		
	<?php
$I->Footer();
}

if(isset($_GET["action"])){
	$I	= new Interfaz("Administraci&oacute;n :: Empresas",true,"onload=\"inicial();\"");
	?>
		
		<div id="empresas">Si no puedes ver el contenido por favor descarga el plug-in de flash </div>
		
	<?php
$I->Footer();
	
}
else if(isset($_GET["id"])){

$I	= new Interfaz("Administraci&oacute;n :: Empresas",true,"onload=\"inicial2({$_GET['id']});\"");
	?>
		
		<div id="empresas">Si no puedes ver el contenido por favor descarga el plug-in de flash </div>
		
		
	<?php
$I->Footer();
}
else{
	$query=" select te_id,te_nombre,B.b_nombre,te_cuenta from tipo_empresas TE inner join bancos B on (TE.te_banco=B.b_id) where TE.te_activo=true order by te_nombre;";
	 $I	= new Interfaz("Administraci&oacute;n :: Empresas");
	 
		$L	= new Lista();
		$L->Cabeceras("ID");
		$L->Cabeceras("Nombre");
		$L->Cabeceras("Banco");
		$L->Cabeceras("Cuenta");
		$L->Herramientas("E","./index.php?id=");
		$L->Herramientas("D","./index.php?elimina=delete&id=");
		
		?>
			<a href="./index.php?action=new">Nueva Empresa</a>
		<?php
		$L->muestra_lista($query,0);
		
	$I->Footer();
	
	
}
?>
<script>

	function inicial(){
		swfobject.embedSWF("./Tipo_Empresas.swf", "empresas", "1000", "1000", "8.0.0");
	}
	function inicial2(id){
		swfobject.embedSWF("./Tipo_Empresas.swf?id=" + id, "empresas", "1000", "1000", "8.0.0");
	}
	function elimina(id){
		swfobject.embedSWF("./Delete_Tipo.swf?id=" + id, "empresas", "900", "1000", "8.0.0");
	}
	
</script>