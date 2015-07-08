<?PHP 
require_once("../../lib/php/constantes.php");
require_once "$RUTA_A/Connections/fwk_db.php";
require_once "$RUTA_A/functions/utils.php";
?>
<script language="javascript">
	//validación campos numericos
	function validaNum(valor){
		cTecla=(document.all)?valor.keyCode:valor.which;
		if(cTecla==8) return true;
		patron=/^([0-9.]{1,2})?$/;
		cTecla= String.fromCharCode(cTecla);
		return patron.test(cTecla);
	}
</script>
<?PHP 

function comprobacion_toolbar($noEmpleado){
?>
<div style="border:1px #ececec solid;margin:auto;margin-top:5px;text-align:left;">
<table cellpadding="2" cellspacing="5">
	<tr>
	<?PHP if($_SESSION["perfil"] != 5 && $_SESSION["perfil"] != 6 && $_SESSION["perfil"] != 10){?>
		<th colspan="2" align="center">
			Nueva Comprobaci&oacute;n
		</th>
		<th>&nbsp;
	<?PHP }?>	
		</th>
		<th colspan="2" align="center">
			Mis Comprobaciones
		</th>
		<th>&nbsp;
		</th>
	</tr>
	<tr align="center">
	<?PHP if($_SESSION["perfil"] != 5 && $_SESSION["perfil"] != 6 && $_SESSION["perfil"] != 10){?>
		<td>
			<a href="./index.php?new=new"><img src="../../images/toolbar/page_white_trav_add.png" alt="Nueva Comprobaci&oacute;n de Viaje" title="Nueva Comprobaci&oacute;n de Viaje" border="0" /></a>
		</td>
		<td>
		    <a href="./index.php?comp_solicitud=comp_solicitud"><img src="../../images/toolbar/page_white_inv_add.png" alt="Comprobaci&oacute;n de Solicitud de Invitaci&oacute;n" title="Comprobaci&oacute;n de Solicitud de Gastos" border="0" /></a>
		</td>
<!--			<a href="./index.php?new=new"><img src="../../images/toolbar/page_white_trav_add.png" alt="Nueva Comprobaci&oacute;n de Viaje" title="Nueva Comprobaci&oacute;n de Viaje" border="0" /></a>		</td>
		<td>
			<a href="./index.php?new2=new2"><img src="../../images/toolbar/page_add.png" alt="Nueva Comprobaci&oacute;n Amex" title="Nueva Comprobaci&oacute;n Amex" border="0" /></a>		</td>
		<td>
			<a href="./index.php?new3=new3"><img src="../../images/toolbar/page_safe_add.png" alt="Nueva Comprobaci&oacute;n de Caja Chica" title="Nueva Comprobaci&oacute;n de Caja Chica" border="0" /></a>		</td>
-->     <td>
			<img src="../../images/toolbar/separador.png" border="0"/>
		</td>
	<?PHP }?>
		<td>
			<a href="./index.php?docs=docs&type=3"><img src="../../images/toolbar/documents_t.png" alt="Mis Comprobaciones de Viaje" title="Mis Comprobaciones de Viaje" border="0" /></a>
		</td>
		<td>
			<a href="./index.php?docs=docs&type=4"><img src="../../images/toolbar/documents_cc.png" alt="Mis Comprobaciones de Gastos" title="Mis Comprobaciones de Gastos" border="0" /></a>
		</td>
<!--		<td>
			<a href="./index.php?docs=docs&type=3"><img src="../../images/toolbar/documents_safe.png" alt="Mis Comprobaciones de Caja Chica" title="Mis Comprobaciones de Caja Chica" border="0" /></a>		</td>-->
	</tr>
	<tr align="center" style="color:#8080C0">
	<?PHP if($_SESSION["perfil"] != 5 && $_SESSION["perfil"] != 6 && $_SESSION["perfil"] != 10){?>
		<td>
			Viaje
		</td>
		<td>
			Gastos
		</td>
		<td>&nbsp;</td>
		<?PHP }?>
		<td>
			Viaje
		</td>
		<td>
			Gastos
		</td>
	</tr>
</table>
</div>
<?PHP 
}

function busca_comprobacion($flujo){
    $FLUJO_COMPROBACION_GASTOS=1;
    $FLUJO_REEMBOLSO_CAJA_CHICA=2;
    // adiciona parametro para desplegar el flujo correcto
    $type = "";
    switch($flujo){
        case $FLUJO_COMPROBACION_GASTOS;
            $type = "&docs=docs&type=4";
            break;
        case $FLUJO_REEMBOLSO_CAJA_CHICA:
            $type = "&docs=docs&type=3";
            break;
    }
    
?>
<script language="JavaScript" src="../../lib/js/jquery/jquery-1.3.2.js" type="text/javascript"></script>
<script language="JavaScript" src="../../lib/js/jquery/jquery.date_input.js" type="text/javascript"></script>
<script language="JavaScript" type="text/javascript">//Fecha
$(function() {
	//campo 
	$("#finicial").date_input();
	$("#ffinal").date_input();
	});
		
	//obtiene formato de fecha YYYY/mm/dd
	$.extend(DateInput.DEFAULT_OPTS, {
	stringToDate: function(string) {
	var matches;
	if (matches = string.match(/^(\d{4,4})-(\d{2,2})-(\d{2,2})$/)) {
	return new Date(matches[1], matches[2] - 1, matches[3]);
	} else {
	return null;
	};
	},
	dateToString: function(date) {
	var month = (date.getMonth() + 1).toString();
	var dom = date.getDate().toString();
	if (month.length == 1) month = "0" + month;
	if (dom.length == 1) dom = "0" + dom;
	return dom + "/" + month + "/" + date.getFullYear();
	}
	});
		
	//Opciones de Idioma del Calendario
	jQuery.extend(DateInput.DEFAULT_OPTS, {
	month_names: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
	short_month_names: ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"],
	short_day_names: ["Dom", "Lun", "Mar", "Mié", "Jue", "Vie", "Sab"]
	});
//fin Fecha	
</script>
<link rel="stylesheet" type="text/css" href="../../css/date_input.css"/>
<div style="border:1px #ececec solid;margin:auto;margin-top:5px;text-align:left;">
<form action="<?PHP echo $_SERVER ['PHP_SELF']; ?>?buscar<?PHP echo $type; ?>" method="post">
<div align="center"><h1>B&uacute;squeda de Mis Comprobaciones</h1></div>
  <div align="center">No. De Folio:
    <input name="noComp" id="noComp" size="12" onkeypress="return validaNum (event)"/>
    Fecha Inicial:
  <input name="finicial" id="finicial" size="10"  readonly="readonly"/>
    Fecha Final:
  <input name="ffinal" id="ffinal" size="10" readonly="readonly"/>
    Etapa:
    <select id="et_etapa_id" name="et_etapa_id">
	  <?PHP 
		$cnn    = new conexion();
		$query=sprintf("SELECT et_etapa_id, et_etapa_nombre FROM etapas WHERE et_flujo_id = '%s' ORDER BY et_etapa_id", $flujo);
		$rst    = $cnn->consultar($query);	
        echo "<option id='-1' value='-1'>Todas</option>";		
		while($fila=mysql_fetch_assoc($rst)){
			echo "<option id=".$fila["et_etapa_id"]." value=".$fila["et_etapa_id"];
			if(isset($_POST['et_etapa_id']) && $_POST['et_etapa_id']==$fila["et_etapa_id"])
				echo " selected = 'selected' ";
			echo ">".$fila["et_etapa_nombre"]."</option>";
		}
      ?>	
    </select>
  <input type="submit" name="buscar" id="buscar" value="    Buscar"  style="background:url(../../images/fwk/system-search.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;" />
  <input name="busca" id="busca" type="hidden"/>
  <input name="type" id="type" type="hidden" value='<?PHP echo $flujo; ?>'/>
  </div>
</form></div>
<?PHP 
}
?>

<?PHP 

/**************Solicitudes*************/
function solicitud_toolbar($noEmpleado){
?>

<div style="border:1px #ececec solid;margin:auto;margin-top:5px;text-align:left;">
<table cellpadding="2" cellspacing="5">
	<tr>
        <?PHP if($_SESSION["perfil"] != 5 && $_SESSION["perfil"] != 6 && $_SESSION["perfil"] != 10){?>
		<th colspan="2" align="center">
			Nueva Solicitud
		</th>
		<th>&nbsp;
   		<?PHP }?>
			
		</th>
		<th colspan="2" align="center">
			Mis Solicitudes		
		</th>
		<th>&nbsp;
		
		</th>		
	</tr>
	<tr align="center">
        <?PHP if($_SESSION["perfil"] != 5 && $_SESSION["perfil"] != 6 && $_SESSION["perfil"] != 10){?>
		<td>
			<a href="./index.php?new=new"><img src="../../images/toolbar/page_white_trav_add.png" alt="Nueva Solicitud de Viajes" title="Nueva Solicitud de Viaje" border="0" /></a>
		</td>
		<td>
			<a href="./index.php?new2=new2"><img src="../../images/toolbar/page_white_inv_add.png" alt="Nueva Solicitud de Gastos" title="Nueva Solicitud de Gastos" border="0" /></a>
		</td>
		<td>	
			<img src="../../images/toolbar/separador.png" border="0"/>
		</td>
   		<?PHP }?>
		<td>
			<a href="./index.php?docs=docs&type=1"><img src="../../images/toolbar/documents_t.png" alt="Mis Solicitudes de Viajes" title="Mis Solicitudes de Viajes" border="0" /></a>
		</td>
		<td>
			<a href="./index.php?docs=docs&type=2"><img src="../../images/toolbar/documents_cc.png" alt="Mis Solicitudes de Gastos" title="Mis Solicitudes de Gastos" border="0" /></a>
		</td>		
	</tr>
	<tr align="center"  style="color:#8080C0">
        <?PHP if($_SESSION["perfil"] != 5 && $_SESSION["perfil"] != 6 && $_SESSION["perfil"] != 10){?>
		<td>
			Viaje
		</td>		
		<td>
			Gastos
		</td>
		<td>&nbsp;
   		<?PHP }?>
			
		</td>
		<td>
			Viaje
		</td>
		<td>
			Gastos
		</td>
		<td>&nbsp;
			
		</td>		
	</tr>
</table>
</div>
<?PHP 
}
/********Busca solicitud historial*******/
function busca_solicitud_historial($flujo){ ?>
	<div style="border:1px #ececec solid;margin:auto;margin-top:5px;text-align:left;">
		<form action="<?PHP echo $_SERVER ['PHP_SELF']; ?>" method="post">
			<div align="center">
				No. De Folio: <input name="noComp" id="noComp" size="12" onkeypress="return validaNum (event)" placeholder="0004"/>
				Fecha Inicial: <input name="finicial" id="finicial" size="10" readonly="readonly" placeholder="15/02/2014"/>
				Fecha Final: <input name="ffinal" id="ffinal" size="10" readonly="readonly" placeholder="15/02/2014"/>
				Solicitante: <input name="solicitante" id="solicitante" size="12" placeholder="FERNANDO ESQUIVEL" />
				Etapa: <select id="et_etapa_id" name="et_etapa_id">
				
				<?PHP 
					$cnn = new conexion();
					$query = sprintf("SELECT et_flujo_id, et_etapa_id, et_etapa_nombre FROM etapas ORDER BY et_flujo_id,et_etapa_id");
					$rst = $cnn->consultar($query);
					
					echo "<option id='-1' value='-1'>Todas</option>";
					while($fila = mysql_fetch_assoc($rst)){
						if($fila["et_flujo_id"] == 1){
							$param = "SV";
						}else if($fila["et_flujo_id"] == 2){
							$param = "SG";
						}else if($fila["et_flujo_id"] == 3){
							$param = "CV";
						}else if($fila["et_flujo_id"] == 4){
							$param = "CG";
						}
						echo "<option id='".$fila["et_etapa_id"]."' value='".$fila["et_flujo_id"]."|".$fila["et_etapa_id"]."'";
						if(isset($_POST['et_etapa_id']) && $_POST['et_etapa_id']==$fila["et_etapa_id"])
							echo " selected ";
						echo "> Flujo ".$param."-".$fila["et_etapa_nombre"]."</option>";
					}
				?>
				</select>
				<input type="submit" name="buscar" value="     Buscar" style="background:url(../../images/fwk/system-search.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;" />
			</div>
			<input name="busca" id="busca" type="hidden"/>
			<input name="type" id="type" type="hidden" value='<?PHP echo $flujo; ?>'/>
		</form>
	</div>
<?PHP 
}

/********Busca solicitud********/
function busca_solicitud($flujo){
?>
<script language="JavaScript" src="../../lib/js/jquery/jquery-1.3.2.js" type="text/javascript"></script>
<script language="JavaScript" src="../../lib/js/jquery/jquery.date_input.js" type="text/javascript"></script>
<script language="JavaScript" type="text/javascript">//Fecha
$(function() {
	//campo 
	$("#finicial").date_input();
	$("#ffinal").date_input();
	});
		
	//obtiene formato de fecha YYYY/mm/dd
	$.extend(DateInput.DEFAULT_OPTS, {
	stringToDate: function(string) {
	var matches;
	if (matches = string.match(/^(\d{4,4})-(\d{2,2})-(\d{2,2})$/)) {
	return new Date(matches[1], matches[2] - 1, matches[3]);
	} else {
	return null;
	};
	},
	dateToString: function(date) {
	var month = (date.getMonth() + 1).toString();
	var dom = date.getDate().toString();
	if (month.length == 1) month = "0" + month;
	if (dom.length == 1) dom = "0" + dom;
	return dom + "/" + month + "/" + date.getFullYear();
	}
	});
		
	//Opciones de Idioma del Calendario
	jQuery.extend(DateInput.DEFAULT_OPTS, {
	month_names: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
	short_month_names: ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"],
	short_day_names: ["Dom", "Lun", "Mar", "Mié", "Jue", "Vie", "Sab"]
	});
//fin Fecha	
</script>
<script language="JavaScript" type="text/javascript">

</script>
<link rel="stylesheet" type="text/css" href="../../css/date_input.css"/>
<div style="border:1px #ececec solid;margin:auto;margin-top:5px;text-align:left;">
<form action="<?PHP echo $_SERVER ['PHP_SELF']; ?>" method="post">
  <div align="center"><h1><?PHP if($flujo == 2){?>B&uacute;squeda de Mis Solicitudes de Gastos Creadas<?PHP }else{?>B&uacute;squeda de Mis Solicitudes de Viaje Creadas<?PHP }?></h1></div>
  <div align="center">No. De Folio:
  <input name="noComp" id="noComp" size="12" onkeypress="return validaNum (event)"/>
    Fecha Inicial:
  <input name="finicial" id="finicial" size="10" readonly="readonly"/>
    Fecha Final:
  <input name="ffinal" id="ffinal" size="10" readonly="readonly"/>
    Etapa:
    <select id="et_etapa_id" name="et_etapa_id">
	  <?PHP 
		$cnn    = new conexion();
		$query=sprintf("SELECT et_etapa_id, et_etapa_nombre FROM etapas WHERE et_flujo_id = '%s' ORDER BY et_etapa_id", $flujo);
		error_log($query);
		$rst    = $cnn->consultar($query);	
        echo "<option id='-1' value='-1'>Todas</option>";	
		while($fila=mysql_fetch_assoc($rst)){
			echo "<option id=".$fila["et_etapa_id"]." value=".$fila["et_etapa_id"];
			if(isset($_POST['et_etapa_id']) && $_POST['et_etapa_id']==$fila["et_etapa_id"])
				echo " selected ";
			echo ">".$fila["et_etapa_nombre"]."</option>";
		}
      ?>	
    </select>
  <input type="submit" name="buscar" value="    Buscar"  style="background:url(../../images/fwk/system-search.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;" />
  </div>
  <input name="busca" id="busca" type="hidden"/>
  <input name="type" id="type" type="hidden" value='<?PHP echo $flujo; ?>'/>
</form></div>
<?PHP 
}
/**************Usuarios*************/

function usuarios_toolbar(){
?>
<table cellpadding="1" cellspacing="5">
<tr>
	<td>
		<h1>Usuarios</h1>
	</td>
</tr>
</table>
<table style="border:1px #ececec solid;margin:auto;margin-top:5px;text-align:left;" width="100%">
<tr>
	<td>
		<table>
			<tr>
				<td align="center" style="color:#8080C0">
					<a href="./index.php?mode=NUEVO"><img src="../../images/toolbar/page_user_add.png" alt="Nuevo Usuario" title="Nuevo Usuario" border="0" /><br />Nuevo usuario +&nbsp;&nbsp;</a>
				</td>
				<td>
					<img src="../../images/toolbar/separador.png"border="0" />&nbsp;&nbsp;
				</td>

				<td valign="bottom">
					<form action="<?PHP echo $_SERVER ['PHP_SELF']; ?>?mode=BUSCAR" method="post">
						<div align="left">Empleado:
							<input name="criterio" id="criterio" size="12"  />
							<input type="submit" name="buscar" value="    Buscar"  style="background:url(../../images/fwk/system-search.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;" />
						</div>
					</form>
				</td>
			</tr>
		</table>
	</td>
</tr>
</table>
<?PHP 
	}

/**************Empresas*************/

function empresas_toolbar(){
?>
<table cellpadding="1" cellspacing="5">
<tr>
	<td>
		<h1>Empresas</h1>
	</td>
</tr>
</table>
<table style="border:1px #ececec solid;margin:auto;margin-top:5px;text-align:left;" width="100%">
<tr>
	<td>
		<table>
			<tr>
				<td align="center" style="color:#8080C0">
					<a href="./index.php?mode=NUEVO"><img src="../../images/toolbar/page_user_add.png" alt="Nueva Empresa" title="Nueva Empresa" border="0" /><br />Nueva Empresa +&nbsp;&nbsp;</a>
				</td>				
				<td><form action="./index.php?mode=INDEX">
					Buscar:<input type="text" id="name_empresa" name="name_empresa" size="30" value=""/> 
						       <input type="submit" name="buscar" value="    Buscar"  style="background:url(../../images/fwk/system-search.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;" />
					</form>
				</td>            
			</tr>
		</table>
	</td>
</tr>
</table>
<?PHP 
	}

/**************Cecos*************/


function cecos_toolbar(){
?>
<table cellpadding="1" cellspacing="5">
<tr>
	<td>
		<h1>Centro de Costos</h1>
	</td>
</tr>
</table>
<table style="border:1px #ececec solid;margin:auto;margin-top:5px;text-align:left;" width="100%">
<tr>
	<td>
		<table>
			<tr>
				<td align="center" style="color:#8080C0">
					<a href="./index.php?mode=NUEVO"><img src="../../images/toolbar/page_user_add.png" alt="Nuevo Departamento" title="Nuevo Departamento" border="0" /><br />Nuevo Centro de Costos +&nbsp;&nbsp;</a>
				</td>
				<td>
					<img src="../../images/toolbar/separador.png"border="0" />&nbsp;&nbsp;
				</td>
                
				<td valign="bottom">
					<form action="<?PHP echo $_SERVER ['PHP_SELF']; ?>?mode=BUSCAR" method="post">
						<div align="left">Centro de Costos:
							<input name="criterio" id="criterio" size="12"  />
							<input type="submit" name="buscar" value="    Buscar"  style="background:url(../../images/fwk/system-search.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;" />
						</div>
					</form>
				</td>                  
                
			</tr>
		</table>
	</td>
</tr>
</table>

<?PHP 
	}


/**************Conceptos*************/


function conceptos_toolbar(){
?>
<table cellpadding="1" cellspacing="5">
<tr>
	<td>
		<h1>Conceptos</h1>
	</td>
</tr>
</table>
<table style="border:1px #ececec solid;margin:auto;margin-top:5px;text-align:left;" width="100%">
<tr>
	<td>
		<table>
			<tr>
				<td align="center" style="color:#8080C0">
					<a href="./index.php?mode=NUEVO"><img src="../../images/toolbar/page_user_add.png" alt="Nuevo Concepto" title="Nuevo Concepto" border="0" /><br />Nuevo Concepto +&nbsp;&nbsp;</a>
				</td>
						<td><form action="./index.php?mode=INDEX">
						Buscar:<input type="text" id="name_concepto" name="name_concepto" size="30" value=""/> 
						       <input type="submit" name="buscar" value="    Buscar"  style="background:url(../../images/fwk/system-search.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;" />
					</form>
				</td>
				<td>
					<img src="../../images/toolbar/separador.png"border="0" />&nbsp;&nbsp;
				</td>
			</tr>
		</table>
	</td>
</tr>
</table>
<?PHP 
	}
	
/**************Presupuesto*************/
/*Se agrega con el objetivo de que el cliente pueda filtrar el presupuesto
 * mostrado por el centro de costos, en esta seccción se asigna el "mode" de
 * la pantalla index.php y el criterio de búsqueda
 */

function presupuesto_toolbar(){
?>
<script language="JavaScript" src="../../lib/js/jquery/jquery-1.3.2.js" type="text/javascript"></script>
<script language="JavaScript" src="../../lib/js/jquery/jquery.date_input.js" type="text/javascript"></script>
<script language="JavaScript" type="text/javascript">//Fecha
$(function() {
	//campo 
	$("#finicial").date_input();
	$("#ffinal").date_input();
});
	$( "#ffinal" ).live( "change", function() {
	var startDate = $('#finicial').val().replace('-','/');
	var endDate = $('#ffinal').val().replace('-','/');
		if(startDate > endDate){
			$( "#finicial" ).val("");
			$( "#ffinal" ).val("");
			alert("La fecha inicial no puede ser mayor a la fecha final");
			return false;
		}
	});
	$( "#buscar" ).live( "click", function() {
	var startDate = $('#finicial').val().replace('-','/');
	var endDate = $('#ffinal').val().replace('-','/');
		if(startDate > endDate){
			$( "#finicial" ).val("");
			$( "#ffinal" ).val("");
			alert("La fecha inicial no puede ser mayor a la fecha final");
			return false;
		}
	});	
	//obtiene formato de fecha YYYY/mm/dd
	$.extend(DateInput.DEFAULT_OPTS, {
	stringToDate: function(string) {
	var matches;
	if (matches = string.match(/^(\d{4,4})-(\d{2,2})-(\d{2,2})$/)) {
	return new Date(matches[1], matches[2] - 1, matches[3]);
	} else {
	return null;
	};
	},
	dateToString: function(date) {
	var month = (date.getMonth() + 1).toString();
	var dom = date.getDate().toString();
	if (month.length == 1) month = "0" + month;
	if (dom.length == 1) dom = "0" + dom;
	return dom + "/" + month + "/" + date.getFullYear();
	}
	});
		
	//Opciones de Idioma del Calendario
	jQuery.extend(DateInput.DEFAULT_OPTS, {
	month_names: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
	short_month_names: ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"],
	short_day_names: ["Dom", "Lun", "Mar", "Mié", "Jue", "Vie", "Sab"]
	});
//fin Fecha	
</script>
<link rel="stylesheet" type="text/css" href="../../css/date_input.css"/>
<table cellpadding="1" cellspacing="5">
<tr>
	<td>
		<br />  		  		
		<h1>Presupuesto</h1>
	</td>
</tr>
</table>
<table style="border:1px #ececec solid;margin:auto;margin-top:5px;text-align:left;" width="100%">
<tr>
	<td>
		<table>
			<tr>
				<td valign="bottom">
					<form action="<?PHP echo $_SERVER ['PHP_SELF']; ?>?mode=BUSCAR" method="post">
						<div align="left">Centro de Costos:
							<input name="criterio" id="criterio" size="12"  />
							Fecha Inicial: <input name="finicial" id="finicial" size="12"  />
							Fecha Final: <input name="ffinal" id="ffinal" size="12"  />
							<input type="submit" id = "buscar" name="buscar" value="    Buscar"  style="background:url(../../images/fwk/system-search.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;" />
						</div>
					</form>
				</td>                  
                
			</tr>
		</table>
	</td>
</tr>
</table>

<?PHP 
	}	
	
/**************Presupuesto*************/
/*Se agrega con el objetivo de que el cliente pueda cargar el presupuesto
 * anualmente o cada vez que requiera actualizarlo, asigna el mode
 * la pantalla index.php
 */

function cargaPresupuesto_toolbar($resultadoCarga){
?>

<table width="413" border="0" cellspacing="0" cellpadding="0">
  <tr>
	<td>
		<h1>Carga Presupuestal</h1>
	</td>
  </tr>
  <tr>
    <td>Por favor seleccione el archivo a subir:</td>
  </tr>
  <tr>
    <form action="<?PHP echo $_SERVER ['PHP_SELF']; ?>?mode=CARGAR" method="post" enctype="multipart/form-data">  	
    	<td class="text">
      		<input name="archivo" type="file" class="casilla" id="archivo" size="90" />
      		<input name="enviar" type="submit" class="boton" id="enviar" value="     Cargar" style="background:url(../../images/ok.png); background-position:left; background-repeat:no-repeat"/>
	  	</td>
	</form>
  </tr>
  		<tr>    
     		<td class="infsub">
    			<?PHP echo $resultadoCarga; ?>
     		</td>    
  		</tr>   
</table>
<?PHP 
}	

/**************Modelos Autos*************/


function modelos_autos_toolbar(){
?>
<table cellpadding="1" cellspacing="5">
<tr>
	<td>
		<h1>Modelos de Autos de BMW</h1>
	</td>
</tr>
</table>
<table style="border:1px #ececec solid;margin:auto;margin-top:5px;text-align:left;" width="100%">
<tr>
	<td>
		<table>
			<tr>
				<td align="center" style="color:#8080C0">
					<a href="./index.php?mode=NUEVO"><img src="../../images/admin/carrito.png" alt="Nuevo Modelo de Auto" title="Nuevo Modelo de Auto" border="0" /><br />Nuevo Modelo de Auto +&nbsp;&nbsp;</a>
				</td>
				<td>
					<img src="../../images/toolbar/separador.png"border="0" />&nbsp;&nbsp;
				</td>
                
				<td valign="bottom">
					<form action="<?PHP echo $_SERVER ['PHP_SELF']; ?>?mode=BUSCAR" method="post">
						<div align="left">Modelo de Auto:
							<input name="criterio" id="criterio" size="12"  />
							<input type="submit" name="buscar" value="    Buscar"  style="background:url(../../images/fwk/system-search.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;" />
						</div>
					</form>
				</td>                  
                
			</tr>
		</table>
	</td>
</tr>
</table>

<?PHP 
	}

/**************Estado Cuenta*************/
	function estado_cuenta($noEmpleado){
	?>		
		<script language="JavaScript" src="../../lib/js/jquery/jquery-1.3.2.js" type="text/javascript"></script>
		<script language="JavaScript" src="../../lib/js/jquery/jquery.date_input.js" type="text/javascript"></script>
		<script>
			$(function() {			
				$("#finicial").date_input();
				$("#ffinal").date_input();
			});
					
			//obtiene formato de fecha YYYY/mm/dd
			$.extend(DateInput.DEFAULT_OPTS,{
				stringToDate: function(string){
					var matches;
					if (matches = string.match(/^(\d{4,4})-(\d{2,2})-(\d{2,2})$/)){
						return new Date(matches[1], matches[2] - 1, matches[3]);
					}else{
						return null;
					};
				},
				dateToString: function(date){
					var month = (date.getMonth() + 1).toString();
					var dom = date.getDate().toString();
					if (month.length == 1) month = "0" + month;
					if (dom.length == 1) dom = "0" + dom;
					return dom + "/" + month + "/" + date.getFullYear();
				}
			});
					
			//Opciones de Idioma del Calendario
			jQuery.extend(DateInput.DEFAULT_OPTS, {
				month_names: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
				short_month_names: ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"],
				short_day_names: ["Dom", "Lun", "Mar", "Mié", "Jue", "Vie", "Sab"]
			});		
		</script>
		<link rel="stylesheet" type="text/css" href="../../css/date_input.css"/>
		<table  style="border:1px #ececec solid;margin:auto;margin-top:5px;text-align:left;" width="100%">
			<?PHP if($_SESSION["perfil"] == 6){ ?>
			<th colspan="2">Tipo de tarjeta</th>	
			<tr>
				<td style="color:#8080C0" width="50">
					<a href="./index.php?type=1"><img src="../../images/toolbar/cargo_amex.png" width="32" alt="Gastos" title="Gastos" border="0" /></a><br />
					Gastos
				</td>
				<td style="color:#8080C0">
					<a href="./index.php?type=2"><img src="../../images/toolbar/cargo_amex_gas.png" width="32" alt="Gasolina" title="Gasolina" border="0" /></a><br />
					Gasolina
				</td>				
				<?PHP } ?>				
			</tr>
		</table>
		<h1 align="center">B&uacute;queda de cargos Amex</h1>		
		<form name='busca' method='post' action="index.php?<?PHP if (isset($_GET["type"])) echo "type=".$_GET["type"];?>">
		<table align="center">
			<tr>
				<td>Fecha inicial:</td>
				<td><input name="finicial" id="finicial" size="10" readonly="readonly"/></td>
				<td>Fecha final:</td>
				<td><input name="ffinal" id="ffinal" size="10" readonly="readonly"/></td>				
				<td>Tarjeta:</td>
				<td><input name="tarjeta" onkeypress="validaNum(this.value);"></td>				
				<td><input type="submit" name="buscar" value="buscar"></td>
			</tr>		
		</table>					
	<?PHP } ?>