<?php
session_start();
require_once("../../lib/php/constantes.php");
require_once "$RUTA_A/Connections/fwk_db.php";
require_once "$RUTA_A/functions/utils.php";

if(isset($_GET["id"])){
	$I	= new Interfaz("Empresas",true,"onload=\"inicial({$_GET['id']});\"");
	?>
		<center><h2></h2></center>
		<div id="empresas">Si no puedes ver el contenido por favor descarga el plug-in de flash </div>
	<?php
	$I->Footer();
	
}
else{

	$query="  select R.r_id,TP.te_nombre as tipo,E.e_nombre as empresa,R.r_nombre as rol  from empresas E inner join tipo_empresas TP on (TP.te_id=E.e_tipo) inner join roles R on (R.r_empresa=E.e_id) where E.e_activo=true and R.r_activo=true order by empresa,rol";
	 $I	= new Interfaz("Parametros por Rol");
	 	?>
	 	<?php
		$L	= new Lista();
		$L->Cabeceras("ID");
		$L->Cabeceras("Empresa");
		$L->Cabeceras("Proceso");
		$L->Cabeceras("Rol");
		$L->Herramientas("E","./index.php?id=");
		$L->lista_agrupada($query,"tipo");
	$I->Footer();
	
}	

?>

<script>

	function inicial(id){
		swfobject.embedSWF("./Catalogos_Rol.swf?id=" + id, "empresas", "1000", "900", "8.0.0");
	}
	
</script>