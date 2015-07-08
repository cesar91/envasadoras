<?php
	require_once("../../lib/php/constantes.php");
	require_once "$RUTA_A/Connections/fwk_db.php";
	require_once "$RUTA_A/functions/utils.php";
			
		if(isset($_POST["guardar"])){
			guardar();
		} else if(isset($_POST["volver"])){
			header("Location: ./index.php");        
		} else {
			Muestra();
		}	
			
		function guardar(){
			$Usu= new Usuario();
			$iduser=0;
			if (isset($_POST['numdelegaciones']) || $_POST['numdelegaciones'] != 0) 
			{
				if(isset($_POST['yatienedelegaciones'])){
					$delegadoid = $_POST['delegadosel'];
					$Usu->del_delegacion($delegadoid);
				}
				
				$cnn = new conexion();
				for ($i = 1; $i <= $_POST['numdelegaciones']; $i++) {
					$delegadoid = $_POST['delegadosel'];
					echo $delegadorid = $_POST['idu'.$i];
					echo $autorizador = $_POST['autorizar'.$i];
					$perfil = $_POST['tipo' . $i];
					$Usu->add_delegacion($delegadoid, $delegadorid, $autorizador,$perfil);
				}			
				header("Location: index.php");
			} else {
				header("Location: index.php?error");
			}
		}
				
		function Muestra(){			
			$Usu= new Usuario();
			if(isset($_GET['usuario_id'])){
				$usuario_id=$_GET['usuario_id'];    
				$Usu->Load_Usuario_By_ID($usuario_id);
			} else {
				header("Location: index.php");
			}
			
			$I  = new Interfaz("Usuarios:: Asignar Privilegios a Usuario",true);
			?>
	<style type="text/css">
	<!--
	.Estilo1 {
		color: #FF0000
	}
	-->
	</style>
	<script language="JavaScript" src="../../lib/js/jquery/jquery-1.3.2.js"	type="text/javascript"></script>
	<script language="JavaScript" src="../../lib/js/validateForm.js" type="text/javascript"></script>
	<script language="javascript" type="text/javascript">

	doc = $(document);
	doc.ready(inicializarEventos);//cuando el documento esté listo
	function inicializarEventos(){
		var frm=document.form1;
		var idusuario=$("#delegadosel").val();
		$.ajax({
			type: "POST",
			url: "services/ajax_usuario.php",
			data: "uidsel="+idusuario,
			success: function(json){
				if(json=="TRUE"){
					fillform(idusuario);
				}	
			}
		});
		
		cargarinfousuario($("#usuario_delegado option:selected").val());
		$("#empleadoid").val($("#usuario_delegado option:selected").val());
	}

	function fillform(valor){
		$.ajax({
			type: "POST",
			url: "services/ajax_usuario.php",
			data: "uidselfill="+valor,
			dataType: "json",
			success: function(json){
				for(var i=0;i<json.delegado.length;i++){
				var nuevaFila='<tr>';
				var frm=document.form1;
				var delegados=$("#delegados").val();
				var tipos_delegados=$("#delegadostipos").val();
				var id=parseInt($("#delegado_table>tbody>tr").length);
				
				if(isNaN(id)){
					id=1;
				}else{
					id+=parseInt(1);
				}
				if(json.privilegio[i]=="1"){
					aux_val=1;
					aux_dat="checked";
							}else{
								aux_val=0;
								aux_dat="";
								}
				nuevaFila+="<td><input type='hidden' name='idu"+id+"' id='idu"+id+"' value='"+json.info[i].uid+"' readonly='readonly' /><input type='hidden' name='empleado"+id+"' id='empleado"+id+"' value='"+json.info[i].usuario+"' readonly='readonly' />"+json.info[i].usuario+"</td>";
				nuevaFila+="<td><input type='hidden' name='nombre"+id+"' id='nombre"+id+"' value='"+json.info[i].nombre+"' readonly='readonly' />"+json.info[i].nombre+"</td>";
				nuevaFila+="<td><input type='hidden' name='tipo"+id+"' id='tipo"+id+"' value='"+json.info[i].tipo_id+"' readonly='readonly' />"+json.info[i].tipo+"</td>";
				nuevaFila+="<td ><input type='hidden' name='cecos"+id+"' id='cecos"+id+"' value='"+json.info[i].cc+"' readonly='readonly' />"+json.info[i].cc+"</td>";
				if(json.info[i].tipo== "Normal" || json.info[i].tipo== "Finanzas" || json.info[i].tipo== "Controlling"){
					nuevaFila+="<td align='center'><input type='hidden' name='autorizar"+id+"' id='autorizar"+id+"' value='"+aux_val+"' readonly='readonly' />"+"<input type='checkbox' name='"+id+"autorizar_data' id='"+id+"autorizar_data' "+aux_dat+" onchange='cambiarautorizacion(this.id);'/>"+"</td>";
				}else{
					nuevaFila+="<td align='center'><input type='hidden' name='autorizar"+id+"' id='autorizar"+id+"' value='"+aux_val+"' readonly='readonly' />"+"<input type='hidden' name='"+id+"autorizar_data' id='"+id+"autorizar_data' "+aux_dat+" onchange='cambiarautorizacion(this.id);'/>"+"</td>";
				}
				nuevaFila+="<td><div align='center'><img class='elimina' src='../../images/delete.gif' alt='Click aqu&iacute; para Eliminar' name='"+id+"del' id='"+id+"del' onmousedown='borrarDelegacion(this.id);' style='cursor:pointer;' /></div></td>";
				nuevaFila+= '</tr>';

				$("#delegado_table").append(nuevaFila);
				$("#numdelegaciones").val(id);
				delegados+=json.info[i].uid+'|';
				tipos_delegados+=json.info[i].tipo_id+'|';
				$("#delegados").val(delegados);
				$("#delegadostipos").val(tipos_delegados);				
				}
			}
		});
		$("#yatienedelegaciones").val(1);
		}
			
	function validaNum(valor){
		cTecla=(document.all)?valor.keyCode:valor.which;
		if(cTecla==8) return true;
		patron=/^([0-9.]{1,2})?$/;
		cTecla= String.fromCharCode(cTecla);
		return patron.test(cTecla);
	}

	function validate(){
		if(!validateForm("empleadouser","","Debe seleccionar un usuario para delegar.",1)){
			 return false;
		}else{
	return true;
			}
	}


	function bloqueaEspacio(valor){
		cTecla=(document.all)?valor.keyCode:valor.which;
		if(cTecla==32)
			return false;
		else
			return true;
	}

	function validaNormal(){
		if( $("#tipoUser").val() != 1 && $("#tipoUser").val() != 5 && $("#tipoUser").val() != 6){
			$("#estatus").hide();
			$("#labelpriv").html('');		
		}else{
			$("#estatus").show();
			$("#labelpriv").html('&nbsp;&nbsp;Privilegios para Autorizar');
		}	
	}

	function cargarinfousuario(valor){
		var frm=document.form1;
		if(valor!=""){
			$.ajax({
				type: "POST",
				url: "services/ajax_usuario.php",
				data: "uid="+valor,
				dataType: "json",
				success: function(json){
					$("#tipoUser").val(json.tipo);
					$("#empleadoid").val(valor);
					$("#empleadouser").val(json.usuario);
					$("#empleadonombre").val(json.nombre);
					$("#empleadotipo").val(json.tipo);
					$("#empleadocecos").val(json.cc);
					$.ajax({
						type: "POST",
						url: "services/ajax_usuario.php",
						data: "uid_tipo_usuario="+valor,
						dataType: "json",
						success: function(json){
							LimpiarCombo(frm.tipoUser);
							frm.tipoUser.options[0] = new Option('Seleccione...', '');
							for(var i=0;i<json.length;i++){
								var str=json[i];							
								frm.tipoUser.options[frm.tipoUser.length] = new Option(str.tu_nombre,str.ut_tipo);
							}
							
						}
					});
				}
			});
		}
	}

	function LimpiarCombo(combo){
		while(combo.length > 0){
			combo.remove(combo.length-1);
		}
	}

	function cambiarautorizacion(id){
		i=parseInt(id);

		if($("#"+i+"autorizar_data").is(':checked')){
			$("#autorizar"+i).val(1);
		}else{
			$("#autorizar"+i).val(0);
		}
	}

	function verificardelegados(usuario,tipo){
		var delegados=$("#delegados").val();
		var empleado = $("#empleadoid").val();
		var filas = $("#delegado_table>tbody>tr").length;
		var tipos_delegados=$("#delegadostipos").val();	
		if(delegados != "" && filas != 0){
			var alerta=0;
			var delegaciones=delegados.split('|');
			var tipos=tipos_delegados.split('|');
			for(var i=0;i<=parseInt(delegaciones.length); i++){
				if(delegaciones[i]==usuario && tipos[i]==tipo){
					alerta=1;
				}
			}
		}else{
			alerta=0;
		}

		if(alerta==1){
			alert("El usuario ingresado ya está considerado para delegar con este tipo de usuario.");
			return false;
		}else{	
			return true;
		}	
	}

	function agregardelegacion(){	
		if($("#tipoUser").val()==''){
			alert("Seleccione un tipo de usuario");
			$("#tipoUser").focus();
			return false;
		}

		if(verificardelegados($("#empleadoid").val(),$("#tipoUser option:selected").val())){
			
		var nuevaFila='<tr>';
		var frm=document.form1;
		var delegados=$("#delegados").val();
		var tipos_delegados=$("#delegadostipos").val();
		var nombre_empleado=$("#usuario_delegado option:selected").text();
		var id=parseInt($("#delegado_table>tbody>tr").length);
		
		if(isNaN(id)){
			id=1;
		}else{
			id+=parseInt(1);
		}

		if($("#estatus").is(':checked')){
			aux_val=1;
			aux_dat="checked";
		}else{
			aux_val=0;
			aux_dat="";
		}
			
		nuevaFila+="<td><input type='hidden' name='idu"+id+"' id='idu"+id+"' value='"+$("#empleadoid").val()+"' readonly='readonly' /><input type='hidden' name='empleado"+id+"' id='empleado"+id+"' value='"+$("#empleadouser").val()+"' readonly='readonly' />"+$("#empleadouser").val()+"</td>";
		nuevaFila+="<td><input type='hidden' name='nombre"+id+"' id='nombre"+id+"' value='"+$("#usuario_delegado").val()+"' readonly='readonly' />"+nombre_empleado+"</td>";
		nuevaFila+="<td><input type='hidden' name='tipo"+id+"' id='tipo"+id+"' value='"+$("#tipoUser option:selected").val()+"' readonly='readonly' />"+$("#tipoUser option:selected").html()+"</td>";
		nuevaFila+="<td ><input type='hidden' name='cecos"+id+"' id='cecos"+id+"' value='"+$("#empleadocecos").val()+"' readonly='readonly' />"+$("#empleadocecos").val()+"</td>";
		if( $("#tipoUser").val() == 1 || $("#tipoUser").val() == 5 || $("#tipoUser").val() == 6  ){
			nuevaFila+="<td align='center' ><input type='hidden' name='autorizar"+id+"' id='autorizar"+id+"' value='"+aux_val+"' readonly='readonly' />"+"<input type='checkbox' name='"+id+"autorizar_data' id='"+id+"autorizar_data' "+aux_dat+" onchange='cambiarautorizacion(this.id);'/>"+"</td>";
		}else{
				nuevaFila+="<td align='center'><input type='hidden' name='autorizar"+id+"' id='autorizar"+id+"' value='"+aux_val+"' readonly='readonly' />"+"<input type='hidden' name='"+id+"autorizar_data' id='"+id+"autorizar_data' "+aux_dat+" onchange='cambiarautorizacion(this.id);'/>"+"</td>";
		}	
		nuevaFila+="<td><div align='center'><img class='elimina' src='../../images/delete.gif' alt='Click aqu&iacute; para Eliminar' name='"+id+"del' id='"+id+"del' onmousedown='borrarDelegacion(this.id);' style='cursor:pointer;' /></div></td>";
		nuevaFila+= '</tr>';

		$("#delegado_table").append(nuevaFila);
		$("#numdelegaciones").val(id);
		delegados+=$("#empleadoid").val()+'|';
		tipos_delegados+=$("#tipoUser option:selected").val()+'|';
		$("#delegados").val(delegados);
		$("#delegadostipos").val(tipos_delegados);
		$("#tipoUser").val("");	
		$("#empleadonombre").val("");
		$("#empleadotipo").val("");	
		$("#estatus").hide();
		$("#labelpriv").html('');
		}
	}

	function confirmBorrarDelegacion(){
		if(confirm("¿Está seguro que desea Eliminar el registro?")){
			return true;
		}else{
			return false;
		}
	}

	function borrarDelegacion(id){
		$("#numdelegaciones").bind("restar",function(e,data,data1){
			e.stopImmediatePropagation();
			$("#numdelegaciones").val(parseInt($("#delegado_table>tbody>tr").length));
		});

		$("#confirmacion_elimina").bind("confirma",function(e,data,data1){
			if(confirm("¿Está seguro que desea Eliminar el registro?")){
				e.stopImmediatePropagation();
				$("#confirmacion_elimina").val('1');			
			}else{
				e.stopImmediatePropagation();
				$("#confirmacion_elimina").val('0');
			}
		});
		
		$("#rowDel").bind("cambiar",function(e,inicio,tope){		
			
			e.stopImmediatePropagation();
			var nextidu="";
			var idu="";
			var jqueryidu="";
			var empleado="";
			var nextempleado="";
			var jqueryempleado = "";

			var nombre="";
			var nextnombre="";
			var jquerynombre = "";

			var tipo="";
			var nexttipo="";
			var jquerytipo = "";

			var cecos="";
			var nextcecos="";
			var jquerycecos = "";

			var autorizar="";
			var nextautorizar="";
			var jqueryautorizar = "";

			var autorizarb="";
			var nextautorizarb="";
			var jqueryautorizarb = "";

			var del="";
			var nextdel="";
			var jquerydel = "";
		
			for (var i=parseFloat(id);i<=parseFloat(tope);i++){
				nextidu="#idu"+((parseInt(i)+(1)));
				idu="idu" + parseInt(i);
				jqueryidu="#idu"+parseInt(i);
				
				nextempleado="#empleado"+((parseInt(i)+(1)));
				empleado="empleado" + parseInt(i);
				jqueryempleado="#empleado"+parseInt(i);
				
				nextnombre="#nombre"+((parseInt(i)+(1)));
				nombre="nombre" + parseInt(i);
				jquerynombre="#nombre"+parseInt(i);
				
				nexttipo="#tipo"+((parseInt(i)+(1)));
				tipo="tipo" + parseInt(i);
				jquerytipo="#tipo"+parseInt(i);
				
				nextcecos="#cecos"+((parseInt(i)+(1)));
				cecos="cecos" + parseInt(i);
				jquerycecos="#cecos"+parseInt(i);
				
				nextautorizar="#autorizar"+((parseInt(i)+(1)));
				autorizar="autorizar" + parseInt(i);
				jqueryautorizar="#autorizar"+parseInt(i);
				
				nextautorizarb="#"+((parseInt(i)+(1)))+"autorizar_data";
				autorizarb=parseInt(i)+"autorizar_data";
				jqueryautorizarb=="#"+parseInt(i)+"autorizar_data";
				
				nextdel="#"+((parseInt(i)+(1)))+"del";
				del=parseInt(i)+"del";
				jquerydel="#"+parseInt(i)+"del";

				$(nextidu).attr("id",idu);
				$(jqueryidu).attr("name",idu);
				
				$(nextempleado).attr("id",empleado);
				$(jqueryempleado).attr("name",empleado);
				
				$(nextnombre).attr("id",nombre);
				$(jquerynombre).attr("name",nombre);
				
				$(nexttipo).attr("id",tipo);
				$(jquerytipo).attr("name",tipo);
				
				$(nextcecos).attr("id",cecos);
				$(jquerycecos).attr("name",cecos);
				
				$(nextautorizar).attr("id",autorizar);
				$(jqueryautorizar).attr("name",autorizar);
				
				$(nextautorizarb).attr("id",autorizarb);
				$(jqueryautorizarb).attr("name",autorizarb);
				
				$(nextdel).attr("id",del);
				$(jquerydel).attr("name",del);           
			}
			
		});    

		$("img.elimina").one("click",function(e,data,data1){
			e.stopImmediatePropagation();		
			if(confirmBorrarDelegacion()){
				$(this).parent().parent().parent().fadeOut("normal", function(){
					$(this).remove();
					$("#numdelegaciones").trigger("restar");
					$("#numdelegaciones").unbind("restar");
					var tope=$("#numdelegaciones").val();
					i=parseFloat(id);
					$("#rowDel").trigger("cambiar",[i,tope]);
					$("#rowDel").unbind("cambiar");
					
					var no_registros  = $("#delegado_table>tbody>tr").length;
					var delegado = '';
					var tipo = '';
					for(var i = 1; i <= no_registros; i++){
						delegado+= $("#idu"+i).val()+"|";
						tipo+= $("#tipo"+i).val()+"|";			
					}
					$("#delegados").val(delegado);
					$("#delegadostipos").val(tipo);			

					i=parseFloat(id);
					$("#rowDel").trigger("cambiar",[i,tope]);
					$("#rowDel").unbind("cambiar");								
					return false;
				});
			}				
			return false;		
		});
		
		
	}//borrar invitado

	</script>
	<link rel="stylesheet" type="text/css" href="../../css/table_style.css" />
	<style type="text/css">
	.style1 {
		color: #FF0000
	}

	.fader {
		opacity: 0;
		display: none;
	}

	.trans {
		background-color: #D7D7D7;
		color: #0000FF;
		position: absolute;
		vertical-align: middle;
		width: 690px;
		height: 200px;
		padding: 65px;
		font-size: 15px;
		font-weight: bold;
		top: 26%;
		left: 18%;
	}

	.boton {
		background: #666666;
		color: #FFFFFF;
		border-color: #CCCCCC;
	}
	</style>
	<br>
	<br>
	<form name="form1" method="post" action="" />
	<table id="comprobacion_table" border="0" cellspacing="3" width="80%" align="center" 
		style="border: 1px #CCCCCC solid; margin: auto; margin-top: 5px; text-align: left; background-color: #F8F8F8">
		<tr>
			<td colspan="5"><div align="center" style="color: #003366">
					<strong>Informaci&oacute;n general</strong>
				</div></td>
			<!--  <td width="49%" ></td>-->
		</tr>
		<tr>
			<td colspan="5">&nbsp;</td>
		</tr>
		<tr>
			<td width="6%"><div align="left">Empleado:</div></td>
			<td width="18%"><strong><?php echo $Usu->Get_dato("u_usuario");?></strong></td>
			<td width="2%">&nbsp;</td>
			<td width="12%"><div align="left">Usuario que recibe privilegios:</div></td>
			<td width="14%"><div align="left">
					<strong><?php echo $Usu->Get_dato("u_nombre")." ".$Usu->Get_dato("u_paterno")." ".$Usu->Get_dato("u_materno");?></strong>
				</div></td>
		</tr>
		<tr>
			<td width="6%"><div align="left">Tipo Usuario:</div></td>
			<td width="18%"><strong><?php $sql = "SELECT tu_nombre FROM cat_tipo_usuario, usuario_tipo WHERE ut_tipo = tu_id AND ut_usuario  = ".$_GET['usuario_id'];
										$res = @mysql_query($sql);
										$row = @mysql_fetch_assoc($res);
										echo $row['tu_nombre'];?></strong></td>
			<td width="2%">&nbsp;</td>
			<td width="12%"><div align="left">Centro de Costos:</div></td>
			<td width="14%"><div align="left">
					<strong><?php 
							$query=sprintf("SELECT cc_id, cc_centrocostos, cc_nombre FROM cat_cecos c WHERE cc_estatus = 1");
							$var=mysql_query($query);
							while($arr=mysql_fetch_assoc($var)){
								if ($Usu->Get_dato("idcentrocosto") == $arr['cc_id']) {
									echo sprintf("%s - %s", $arr['cc_centrocostos'], $arr['cc_nombre']);    
								}                          
							}
						?></strong>
				</div></td>
		</tr>
	</table>
	<br />
	<table width="80%" align="center" 
		style="border: 1px #CCCCCC solid; margin: auto; margin-top: 5px; text-align: left; background-color: #F8F8F8">
		<tr>
			<td colspan="3">&nbsp;</td>
		</tr>
		<?php
						$Usu = new Usuario();
						?>
		<tr>
			<td align="right" width="30%">Asignador<span class="Estilo1">*</span>:
			</td>
			<td><select name="usuario_delegado" id="usuario_delegado"  onchange="cargarinfousuario(this.value);">
					<?php 
							$query=sprintf("SELECT u_id,u_nombre,u_paterno, u_materno FROM usuario WHERE u_activo = 1  AND u_id !='".$usuario_id."' ORDER BY u_nombre;");
							
							$var=mysql_query($query);
							while($arr=mysql_fetch_assoc($var)){
								
								if(!($Usu->find_esAgencia($arr['u_id']))){
									echo sprintf("<option value='%s'>%s %s %s</option>", $arr['u_id'], $arr['u_nombre'], $arr['u_paterno'], $arr['u_materno']);
								}                                                       
							}
						?>
			</select></td>
			<td rowspan="2">
				<input type="checkbox" name="estatus" id="estatus"  style="display:none"/>
				<span id="labelpriv" ></span>
			</td>
		</tr>

		<tr>
			<td align="right" width="30%">Tipo de usuario<span class="Estilo1">*</span>:</td>
			<td><select id="tipoUser" name="tipoUser" onchange="validaNormal();"></select></td>
		</tr>
		<tr>
			<td colspan="3" style="text-align: center;"><input type="button"
				id="agregar" name="agregar" value="Agregar"
				onclick="agregardelegacion();" /></td>
		</tr>
		<tr>
			<td colspan="3" style="text-align: center;">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="3" style="text-align: center;"><strong>Privilegios
					Asignados</strong></td>
		</tr>
		<tr>
			<td colspan="3">
				<table id="delegado_table" class="tablesorter" cellspacing="1">
					<thead>
						<tr>
							<th width="35%">Empleado</th>
							<th width="25%">Nombre del Empleado que Delega</th>
							<th width="20%">Tipo de Usuario</th>
							<th width="15%">Centro de Costos</th>
							<th width="20%">Privilegios para Autorizar</th>
							<th width="5%">Eliminar</th>
						</tr>
					</thead>
					<tbody>
						<!-- cuerpo tabla-->
					</tbody>
				</table>
			</td>
		</tr>
		<td></td>

		<tr></tr>
		<tr>
			<td>
			<input type="hidden" name="empleadoid" id="empleadoid" value="" readonly="readonly" /> 
			<input type="hidden" name="empleadouser" id="empleadouser" value="" readonly="readonly" /> 
			<input type="hidden" name="empleadonombre" id="empleadonombre" value="" readonly="readonly" /> 
				<input type="hidden" name="empleadotipo" id="empleadotipo" value="" readonly="readonly" /> 
				<input type="hidden" name="empleadocecos" id="empleadocecos" value="" readonly="readonly" /> 
				<input type="hidden" name="numdelegaciones" id="numdelegaciones" value="0" readonly="readonly" />
				<input type="hidden" id="rowDel" name="rowDel" value="0" readonly="readonly"/>
				<input type="hidden" name="delegadosel" id="delegadosel" readonly="readonly" value='<? echo $usuario_id; ?>' />
				<input type="hidden" name="iu" id="iu" readonly="readonly" value='<? echo $_SESSION["idusuario"]; ?>' />
				<input type="hidden" name="ne" id="ne" readonly="readonly" value='<? echo $_SESSION["idusuario"]; ?>' />
				<input type="hidden" name="delegados" id="delegados" readonly="readonly" value='' />
				<input type="hidden" name="delegadostipos" id="delegadostipos" readonly="readonly" value='' />
				<input type="hidden" name="yatienedelegaciones" id="yatienedelegaciones" readonly="readonly" value='' />
				<input type="hidden" name="confirmacion_elimina" id="confirmacion_elimina" readonly="readonly" value='' />
				</td>
				
		</tr>
		<tr>
			<td colspan="3" style="text-align: center;">
				<input type="submit" value="Volver" name="volver" id="volver"> 
				<input type="submit" value="Guardar" id="guardar" name="guardar" >
			</td>
		</tr>
	</table>
	<form action=""></form>
	<?php
		$I->Footer();
	}
?>
