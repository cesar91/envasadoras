<?php
header("Cache-Control: no-store, no-cache, must-revalidate");
require_once("../../../lib/php/constantes.php");
require_once "$RUTA_A/Connections/fwk_db.php";
require_once "$RUTA_A/functions/utils.php";


if(isset($_POST['idConcepto']) && $_POST['idConcepto']!="" && isset($_POST['tipo']) && $_POST['tipo']!=""){

			$idConcepto=$_POST['idConcepto'];
			$tipo=$_POST['tipo'];
			
			$cnn			= new conexion();
			$auxDatos= array();

			$query="select * from parametros where p_concepto ={$idConcepto} and p_nivel_usuario={$tipo}";
			$rst=$cnn->consultar($query);			
			
			$cad="";
			
			while($datos=mysql_fetch_assoc($rst)){
				$cad.=$datos['pr_id']."|".$datos['pr_cantidad']."|".utf8_encode($datos['pr_divisa'])."&";
			}
			
			echo $cad;
}
elseif(isset($_POST['Aceptar']) && isset($_POST['idEuros'])  && $_POST['idEuros']!="" && isset($_POST['idDolares']) && $_POST['idDolares']!="" && isset($_POST['idPesos'])  && $_POST['idPesos']!="" && isset($_POST['euros'])  && $_POST['euros']!="" && isset($_POST['dolares']) && $_POST['dolares']!="" && isset($_POST['pesos'])  && $_POST['pesos']!="" && isset($_POST['idConcepto']) && isset($_POST['niveluser'])){


	$vb=$_POST['Aceptar'];

	$pesos=$_POST['pesos'];
	$dolares=$_POST['dolares'];
	$euros=$_POST['euros'];

	$idPesos=$_POST['idPesos'];
	$idDolares=$_POST['idDolares'];
	$idEuros=$_POST['idEuros'];
	
	$idConcepto=$_POST['idConcepto'];
	$tipo=$_POST['niveluser'];
	
	$paginacion="";		
	
	$obCat=new Parametro();
	$result="";
	
	if($vb==1)
	{
		
		if(isset($_POST['"lTot"']) && $_POST["lTot"]!=""&& $_POST["lAc"]!="" && isset($_POST['lAc']))
			$paginacion="ltotal=".$_POST["lTot"]."&lactual=".$_POST["lAc"]."&";		
		//
		//$paginacion="";
			
		if($obCat->Load($idPesos)==1){
			$obCat->Modifica_Parametro($pesos, $idPesos);
			$result="ok";
			
		}
		else
			$result.="Pesos|".$obCat->insertar_Parametro($pesos,$idConcepto,$tipo,"Pesos");

		if($obCat->Load($idDolares)==1)	{
			$obCat->Modifica_Parametro($dolares, $idDolares);
			$result="ok";
		}
		else
			$result.="&".utf8_encode("Dólares")."|".$obCat->insertar_Parametro($dolares,$idConcepto,$tipo,"Dólares");
			
		if($obCat->Load($idEuros)==1){
			$obCat->Modifica_Parametro($euros, $idEuros);
			$result="ok";
		}
		else
			$result.="&Euros|".$obCat->insertar_Parametro($euros,$idConcepto,$tipo,"Euros");
	
		echo $result;

	}
	
	//header('Location:./index.php?'.$paginacion);
}
?>