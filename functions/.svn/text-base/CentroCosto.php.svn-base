<?php
class CentroCosto extends conexion {
	
	protected $rst_ceco;
	protected $rst_pp;
	protected $rst_ceco_search;

    /*
     *  Carga el centro de costos
     */
	public function Load_CeCo($id){
		$this->Busca_CeCo($id);
	}
	
    /*
     *  Lee un campo del centro de costos
     */    
	public function Get_Dato($nombre){
		return(mysql_result($this->rst_ceco,0,$nombre));
	}
    
    /*
     *  Borra el centro de costos
     */    
    public function Delete_Ceco($id){
        $query=sprintf("UPDATE cat_cecos SET cc_estatus = 1 WHERE cc_id='%s'",$id);
        $this->rst_empresa=parent::ejecutar($query);        
	}        
    	
    /*
     *  Crea un nuevo centro de costos
     */

	public function Nuevo_CentroCosto($nombre,$codigo,$empresa_id,$estatus,$responsable,$directorarea){
        $query=sprintf("insert into cat_cecos
                    (
                        cc_id,
                        cc_centrocostos,
                        cc_nombre,
                        cc_empresa_id,    
                        cc_estatus,
                        cc_responsable,
                        cc_director_de_area                    
                    )
                    VALUES(
                         (select max(cc_id) from cat_cecos cat)+1,
                        '%s',
                        '%s',
                        %s,
                        %s,
                        '%s',
                        '%s'
                    )",
                    $codigo,
                    $nombre,
                    $empresa_id,
                    $estatus,
                    $responsable,
                    $directorarea                    
            );
            error_log($query);
        $id=parent::insertar($query);
        $this->Load_CeCo($id);
	}   
	
    /*
     *  Edita el centro de costos
     */
	public function Edita_CentroCosto($ceco_id,$nombre,$codigo,$empresa_id,$estatus,$responsable,$directorarea){
		
		$query=sprintf("UPDATE cat_cecos SET cc_nombre = '%s', cc_centrocostos = '%s', cc_empresa_id = '%s', cc_estatus = '%s',cc_responsable='%s',cc_director_de_area='%s' WHERE cc_id = %s",
					$nombre, $codigo, $empresa_id, $estatus,$responsable,$directorarea, $ceco_id
			);
        parent::ejecutar($query);
        $this->Load_CeCo($ceco_id);
	}    
    
    /*
     *  Carga un arreglo con todos los centros de costos
     */    
	public function Load_all(){
		$arr= array();
		$query=sprintf("SELECT * FROM cat_cecos ");
		$arrCecos=array();
		$var=parent::consultar($query);
		
		while($arr=mysql_fetch_assoc($var)){
			array_push($arrCecos,$arr);
		}
		return $arrCecos;
	}

    /*
	 * Busca un centro de costos
	 */
	public function Busca_CeCo($id){		
        $query=sprintf("SELECT * FROM cat_cecos WHERE cc_id=%s",$id);
        $this->rst_ceco=parent::consultar($query);
	}
	
	/*
	 * Busca el IdCentroDeCostos brindándole el Código de Centro de Costos.
	 */
	public function Busca_CeCoXCodigo($codigoCC,$empresa){		
        $query=sprintf("SELECT * FROM cat_cecos WHERE cc_centrocostos = '%s' AND cc_empresa_id='%s' LIMIT 1",$codigoCC,$empresa);
        $result=parent::consultar($query);
		if(parent::get_rows()>0){
			$row=mysql_fetch_assoc($result);
			return $row;
		}else{
			return NULL;
		}        
	}
	
	/*
	 * Regresa el cc_id de un CECO
	 */
	public function Busca_CeCo_Codigo($codigoCC){
		$query=sprintf("SELECT cc_id FROM cat_cecos WHERE cc_centrocostos =%s",$codigoCC);
		$result=parent::consultar($query);
		$CECO=mysql_result($result, 'cc_id',0);
		return $CECO;
		  
	}
	
	/* Obtener id del Centro de Costos
	 */
	public function getCECOId($CC_codigo){
		$query=sprintf("SELECT cc_id FROM cat_cecos WHERE cc_centrocostos = '%s'", $CC_codigo);
		$rst = parent::consultar($query);
		while ($fila = mysql_fetch_assoc($rst)) {
			$cecoid = $fila['cc_id'];
		}
		return $cecoid;
	}
	
    /*
	 * Busca el periodo presupuestal que corresponde al centro de costos
	 */	
	public function Busca_PeriodoP($ceco_id,$fecha){
        $query=sprintf("SELECT * FROM periodo_presupuestal WHERE pp_ceco=%s AND pp_periodo_inicial<='%s' AND pp_periodo_final>='%s'",$ceco_id,$fecha,$fecha);
        $this->rst_pp=parent::consultar($query);
        return parent::get_rows();
	}
	
    /*
	 * Actualiza el presupuesto del ceco en el periodo seleccionado, el monto se resta del presupuesto disponible.
	 */	    
	public function Update_PeriodoP($ceco_id,$fecha,$monto_a_restar){
        $query=sprintf("UPDATE periodo_presupuestal SET pp_presupuesto_disponible = pp_presupuesto_disponible - %s WHERE pp_ceco=%s AND pp_periodo_inicial<='%s' AND pp_periodo_final>='%s'",$monto_a_restar,$ceco_id,$fecha,$fecha);
        parent::insertar($query);
        return;
	}    
    
    /*
     *  Lee un campo del periodo presupuestal
     */    
	public function Get_Dato_PP($nombre){
		return(mysql_result($this->rst_pp,0,$nombre));
	}
	
    /*
     *  Esta funcion se encarga de revisar si se tiene presupuesto 
     * disponible para cierta fecha. Recibe los siguientes parametros:
     * 
     *      centroCostoId - ID del centro de costos
     *      monto         - Monto que se quiere solicitar
     *      fecha         - Fecha para la cual se quiere solicitar del presupuesto.
     * 
     *   Esta funcion regresa 3 valores posibles:
     * 
     *       1 - No se tiene presupuesto definido para el ceco en la fecha deseada
     */
	public function revisa_presupuesto($centroCostoId, $monto, $fecha){
        
		$monto_actual=0;
                
        // Busca el presupuesto actual
		if($this->Busca_PeriodoP($centroCostoId,$fecha)){
			$monto_actual=$this->Get_Dato_PP('pp_presupuesto_disponible');
        } else {
			return 1; // No tiene presupuesto el cc en el mes actual
		}
        
		if($monto<$monto_actual){
			return 2; // Aprobador por defecto
        } else {
			return 3; // Aprobador por excepcion
        }
	}
    
    /*
     *  Regresa el presupuesto disponible para el centro de costos en la fecha definida
     */
	public function get_presupuesto_disponible($centroCostoId, $fecha){
                
        // Busca el presupuesto actual
		if($this->Busca_PeriodoP($centroCostoId,$fecha)){
			return $this->Get_Dato_PP('pp_presupuesto_disponible');
        } else {
			return 0; // No tiene presupuesto el cc en el mes actual
		}
	}    
	
    /*
     * Resta el monto indicado al presupuesto
     */
	public function resta_presupuesto($CentroCostoId, $monto, $fecha){
        $this->Update_PeriodoP($CentroCostoId, $fecha, $monto);
	}
	

	/*
     *  Regresa (suma) el monto indicado al presupuesto
     */
	public function regresa_monto($CentroCostoId, $monto,$fecha){
        $this->Update_PeriodoP($CentroCostoId, $fecha, -$monto);
	}
	
	

}
?>
