<?php
session_start();
require_once("../../lib/php/constantes.php");
$id=$_GET["id"];
$C= new Comprobaciones();
$aux1=$C->Reporte1($id);
$aux2=$C->Reporte2($id);

		$aux="";
		$aux.="<html>";
		$aux.="<body onload=\"window.print();\">";
		$aux.="<center><h3>Detalle de Comprobaci&oacute;n</h3><center>";
		$aux.="<table  align='center' cellspacing='4' cellpadiing='4' width='80%'>";
		$aux.="<tr><td style=\"font-size: 9px;\" align='right'><strong>Folio Comprobaci&oacute;n:</strong></td><td style=\"font-size: 9px;\">{$aux1['folio']}</td>";
		$aux.="<td style=\"font-size: 9px;\" align='right'><strong>Solicita:</strong></td><td style=\"font-size: 9px;\">{$aux1['nombre']}</td></tr>";
		$aux.="<tr><td style=\"font-size: 9px;\" align='right'><strong>Proceso:</strong></td><td style=\"font-size: 9px;\">{$aux1['flujo']}</td>";
		$aux.="<td style=\"font-size: 9px;\" align='right'><strong>Anticipo Otorgado:</strong></td><td style=\"font-size: 9px;\">{$aux1['anticipo']}</td></tr>";
		
		
		$aux.="</table>";
		$aux.="<br>";
		
		$aux.="<table width='100%' align='center' cellspacing='2' cellpadding='2'>";
		$aux.="<tr>";
		$aux.="<td style=\"font-size: 7px;\" align='left'><strong>Clasificaci&oacute;n</strong></td>";
		$aux.="<td style=\"font-size: 7px;\" align='left'><strong>Concepto</strong></td>";
		$aux.="<td style=\"font-size: 7px;\" align='left'><strong>R.F.C.</strong></td>";
		$aux.="<td style=\"font-size: 7px;\" align='right'><strong>Monto</strong></td>";
		$aux.="<td style=\"font-size: 7px;\" align='right'><strong>% Impuesto</strong></td>";
		$aux.="<td style=\"font-size: 7px;\" align='right'><strong>Impuesto</strong></td>";
		$aux.="<td style=\"font-size: 7px;\" align='right'><strong>Total</strong></td>";
		$aux.="<td style=\"font-size: 7px;\" align='right'><strong>T.Aprobado</strong></td>";
		$aux.="</tr>";
		
		$total=0;
		$aprobado=0;
		foreach ($aux2 as $fila){
			
	
			$aux.="<tr>";
			
			$aux.="<td style=\"font-size: 7px;\" align='left'>{$fila['clasificacion']}</td>";
			$aux.="<td style=\"font-size: 7px;\" align='left'>{$fila['concepto']}</td>";
			$aux.="<td style=\"font-size: 7px;\" align='left'>{$fila['rfc']}</td>";
			$aux.="<td style=\"font-size: 7px;\" align='right'>" . number_format($fila['monto'],2,".",",") ."</td>";
			$aux.="<td style=\"font-size: 7px;\" align='right'>{$fila['porcentaje']}</td>";
			$aux.="<td style=\"font-size: 7px;\" align='right'>" .number_format($fila['iva'],2,".",",") ."</td>";
			$aux.="<td style=\"font-size: 7px;\" align='right'>" .number_format($fila['total'],2,".",",") ."</td>";
			$aux.="<td style=\"font-size: 7px;\" align='right'>" .number_format($fila['aprobado'],2,".",",") ."</td>";
			
			$total	 += Limpiar_numero($fila['total']);
			$aprobado+= Limpiar_numero($fila['aprobado']);
			$aux.="</tr>";
			
			
			
		}
		$total		=number_format($total	,2,".",",");
		$aprobado	=number_format($aprobado,2,".",",");
		$diferencia	=number_format(Limpiar_numero($aprobado) - Limpiar_numero($total),2,".",",");
		
		$aux.="<tr>";
			$aux.="<td colspan='8'>&nbsp;</td>";
		$aux.="</tr>";
		
		$aux.="<tr>";
			$aux.="<td colspan='6'>&nbsp;</td>";
			$aux.="<td style=\"font-size: 7px;\" align='right' ><strong>Total Solicitado:</strong></td>";
			$aux.="<td style=\"font-size: 7px;\" align='right' >$total</td>";
		$aux.="</tr>";
		$aux.="<tr>";
			$aux.="<td colspan='6'>&nbsp;</td>";
			$aux.="<td style=\"font-size: 7px;\" align='right' ><strong>Total Aprobado:</strong></td>";
			$aux.="<td style=\"font-size: 7px;\" align='right' >$aprobado</td>";
		$aux.="</tr>";
		$aux.="<tr>";
			$aux.="<td colspan='6'>&nbsp;</td>";
			$aux.="<td style=\"font-size: 7px;\" align='right' ><strong>Diferencia:</strong></td>";
			$aux.="<td style=\"font-size: 7px;\" align='right' >$diferencia</td>";
		$aux.="</tr>";
			
		$aux.="</table>";
		$aux.="</body>";
		$aux.="</html>";
		echo $aux;
		
		
		function Limpiar_numero($numero){
			return(str_replace(",","",$numero));
		}
?>
