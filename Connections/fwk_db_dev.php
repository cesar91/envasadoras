<?php
class conexion{
    
	public $hostname_fwk_db;
	public $database_fwk_db;
	public $username_fwk_db;
	public $password_fwk_db;
	public $fwk_db;
	protected $rst_c;
	protected $rows;
	protected $columns;
	protected $estado;
	    
	function __construct(){
        
        global $MYSQL_HOST;
        global $MYSQL_PORT;
        global $MYSQL_USER;
        global $MYSQL_PASSWORD;
        global $MYSQL_DATABASE;                        

		$this->hostname_fwk_db = $MYSQL_HOST;
		$this->port_fwk_db     = $MYSQL_PORT;
		$this->username_fwk_db = $MYSQL_USER;
		$this->password_fwk_db = $MYSQL_PASSWORD;
	 	$this->database_fwk_db = $MYSQL_DATABASE;        
                
        if($this->username_fwk_db==""){
            error_log(print_r(debug_backtrace(), True));
        }

        if(!isset($this->username_fwk_db)){
            error_log(print_r(debug_backtrace(), True));
        }
        
		$this->rows			   = 0;
		$this->columns         = 0;        
		$this->estado          = 0; 
		$this->fwk_db = mysql_connect($this->hostname_fwk_db.":".$this->port_fwk_db, $this->username_fwk_db, $this->password_fwk_db) or die("No se realiza la conexxion");
		
	}
    
    /*
     *  Ejecuta una consulta y regresa un resulset
     */
	function consultar($query){
		$this->rows		=0;
		$this->columns	=0;
		$rst;
		mysql_select_db($this->database_fwk_db, $this->fwk_db) or die("Error al seleccionar la db");
		$this->rst_c	=mysql_query	 ($query, $this->fwk_db) or die(mysql_error());
		$this->rows		=mysql_num_rows  ($this->rst_c);
		$this->columns	=mysql_num_fields($this->rst_c);
		$this->estado   =1;
  		return($this->rst_c);
	}
    
    // Regresa el numero de renglones del resultset
	function get_rows(){
		return($this->rows);
	}

    // Regresa el numero de columnas del resultset    
	function get_columns(){
		return($this->columns);
	}
    
    /*
     *  Ejecuta un query de insercion y regresa el ID del objeto insertado
     */
	function insertar($query){
        $this->rows		=0;
        $this->columns	=0;
        mysql_select_db($this->database_fwk_db, $this->fwk_db);
        //$this->rst_c=mysql_query($query, $this->fwk_db) or die(mysql_error());
        // TODO: Add better error handling here. ap
        //  The error should be sent to the log.
        $this->rst_c=mysql_query($query, $this->fwk_db);
        return(mysql_insert_id());
	}
    
    /*
     *  Ejecuta el query
     */
	function ejecutar($query){
        $this->rows		=0;
        $this->columns	=0;
        mysql_select_db($this->database_fwk_db, $this->fwk_db);
        mysql_query($query, $this->fwk_db) or die(mysql_error());
	}    
    
	function __destruct() {
	    /*if(trim($this->estado)>0){
			mysql_free_result($this->rst_c);
		}*/
	}
	
	
	/**
	 * Retornamos todo el recordset como un array
	 *
	 * @return array
	 */
	public function get_data_array(){
		$vt_aux=array();
		while($fila = mysql_fetch_assoc($this->rst_c)){
			array_push($vt_aux,$fila);
		}
		return($vt_aux);
	}
}
?>