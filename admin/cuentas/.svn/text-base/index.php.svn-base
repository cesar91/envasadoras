<?php
session_start();
require_once("../../lib/php/constantes.php");
require_once "$RUTA_A/Connections/fwk_db.php";
require_once "$RUTA_A/functions/utils.php";

if(isset($_GET["id"])){

$I	= new Interfaz("Empresas:: Catalogo de Cuentas",true,"onload=\"inicial({$_GET['id']});\"");
	?>
		<center><h2>Cat&aacute;logos de Cuentas por Proceso</h2></center>
		
		
		  <div id="empresas">Si no puedes ver el contenido por favor descarga el plug-in de flash </div>
		
	<?php
$I->Footer();
}
else{
	$query=" select (select p_id from perfil where p_empresa=e_id) as e_id,e_nombre,(select te_nombre from tipo_empresas where te_id=e_tipo) as tipo from empresas where e_activo=true order by e_nombre;";
	 $I	= new Interfaz("Empresas:: Catalogo de Cuentas");
	 	?>
	 	<?php
		$L	= new Lista();
		$L->Cabeceras("ID");
		$L->Cabeceras("Proceso");
		$L->Cabeceras("Empresa Pertenece");
		$L->Herramientas("E","./index.php?id=");
		$L->muestra_lista($query,0);
		
	$I->Footer();
	
	
}
?>
<script>

	function inicial(id){
		swfobject.embedSWF("./Catalogos.swf?id=" + id, "empresas", "900", "720", "8.0.0");
	}
</script>