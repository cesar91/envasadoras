<?php
	class Lista extends conexion{
		
	
		protected $cabeceras 	= array();
		protected $herramientas = array();
		protected $rst_lista;
		protected $nombre_check;
		
		protected $count;
		protected $limit;
		protected $offset;
		
		protected $ltotal;
		protected $lactual;
		protected $lpaginacion;
		protected $page;
		public $type;
	
		function __construct($pg=""){
			parent::__construct();
			$this->rst_lista	="";
			$this->nombre_check	="lista_valores";
			$this->ltotal		=1;
			$this->lactual		=1;
			$this->lpaginacion	=10;
			$this->page=$pg;
		}
	
		
		public function set_nombre_seleccion($nombre){
			$this->nombre_check=$nombre;
		}
		
		/**
		 * Imprime una cabecera, el tipo puede set
		 * text
		 * number
		 * align puede ser:
		 * L,R,C
		 *
		 * @param text $nombre
		 * @param text $tipo
		 * @param text $estilo
		 */
		public function Cabeceras($nombre,$estilo="",$tipo="text",$align="L"){
			
			$aux="";

			switch(strtolower($align)){
				case "l":
					$aux="left";
				break;
				case "r":
					$aux="right";
				break;
				case "c":
					$aux="center";
				break;
				
			}
			array_push($this->cabeceras,array(0=>$nombre,1=>$estilo,2=>$tipo, 3=>$aux));
			
		}
		public function Limpia_Cabeceras(){
			$this->cabeceras	="";
			$this->cabeceras 	= array();
		}
		
		function Herramientas($tipo,$ruta){
			/**EL TIPO PUEDE SER:
				S: BUSQUEDA
				E: EDITAR
				D: ELIMINAR
				V: Visualizar en _blank
				M: Mail
				C:Cancelar
				A:Aceptar
			*/
			$tipo=trim($tipo);
			if($tipo=="S" || $tipo=="E" || $tipo=="D" || $tipo=="M" || $tipo=="V" || $tipo=="P" || $tipo=="C" || $tipo=="A"||  $tipo=="I"){
				array_push($this->herramientas,array(0=>$tipo,1=>$ruta));
			}		
		}
		
		function HerramientasCheck($tipo,$ruta,$mostrarCheck){
			/**EL TIPO PUEDE SER:
				S: BUSQUEDA
				E: EDITAR
				D: ELIMINAR
				V: Visualizar en _blank
				M: Mail
				C:Cancelar
				A:Aceptar
			*/
			$tipo=trim($tipo);
			if($tipo=="S" || $tipo=="E" || $tipo=="D" || $tipo=="M" || $tipo=="V" || $tipo=="P" || $tipo=="C" || $tipo=="A" ||  $tipo=="I"){
				array_push($this->herramientas,array(0=>$tipo,1=>$ruta));
				if($mostrarCheck)
					array_push($this->herramientas,array(0=>"K",1=>"0"));
			}		
		}
		
		private function Imprime_cabeceras(){
			static $cabecera, $estilo,$cols;
			?>
				<tr>
					<th class="lista_cabecera2">No</th>
					<?php
					
						foreach($this->cabeceras as $cabecera){
							?>
							<th class="<?php echo ($cabecera[1]!="")?$cabecera[1]:"lista_cabecera";?>" align="left">
								<?php echo $cabecera[0];?>
							</th>
							<?php
						}
						if(count($this->herramientas)>0){
							?><th  align="center" class="lista_cabecera">Consultar</th><?php
							
						}
						
					?>
				</tr>
			<?php
		}
		
		
		private function Imprime_herramientas($valor){
			global $APP_RUTA_P;
			static $herramienta;
			static $ruta_img;
			static $titulo;
			static $extra;
			global $RUTA_R;
			 $APP_RUTA_P =$RUTA_R;
			?><table border="0" cellpadding="0" cellspacing="0" align="center" style="padding-right: 8px;"><TR><?php
			
			foreach($this->herramientas as $herramienta){
				$extra="";
				switch($herramienta[0]){
					case "S": //@search
						$ruta_img= $APP_RUTA_P . "/images/btn-search.gif";
						
						$titulo="Buscar";
					break;
					case "E": //@Edit
						$ruta_img= $APP_RUTA_P . "/images/addedit.png";
						$extra="id='edit".$valor ."' name='edit".$valor ."'";

						$titulo="Editar";
					break;
					case "D": //@Delete
						$ruta_img= $APP_RUTA_P . "/images/delete.png";
						$titulo="Eliminar";
						$extra="id='".$valor ."delete' name='".$valor ."delete' onclick=confirma_eliminacion('" . $herramienta[1] . $valor .  "',1)";
						$onload="";
						$herramienta[1]="#";
					break;
					case "M": //@Mail
						$ruta_img= $APP_RUTA_P . "/img/mail.gif";
						$titulo="Enviar por Mail";
						
					break;
					
					case "V": //@Visualizar
						$ruta_img= $APP_RUTA_P . "/img/solo_ver.gif";
						$titulo="Visualizar";
						$extra="target='_blank'";
					break;
					case "P": //@Visualizar
						$ruta_img= $APP_RUTA_P . "/img/print.png";
						$titulo="Imprimir";
						$extra="target='_blank'";
					break;
					case "K": //@Visualizar
						$extra="check";
					break;
					
					case "C"://@Cancelar
						$ruta_img= $APP_RUTA_P . "/images/delete.png";
						$titulo="Cancelar";
						$extra="onclick=confirma_eliminacion('" . $herramienta[1] . $valor .  "',2)";
						$herramienta[1]="#";
					break;
					
					case "A"://@Aceptar
						$ruta_img= $APP_RUTA_P . "/images/ok.png";
						$titulo="Aceptar";
						$extra="onclick=confirma_eliminacion('" . $herramienta[1] . $valor .  "',3)";
						$herramienta[1]="#";
					break;

					case "I"://@Impostar-Asignar privilegios.
						$ruta_img= $APP_RUTA_P . "/images/fwk/action_scannew.gif";
						$extra="id='edit".$valor ."' name='edit".$valor ."'";
						$titulo="Asignar Privilegios";
						break;
				}
				
				?>
					 <?php if($extra=="check") { ?>
				 		<td align="right" style="padding-left: 10">
							<input type="checkbox" name="documentos[]" value="<?php echo $valor; ?>"> 
						</td>
				 	<?php }else{ ?>
						<td align="right" style="padding-left: 10">
							<a href="<?php echo $herramienta[1] . $valor; ?>" <?php echo $extra; ?>   class="lista"  style="padding-top: 0px;">
								<img src="<?php echo $ruta_img;?>"   title="<?php echo $titulo;?>"  border="0">
							</a>
						</td>
					<?php } ?>
				<?php
			}
			?></TR></table><?php
			
			
		
		}
		
		
		public function lista_agrupada($sql,$orden="",$seleccion=FALSE,$indice=0,$idcomp=-1,$comparacion="",$lstnombre=""){
			
			$vector = array();
			$this->rst_lista=parent::consultar($sql);
			$cnt = parent::get_rows();
			$cc  = parent::get_columns();
			
			for($n=0; $n<$cnt; $n++){
				$orden_val=strtolower(mysql_result($this->rst_lista,$n,$orden));
				$ax= array();
				for($a=0; $a<$cc; $a++){
					$ax[$a]=mysql_result($this->rst_lista,$n,$a);
				}
				
				$vector[$orden_val][$n]=$ax;
			}
			$this->enlista_datos($vector,$seleccion,$indice,$idcomp,$comparacion,$lstnombre);
		}
		
		
		
		private function enlista_datos($vector,$seleccion,$indice,$idcomp=-1,$comparacion="",$lstnombre="fila"){
			
			$colspan=count($this->herramientas) +14;
			?>
			<table width="100%" cellpadding="0" cellspacing="0" class="lista_marco">
				<TR><TD>
			
					<table width="100%" border="0" cellpadding="0" cellspacing="0">
					
						<?php $this->Imprime_cabeceras();?>
							
						<?php
							$cfd=0;
							$contador=0;
							foreach($vector as $key=> $vect){
								$cfd++;
								
								?>
									<tr bgcolor="" class="lista">
										<TD colspan="<?php echo $colspan +1;?>"><A href="#"<?php echo $cfd; ?>"></A> <a href="#aqui<?php echo $cfd; ?>" onclick="muestra_valores('<?php echo $lstnombre; ?>',<?php echo $contador;?>,<?php echo count($vect);?>);"><?php echo strtoupper($key);?></a></TD>
									</tr>
										<?php
											$cfc=0;
											foreach($vect as $valor){
												$cfc++;
												?>
													
													<tr id="<?php echo $lstnombre. $contador;?>" style="display:none;" bgcolor="<?php echo (($cfc%2)==0)?"#e0e0e0":"";?>" class="sublista">
																
														<?php		if($seleccion==FALSE){
																echo "<td>&nbsp;</td>";
																}
																else{
																	$checked="";
																	if($idcomp!=-1){
																		if($valor[$idcomp]==$comparacion){
																			$checked="checked";
																			
																		}
																	}
																?>
																	<td>
																		<input type="checkbox" name="<?php echo $this->nombre_check;?>[]" value="<?php echo $valor[$indice];?>" <?php echo $checked;?>>
																	</td>
																<?php
																
																}
															$cnt_col=0;
															foreach($valor as $val){
																if($cnt_col==$indice){
																	$valor_col=$val;
																}
																if(($seleccion==TRUE && $idcomp==-1)|| ($seleccion==TRUE && $cnt_col!=$idcomp) || $seleccion==FALSE){
																		if(strpos($val,"<a")==true){
																			echo "<td>" . strtolower($val) . "</td>";
																		}else{
																	echo "<td>" . strtoupper($val) . "</td>";
																		}
																}
																$cnt_col++;
															}
															?><td align="right" width="10%"><?php
																$this->Imprime_herramientas($valor_col);
															?></td><?php
														?>
													
													</tr>
												<?php
												$contador++;
											}
										?>
								
								<?php
							}
						?>
						
					</table>
				</TD></TR>
			</table>
				
			<?php
		}
		
		private function Obten_datos_paginacion($query,$prefix){
			$query			=str_replace(";","",$query);
			$aux			="select count(*) as total from (" . $query . ") as la1;";
			$rst			=parent::consultar($aux);
			$fila			=mysql_fetch_assoc($rst);
			$this->ltotal	=ceil($fila["total"]/$this->lpaginacion);
			$this->lactual	=1;	
			
		}
		
		private function Prepara_query($query){
			$this->offset	=($this->lactual -1) * $this->lpaginacion;
			$query=str_replace(";", " ",$query);
			$query.=sprintf(" limit %s offset %s",$this->lpaginacion,$this->offset);
			return($query);
		}
		
		
		/**
		 * Genermos la paginacion para la lista ;)
		 *
		 */
		private function Genera_paginacion($prefix){
			$auxp		=ceil($this->lactual/10);
			$tope		=$auxp*10;
			$auxi		=$tope -9;
			$tope		=($tope<=$this->ltotal)?$tope:$this->ltotal;
			$anterior	=($tope>10)?$tope -10:0;
			$siguiente	=($tope < $this->ltotal && $this->ltotal>10)?$tope + 1: 0;
			
			if($tope>1){
			?>
				<table cellpadding="0" cellspacing="0" align="center" class="lista" style="padding-top: 20px;" >
					<tr>
					<?php if($anterior!=0){ ?><td  class="pagnormal"><a href="?<?php echo $this->page; ?>&<?PHP echo $prefix;?>ltotal=<?php echo $this->ltotal; ?>&<?PHP echo $prefix;?>lactual=<?php echo $anterior; ?><?php echo $this->type; ?>"><?php echo "<<" ?></a></td><?php } ?>
			<?php
					for($n=$auxi; $n<=$tope; $n++){
							$clase="pagnormal";
							
							if($n==$this->lactual){								
								$clase="pagactual";	
							}
						?>
							<td class="<?php echo $clase; ?>"><a href="?<?php echo $this->page; ?>&<?PHP echo $prefix;?>ltotal=<?php echo $this->ltotal; ?>&<?PHP echo $prefix;?>lactual=<?php echo $n; ?><?php echo $this->type; ?>"><?php echo $n; ?></a></td>
						<?php								
					}
			?>	
					<?php if($siguiente!=0){ ?><td  class="pagnormal"><a href="?<?php echo $this->page; ?>&<?PHP echo $prefix;?>ltotal=<?php echo $this->ltotal; ?>&<?PHP echo $prefix;?>lactual=<?php echo $siguiente; ?><?php echo $this->type; ?>"><?php echo ">>" ?></a></td><?php } ?>
					</tr>
				</table>
			<?php
			}
			
		}
		
		
		
		function muestra_lista($sql,$indice,$seleccion=FALSE,$idcomp=-1,$comparacion="",$limite=10,$prefix="default"){
			if($limite > 0 && $limite!=""){
				$this->lpaginacion=$limite;
				if(!isset($_GET[$prefix."ltotal"])){ //OBtenemos el total
					$this->Obten_datos_paginacion($sql,$prefix);
				}
				else{
					$this->lactual	=$_GET[$prefix."lactual"];
					$this->ltotal	=$_GET[$prefix."ltotal"];
					
				}
				$sql=$this->Prepara_query($sql);
			}
			
			
			$this->rst_lista=parent::consultar($sql);
			static $filas;
			static $columnas;
			static $ruta;
				$filas		=parent::get_rows();
				$columnas	=parent::get_columns();
				
			?>
			
			<table width="100%"  cellpadding="2" cellspacing="2" class="lista_marco" ><TR><TD>
			
				<table width="100%" border="0" cellpadding="2" cellspacing="2" id="lista_datos" name="lista_datos">
				
					<?php 
					if($filas==0)
							echo "<div style='widht=100%'><center><strong><h1>No se encontr&oacute; ning&uacute;n registro de acuerdo con la b&uacute;squeda.</center></h1><div></strong>";
					else
						$this->Imprime_cabeceras();?>
					<?php
						$lcontador;
						$lcontador=($this->lactual*$this->lpaginacion) - $this->lpaginacion;
						

						for($n=0; $n<$filas; $n++){
							$lcontador++;
							?><tr bgcolor="<?php echo (($n%2)==0)?"#f7f7f7":"";?>" class="lista">
							
								<?php if($seleccion==FALSE){?>
									<td width="5%"><strong><?php echo ($lcontador<=9)?"0":""; echo  $lcontador;?></strong></td><?php
								}
								else{
									$checked="";
									if($idcomp!=-1){
										if(mysql_result($this->rst_lista,$n,$idcomp)==$comparacion){
											$checked="checked";
										}
									}
									?><td width="5%"><input type="checkbox" name="<?php echo $this->nombre_check;?>[]" value="<?php echo mysql_result($this->rst_lista,$n,$indice);?>" <?php echo $checked;?>>  </td><?php
								}
								
								
							for($a=0; $a<$columnas; $a++){
								if($idcomp!=$a){
								?>
									<?php //echo strtr(utf8_decode(strtoupper($this->Evalua_tipo_dato($a,mysql_result($this->rst_lista,$n,$a)))),"àèìòùáéíóúçñäëïöü","ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ");
									if(strpos((($this->Evalua_tipo_dato($a,mysql_result($this->rst_lista,$n,$a)))),"<a")===false){
									?>	
									<td style="font-size: 10px;" align="<?php echo $this->cabeceras[$a][3]; ?>"><?php echo str_replace("ACUTE","acute",strtr((strtoupper($this->Evalua_tipo_dato($a,mysql_result($this->rst_lista,$n,$a)))),"àèìòùáéíóúçñäëïöü","ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ"));?></td>
									<?php	
									}else{
									?>
									<td style="font-size: 10px;" align="<?php echo $this->cabeceras[$a][3]; ?>"><?php echo strtr((($this->Evalua_tipo_dato($a,mysql_result($this->rst_lista,$n,$a)))),"àèìòùáéíóúçñäëïöü","ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ");?></td>
									<?php 
									}
									}
								else{
									?>
									<!--<td>&nbsp;</td>--><?php
								}
								
							} //for de a
							if(count($this->herramientas)>0){
								?><td align="right" width="10%"><?php
								$this->Imprime_herramientas(mysql_result($this->rst_lista,$n,$indice));
								?></td></tr><?php
							}
						}//for de n
					?>
					
				</table>
			</TD></TR></table>
			
				
			<?php
			if($limite >0 && $limite!=""){
				$this->Genera_paginacion($prefix);
			}
			
		}//fin metodo muestra_lista
		
		private function Obten_datos_paginacion2($query,$prefix){			
			$this->ltotal	= ceil(count($query)/$this->lpaginacion);
			$this->lactual	= 1;				
		}
		
		function muestra_lista2($sql, $indice, $seleccion = FALSE, $idcomp = -1, $comparacion = "", $limite = 10, $prefix = "default"){
			if($limite > 0 && $limite != ""){
				$this->lpaginacion = $limite;
				if(!isset($_GET[$prefix."ltotal"]))
					$this->Obten_datos_paginacion2($sql,$prefix);
				else{
					$this->lactual = $_GET[$prefix."lactual"];
					$this->ltotal  = $_GET[$prefix."ltotal"];					
				}				
			}
			
			static $filas;
			static $columnas;
			static $ruta;
			$filas	  = count($sql);
			$columnas = count($sql[0]);
			
			?>			
			<table width="100%"  cellpadding="2" cellspacing="2" class="lista_marco" ><TR><TD>			
				<table width="100%" border="0" cellpadding="2" cellspacing="2" id="lista_datos" name="lista_datos">				
					<?php 
					if($filas==0)
							echo "<div style='widht=100%'><center><strong><h1>No se encontr&oacute; ning&uacute;n registro de acuerdo con la b&uacute;squeda.</center></h1><div></strong>";
					else
						$this->Imprime_cabeceras();
					
					$lcontador;
					$lcontador = ($this->lactual * $this->lpaginacion) - $this->lpaginacion;
					
					for($n=0; $n<$limite; $n++){
						$lcontador++;
						if(array_key_exists($lcontador-1,$sql)){						
							
							?><tr bgcolor="<?php echo (($n%2)==0)?"#f7f7f7":"";?>" class="lista">							
								<?php //if($seleccion == FALSE){?>
									<td width="5%"><strong><?php echo ($lcontador<=9)?"0":""; echo  $lcontador;?></strong></td><?php
								/*}else{
									$checked = "";
									if($idcomp != -1){
										/*if(mysql_result($this->rst_lista,$n,$idcomp)==$comparacion){
											$checked="checked";
										}
									}
									?><td width="5%"><input type="checkbox" name="<?php echo $this->nombre_check;?>[]" value="<?php echo mysql_result($this->rst_lista,$n,$indice);?>" <?php echo $checked;?>>  </td><?php
								}		*/					
							
							for($a = 0; $a<$columnas; $a++){
								if($idcomp != $a){								
									if(strpos((($this->Evalua_tipo_dato($a,$sql[$lcontador-1][$a]))),"<a")===false){
										 ?>	
										<td style="font-size: 10px;" align="<?php echo $this->cabeceras[$a][3]; ?>">
										<?php echo $this->Evalua_tipo_dato($a,strtoupper($sql[$lcontador-1][$a]));?>
										</td>
										<?php	
									}else{
										?>
										<td style="font-size: 10px;" align="<?php echo $this->cabeceras[$a][3]; ?>">
										<?php echo $this->Evalua_tipo_dato($a,$sql[$lcontador-1][$a]);?>
										</td>
										<?php 
									}
								}else{
									?>
									<!--<td>&nbsp;</td>--><?php
								}
								
							} //for de a
							
							if(count($this->herramientas)>0){
								?><td align="right" width="10%"><?php
								$this->Imprime_herramientas($sql[$n][$indice]);
								?></td></tr><?php
							}						
						}//for de n
					}
					?>					
				</table>
			</TD></TR>
			</table>				
			<?php
			
			if($limite > 0 && $limite != "")
				$this->Genera_paginacion($prefix);			
		}//fin metodo muestra_lista
		
		private function Evalua_tipo_dato($indice,$valor){
			switch(strtolower($this->cabeceras[$indice][2])){
				case "number":
					return(number_format($valor,2,".",","));
				break;
				
				case "text":
						return($valor);
				break;
			}
			
		}
		
			
		public function generar_lista($rst,$cabeceras,$pagina="#",$orden,$link="",$indice=""){
			/**el campo link puede ser:
				-1 no pondra link en ningun campo
				0 pondra link en todos los campos de la fila
				>0 sera la posicion del campo de la fila que tendra el link
				
			   el campo indice es el indice de la consulta que se requiere sea pasado por get :)
			*/
			$ln=count($cabeceras);
			$rst_ln=mysql_num_rows($rst);
			$columnas=mysql_num_fields($rst);
			$aux="";
			$cnt=0;
			?>
				<strong>Resultado de la busqueda:</strong><br><br>
				<table width="100%" cellpadding="2" cellspacing="2" border="0">
					
					<?php
						for($n=0; $n<$rst_ln; $n++){
							if(($orden!="" && $aux!=mysql_result($rst,$n,$orden)) || ($orden=="" && $n==0)){
								$cnt=0;
								if($orden!="")
								$aux=mysql_result($rst,$n,$orden);
								if($n>0){
									?><tr><TD>&nbsp;</TD></tr><?php
								}
								
								?>
								<TR>
									<?php
										if($n==0){
											for($p=0; $p<$ln; $p++){
												?><td bgcolor="#EFEFEF"  class="lista0"><strong><?php echo $cabeceras[$p];?></strong></td><?php
											}
										}
									?>
								</TR>
								<tr><td colspan="0"><strong ><?php echo $aux;?></strong></td></tr><?php
							}	
							?><tr><?php
							for($a=0; $a<$columnas; $a++){
								$valor=mysql_result($rst,$n,$indice);
								if($a!=$indice && $a!=$orden){
									?><td  <?php echo (($cnt%2)!=0)?"class='lista1'":"class='lista2'"; ?> ><?php
									if($a!=$indice){
										if($link==0 || $link==$a){
								?>
								  			<a href="<?php echo $pagina . $valor;?>" ><?php echo strtoupper(mysql_result($rst,$n,$a));?> </a>
								<?php
										}
										else{
											echo mysql_result($rst,$n,$a);
										}
									}
									?></td><?php
								}
							}
							
							?></tr><?php
							$cnt++;
						}
					?>
					
				</table>
			<?php
		}
	}
?>
 <script>

	function muestra_valores(nombre,inicial,lineas){
		var n=0;
		var aux;
		var tope=inicial + lineas;
		
		for(n=inicial; n<tope; n++){
			aux=nombre + "" + n;
			if(document.getElementById(aux).style.display=="none"){
				document.getElementById(aux).style.display="";	
			}
			else{
				document.getElementById(aux).style.display="none";	
			}
		}
	}
	
	function confirma_eliminacion(ruta, tipo){
		if(tipo==1)
			msj=" eliminar ";
		
		else if(tipo==2)
			msj=" cancelar ";
			
		else if(tipo==3)
			msj=" aceptar ";
			
		if(confirm("¿Realmente desea"+ msj +"el objeto?")){
			document.location=ruta;
		}
	}
</script>
