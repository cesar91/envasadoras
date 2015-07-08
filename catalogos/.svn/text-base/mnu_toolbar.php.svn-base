<?PHP 
require_once("../../lib/php/constantes.php");
require_once "$RUTA_A/Connections/fwk_db.php";
require_once "$RUTA_A/functions/utils.php";
?>
<script language="javascript">
// Validación campos numericos
function validaNum(valor){
	cTecla=(document.all)?valor.keyCode:valor.which;
	if(cTecla==8) return true;
		patron=/^([0-9.]{1,2})?$/;
		cTecla= String.fromCharCode(cTecla);
		return patron.test(cTecla);
	}
</script>
<?php function empresas_toolbar(){ ?>
	<table cellpadding="1" cellspacing="5" width="100%">
		<tr>
			<td align="center"><h1>Cat&aacute;logo de Empresas</h1></td>
		</tr>
	</table>
	<table style="border:1px #ececec solid;margin:auto;" width="30%">
		<tr>
			<td align="center" valign="middle">
				<form action="./index.php?mode=INDEX">
					<div align="center">
						Buscar:<input type="text" id="name_empresa" name="name_empresa" size="30" value=""/>
						<input type="submit" name="buscar" value="    Buscar"  style="background:url(../../images/fwk/system-search.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;" />
					</div>
				</form>
			</td>
		</tr>
	</table>
	<br />
<?PHP 
	}
function cecos_toolbar(){ ?>
	<table cellpadding="1" cellspacing="5" width="100%">
		<tr>
			<td align="center"><h1>Cat&aacute;logo de Centro de Costos</h1></td>
		</tr>
	</table>
	<table style="border:1px #ececec solid;margin:auto;margin-top:5px;" width="30%">
		<tr>
			<td align="center" valign="middle">
				<form action="<?PHP echo $_SERVER ['PHP_SELF']; ?>?mode=BUSCAR" method="post">
					<div align="center">
						Centro de Costos:	<input name="criterio" id="criterio" size="12" />
						<input type="submit" name="buscar" value="    Buscar"  style="background:url(../../images/fwk/system-search.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;" />
					</div>
				</form>
			</td>
		</tr>
	</table>
	<br />
<?php } 

function conceptos_toolbar(){ ?>
	<table cellpadding="1" cellspacing="5" width="100%">
		<tr>
			<td align="center"><h1>Cat&aacute;logo de Conceptos</h1></td>
		</tr>
	</table>
	<table style="border:1px #ececec solid;margin:auto;margin-top:5px;" width="30%">
		<tr>
			<td align="center" valign="middle">
				<form action="./index.php?mode=INDEX">
					<div align="center">
						Buscar:<input type="text" id="name_concepto" name="name_concepto" size="30" value=""/>
						<input type="submit" name="buscar" value="    Buscar"  style="background:url(../../images/fwk/system-search.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;" />
					</div>
				</form>
			</td>
		</tr>
	</table>
	<br />
<?PHP 
	}

function presupuesto_toolbar(){ ?>
<script language="JavaScript" src="../../lib/js/jquery/jquery-1.3.2.js" type="text/javascript"></script>
<script language="JavaScript" src="../../lib/js/jquery/jquery.date_input.js" type="text/javascript"></script>
<script language="JavaScript" type="text/javascript">
	$(function() {
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
	
	//obtiene formato de fecha YYYY/mm/dd
	$.extend(DateInput.DEFAULT_OPTS, {
		stringToDate: function(string){
			var matches;
			
			if (matches = string.match(/^(\d{4,4})-(\d{2,2})-(\d{2,2})$/)) {
				return new Date(matches[1], matches[2] - 1, matches[3]);
			}else{
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
</script>
<link rel="stylesheet" type="text/css" href="../../css/date_input.css"/>
	<table cellpadding="1" cellspacing="5" width="100%">
		<tr>
			<td align="center"><h1>Cat&aacute;logo de Presupuestos</h1></td>
		</tr>
	</table>
	<table style="border:1px #ececec solid;margin:auto;margin-top:5px;" width="55%">
		<tr>
			<td align="center" valign="middle">
				<form action="<?PHP echo $_SERVER ['PHP_SELF']; ?>?mode=BUSCAR" method="post">
					<div align="center">
						Centro de Costos:<input name="criterio" id="criterio" size="12" />
						Fecha Inicial: <input name="finicial" id="finicial" size="12" />
						Fecha Final: <input name="ffinal" id="ffinal" size="12" />
						<input type="submit" name="buscar" value="    Buscar"  style="background:url(../../images/fwk/system-search.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;" />
					</div>
				</form>
			</td>
		</tr>
	</table>
	<br />
<?PHP 
	}

function modelos_autos_toolbar(){ ?>
	<table cellpadding="1" cellspacing="5" width="100%">
		<tr>
			<td align="center"><h1>Cat&aacute;logo de Modelo de Autos</h1></td>
		</tr>
	</table>
	<table style="border:1px #ececec solid;margin:auto;margin-top:5px;" width="30%">
		<tr>
			<td align="center" valign="middle">
				<form action="<?PHP echo $_SERVER ['PHP_SELF']; ?>?mode=BUSCAR" method="post">
					<div align="center">
						Modelo de Auto:
						<input name="criterio" id="criterio" size="12" />
						<input type="submit" name="buscar" value="    Buscar"  style="background:url(../../images/fwk/system-search.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;" />
					</div>
				</form>
			</td>
		</tr>
	</table>
	<br />
<?PHP 
	}

function usuarios_toolbar(){ ?>
	<table cellpadding="1" cellspacing="5" width="100%">
		<tr>
			<td align="center"><h1>Cat&aacute;logo de Usuarios</h1></td>
		</tr>
	</table>
	<table style="border:1px #ececec solid;margin:auto;margin-top:5px;" width="30%">
		<tr>
			<td align="center" valign="middle">
				<form action="<?PHP echo $_SERVER ['PHP_SELF']; ?>?mode=BUSCAR" method="post">
				<div align="center">
					Empleado:
					<input name="criterio" id="criterio" size="12" />
					<input type="submit" name="buscar" value="    Buscar"  style="background:url(../../images/fwk/system-search.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;" />
				</div>
			</td>
		</tr>
	</table>
	<br />
<?PHP  } ?>