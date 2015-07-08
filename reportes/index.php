<?php
	session_start();
	require_once("../lib/php/constantes.php");
	require_once "$RUTA_A/Connections/fwk_db.php";
	require_once "$RUTA_A/functions/utils.php";
	$I	= new Interfaz("Reportes",true);
?>
	<h3>Reportes</h3>
	<ul>
		<li><a href="reporte.php?reporte=Reporte de Solicitudes">Reporte de Solicitudes</a></li>
		<li><a href="reporte.php?reporte=ReporteComprobaciones">Reporte de Comprobaciones</a></li>
		<li><a href="reporte.php?reporte=Reporte de Anticipos no comprobados">Reporte de Anticipos no comprobados</a></li>
		<li><a href="reporte.php?reporte=Reporte de Anticipos generados y registrados en SAP">Reporte de Anticipos generados y registrados en SAP</a></li>
		<?php if($_SESSION['perfil'] == 9) { ?>
		<li><a href="../fiscal/index.php">Reporte CFDI</a></li>
		<?php } ?>
		<!--<li><a href="reporte.php?reporte=Reporte de Gastos">Reporte de Gastos</a></li>-->
		<!--<li><a href="reporte.php?reporte=Reporte de Viajes en Avion">Reporte de Viajes en Avi&oacute;n</a></li>-->
		<!--<li><a href="reporte.php?reporte=Reporte de Gastos AMEX no comprobados">Reporte de Gastos AMEX no comprobados</a></li>-->
		<!--<li><a href="reporte.php?reporte=Reporte de Descuentos">Reporte de Descuentos</a></li>-->
		<!--<li><a href="reporte.php?reporte=Reporte de Comprobaciones de Gasolina fuera de politica">Reporte de Comprobaciones de Gasolina fuera de política</a></li>-->
	</ul>
<?php
	$I->Footer();
?>