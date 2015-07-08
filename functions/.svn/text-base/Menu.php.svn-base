<?php
class Menu extends conexion {

	protected $sub_contador;
	protected $padres;
	public $hijos;
	function __construct(){
		parent::__construct();
		$this->sub_contador=0;
		$this->padres	= array();
		$this->hijos	= array();

	}

	public function Busca_Menu(){
		global $RUTA_R;
		$dir=str_replace($RUTA_R,"/",$_SERVER['PHP_SELF']);
		
		$query	=sprintf("SELECT * FROM menu m INNER JOIN usuario_menu u ON m.m_id = u.um_menu WHERE um_tipo = '%s' ORDER BY m_orden ",$_SESSION["perfil"]);
        //error_log($query);
		$rst	=parent::consultar($query);
		// Si el perfil elegido, es el del Administrador, entonces no mostraremos el link a inicial
		if($_SESSION["perfil"] != 2 && $_SESSION["perfil"] != 11 && $_SESSION["perfil"] != 12){ 
			array_push($this->padres,array("ruta"=>$RUTA_R . "/inicial.php", "nombre"=>"Inicio", "actual"=>false));
		}
		while($fila=mysql_fetch_assoc($rst)){
			//array_push($this->padres,array("ruta"=>$RUTA_R . $fila["m_ruta"], "nombre"=>$fila["m_nombre"], "actual"=>$actual));
			array_push($this->padres,array("ruta"=>$RUTA_R . $fila["m_ruta"], "nombre"=>$fila["m_nombre"], "actual"=>true));
		}
		array_push($this->padres,array("ruta"=>$RUTA_R . "/index.php", "nombre"=>"Salir", "actual"=>false));
	}

//cargamos el submenu a partir del papa :)(Agregada 09082011 para la funcionalidad Heredar Privilegios)
	private function carga_hijos($padre){
		global $RUTA_R;
		$query	= sprintf("select * from menu where m_pertenece_a=%s order by m_orden, m_nombre",$padre);
		$rst	= parent::consultar($query);
		while($fila = mysql_fetch_assoc($rst)){
			$this->hijos[$fila["m_nombre"]]=$RUTA_R . $fila["m_ruta"];
		}
	}
	
	
	private function Busca_papa_por_hijo($dir){
		global $RUTA_R;
		$query	= sprintf("select * from menu where m_pertenece_a in (select m_pertenece_a from menu where m_ruta='%s') order by m_orden,m_nombre;",$dir);
		$rst	= parent::consultar($query);
		while($fila = mysql_fetch_assoc($rst)){
			$this->hijos[$fila["m_nombre"]]=$RUTA_R . $fila["m_ruta"];
			
		}
    
	}

	public function Imprime_Papas(){
		?>
		
			<div id="menu">
			<?php
		foreach ($this->padres as $papa){
				?><a href="<?php echo str_replace("//","/",$papa["ruta"]); ?>"> <span><?php echo $papa["nombre"]; ?></span> </a><?php
		}
		?></div><?php
	}

	function Menu_Sup(){
		$this->Sup_Open();
		?>
<li><a href="">Home:</a></li>
<li><a href="">Mis Gastos</a></li>
<li><a href="">Configuraci&oacute;n </a></li>
		<?php
		$this->Sup_Close();
	}
	public function Sup_Open(){
		?>
<div id="menu" align="right">
<ul id="main">
<?php

	}

	public function Sup_Add($nombre, $ruta="#"){
		?>
	<li><a href="<?php echo $ruta; ?>"><?php echo $nombre; ?></a></li>
	<?php
	}

	public function  Sup_Close(){
		?>
</ul>
</div>
		<?php
	}


	public function Sub_Open(){
		?>
<div>
<ul style="clear: left;">
<?php
	}

	public function Sub_Add($nombre,$childs){
		$nombre_id="submenu_" . $this->sub_contador;
		?>
		<?php
		foreach ($childs as $nombre=>$ruta){
			?>
	<li
		style="padding-top: 3px; display: block; margin: 0px 0px 0px 0px;"><a
		href="<?php echo str_replace("//","/",$ruta); ?>" class="txtMenu"
		style="text-decoration: none;"><?php echo $nombre; ?></a></li>
		<?php
		}
		?>

		<?php
		$this->sub_contador++;
	}


	public function Sub_Close(){
		?>
</ul>
</div>

		<?php
	}

	public function __destruct(){
		?>
<script>
				var abierto="";
				function abrir(nombre){
					if(document.getElementById(nombre).style.display=="none"){
						if(abierto!=""){
							document.getElementById(abierto).style.display="none";
						}
						document.getElementById(nombre).style.display='';
						abierto=nombre;
					}
					else{
						document.getElementById(nombre).style.display="none";
					}
				}
			</script>

		<?php
	}
}
?>
