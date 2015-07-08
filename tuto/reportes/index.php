<?php
session_start();
require_once("../lib/php/constantes.php");
require_once "$RUTA_A/Connections/fwk_db.php";
require_once "$RUTA_A/functions/utils.php";
$I	= new Interfaz("Reportes",true);

	$query="select * from usuario where u_id=" . $_SESSION["idusuario"];
	$cnn	= new conexion();
	$rst=$cnn->consultar($query);
	$fila=mysql_fetch_assoc($rst);
	?>
        <h3>Reportes</h3>
		<p>
<!--
			<a href="tramites.php">Integral de Trámites</a><br>        
			<a href="saldos.php">Saldos por Comprobar</a><br>
			<a href="estado_de_cuenta.php">Estado de Cuenta</a><br>
			<a href="gastos_conceptos.php">Reporte de Gastos</a><br>
			<a href="#">Reporte de Descuentos</a><br>
			<br>
			-->
			<ul>
				<li type="disc">
					<a href="reporte.php?reporte=Reporte de Gastos">Reporte de Gastos</a><br>
				</li>
				<li type="disc">
					<a href="reporte.php?reporte=Reporte de Anticipos no comprobados">Reporte de Anticipos no comprobados</a><br>
				</li>
				<li type="disc">
					<a href="reporte.php?reporte=Reporte de Viajes en Avion">Reporte de Viajes en Avi&oacute;n</a><br>
				</li>
				<li type="disc">
					<a href="reporte.php?reporte=Reporte de Anticipos generados y registrados en SAP">Reporte de Anticipos generados y registrados en SAP</a><br>
				</li>
				<li type="disc">
					<a href="reporte.php?reporte=Reporte de Gastos AMEX no comprobados">Reporte de Gastos AMEX no comprobados</a><br>
				</li>
				<li type="disc">
					<a href="reporte.php?reporte=Reporte de Descuentos">Reporte de Descuentos</a><br>
				</li>
				<li type="disc">
					<a href="reporte.php?reporte=Reporte de Solicitudes fuera de politica">Reporte de Solicitudes fuera de política</a><br>
				</li>
			</ul>
		</p>
	
	<?php
$I->Footer();
?>
