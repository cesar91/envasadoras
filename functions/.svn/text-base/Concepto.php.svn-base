<?php
class Concepto extends conexion {
	
	protected $rst_concepto;

	public function Load_Concepto($id){
        $query=sprintf("SELECT * FROM cat_conceptos WHERE cp_id = '%s'",$id);
        $this->rst_concepto=parent::consultar($query); 
	}
	
	public function Load_Concepto_By_Nombre($cp_concepto){
        $query=sprintf("SELECT * FROM cat_conceptos WHERE cp_concepto = '%s'",$cp_concepto);
        $this->rst_concepto=parent::consultar($query); 
	}
	
	public function Get_Dato($nombre){
		return(mysql_result($this->rst_concepto,0,$nombre));
	}
    
    public function Delete_Concepto($id){
        $query=sprintf("UPDATE cat_conceptos SET cp_activo = 0 WHERE cp_id = '%s'",$id);
        //error_log($query);
        $this->rst_concepto=parent::ejecutar($query);        
	}        
    	
	public function Nuevo_Concepto($dc_catalogo, $cp_clasificacion, $cp_concepto, $cp_cuenta, $cp_estatus){
        $query=sprintf("INSERT INTO cat_conceptos(
        		cp_id, 
        		cp_concepto, 
        		cp_cuenta, 
        		cp_activo, 
        		cp_deducible ) 
        	VALUES(
        		default, 
        		'%s', 
        		'%s', 
        		'%s', 
        		'%s' )", 
        		$cp_concepto, 
        		$cp_cuenta, 
        		$cp_estatus, 
        		$cp_clasificacion
            );
        //error_log($query);
        $id2=parent::insertar($query);
        $this->Load_Concepto($id);
	}
	
	/**
     * Edita un concepto
	 */
	public function Edita_Concepto($cp_id, $cp_clasificacion, $cp_concepto, $cp_cuenta, $cp_estatus){
        $query = sprintf("UPDATE cat_conceptos SET 
        		cp_deducible = '%s', 
        		cp_concepto = '%s', 
        		cp_cuenta = '%s', 
        		cp_activo='%s' 
        		WHERE cp_id = %s", 
        		$cp_clasificacion, 
        		$cp_concepto, 
        		$cp_cuenta, 
        		$cp_estatus, 
        		$cp_id);
        //error_log($query);
        parent::ejecutar($query);
        $this->Load_Concepto($cp_id);
	}
    
	public function Load_all(){
		$arr= array();
		$query=sprintf("SELECT * FROM cat_conceptos");
		$arrConceptos=array();
		$var=parent::consultar($query);
		
		while($arr=mysql_fetch_assoc($var)){
			array_push($arrConceptos,$arr);
		}
		return $arrConceptos;
	}
	/**
     * Busca concepto por cuenta
	 */
	public function Busca_ConceptoXCuenta($cuenta){		
        echo $query=sprintf("SELECT cp_id FROM cat_conceptos WHERE cp_cuenta = %s",$cuenta);        
        $result=parent::consultar($query);
		if(parent::get_rows()>0){
			$row=mysql_fetch_assoc($result);
			return $row;
		}else{
			return NULL;
		}        
	}

	/**
	 * Funciones relacionadas con 3 conceptos (Alimento,Hotel y Lavanderia)
	 * dichas funciones van relacionadas a la inserccion de excepciones en la creacion
	 * de comprobaciones si es que existen.
	 * NOTA:Los nombres utilizados a continuacion son datos que encontramos en la tabla cat_conceptosbmw.
	 */
	
	//Funcion propia para Alimentos (Comprobacion de viaje)
	public function GetIdAlimentos(){
		$idConceptoAlimento=0;
		$this->Load_Concepto_By_Nombre("Alimentos");
		$idConceptoAlimento=$this->Get_Dato('dc_id');
		return $idConceptoAlimento;
	}
	
	//Funcion propia para Hotel (Comprobacion de viaje)
	public function GetIdHotel(){
		$idConceptoHotel=0;
		$this->Load_Concepto_By_Nombre("Hotel");
		$idConceptoHotel=$this->Get_Dato('dc_id');
		return $idConceptoHotel;
	}
	
	//Funcion propia para Lavanderia (Comprobacion de viaje)
	public function GetIdLavanderia(){
		$idConceptoLavanderia=0;		
		$this->Load_Concepto_By_Nombre("Lavandería");
		$idConceptoLavanderia=$this->Get_Dato('dc_id');
		return $idConceptoLavanderia;
	}

	//Funcion propia para Renta de Auto (Sol. de viaje)
	public function GetRentaAuto(){
		$idConceptoRentaAuto=0;
		$this->Load_Concepto_By_Nombre("Renta de auto");
		$idConceptoRentaAuto=$this->Get_Dato('dc_id');
		return $idConceptoRentaAuto;
	}
	
	//funcion para obtener la tasa correspondiente del concepto ( validacion conceptos X viaje)
	function tasaConceptos($conceptoNombre,$viaje){
		$tasaDelConcepto="";
		if($viaje == 'Nacional'){
			if($conceptoNombre == "Alimentos"){
				$tasaDelConcepto="TasaAlimentosDiariaNacional";
			}elseif($conceptoNombre == "Hotel"){
				$tasaDelConcepto="TasaHospedajeNacional";
			}elseif($conceptoNombre == "Lavanderia"){
				$tasaDelConcepto="MinimoDiasLavanderiaNacional";
			}
		}else if($viaje='Intercontinental' || $viaje='Continental'){
			if($conceptoNombre == "Alimentos"){
				$tasaDelConcepto="TasaAlimentosDiariaInternacional";
			}elseif($conceptoNombre == "Hotel"){
				$tasaDelConcepto="TasaHospedajeInternacional";
			}elseif($conceptoNombre == "Lavanderia"){
				$tasaDelConcepto="MinimoDiasLavanderiaInter";
			}
		}
		
		return $tasaDelConcepto;
		
	}	
	
}
?>
