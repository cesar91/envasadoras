<?php

/*
 *  Esta clase implementa las funciones para enviar informacion a los ERPs
 *  de Marti: PeopleSoft y Dynamics. AP
 */

define("PEOPLESOFT", "0");
define("DYNAMICS", "1");

// Define el tipo de ERP al cual nos conectamos
$TIPO_ERP=PEOPLESOFT;

// '2010-10-10' --> '20-OCT-10';
function MySQLDateToOracleDate($fechas){
    $ArrFecha = explode(" ", $fechas);
    $ArrFecha1 = explode("-", $ArrFecha[0]);
    $fecha=date("d-M-Y", mktime(0, 0, 0, $ArrFecha1[1], $ArrFecha1[2], $ArrFecha1[0]));
    return $fecha;
}

// '2010-10-10' --> 'YYYY/MM/DD';
function MySQLDateToYYYYMMDD($fecha){
    return strftime('%Y/%m/%d', strtotime($fecha));
}

// '2010-10-10' --> '2010';
function MySQLDateToAnoContable($fecha){
    return strftime('%Y', strtotime($fecha));
}

// '2010-10-10' --> '10';
function MySQLDateToMesContable($fecha){
    return strftime('%m', strtotime($fecha));
}

// Valida que la variable no venga vacia, si viene la anade a la lista de validaciones
function validaVariable($name, $str, $allow_zero=false){
    global $VALIDACIONES;
    if($allow_zero){
        if(strlen($str)==0){
            $VALIDACIONES = $VALIDACIONES . "Variable: $name vacia.\n";
            return false;
        }         
    } else {
        if(empty($str) || $str==""){
            $VALIDACIONES = $VALIDACIONES . "Variable: $name vacia.\n";
            return false;
        }        
    }
    
    return true;
}

/*
     ANTICIPO: (La plantilla que se usa en AP es Pagado p/Anticipado de CxP)
            Business Unit  (por ejemplo: GMDEM)
            ID Departamento (por ejemplo: DFM046)
            ID Proveedor o Producto (por ejemplo: DD0055)
            Fecha
            Monto (Es total en anticipo no se manejan impuestos)
            Cuenta a aplicar, la cual se calcula como:

                1014110103 + ID_Depto + ID_Producto
*/
class Anticipo {

    // por ejemplo: GMDEM
    public $TRAMITE_ID;
    
    // por ejemplo: GMDEM
    public $BUSINESS_UNIT;
    
    // por ejemplo: DFM046
    public $ID_DEPTO;
    
    // por ejemplo: 000001
    public $ID_USUARIO;       
    
    // por ejemplo: DD0055
    public $ID_PRODUCTO;    
    
    // por ejemplo: 10869
    public $ID_VENDOR;        
    
    // Fecha de autorizacion de la solicitud
    public $FECHA;
    public $FECHA_ORACLE;
    public $FECHA_CONTABLE;
    public $ANO_CONTABLE;
    public $PERIODO_CONTABLE;    // mes    
    
    // Monto total (en anticipo no se manejan impuestos)
    public $MONTO;    

    // Cuenta contable a Afectar: TODO: Deberia de venir de la base de datos
    public $CUENTA = "1014110103";   
    
    // Moneda del anticipo: MXP, USD, EUR
    public $MONEDA; 
    
    // Tasa a la que se convierte la moneda
    public $TASA;     
    
    // Hace cualquier conversion de datos que haga falta
    public function convert(){
        $this->FECHA_ORACLE = MySQLDateToOracleDate($this->FECHA);
        $this->FECHA_CONTABLE=MySQLDateToYYYYMMDD($this->FECHA);
        $this->ANO_CONTABLE = MySQLDateToAnoContable($this->FECHA);
        $this->PERIODO_CONTABLE=MySQLDateToMesContable($this->FECHA);   
        
        // Asegurarse que este en formato: 0000010869
        $this->ID_VENDOR = str_pad($this->ID_VENDOR, 10, '0', STR_PAD_LEFT);              
    }
    
    // Checa que los valores sean correctos, o al menos no vacios.
    public function valida(){
        
        $this->convert();
        $valida = true;
        $valida = $valida && validaVariable(TRAMITE_ID, $this->TRAMITE_ID);
        $valida = $valida && validaVariable(BUSINESS_UNIT, $this->BUSINESS_UNIT);
        $valida = $valida && validaVariable(ID_DEPTO, $this->ID_DEPTO);
        $valida = $valida && validaVariable(ID_USUARIO, $this->ID_USUARIO);
        $valida = $valida && validaVariable(ID_PRODUCTO, $this->ID_PRODUCTO);                        
        $valida = $valida && validaVariable(ID_VENDOR, $this->ID_VENDOR);
        $valida = $valida && validaVariable(FECHA, $this->FECHA);
        $valida = $valida && validaVariable(FECHA_ORACLE, $this->FECHA_ORACLE);
        $valida = $valida && validaVariable(FECHA_CONTABLE, $this->FECHA_CONTABLE);
        $valida = $valida && validaVariable(ANO_CONTABLE, $this->ANO_CONTABLE);
        $valida = $valida && validaVariable(PERIODO_CONTABLE, $this->PERIODO_CONTABLE);
        $valida = $valida && validaVariable(MONTO, $this->MONTO);
        $valida = $valida && validaVariable(CUENTA, $this->CUENTA);      
        $valida = $valida && validaVariable(MONEDA, $this->MONEDA);  
        $valida = $valida && validaVariable(CUENTA, $this->TASA);          
        
        if($valida){
            global $VALIDACIONES;
            $VALIDACIONES = $VALIDACIONES . "Validaciones existosas!!!\n";            
        }            
        
        if($this->BUSINESS_UNIT=="GMAIF"){
            $this->CUENTA = "1014110103";
            $this->ID_DEPTO = "AIF001";
        }
        if($this->BUSINESS_UNIT=="GMAIL"){
            $this->CUENTA = "1014110103";
            $this->ID_DEPTO = "AIL001";
        }
        if($this->BUSINESS_UNIT=="GMSAM"){
            $this->CUENTA = "1014110103";
            $this->ID_DEPTO = "SAM001";
        }                        
    }    
}


class Comprobacion {
 
    /* Tipo de Comprobacion o Reembolso
        1 = Viajes
        2 = Reembolso Amex    
        3 = Reembolso Caja Chica
    */
    public $TIPO;     
    
    // por ejemplo: GMDEM
    public $TRAMITE_ID;    
    
    // por ejemplo: GMDEM
    public $BUSINESS_UNIT;
    
    // por ejemplo: DFM046
    public $ID_DEPTO;
    
    // por ejemplo: 000001
    public $ID_USUARIO;        
    
    // por ejemplo: DD0055
    public $ID_PRODUCTO;    
    
    // por ejemplo: 10869
    public $ID_VENDOR;   
    
    // id del anticipo ligado a esta comprobacion
    public $ID_ANTICIPO_RELACIONADO;       
    
    // En el caso de una comprobacion que tiene un gasto
    // adicional y este no fue aprobado, aqui se guarda
    // el monto no aprobado
    public $MONTO_NO_APROBADO=0;
    
    // Fecha de autorizacion de la comprobacion
    public $FECHA;
    public $FECHA_ORACLE;
    public $FECHA_CONTABLE;
    public $ANO_CONTABLE;
    public $PERIODO_CONTABLE;    // mes
        
    // TODO: Estas cuentas deberian de venir de un catalogo.
    public $CUENTA_IVA_16_COMPROBACIONES='1016110205';
    public $CUENTA_IVA_11_COMPROBACIONES='1016110204';
    public $CUENTA_DEUDORES='1014110101';
    public $DEPARTAMENTO_DEUDORES='ADM005';
    public $PRODUCTO_DEUDORES='DD0001';  
    
    // Estas cuentas se usan para el caso que parte del gasto adicional
    // de la comprobacion no viene aprobado.
    public $CUENTA_OTROS_INGRESOS='9021110104';
    public $DEPARTAMENTO_OTROS_INGRESO='DAD012';
    
    // Arreglo de las lineas de comprobacion
    public $LINEAS_COMPROBACION = Array();
    
    // montos (son calculados en base a las lineas)
    public $MONTO_SUBTOTAL;        
    public $MONTO_IVA;       
    public $MONTO_TOTAL;      
    
    // Moneda del anticipo: MXP, USD, EUR    
    public $MONEDA;
    
    // Hace cualquier conversion de datos que haga falta
    public function convert(){
        $this->FECHA_ORACLE = MySQLDateToOracleDate($this->FECHA);
        $this->FECHA_CONTABLE=MySQLDateToYYYYMMDD($this->FECHA);
        $this->ANO_CONTABLE = MySQLDateToAnoContable($this->FECHA);
        $this->PERIODO_CONTABLE=MySQLDateToMesContable($this->FECHA);  
        
        $this->MONTO_SUBTOTAL = 0.0;
        $this->MONTO_IVA = 0.0;
        $this->MONTO_TOTAL = 0.0;                
        
        // Calcula los totales en base a las lineas
        foreach ($this->LINEAS_COMPROBACION as $key => $LINEA){
            $this->MONTO_SUBTOTAL = $this->MONTO_SUBTOTAL + $LINEA->MONTO_SUBTOTAL;
            $this->MONTO_IVA      = $this->MONTO_IVA      + $LINEA->MONTO_IVA;
            $this->MONTO_TOTAL    = $this->MONTO_TOTAL    + $LINEA->MONTO_TOTAL;            
        }
        
        if (empty($this->ID_ANTICIPO_RELACIONADO) or $this->ID_ANTICIPO_RELACIONADO=='' or $this->ID_ANTICIPO_RELACIONADO=='-1' or $this->ID_ANTICIPO_RELACIONADO==-1) {
            $this->ID_ANTICIPO_RELACIONADO = '0';
        }
        
        // Asegurarse que este en formato: 0000010869
        $this->ID_VENDOR = str_pad($this->ID_VENDOR, 10, '0', STR_PAD_LEFT); 

        if($this->BUSINESS_UNIT=="GMDEM"){
            $this->CUENTA_OTROS_INGRESOS='9021110104';
            $this->DEPARTAMENTO_OTROS_INGRESO='DAD012';
        }        
        if($this->BUSINESS_UNIT=="GMAIF"){
            $this->CUENTA_OTROS_INGRESOS='9021110104';
            $this->DEPARTAMENTO_OTROS_INGRESO='AIF001';
        }
        if($this->BUSINESS_UNIT=="GMAIL"){
            $this->CUENTA_OTROS_INGRESOS='9021110104';
            $this->DEPARTAMENTO_OTROS_INGRESO='AIL001';
        }
        if($this->BUSINESS_UNIT=="GMSAM"){
            $this->CUENTA_OTROS_INGRESOS='9021110104';
            $this->DEPARTAMENTO_OTROS_INGRESO='SAM001';
        }         
    }    
    
    // Checa que los valores sean correctos, o al menos no vacios.
    public function valida(){
        
        $this->convert();
        $valida = true;
        $valida = $valida && validaVariable(TIPO, $this->TIPO);
        $valida = $valida && validaVariable(TRAMITE_ID, $this->TRAMITE_ID);
        $valida = $valida && validaVariable(BUSINESS_UNIT, $this->BUSINESS_UNIT);
        $valida = $valida && validaVariable(ID_DEPTO, $this->ID_DEPTO);
        $valida = $valida && validaVariable(ID_USUARIO, $this->ID_USUARIO);
        $valida = $valida && validaVariable(ID_PRODUCTO, $this->ID_PRODUCTO);                        
        $valida = $valida && validaVariable(ID_VENDOR, $this->ID_VENDOR);
        // El anticipo relacionado solo se ocupa en la comprobacion de gastos, en amex y cc no se usa
        if($this->TIPO==1){
            $valida = $valida && validaVariable(ID_ANTICIPO_RELACIONADO, $this->ID_ANTICIPO_RELACIONADO);
        }
        $valida = $valida && validaVariable(FECHA, $this->FECHA);
        $valida = $valida && validaVariable(FECHA_ORACLE, $this->FECHA_ORACLE);
        $valida = $valida && validaVariable(FECHA_CONTABLE, $this->FECHA_CONTABLE);
        $valida = $valida && validaVariable(ANO_CONTABLE, $this->ANO_CONTABLE);
        $valida = $valida && validaVariable(PERIODO_CONTABLE, $this->PERIODO_CONTABLE);
        $valida = $valida && validaVariable(CUENTA_IVA_16_COMPROBACIONES, $this->CUENTA_IVA_16_COMPROBACIONES);  
        $valida = $valida && validaVariable(CUENTA_IVA_11_COMPROBACIONES, $this->CUENTA_IVA_11_COMPROBACIONES);
        $valida = $valida && validaVariable(CUENTA_DEUDORES, $this->CUENTA_DEUDORES);
        $valida = $valida && validaVariable(DEPARTAMENTO_DEUDORES, $this->DEPARTAMENTO_DEUDORES);                        
        $valida = $valida && validaVariable(PRODUCTO_DEUDORES, $this->PRODUCTO_DEUDORES);  
        $valida = $valida && validaVariable(MONTO_SUBTOTAL, $this->MONTO_SUBTOTAL);
        $valida = $valida && validaVariable(MONTO_IVA, $this->MONTO_IVA, true);
        $valida = $valida && validaVariable(MONTO_TOTAL, $this->MONTO_TOTAL);     
        // OJO: La moneda puede venir vacia, en ese caso el default que toma la interface es pesos.
        //$valida = $valida && validaVariable(MONEDA, $this->MONEDA);  
        
        foreach ($this->LINEAS_COMPROBACION as $key => $LINEA){
            $valida = $valida && validaVariable(LINEA_ID_CONCEPTO, $LINEA->ID_CONCEPTO);           
            $valida = $valida && validaVariable(LINEA_CONCEPTO, $LINEA->CONCEPTO);   
            $valida = $valida && validaVariable(LINEA_CUENTA, $LINEA->CUENTA);   
            $valida = $valida && validaVariable(LINEA_MONTO_SUBTOTAL, $LINEA->MONTO_SUBTOTAL);   
            $valida = $valida && validaVariable(LINEA_MONTO_IVA, $LINEA->MONTO_IVA, true);   
            $valida = $valida && validaVariable(LINEA_MONTO_TOTAL, $LINEA->MONTO_TOTAL);                                                   
            $valida = $valida && validaVariable(LINEA_TASA_IVA, $LINEA->TASA_IVA, true); 
        }    
        
        if($valida){
            global $VALIDACIONES;
            $VALIDACIONES = $VALIDACIONES . "Validaciones existosas!!!\n";            
        }            
    }  
    
    public function AgregaLinea($linea){
        array_push($this->LINEAS_COMPROBACION, $linea);
    }
    
    public function AgregaLineaDeConcepto($concepto, $cuenta, $subtotal, $iva, $tasa){        
        $linea = new LineaComprobacion();
        $linea->ID_CONCEPTO = 1;
        $linea->CONCEPTO = $concepto;
        $linea->CUENTA = $cuenta;
        $linea->MONTO_SUBTOTAL = $subtotal;
        $linea->MONTO_IVA = $iva;
        $linea->MONTO_TOTAL = $subtotal + $iva;       
        $linea->TASA_IVA = $tasa;
        $this->AgregaLinea($linea);        
    }    
}

class LineaComprobacion {
    
    // por ejemplo: 1
    public $ID_CONCEPTO;    
    
    // por ejemplo: Taxis
    public $CONCEPTO;
        
    // por ejemplo: 
    public $CUENTA;
        
    // montos
    public $MONTO_SUBTOTAL;        
    public $MONTO_IVA;  
    public $MONTO_TOTAL;            
    public $TASA_IVA;  
}


class ConexionERP {
        
    // Activa o desactiva informacion de depuracion
    protected $DEBUG_CONEXION_ERP = True;
    
    // Esta es la conexion a Oracle
    private $oracle_con;
    	
    // Regresa una conexion a la BD del ERP
	public function Conectar(){
        
        global $DISABLE_ORACLE;
        global $ORACLE_USER;
        global $ORACLE_PASSWORD;
        global $ORACLE_HOST_SID;
        
        error_log("DISABLE_ORACLE=".$DISABLE_ORACLE);
        error_log("ORACLE_USER=".$ORACLE_USER);
        error_log("ORACLE_PASSWORD=".$ORACLE_PASSWORD);        
        error_log("ORACLE_HOST_SID=".$ORACLE_HOST_SID);

        if(!$DISABLE_ORACLE){
            $this->oracle_con = oci_connect($ORACLE_USER, $ORACLE_PASSWORD, $ORACLE_HOST_SID); 
            if (!$this->oracle_con){ 
              $msg = "-- Cannot connect to Oracle ".oci_error(); 
            } else { 
              $msg = "-- Connected to Oracle"; 
            } 
            
            if($this->DEBUG_CONEXION_ERP){
                error_log($msg);            
            }
        }
        
        date_default_timezone_set('America/Chicago');
        
	}
    
    public function Desconectar(){
        global $DISABLE_ORACLE;        
        if(!$DISABLE_ORACLE){
            oci_close($this->oracle_con);
        }
    }
	
    public function PruebaConexion(){
        $SQL='select BUSINESS_UNIT from PS_MTI_VCHD_AP_TBL';

        $stid = oci_parse($this->oracle_con, $SQL); 
        oci_execute($stid); 
        while ($row = oci_fetch_assoc($stid)) { 
            echo "<tr>\n"; 
            echo "<td>". $row["BUSINESS_UNIT"] . "</td>\n"; 
            echo "</tr>\n"; 
         }         
    }
    
    /*
     *  Esta funcion obtiene la tasa de cambio de la moneda del catalog de expenses
     */
    public function getTasaCambio($moneda){
        
        $d = new Divisa();
        return $d->Get_Divisa("USD");
    }    
    
    /*
     *  Esta function sincroniza el catalogo de monedas de People con el de Expenses
     */
    public function actualizaTasaCambio(){
        
        $SQL="SELECT RATE_MULT FROM PS_MTI_RT_EXP_VW WHERE FROM_CUR = 'USD' AND TO_CUR = 'MXP'";
        error_log($SQL);
        $stid = oci_parse($this->oracle_con, $SQL); 
        oci_execute($stid); 
        while ($row = oci_fetch_assoc($stid)) { 
            $tasa = $row["RATE_MULT"];
            error_log("Usando nueva tasa para USD =".$tasa);
         }   
         
        $d = new Divisa();
        $d->Update_Divisa('USD', $tasa);         
    }
    
    public function ExecuteSQL($SQL_STMT){
        //error_log($SQL_STMT.";");
        //echo $SQL_STMT;
        global $DISABLE_ORACLE;  
        global $ORACLE_QUERIES;
        if(isset($ORACLE_QUERIES)){
            if(strlen($ORACLE_QUERIES)==0){
                $ORACLE_QUERIES = $SQL_STMT . ";";
            } else {
                $ORACLE_QUERIES = $ORACLE_QUERIES . "\n" . $SQL_STMT . ";";
            }
        }
              
        if(!$DISABLE_ORACLE){
            $STID = oci_parse($this->oracle_con, $SQL_STMT); 
            oci_execute($STID);        
        }
    }
    
    // Guarda un anticipo en el ERP
	public function Registra_Anticipo($anticipo){
		
        $anticipo->convert();
        
        /******************************************
         *  
         *         Encabezado
         * 
         *****************************************/ 
        $data = $this->GetPS_MTI_VCHD_AP_TBLDefaultLine();
        $data['BUSINESS_UNIT'] = $anticipo->BUSINESS_UNIT;  // Empresa
        $data['VCHR_BLD_KEY_N1'] = '0';                     // El anticipo no hace referencia a ningun campo
        $data['VOUCHER_ID'] = 'NEXT';                       // Valor por defecto
        $data['VCHR_BLD_KEY_C1'] = $anticipo->TRAMITE_ID;   // ID del tramite
        $data['PREPAID_REF'] = $anticipo->TRAMITE_ID;       // ID del tramite        
        $data['VOUCHER_STYLE'] = 'PPAY';                    // Indica que es un anticipo
        $data['INVOICE_ID'] = $anticipo->TRAMITE_ID;        // ID del tramite
        $data['INVOICE_DT'] = $anticipo->FECHA_ORACLE;      // Fecha de la autorizacion
        $data['VENDOR_ID'] = $anticipo->ID_VENDOR;          // ID del proveedor al que se le hace el pago (empleado)
        $data['ORIGIN'] = 'EXP';                            // Valor por defecto
        $data['ACCOUNTING_DT'] = $anticipo->FECHA_ORACLE;   // Fecha de la autorizacion
        $data['GROSS_AMT'] = $anticipo->MONTO * $anticipo->TASA;  // Monto total del anticipo, no se manejan impuestos
        $data['TAX_EXEMPT'] = 'N';                          // Valor por defecto
        $data['MATCH_ACTION'] = 'Y';                        // Valor por defecto
        
        /* De acuerdo al correo de Oscar Alvarez:
         * 
            Para los comprobantes de pago por anticipado solo hay que cambiar el valor del segundo campo:
            PREPAID_AUTO_APPLY               en el cual ponen el valor N
            Para los casos de pago por anticipado deben venir el valor
            PREPAID_AUTO_APPLY               Y
        */
        $data['PREPAID_AUTO_APPLY'] = 'Y';                  // Checar comentario arriba
        $data['IN_PROCESS_FLG'] = 'N';                      // Valor por defecto
        $data['IMAGE_DATE'] = $anticipo->FECHA_ORACLE;      // Fecha de la autorizacion
        $data['INSPECT_DT'] = $anticipo->FECHA_ORACLE;      // Fecha de la autorizacion
        $data['INV_RECPT_DT'] = $anticipo->FECHA_ORACLE;    // Fecha de la autorizacion
        $data['RECEIPT_DT'] = $anticipo->FECHA_ORACLE;      // Fecha de la autorizacion
        $data['DSCNT_DUE_DT'] = $anticipo->FECHA_ORACLE;    // Fecha de la autorizacion
        $data['DUE_DT'] = $anticipo->FECHA_ORACLE;          // Fecha de la autorizacion
        
        // En caso que sea una moneda distinta a pesos (USD) se hace el envio en pesos
        // y se hace la conversion a la tasa definida en el anticipo
        if(!empty($anticipo->MONEDA) && $anticipo->MONEDA!='MXP' && $anticipo->MONEDA!=''){
            //$data['TXN_CURRENCY_CD'] = $anticipo->MONEDA;        
        }
        
        $SQL_STMT = $this->ArrayToInsert('PS_MTI_VCHD_AP_TBL', $data);
        $this->ExecuteSQL($SQL_STMT);
            
        /******************************************
         *  
         *         Linea
         * 
         *****************************************/ 
        $data = $this->GetPS_MTI_VCLN_AP_TBLDefaultLine();      
        $data['BUSINESS_UNIT'] = $anticipo->BUSINESS_UNIT;      // Empresa
        $data['VCHR_BLD_KEY_N1'] = '0';                         // El anticipo no hace referencia a ningun campo
        $data['VOUCHER_ID'] = 'NEXT';                           // Valor por defecto
        $data['VCHR_BLD_KEY_C1'] = $anticipo->TRAMITE_ID;       // ID del tramite         
        $data['VOUCHER_LINE_NUM'] = '1';                        // Numero de lineas del tramite, el anticipo siempre es 1
        $data['LINE_NBR'] = '1';                                // Numero de la linea actual, en el anticipo siempre es 1
        $data['DESCR'] = 'ANTICIPO DE VIAJE';                   // Descripcion de la operacion
        $data['MERCHANDISE_AMT'] = $anticipo->MONTO * $anticipo->TASA;  // Monto total del anticipo, no se manejan impuestos
        $data['BUSINESS_UNIT_RECV'] = $anticipo->BUSINESS_UNIT; // Empresa
        $data['MATCH_LINE_OPT'] = 'E';                          // Valor por defecto
        $data['DISTRIB_MTHD_FLG'] = 'A';                        // Valor por defecto
        $data['SHIPTO_ID'] = 'CMART';                           // Valor por defecto
        $data['ADDR_SEQ_NUM_SHIP'] = '1';                       // Valor por defecto
        $data['VENDOR_ID'] = $anticipo->ID_VENDOR;              // ID del proveedor al que se le hace el pago (empleado)
        $data['DESCR254_MIXED'] = 'ANTICIPO';                   // Descripcion de la operacion
        $data['BUSINESS_UNIT_GL'] = $anticipo->BUSINESS_UNIT;   // Empresa
        $data['ACCOUNT'] = $anticipo->CUENTA;                   // Cuenta contable a afectar
        $data['PRODUCT'] = $anticipo->ID_PRODUCTO;              // Producto a afectar
        $data['DEPTID'] = $anticipo->ID_DEPTO;                  // Departamento a afectar
        $data['TRANS_DT'] = $anticipo->FECHA_ORACLE;            // Fecha de la autorizacion    
        
        /*
         *   Se incluyó información al campo VAT_APPLICABILITY de la tabla PS_MTI_VCLN_AP_TBL correspondiente 
         * a la aplicabilidad del IVA, donde debe capturarse como valor  la letra  “O” en aquellos casos donde los comprobantes o anticipos no contiene IVA.
         */
        $data['VAT_APPLICABILITY'] = 'O';           
        
        $SQL_STMT = $this->ArrayToInsert('PS_MTI_VCLN_AP_TBL', $data);
        $this->ExecuteSQL($SQL_STMT);
        
        /******************************************
         *  
         *         Distrib
         * 
         *****************************************/ 
        $data = $this->GetPS_MTI_VCDS_AP_TBLDefaultLine();        
        $data['BUSINESS_UNIT'] = $anticipo->BUSINESS_UNIT;      // Empresa
        $data['VCHR_BLD_KEY_N1'] = '0';                         // El anticipo no hace referencia a ningun campo
        $data['VOUCHER_ID'] = 'NEXT';                           // Valor por defecto
        $data['VCHR_BLD_KEY_C1'] = $anticipo->TRAMITE_ID;       // ID del tramite        
        $data['VOUCHER_LINE_NUM'] = '1';                        // Numero de lineas del tramite, el anticipo siempre es 1
        $data['DISTRIB_LINE_NUM'] = '1';                        // Numero de la linea actual, en el anticipo siempre es 1
        $data['BUSINESS_UNIT_GL'] = $anticipo->BUSINESS_UNIT;   // Empresa
        $data['ACCOUNT'] = $anticipo->CUENTA;                   // Cuenta contable a afectar
        $data['DESCR'] = 'ANTICIPO DE VIAJE';                   // Descripcion de la operacion
        $data['MERCHANDISE_AMT'] = $anticipo->MONTO * $anticipo->TASA;  // Monto total del anticipo, no se manejan impuestos
        $data['PRODUCT'] = $anticipo->ID_PRODUCTO;              // Producto a afectar
        $data['BUDGET_DT'] = $anticipo->FECHA_ORACLE;           // Fecha de la autorizacion
        $data['USER_VCHR_DATE'] = $anticipo->FECHA_ORACLE;      // Fecha de la autorizacion
        $data['CREATED_DTTM'] = $anticipo->FECHA_ORACLE;        // Fecha de la autorizacion    
        $data['DEPTID'] = $anticipo->ID_DEPTO;                  // Departamento a afectar            
        $SQL_STMT = $this->ArrayToInsert('PS_MTI_VCDS_AP_TBL', $data);
        $this->ExecuteSQL($SQL_STMT);           
        
	}
    
    // Guarda un comprobacion en el ERP
	public function Registra_Comprobacion($comprobacion){
		
        $comprobacion->convert();
        
        /******************************************
         *  
         *         Encabezado
         * 
         *****************************************/ 
        $data = $this->GetPS_MTI_VCHD_AP_TBLDefaultLine();
        $data['BUSINESS_UNIT'] = $comprobacion->BUSINESS_UNIT;              // Empresa
        $data['PREPAID_REF'] = $comprobacion->ID_ANTICIPO_RELACIONADO;      // Anticipo ligado a esta comprobacion   
        $data['VOUCHER_ID'] = 'NEXT';                                       // Valor por defecto
        $data['VCHR_BLD_KEY_C1'] = $comprobacion->TRAMITE_ID;               // ID del tramite        
        $data['VOUCHER_STYLE'] = 'REG';                                     // Indica que es una comprobacion
        $data['INVOICE_ID'] = $comprobacion->TRAMITE_ID;                    // ID del tramite
        $data['INVOICE_DT'] = $comprobacion->FECHA_ORACLE;                  // Fecha de la autorizacion
        $data['VENDOR_ID'] = $comprobacion->ID_VENDOR;                      // ID del proveedor al que se le hace el pago (empleado)
        $data['ORIGIN'] = 'EXP';                                            // Valor por defecto
        $data['ACCOUNTING_DT'] = $comprobacion->FECHA_ORACLE;               // Fecha de la autorizacion 
        $data['GROSS_AMT'] = $comprobacion->MONTO_TOTAL - $comprobacion->MONTO_NO_APROBADO;                    // Total del comprobante con IVA
        $data['VAT_ENTRD_AMT'] = $comprobacion->MONTO_IVA;                  // IVA del comprobante
        $data['MATCH_ACTION'] = 'Y';                                        // Valor por defecto
        $data['PREPAID_AUTO_APPLY'] = 'N';                                  // Debe ser Y para la comprobacion, creemos Roberto y yo para que se pueda aplicar la comprobacion automatica
        $data['IN_PROCESS_FLG'] = 'N';                                      // Valor por defecto
        $data['IMAGE_DATE'] = $comprobacion->FECHA_ORACLE;                  // Fecha de la autorizacion 
        $data['INSPECT_DT'] = $comprobacion->FECHA_ORACLE;                  // Fecha de la autorizacion 
        $data['INV_RECPT_DT'] = $comprobacion->FECHA_ORACLE;                // Fecha de la autorizacion 
        $data['RECEIPT_DT'] = $comprobacion->FECHA_ORACLE;                  // Fecha de la autorizacion 
        $data['DSCNT_DUE_DT'] = $comprobacion->FECHA_ORACLE;                // Fecha de la autorizacion 
        $data['DUE_DT'] = $comprobacion->FECHA_ORACLE;                      // Fecha de la autorizacion 
        $data['VAT_DCLRTN_POINT'] = 'I';   // Campo fijo con el objetivo de que el IVA se coloque de manera directa.
        
        /*
        Las transacciones de pago de caja chica están llegando a PeopleSoft con el pago por anticipado automático activado y se están aplicando a pagos históricos ya que los busca el sistema por el id de proveedor. Estos pagos no se aplican a ningún anticipo, solo se pagan.
        Veo que en la tabla stage MTI_VCHD_AP_TBL que es la cabecera del voucher que deposita expenses, están los campos:

        PREPAID_REF                      en el cual ponen el valor 0
        PREPAID_AUTO_APPLY               en el cual ponen el valor Y

        Para los casos de pago de caja chica deben venir los valores

        PREPAID_REF                      Id de Expenses
        PREPAID_AUTO_APPLY               N

        Para que no se aplique ningún pago anticipado al voucher.        
        */
        
        if($comprobacion->TIPO==3 || $comprobacion->TIPO=='3'){
            $data['PREPAID_REF'] = $comprobacion->TRAMITE_ID; 
            $data['PREPAID_AUTO_APPLY'] = 'N'; 
        }
        
        if(!empty($comprobacion->MONEDA) && $comprobacion->MONEDA!='')
            $data['TXN_CURRENCY_CD'] = $comprobacion->MONEDA;                // Moneda        

        $SQL_STMT = $this->ArrayToInsert('PS_MTI_VCHD_AP_TBL', $data);
        $this->ExecuteSQL($SQL_STMT);     
        
        /******************************************
         *  
         *         Linea
         * 
         *****************************************/ 
        

        
        // Calcula los totales en base a las lineas
        $contador = 1;
        
        $num_lineas = count($comprobacion->LINEAS_COMPROBACION);        
        foreach ($comprobacion->LINEAS_COMPROBACION as $key => $LINEA){

            $data = $this->GetPS_MTI_VCLN_AP_TBLDefaultLine();        
            $data['BUSINESS_UNIT'] = $comprobacion->BUSINESS_UNIT;                 // Empresa
            $data['VCHR_BLD_KEY_N1'] = $comprobacion->ID_ANTICIPO_RELACIONADO;     // Anticipo ligado a esta comprobacion
            $data['VOUCHER_ID'] = 'NEXT';                                          // Valor por defecto
            $data['VCHR_BLD_KEY_C1'] = $comprobacion->TRAMITE_ID;                  // ID del tramite   
            $data['VOUCHER_LINE_NUM'] = $contador;                                 // Numero de la linea actual
            $data['LINE_NBR'] = $contador;                                         // Numero de la linea actual
            $data['DESCR'] = substr($LINEA->CONCEPTO, 0, 30);                      // Descripcion de el concepto
            $data['MERCHANDISE_AMT'] = $LINEA->MONTO_SUBTOTAL;                     // Subtotal de la linea
            $data['BUSINESS_UNIT_RECV'] = $comprobacion->BUSINESS_UNIT;            // Empresa
            $data['MATCH_LINE_OPT'] = 'E';                                         // Valor por defecto
            $data['DISTRIB_MTHD_FLG'] = 'A';                                       // Valor por defecto
            $data['SHIPTO_ID'] = 'CMART';                                          // Valor por defecto
            $data['ADDR_SEQ_NUM_SHIP'] = '1';                                      // Valor por defecto
            $data['VENDOR_ID'] = $comprobacion->ID_VENDOR;                         // ID del proveedor que hace la comprobacion (empleado)
            $data['DESCR254_MIXED'] = 'COMPROBACION';                              // Descripcion de la operacion
            $data['BUSINESS_UNIT_GL'] = $comprobacion->BUSINESS_UNIT;              // Empresa
            $data['ACCOUNT'] = $LINEA->CUENTA;                                     // Cuenta contable ligada al concepto
            // AP: Se comenta el producto puesto que en la parte de las pruebas
            // con Marti se dice que el producto no va en la comprobacion            
            ///$data['PRODUCT'] = $comprobacion->ID_PRODUCTO;                         // Producto ligado al concepto
            $data['DEPTID'] = $comprobacion->ID_DEPTO;                             // Departamento
            $data['VAT_ENTRD_AMT'] = $LINEA->MONTO_IVA;                            // Monto de IVA del concepto
            $data['TRANS_DT'] = $comprobacion->FECHA_ORACLE;                       // Fecha de Autorizacion
            
            if ($LINEA->TASA_IVA=='0.11'){
               $data['TAX_CD_VAT'] = "IVA 11%";
            }

            if ($LINEA->TASA_IVA=='0.16'){
               $data['TAX_CD_VAT'] = "IVA 16%";
            }
            
            if ($LINEA->TASA_IVA=='0.00'){
               $data['TAX_CD_VAT'] = "IVA 00%";
            }
        
            $data['VAT_APPLICABILITY'] = 'T'; 
            
            $SQL_STMT = $this->ArrayToInsert('PS_MTI_VCLN_AP_TBL', $data);
            $this->ExecuteSQL($SQL_STMT);
            
            /******************************************
             *  
             *         Distrib
             * 
             *****************************************/ 
            
            $data = $this->GetPS_MTI_VCDS_AP_TBLDefaultLine();                
            $data['BUSINESS_UNIT'] = $comprobacion->BUSINESS_UNIT;                 // Empresa
            $data['VCHR_BLD_KEY_N1'] = $comprobacion->ID_ANTICIPO_RELACIONADO;     // Anticipo ligado a esta comprobacion
            $data['VOUCHER_ID'] = 'NEXT';                                          // Valor por defecto
            $data['VCHR_BLD_KEY_C1'] = $comprobacion->TRAMITE_ID;                  // ID del tramite   
            $data['VOUCHER_LINE_NUM'] = $contador;                                 // Numero de la linea actual,
            $data['DISTRIB_LINE_NUM'] = $contador;                                 // Numero de la linea actual,
            $data['BUSINESS_UNIT_GL'] = $comprobacion->BUSINESS_UNIT;              // Empresa
            $data['ACCOUNT'] = $LINEA->CUENTA;                                     // Cuenta contable a afectar
            $data['DESCR'] = substr($LINEA->CONCEPTO, 0, 30);                      // Descripcion de el concepto
            $data['MERCHANDISE_AMT'] = $LINEA->MONTO_SUBTOTAL;                     // Subtotal de la linea
            // AP: Se comenta el producto puesto que en la parte de las pruebas
            // con Marti se dice que el producto no va en la comprobacion
            //$data['PRODUCT'] = $comprobacion->ID_PRODUCTO;                         // Producto a afectar
            $data['BUDGET_DT'] = $comprobacion->FECHA_ORACLE;                      // Fecha de la autorizacion
            $data['USER_VCHR_DATE'] = $comprobacion->FECHA_ORACLE;                 // Fecha de la autorizacion
            $data['CREATED_DTTM'] = $comprobacion->FECHA_ORACLE;                   // Fecha de la autorizacion
            $data['DEPTID'] = $comprobacion->ID_DEPTO;                             // Departamento
            $SQL_STMT = $this->ArrayToInsert('PS_MTI_VCDS_AP_TBL', $data);
            $this->ExecuteSQL($SQL_STMT);                      
            
            $contador = $contador + 1;

        }        
        
        // Este es el caso cuando en la comprobacion se realizo un gasto adicional
        // mas el mismo no se aprobo de tal manera que se necesita meter una linea de otros 
        // ingresos.
        if ($comprobacion->MONTO_NO_APROBADO > 0) {
            
            $data = $this->GetPS_MTI_VCLN_AP_TBLDefaultLine();        
            $data['BUSINESS_UNIT'] = $comprobacion->BUSINESS_UNIT;                 // Empresa
            $data['VCHR_BLD_KEY_N1'] = $comprobacion->ID_ANTICIPO_RELACIONADO;     // Anticipo ligado a esta comprobacion
            $data['VOUCHER_ID'] = 'NEXT';                                          // Valor por defecto
            $data['VCHR_BLD_KEY_C1'] = $comprobacion->TRAMITE_ID;                  // ID del tramite   
            $data['VOUCHER_LINE_NUM'] = $contador;                                 // Numero de la linea actual
            $data['LINE_NBR'] = $contador;                                         // Numero de la linea actual
            $data['DESCR'] = 'OTROS INGRESOS GRAVADOS';                            // Descripcion de el concepto
            $data['MERCHANDISE_AMT'] = 0 - $comprobacion->MONTO_NO_APROBADO;           // Monto en negativo no aprobado
            $data['BUSINESS_UNIT_RECV'] = $comprobacion->BUSINESS_UNIT;            // Empresa
            $data['MATCH_LINE_OPT'] = 'E';                                         // Valor por defecto
            $data['DISTRIB_MTHD_FLG'] = 'A';                                       // Valor por defecto
            $data['SHIPTO_ID'] = 'CMART';                                          // Valor por defecto
            $data['ADDR_SEQ_NUM_SHIP'] = '1';                                      // Valor por defecto
            $data['VENDOR_ID'] = $comprobacion->ID_VENDOR;                         // ID del proveedor que hace la comprobacion (empleado)
            $data['DESCR254_MIXED'] = 'COMPROBACION';                              // Descripcion de la operacion
            $data['BUSINESS_UNIT_GL'] = $comprobacion->BUSINESS_UNIT;              // Empresa
            $data['ACCOUNT'] = $comprobacion->CUENTA_OTROS_INGRESOS;               // Cuenta contable ligada al concepto
            // AP: Se comenta el producto puesto que en la parte de las pruebas
            // con Marti se dice que el producto no va en la comprobacion            
            //$data['PRODUCT'] = $comprobacion->ID_PRODUCTO;                       // Producto ligado al concepto
            $data['DEPTID'] = $comprobacion->DEPARTAMENTO_OTROS_INGRESO;           // Departamento
            $data['VAT_ENTRD_AMT'] = '0';                                          // Otros ingresos no lleva IVA
            $data['TRANS_DT'] = $comprobacion->FECHA_ORACLE;                       // Fecha de Autorizacion
            
            $data['TAX_CD_VAT'] = "IVA 00%";
            $data['VAT_APPLICABILITY'] = 'T'; 
            
            $SQL_STMT = $this->ArrayToInsert('PS_MTI_VCLN_AP_TBL', $data);
            $this->ExecuteSQL($SQL_STMT);
            
            
            /******************************************
             *  
             *         Distrib
             * 
             *****************************************/ 
            //$contador = $contador - 1;
            
            $data = $this->GetPS_MTI_VCDS_AP_TBLDefaultLine();                
            $data['BUSINESS_UNIT'] = $comprobacion->BUSINESS_UNIT;                 // Empresa
            $data['VCHR_BLD_KEY_N1'] = $comprobacion->ID_ANTICIPO_RELACIONADO;     // Anticipo ligado a esta comprobacion
            $data['VOUCHER_ID'] = 'NEXT';                                          // Valor por defecto
            $data['VCHR_BLD_KEY_C1'] = $comprobacion->TRAMITE_ID;                  // ID del tramite   
            $data['VOUCHER_LINE_NUM'] = $contador;                                 // Numero de la linea actual,
            $data['DISTRIB_LINE_NUM'] = $contador;                                 // Numero de la linea actual,
            $data['BUSINESS_UNIT_GL'] = $comprobacion->BUSINESS_UNIT;              // Empresa
            $data['ACCOUNT'] = $comprobacion->CUENTA_OTROS_INGRESOS;               // Cuenta contable a afectar
            $data['DESCR'] = 'OTROS INGRESOS GRAVADOS';                            // Descripcion de el concepto
            $data['MERCHANDISE_AMT'] = 0 - $comprobacion->MONTO_NO_APROBADO;           // Subtotal de la linea
            // AP: Se comenta el producto puesto que en la parte de las pruebas
            // con Marti se dice que el producto no va en la comprobacion
            //$data['PRODUCT'] = $comprobacion->ID_PRODUCTO;                         // Producto a afectar
            // OJO EL PRODUCTO NUNCA VA
            $data['BUDGET_DT'] = $comprobacion->FECHA_ORACLE;                      // Fecha de la autorizacion
            $data['USER_VCHR_DATE'] = $comprobacion->FECHA_ORACLE;                 // Fecha de la autorizacion
            $data['CREATED_DTTM'] = $comprobacion->FECHA_ORACLE;                   // Fecha de la autorizacion
            $data['DEPTID'] = $comprobacion->DEPARTAMENTO_OTROS_INGRESO;           // Departamento
            $SQL_STMT = $this->ArrayToInsert('PS_MTI_VCDS_AP_TBL', $data);
            $this->ExecuteSQL($SQL_STMT);                      
            
            $contador = $contador + 1;            
            
        }              
        
            
    }

    // Guarda un comprobacion en el ERP - GL
	public function Registra_ComprobacionAmex($comprobacion){
        
        $comprobacion->convert();
        
        if(!empty($comprobacion->MONEDA) && $comprobacion->MONEDA!=''){
            $tasa_cambio = $this->getTasaCambio($comprobacion->MONEDA);
        } else {
            $tasa_cambio = 1;
        }
        
        /******************************************
         *  
         *         Encabezado
         * 
         *  En este caso se registran en GL con la siguiente estructura
         * 
         *   Por cada linea de la comprobacion se crean un asiento de cargo
         * en GL:
         * 
         *    1) Cargo a la cuenta del gastos
         * 
         *   Ademas se crea una sola linea por el total del IVA
         * 
         *    2) Cargo a la cuenta de IVA (en caso que lleve iva)
         * 
         *   Adicionalmente se crea una sola linea de abono a la siguiente cuenta
         * 
         *    3) Abono a Deudores: 	1014110101	ADM005	DD0001 DEUDORES			
         * 
         *****************************************/ 
        
        $num_lineas = count($comprobacion->LINEAS_COMPROBACION);
    
        // Calcula los totales en base a las lineas
        $contador = 1;
        $total_iva_16 = 0;
        $total_iva_11 = 0;
        $total_monto_total = 0;
        foreach ($comprobacion->LINEAS_COMPROBACION as $key => $LINEA){        
        
            // Crea linea de la cuenta de gastos, una por concepto.
            $data = $this->GetMTI_JGNO_GL_TBLDefaultLine();
            $data['BUSINESS_UNIT'] = $comprobacion->BUSINESS_UNIT;                      // Empresa
            $data['TRANSACTION_ID'] = $comprobacion->TRAMITE_ID;                        // ID del tramite
            $data['TRANSACTION_LINE'] = $contador;                                      // Numero de la linea actual,
            $data['ACCOUNTING_DT'] = $comprobacion->FECHA_ORACLE;                       // Fecha de la autorizacion
            $data['BUSINESS_UNIT_GL'] =  $comprobacion->BUSINESS_UNIT;                  // Empresa
            $data['FISCAL_YEAR'] = $comprobacion->ANO_CONTABLE;                         // Año actual
            $data['ACCOUNTING_PERIOD'] = $comprobacion->PERIODO_CONTABLE;               // Mes actual
            $data['ACCOUNT'] = $LINEA->CUENTA;                                          // Cuenta contable
            $data['DEPTID'] =  $comprobacion->ID_DEPTO;                                 // Departamento
            $data['MONETARY_AMOUNT'] = $LINEA->MONTO_SUBTOTAL;                          // Subtotal del concepto
            $data['LINE_DESCR'] = 'COMPROBACION GASTO - ' . $comprobacion->TRAMITE_ID;  // Descripcion 
            
            if ($LINEA->CUENTA == "2012110102"){
                $data['PRODUCT'] = 'AC0024'; // Caso especial del telefono
            }
            
            if(!empty($comprobacion->MONEDA) && $comprobacion->MONEDA!=''){
                $data['CURRENCY_CD'] = "MXP";                            // Fijo to MXP
                $data['FOREIGN_CURRENCY'] = $comprobacion->MONEDA;       // Moneda
                $data['RT_TYPE'] = 'DOF'; //  Campo que mostrará la clase de cambio en caso de que la moneda sea extranjera. Anadido a peticion de Roberto Mangas
            
                $data['FOREIGN_AMOUNT'] = $LINEA->MONTO_SUBTOTAL;                   // Monto de la póliza en dólares
                $data['MONETARY_AMOUNT'] = $LINEA->MONTO_SUBTOTAL * $tasa_cambio;   // Monto de la póliza convertido a pesos mexicano.
            }

            if($LINEA->TASA_IVA=='0.16')
                $total_iva_16 = $total_iva_16 + $LINEA->MONTO_IVA;
            if($LINEA->TASA_IVA=='0.11')
                $total_iva_11 = $total_iva_11 + $LINEA->MONTO_IVA;                
                
            $total_monto_total = $total_monto_total + $LINEA->MONTO_TOTAL;
            $contador = $contador + 1;

            $SQL_STMT = $this->ArrayToInsert('PS_MTI_JGNO_GL_TBL', $data);
            $this->ExecuteSQL($SQL_STMT);
        
        }

        // Crea linea de IVA - En caso que haya IVA
        if ($total_iva_16 > 0) {
         
            $data = $this->GetMTI_JGNO_GL_TBLDefaultLine();
            $data['BUSINESS_UNIT'] = $comprobacion->BUSINESS_UNIT;                      // Empresa
            $data['TRANSACTION_ID'] = $comprobacion->TRAMITE_ID;                        // ID del tramite
            $data['TRANSACTION_LINE'] = $contador;                                      // Numero de la linea actual,
            $data['ACCOUNTING_DT'] = $comprobacion->FECHA_ORACLE;                       // Fecha de la autorizacion
            $data['BUSINESS_UNIT_GL'] =  $comprobacion->BUSINESS_UNIT;                  // Empresa
            $data['FISCAL_YEAR'] = $comprobacion->ANO_CONTABLE;                         // Año actual
            $data['ACCOUNTING_PERIOD'] = $comprobacion->PERIODO_CONTABLE;               // Mes actual
            $data['ACCOUNT'] = $comprobacion->CUENTA_IVA_16_COMPROBACIONES;             // Cuenta contable del IVA
            $data['DEPTID'] =  $comprobacion->ID_DEPTO;                                 // Departamento
            $data['MONETARY_AMOUNT'] = $total_iva_16;                                   // Total de IVA de la poliza
            $data['LINE_DESCR'] = 'COMPROBACION GASTO - ' . $comprobacion->TRAMITE_ID;  // Descripcion            
            $contador = $contador + 1;
            
            if(!empty($comprobacion->MONEDA) && $comprobacion->MONEDA!=''){
                
                $data['CURRENCY_CD'] = "MXP";                            // Fijo to MXP
                $data['FOREIGN_CURRENCY'] = $comprobacion->MONEDA;       // Moneda
                $data['RT_TYPE'] = 'DOF'; //  Campo que mostrará la clase de cambio en caso de que la moneda sea extranjera. Anadido a peticion de Roberto Mangas
            
                $data['FOREIGN_AMOUNT'] = $total_iva_16;                   // Monto de la póliza en dólares
                $data['MONETARY_AMOUNT'] = $total_iva_16 * $tasa_cambio;   // Monto de la póliza convertido a pesos mexicano.
            }            

            $SQL_STMT = $this->ArrayToInsert('PS_MTI_JGNO_GL_TBL', $data);
            $this->ExecuteSQL($SQL_STMT);
        }
        
        // Crea linea de IVA - En caso que haya IVA
        if ($total_iva_11 > 0) {
         
            $data = $this->GetMTI_JGNO_GL_TBLDefaultLine();
            $data['BUSINESS_UNIT'] = $comprobacion->BUSINESS_UNIT;                      // Empresa
            $data['TRANSACTION_ID'] = $comprobacion->TRAMITE_ID;                        // ID del tramite
            $data['TRANSACTION_LINE'] = $contador;                                      // Numero de la linea actual,
            $data['ACCOUNTING_DT'] = $comprobacion->FECHA_ORACLE;                       // Fecha de la autorizacion
            $data['BUSINESS_UNIT_GL'] =  $comprobacion->BUSINESS_UNIT;                  // Empresa
            $data['FISCAL_YEAR'] = $comprobacion->ANO_CONTABLE;                         // Año actual
            $data['ACCOUNTING_PERIOD'] = $comprobacion->PERIODO_CONTABLE;               // Mes actual
            $data['ACCOUNT'] = $comprobacion->CUENTA_IVA_11_COMPROBACIONES;             // Cuenta contable del IVA
            $data['DEPTID'] =  $comprobacion->ID_DEPTO;                                 // Departamento
            $data['MONETARY_AMOUNT'] = $total_iva_11;                                   // Total de IVA de la poliza
            $data['LINE_DESCR'] = 'COMPROBACION GASTO - ' . $comprobacion->TRAMITE_ID;  // Descripcion            
            $contador = $contador + 1;
            
            if(!empty($comprobacion->MONEDA) && $comprobacion->MONEDA!=''){
                
                $data['CURRENCY_CD'] = "MXP";                            // Fijo to MXP
                $data['FOREIGN_CURRENCY'] = $comprobacion->MONEDA;       // Moneda
                $data['RT_TYPE'] = 'DOF'; //  Campo que mostrará la clase de cambio en caso de que la moneda sea extranjera. Anadido a peticion de Roberto Mangas
            
                $data['FOREIGN_AMOUNT'] = $total_iva_11;                   // Monto de la póliza en dólares
                $data['MONETARY_AMOUNT'] = $total_iva_11 * $tasa_cambio;   // Monto de la póliza convertido a pesos mexicano.                
             }            

            $SQL_STMT = $this->ArrayToInsert('PS_MTI_JGNO_GL_TBL', $data);
            $this->ExecuteSQL($SQL_STMT);
        }        

        // Linea de deudores (Este es un abono, por lo que el monto es negativo)
        $data = $this->GetMTI_JGNO_GL_TBLDefaultLine();
        $data['BUSINESS_UNIT'] = $comprobacion->BUSINESS_UNIT;                      // Empresa
        $data['TRANSACTION_ID'] = $comprobacion->TRAMITE_ID;                        // ID del tramite
        $data['TRANSACTION_LINE'] = $contador;                                      // Numero de la linea actual,
        $data['ACCOUNTING_DT'] = $comprobacion->FECHA_ORACLE;                       // Fecha de la autorizacion
        $data['BUSINESS_UNIT_GL'] =  $comprobacion->BUSINESS_UNIT;                  // Empresa
        $data['FISCAL_YEAR'] = $comprobacion->ANO_CONTABLE;                         // Año actual
        $data['ACCOUNTING_PERIOD'] = $comprobacion->PERIODO_CONTABLE;               // Mes actual
        $data['ACCOUNT'] = $comprobacion->CUENTA_DEUDORES;                          // Cuenta contable de deudores
        $data['PRODUCT'] = $comprobacion->PRODUCTO_DEUDORES;                        // Producto de los deudores  
        $data['DEPTID'] =  $comprobacion->DEPARTAMENTO_DEUDORES;                    // Departamento de deudores
        # Importante: Debe ser negativo para que cuadre la poliza        
        $data['MONETARY_AMOUNT'] = -$total_monto_total;                             // Total de la poliza en negativo (abono a deudores)  
        $data['LINE_DESCR'] = 'COMPROBACION GASTO - ' . $comprobacion->TRAMITE_ID;  // Descripcion        
        $contador = $contador + 1;
        
        if(!empty($comprobacion->MONEDA) && $comprobacion->MONEDA!=''){
            
            $data['CURRENCY_CD'] = "MXP";                            // Fijo to MXP
            $data['FOREIGN_CURRENCY'] = $comprobacion->MONEDA;       // Moneda
            $data['RT_TYPE'] = 'DOF'; //  Campo que mostrará la clase de cambio en caso de que la moneda sea extranjera. Anadido a peticion de Roberto Mangas
        
            # Importante: Debe ser negativo para que cuadre la poliza        
            $data['FOREIGN_AMOUNT'] = -$total_monto_total;                   // Monto de la póliza en dólares
            $data['MONETARY_AMOUNT'] = -($total_monto_total * $tasa_cambio);   // Monto de la póliza convertido a pesos mexicano.                          
        }        

        $SQL_STMT = $this->ArrayToInsert('PS_MTI_JGNO_GL_TBL', $data);
        $this->ExecuteSQL($SQL_STMT);
    }   
    
    // Guarda un comprobacion en el ERP - GL
	public function Registra_ReembolsoAmex($comprobacion){
        $this->Registra_ComprobacionAmex($comprobacion);        
    }       
    
    // Guarda un comprobacion en el ERP
	public function Registra_ReembolsoCajaChica($comprobacion){
		$this->Registra_Comprobacion($comprobacion);   
    }     
    
    // Guarda un comprobacion en el ERP
	public function TraeStatusAnticipo($anticipo_id){
		//return 1,2,3
        
    }   
    
    public function LimpiaBase(){
        $this->ExecuteSQL('DELETE FROM PS_MTI_VCHD_AP_TBL');  
        $this->ExecuteSQL('DELETE FROM PS_MTI_VCLN_AP_TBL'); 
        $this->ExecuteSQL('DELETE FROM PS_MTI_VCDS_AP_TBL'); 
        $this->ExecuteSQL('DELETE FROM PS_MTI_JGNO_GL_TBL'); 
    }
    
    private function DebugInsert($datos){      
        
        $values = '-- ';  
        foreach ($datos as $key => $value){
            if (($value != '') and ($value != ' ') and ($value != '0') and ($value != '0.0') and ($value != 'SYSDATE') and ($value != 'MASNGEXPENSES')){
                //error_log(sprintf("-- %s = %s", $key, $value));
                $values = $values . sprintf("%s = %s | ", $key, $value);
            }
        }      
        
        //error_log($values);  
        global $ORACLE_QUERIES;
        if(isset($ORACLE_QUERIES)){
            if(strlen($ORACLE_QUERIES)==0){
                $ORACLE_QUERIES = $values . ";";
            } else {
                $ORACLE_QUERIES = $ORACLE_QUERIES . "\n" . $values . ";";
            }
        }        
    }
    
    
    // Funcion auxiliar para convertir los datos del arreglo en un INSERT de SQL
    private function ArrayToInsert($tabla, $datos){
        
        $fields = '';
        $values = '';
        foreach ($datos as $key => $value){
            if (!is_numeric($value) &&  $value == ' ') {
                $value = '\' \'';
            } else if (!is_numeric($value) && $value!='SYSDATE') {
                $value = '\'' . $value .  '\'';
            } else if ($key == "VENDOR_ID"){
                $value = '\'' . $value .  '\'';              
            }
            
            $fields = $fields . $key . ','; 
            $values = $values . $value . ',';
        }
        
        // le quita la ultima coma
        $fields = substr($fields,0,-1);
        $values = substr($values,0,-1);
        $this->DebugInsert($datos);
        
        $sql_stmt = sprintf("INSERT INTO %s ( %s ) VALUES ( %s )", $tabla, $fields, $values);        
        return $sql_stmt;
    }    
        
    //------------------------------------------------------------------
    //
    //      AP
    //
    //------------------------------------------------------------------          
        
    // Crea el registro con los valores por defecto para la tabla: PS_MTI_VCHD_AP_TBL
    private function GetPS_MTI_VCHD_AP_TBLDefaultLine(){
        $data = Array();  
        $data['BUSINESS_UNIT'] = ' '; // BUSINESS_UNIT VARCHAR2(5) NOT NULL,
        $data['VCHR_BLD_KEY_C1'] = ' '; // VCHR_BLD_KEY_C1 VARCHAR2(25) NOT NULL,
        $data['VCHR_BLD_KEY_C2'] = ' '; // VCHR_BLD_KEY_C2 VARCHAR2(25) NOT NULL,
        $data['VCHR_BLD_KEY_N1'] = '0.00'; // VCHR_BLD_KEY_N1 DECIMAL(10) NOT NULL,
        $data['VCHR_BLD_KEY_N2'] = '0.00'; // VCHR_BLD_KEY_N2 DECIMAL(10) NOT NULL,
        $data['VOUCHER_ID'] = ' '; // VOUCHER_ID VARCHAR2(8) NOT NULL,
        $data['VOUCHER_STYLE'] = ' '; // VOUCHER_STYLE VARCHAR2(4) NOT NULL,
        $data['INVOICE_ID'] = ' '; // INVOICE_ID VARCHAR2(30) NOT NULL,
        $data['INVOICE_DT'] = 'SYSDATE'; // INVOICE_DT DATE NOT NULL,
        $data['VENDOR_SETID'] = ' '; // VENDOR_SETID VARCHAR2(5) NOT NULL,
        $data['VENDOR_ID'] = ' '; // VENDOR_ID VARCHAR2(10) NOT NULL,
        $data['VNDR_LOC'] = ' '; // VNDR_LOC VARCHAR2(10) NOT NULL,
        $data['ADDRESS_SEQ_NUM'] = '0'; // ADDRESS_SEQ_NUM SMALLINT NOT NULL,
        $data['GRP_AP_ID'] = ' '; // GRP_AP_ID VARCHAR2(10) NOT NULL,
        $data['ORIGIN'] = ' '; // ORIGIN VARCHAR2(3) NOT NULL,
        $data['OPRID'] = ' '; // OPRID VARCHAR2(30) NOT NULL,
        $data['ACCOUNTING_DT'] = 'SYSDATE'; // ACCOUNTING_DT DATE,
        $data['POST_VOUCHER'] = ' '; // POST_VOUCHER VARCHAR2(1) NOT NULL,
        $data['DST_CNTRL_ID'] = ' '; // DST_CNTRL_ID VARCHAR2(10) NOT NULL,
        $data['VOUCHER_ID_RELATED'] = ' '; // VOUCHER_ID_RELATED VARCHAR2(8) NOT NULL,
        $data['GROSS_AMT'] = '0.00'; // GROSS_AMT DECIMAL(26, 3) NOT NULL,
        $data['DSCNT_AMT'] = '0.00'; // DSCNT_AMT DECIMAL(26, 3) NOT NULL,
        $data['TAX_EXEMPT'] = 'N'; // TAX_EXEMPT VARCHAR2(1) NOT NULL,
        $data['SALETX_AMT'] = '0.00'; // SALETX_AMT DECIMAL(26, 3) NOT NULL,
        $data['FREIGHT_AMT'] = '0.00'; // FREIGHT_AMT DECIMAL(26, 3) NOT NULL,
        $data['MISC_AMT'] = '0.00'; // MISC_AMT DECIMAL(26, 3) NOT NULL,
        $data['PYMNT_TERMS_CD'] = ' '; // PYMNT_TERMS_CD VARCHAR2(5) NOT NULL,
        $data['ENTERED_DT'] = 'SYSDATE'; // ENTERED_DT DATE,
        $data['TXN_CURRENCY_CD'] = 'MXP'; // TXN_CURRENCY_CD VARCHAR2(3) NOT NULL,
        $data['RT_TYPE'] = ' '; // RT_TYPE VARCHAR2(5) NOT NULL,
        $data['RATE_MULT'] = '0.00'; // RATE_MULT DECIMAL(15, 8) NOT NULL,
        $data['RATE_DIV'] = '0.00'; // RATE_DIV DECIMAL(15, 8) NOT NULL,
        $data['VAT_ENTRD_AMT'] = '0.00'; // VAT_ENTRD_AMT DECIMAL(26, 3) NOT NULL,
        $data['MATCH_ACTION'] = ' '; // MATCH_ACTION VARCHAR2(1) NOT NULL,
        $data['CUR_RT_SOURCE'] = ' '; // CUR_RT_SOURCE VARCHAR2(1) NOT NULL,
        $data['DSCNT_AMT_FLG'] = ' '; // DSCNT_AMT_FLG VARCHAR2(1) NOT NULL,
        $data['DUE_DT_FLG'] = ' '; // DUE_DT_FLG VARCHAR2(1) NOT NULL,
        $data['VCHR_APPRVL_FLG'] = ' '; // VCHR_APPRVL_FLG VARCHAR2(1) NOT NULL,
        $data['BUSPROCNAME'] = ' '; // BUSPROCNAME VARCHAR2(30) NOT NULL,
        $data['APPR_RULE_SET'] = ' '; // APPR_RULE_SET VARCHAR2(30) NOT NULL,
        $data['VAT_DCLRTN_POINT'] = ' '; // VAT_DCLRTN_POINT VARCHAR2(1) NOT NULL,
        $data['VAT_CALC_TYPE'] = ' '; // VAT_CALC_TYPE VARCHAR2(1) NOT NULL,
        $data['VAT_CALC_GROSS_NET'] = ' '; // VAT_CALC_GROSS_NET VARCHAR2(1) NOT NULL,
        $data['VAT_RECALC_FLG'] = ' '; // VAT_RECALC_FLG VARCHAR2(1) NOT NULL,
        $data['VAT_CALC_FRGHT_FLG'] = ' '; // VAT_CALC_FRGHT_FLG VARCHAR2(1) NOT NULL,
        $data['VAT_TREATMENT_GRP'] = ' '; // VAT_TREATMENT_GRP VARCHAR2(4) NOT NULL,
        $data['COUNTRY_SHIP_FROM'] = ' '; // COUNTRY_SHIP_FROM VARCHAR2(3) NOT NULL,
        $data['STATE_SHIP_FROM'] = ' '; // STATE_SHIP_FROM VARCHAR2(6) NOT NULL,
        $data['COUNTRY_SHIP_TO'] = ' '; // COUNTRY_SHIP_TO VARCHAR2(3) NOT NULL,
        $data['STATE_SHIP_TO'] = ' '; // STATE_SHIP_TO VARCHAR2(6) NOT NULL,
        $data['COUNTRY_VAT_BILLFR'] = ' '; // COUNTRY_VAT_BILLFR VARCHAR2(3) NOT NULL,
        $data['COUNTRY_VAT_BILLTO'] = ' '; // COUNTRY_VAT_BILLTO VARCHAR2(3) NOT NULL,
        $data['VAT_EXCPTN_CERTIF'] = ' '; // VAT_EXCPTN_CERTIF VARCHAR2(20) NOT NULL,
        $data['VAT_ROUND_RULE'] = ' '; // VAT_ROUND_RULE VARCHAR2(1) NOT NULL,
        $data['COUNTRY_LOC_SELLER'] = ' '; // COUNTRY_LOC_SELLER VARCHAR2(3) NOT NULL,
        $data['STATE_LOC_SELLER'] = ' '; // STATE_LOC_SELLER VARCHAR2(6) NOT NULL,
        $data['COUNTRY_LOC_BUYER'] = ' '; // COUNTRY_LOC_BUYER VARCHAR2(3) NOT NULL,
        $data['STATE_LOC_BUYER'] = ' '; // STATE_LOC_BUYER VARCHAR2(6) NOT NULL,
        $data['COUNTRY_VAT_SUPPLY'] = ' '; // COUNTRY_VAT_SUPPLY VARCHAR2(3) NOT NULL,
        $data['STATE_VAT_SUPPLY'] = ' '; // STATE_VAT_SUPPLY VARCHAR2(6) NOT NULL,
        $data['COUNTRY_VAT_PERFRM'] = ' '; // COUNTRY_VAT_PERFRM VARCHAR2(3) NOT NULL,
        $data['STATE_VAT_PERFRM'] = ' '; // STATE_VAT_PERFRM VARCHAR2(6) NOT NULL,
        $data['STATE_VAT_DEFAULT'] = ' '; // STATE_VAT_DEFAULT VARCHAR2(6) NOT NULL,
        $data['PREPAID_REF'] = ' '; // PREPAID_REF VARCHAR2(10) NOT NULL,
        $data['PREPAID_AUTO_APPLY'] = ' '; // PREPAID_AUTO_APPLY VARCHAR2(1) NOT NULL,
        $data['DESCR254_MIXED'] = ' '; // DESCR254_MIXED VARCHAR2(254) NOT NULL,
        $data['EIN_FEDERAL'] = ' '; // EIN_FEDERAL VARCHAR2(9) NOT NULL,
        $data['EIN_STATE_LOCAL'] = ' '; // EIN_STATE_LOCAL VARCHAR2(20) NOT NULL,
        $data['PROCESS_INSTANCE'] = '0.00'; // PROCESS_INSTANCE DECIMAL(10) NOT NULL,
        $data['IN_PROCESS_FLG'] = ' '; // IN_PROCESS_FLG VARCHAR2(1) NOT NULL,
        $data['BUSINESS_UNIT_PO'] = ' '; // BUSINESS_UNIT_PO VARCHAR2(5) NOT NULL,
        $data['PO_ID'] = ' '; // PO_ID VARCHAR2(10) NOT NULL,
        $data['PACKSLIP_NO'] = ' '; // PACKSLIP_NO VARCHAR2(22) NOT NULL,
        $data['PAY_TRM_BSE_DT_OPT'] = ' '; // PAY_TRM_BSE_DT_OPT VARCHAR2(1) NOT NULL,
        $data['VAT_CALC_MISC_FLG'] = ' '; // VAT_CALC_MISC_FLG VARCHAR2(1) NOT NULL,
        $data['IMAGE_REF_ID'] = ' '; // IMAGE_REF_ID VARCHAR2(12) NOT NULL,
        $data['IMAGE_DATE'] = 'SYSDATE'; // IMAGE_DATE DATE,
        $data['PAY_SCHEDULE_TYPE'] = ' '; // PAY_SCHEDULE_TYPE VARCHAR2(3) NOT NULL,
        $data['TAX_GRP'] = ' '; // TAX_GRP VARCHAR2(4) NOT NULL,
        $data['TAX_PYMNT_TYPE'] = ' '; // TAX_PYMNT_TYPE VARCHAR2(5) NOT NULL,
        $data['INSPECT_DT'] = 'SYSDATE'; // INSPECT_DT DATE,
        $data['INV_RECPT_DT'] = 'SYSDATE'; // INV_RECPT_DT DATE,
        $data['RECEIPT_DT'] = 'SYSDATE'; // RECEIPT_DT DATE,
        $data['BILL_OF_LADING'] = ' '; // BILL_OF_LADING VARCHAR2(30) NOT NULL,
        $data['CARRIER_ID'] = ' '; // CARRIER_ID VARCHAR2(10) NOT NULL,
        $data['DOC_TYPE'] = ' '; // DOC_TYPE VARCHAR2(8) NOT NULL,
        $data['DSCNT_DUE_DT'] = 'SYSDATE'; // DSCNT_DUE_DT DATE,
        $data['DSCNT_PRORATE_FLG'] = ' '; // DSCNT_PRORATE_FLG VARCHAR2(1) NOT NULL,
        $data['DUE_DT'] = 'SYSDATE'; // DUE_DT DATE,
        $data['ECQUEUEINSTANCE'] = '0'; // ECQUEUEINSTANCE INTEGER NOT NULL,
        $data['ECTRANSID'] = ' '; // ECTRANSID VARCHAR2(15) NOT NULL,
        $data['FRGHT_CHARGE_CODE'] = ' '; // FRGHT_CHARGE_CODE VARCHAR2(10) NOT NULL,
        $data['LC_ID'] = ' '; // LC_ID VARCHAR2(12) NOT NULL,
        $data['MISC_CHARGE_CODE'] = ' '; // MISC_CHARGE_CODE VARCHAR2(10) NOT NULL,
        $data['REMIT_ADDR_SEQ_NUM'] = '0'; // REMIT_ADDR_SEQ_NUM SMALLINT NOT NULL,
        $data['SALETX_CHARGE_CODE'] = ' '; // SALETX_CHARGE_CODE VARCHAR2(10) NOT NULL,
        $data['VCHR_BLD_CODE'] = ' '; // VCHR_BLD_CODE VARCHAR2(6) NOT NULL,
        $data['BUSINESS_UNIT_AR'] = ' '; // BUSINESS_UNIT_AR VARCHAR2(5) NOT NULL,
        $data['CUST_ID'] = ' '; // CUST_ID VARCHAR2(15) NOT NULL,
        $data['ITEM'] = ' '; // ITEM VARCHAR2(30) NOT NULL,
        $data['ITEM_LINE'] = '0'; // ITEM_LINE INTEGER NOT NULL,
        $data['VCHR_SRC'] = ' '; // VCHR_SRC VARCHAR2(4) NOT NULL,
        $data['VAT_EXCPTN_TYPE'] = ' '; // VAT_EXCPTN_TYPE VARCHAR2(1) NOT NULL,
        $data['USER_VCHR_CHAR1'] = ' '; // USER_VCHR_CHAR1 VARCHAR2(1) NOT NULL,
        $data['USER_VCHR_CHAR2'] = ' '; // USER_VCHR_CHAR2 VARCHAR2(1) NOT NULL,
        $data['USER_VCHR_DEC'] = '0.00'; // USER_VCHR_DEC DECIMAL(26, 3) NOT NULL,
        $data['USER_VCHR_DATE'] = 'SYSDATE'; // USER_VCHR_DATE DATE,
        $data['USER_VCHR_NUM1'] = '0'; // USER_VCHR_NUM1 SMALLINT NOT NULL,
        $data['USER_HDR_CHAR1'] = ' '; // USER_HDR_CHAR1 VARCHAR2(1) NOT NULL,
        $data['LASTUPDOPRID'] = ' '; // LASTUPDOPRID VARCHAR2(30) NOT NULL,
        $data['LASTUPDDTTM'] = 'SYSDATE'; // LASTUPDDTTM DATE,
        $data['OPRID_ENTERED_BY'] = 'MASNGEXPENSES'; // OPRID_ENTERED_BY VARCHAR2(30) NOT NULL,
        $data['CREATE_DTTM'] = 'SYSDATE'; // CREATE_DTTM DATE,
        $data['PROCESSED_FLG'] = ' '; // PROCESSED_FLG VARCHAR2(1) NOT NULL

        return $data;
    }      
    
    // Crea el registro con los valores por defecto para la tabla: PS_MTI_VCLN_AP_TBL
    private function GetPS_MTI_VCLN_AP_TBLDefaultLine(){
        $data = Array();  
        $data['BUSINESS_UNIT'] = ' '; // BUSINESS_UNIT VARCHAR2(5) NOT NULL,
        $data['VCHR_BLD_KEY_C1'] = ' '; // VCHR_BLD_KEY_C1 VARCHAR2(25) NOT NULL,
        $data['VCHR_BLD_KEY_C2'] = ' '; // VCHR_BLD_KEY_C2 VARCHAR2(25) NOT NULL,
        $data['VCHR_BLD_KEY_N1'] = '0.00'; // VCHR_BLD_KEY_N1 DECIMAL(10) NOT NULL,
        $data['VCHR_BLD_KEY_N2'] = '0.00'; // VCHR_BLD_KEY_N2 DECIMAL(10) NOT NULL,
        $data['VOUCHER_ID'] = ' '; // VOUCHER_ID VARCHAR2(8) NOT NULL,
        $data['VOUCHER_LINE_NUM'] = '0'; // VOUCHER_LINE_NUM INTEGER NOT NULL,
        $data['BUSINESS_UNIT_PO'] = ' '; // BUSINESS_UNIT_PO VARCHAR2(5) NOT NULL,
        $data['PO_ID'] = ' '; // PO_ID VARCHAR2(10) NOT NULL,
        $data['LINE_NBR'] = '0'; // LINE_NBR INTEGER NOT NULL,
        $data['SCHED_NBR'] = '0'; // SCHED_NBR SMALLINT NOT NULL,
        $data['DESCR'] = ' '; // DESCR VARCHAR2(30) NOT NULL,
        $data['MERCHANDISE_AMT'] = '0.00'; // MERCHANDISE_AMT DECIMAL(26, 3) NOT NULL,
        $data['ITM_SETID'] = ' '; // ITM_SETID VARCHAR2(5) NOT NULL,
        $data['INV_ITEM_ID'] = ' '; // INV_ITEM_ID VARCHAR2(18) NOT NULL,
        $data['QTY_VCHR'] = '0.00'; // QTY_VCHR DECIMAL(15, 4) NOT NULL,
        $data['STATISTIC_AMOUNT'] = '0.00'; // STATISTIC_AMOUNT DECIMAL(15, 2) NOT NULL,
        $data['UNIT_OF_MEASURE'] = ' '; // UNIT_OF_MEASURE VARCHAR2(3) NOT NULL,
        $data['UNIT_PRICE'] = '0.00'; // UNIT_PRICE DECIMAL(15, 5) NOT NULL,
        $data['DSCNT_APPL_FLG'] = ' '; // DSCNT_APPL_FLG VARCHAR2(1) NOT NULL,
        $data['TAX_CD_VAT'] = ' '; // TAX_CD_VAT VARCHAR2(8) NOT NULL,
        $data['BUSINESS_UNIT_RECV'] = ' '; // BUSINESS_UNIT_RECV VARCHAR2(5) NOT NULL,
        $data['RECEIVER_ID'] = ' '; // RECEIVER_ID VARCHAR2(10) NOT NULL,
        $data['RECV_LN_NBR'] = '0'; // RECV_LN_NBR INTEGER NOT NULL,
        $data['RECV_SHIP_SEQ_NBR'] = '0'; // RECV_SHIP_SEQ_NBR SMALLINT NOT NULL,
        $data['MATCH_LINE_OPT'] = ' '; // MATCH_LINE_OPT VARCHAR2(1) NOT NULL,
        $data['DISTRIB_MTHD_FLG'] = ' '; // DISTRIB_MTHD_FLG VARCHAR2(1) NOT NULL,
        $data['SHIPTO_ID'] = ' '; // SHIPTO_ID VARCHAR2(10) NOT NULL,
        $data['SUT_BASE_ID'] = ' '; // SUT_BASE_ID VARCHAR2(10) NOT NULL,
        $data['TAX_CD_SUT'] = ' '; // TAX_CD_SUT VARCHAR2(8) NOT NULL,
        $data['ULTIMATE_USE_CD'] = ' '; // ULTIMATE_USE_CD VARCHAR2(8) NOT NULL,
        $data['SUT_EXCPTN_TYPE'] = ' '; // SUT_EXCPTN_TYPE VARCHAR2(1) NOT NULL,
        $data['SUT_EXCPTN_CERTIF'] = ' '; // SUT_EXCPTN_CERTIF VARCHAR2(20) NOT NULL,
        $data['SUT_APPLICABILITY'] = ' '; // SUT_APPLICABILITY VARCHAR2(1) NOT NULL,
        $data['VAT_APPLICABILITY'] = ' '; // VAT_APPLICABILITY VARCHAR2(1) NOT NULL,
        $data['VAT_TXN_TYPE_CD'] = ' '; // VAT_TXN_TYPE_CD VARCHAR2(4) NOT NULL,
        $data['VAT_USE_ID'] = ' '; // VAT_USE_ID VARCHAR2(6) NOT NULL,
        $data['ADDR_SEQ_NUM_SHIP'] = '0'; // ADDR_SEQ_NUM_SHIP SMALLINT NOT NULL,
        $data['BUS_UNIT_RELATED'] = ' '; // BUS_UNIT_RELATED VARCHAR2(5) NOT NULL,
        $data['VOUCHER_ID_RELATED'] = ' '; // VOUCHER_ID_RELATED VARCHAR2(8) NOT NULL,
        $data['VENDOR_ID'] = ' '; // VENDOR_ID VARCHAR2(10) NOT NULL,
        $data['VNDR_LOC'] = ' '; // VNDR_LOC VARCHAR2(10) NOT NULL,
        $data['DESCR254_MIXED'] = ' '; // DESCR254_MIXED VARCHAR2(254) NOT NULL,
        $data['SPEEDCHART_KEY'] = ' '; // SPEEDCHART_KEY VARCHAR2(10) NOT NULL,
        $data['BUSINESS_UNIT_GL'] = ' '; // BUSINESS_UNIT_GL VARCHAR2(5) NOT NULL,
        $data['ACCOUNT'] = ' '; // ACCOUNT VARCHAR2(10) NOT NULL,
        $data['ALTACCT'] = ' '; // ALTACCT VARCHAR2(10) NOT NULL,
        $data['OPERATING_UNIT'] = ' '; // OPERATING_UNIT VARCHAR2(8) NOT NULL,
        $data['PRODUCT'] = ' '; // PRODUCT VARCHAR2(6) NOT NULL,
        $data['FUND_CODE'] = ' '; // FUND_CODE VARCHAR2(5) NOT NULL,
        $data['CLASS_FLD'] = ' '; // CLASS_FLD VARCHAR2(5) NOT NULL,
        $data['PROGRAM_CODE'] = ' '; // PROGRAM_CODE VARCHAR2(5) NOT NULL,
        $data['BUDGET_REF'] = ' '; // BUDGET_REF VARCHAR2(8) NOT NULL,
        $data['AFFILIATE'] = ' '; // AFFILIATE VARCHAR2(5) NOT NULL,
        $data['AFFILIATE_INTRA1'] = ' '; // AFFILIATE_INTRA1 VARCHAR2(10) NOT NULL,
        $data['AFFILIATE_INTRA2'] = ' '; // AFFILIATE_INTRA2 VARCHAR2(10) NOT NULL,
        $data['CHARTFIELD1'] = ' '; // CHARTFIELD1 VARCHAR2(10) NOT NULL,
        $data['CHARTFIELD2'] = ' '; // CHARTFIELD2 VARCHAR2(10) NOT NULL,
        $data['CHARTFIELD3'] = ' '; // CHARTFIELD3 VARCHAR2(10) NOT NULL,
        $data['DEPTID'] = ' '; // DEPTID VARCHAR2(10) NOT NULL,
        $data['PROJECT_ID'] = ' '; // PROJECT_ID VARCHAR2(15) NOT NULL,
        $data['ECQUEUEINSTANCE'] = '0'; // ECQUEUEINSTANCE INTEGER NOT NULL,
        $data['ECTRANSID'] = ' '; // ECTRANSID VARCHAR2(15) NOT NULL,
        $data['TAX_DSCNT_FLG'] = ' '; // TAX_DSCNT_FLG VARCHAR2(1) NOT NULL,
        $data['TAX_FRGHT_FLG'] = ' '; // TAX_FRGHT_FLG VARCHAR2(1) NOT NULL,
        $data['TAX_MISC_FLG'] = ' '; // TAX_MISC_FLG VARCHAR2(1) NOT NULL,
        $data['TAX_VAT_FLG'] = ' '; // TAX_VAT_FLG VARCHAR2(1) NOT NULL,
        $data['PHYSICAL_NATURE'] = ' '; // PHYSICAL_NATURE VARCHAR2(1) NOT NULL,
        $data['VAT_RCRD_INPT_FLG'] = ' '; // VAT_RCRD_INPT_FLG VARCHAR2(1) NOT NULL,
        $data['VAT_RCRD_OUTPT_FLG'] = ' '; // VAT_RCRD_OUTPT_FLG VARCHAR2(1) NOT NULL,
        $data['VAT_TREATMENT'] = ' '; // VAT_TREATMENT VARCHAR2(4) NOT NULL,
        $data['VAT_SVC_SUPPLY_FLG'] = ' '; // VAT_SVC_SUPPLY_FLG VARCHAR2(1) NOT NULL,
        $data['VAT_SERVICE_TYPE'] = ' '; // VAT_SERVICE_TYPE VARCHAR2(1) NOT NULL,
        $data['COUNTRY_LOC_BUYER'] = ' '; // COUNTRY_LOC_BUYER VARCHAR2(3) NOT NULL,
        $data['STATE_LOC_BUYER'] = ' '; // STATE_LOC_BUYER VARCHAR2(6) NOT NULL,
        $data['COUNTRY_LOC_SELLER'] = ' '; // COUNTRY_LOC_SELLER VARCHAR2(3) NOT NULL,
        $data['STATE_LOC_SELLER'] = ' '; // STATE_LOC_SELLER VARCHAR2(6) NOT NULL,
        $data['COUNTRY_VAT_SUPPLY'] = ' '; // COUNTRY_VAT_SUPPLY VARCHAR2(3) NOT NULL,
        $data['STATE_VAT_SUPPLY'] = ' '; // STATE_VAT_SUPPLY VARCHAR2(6) NOT NULL,
        $data['COUNTRY_VAT_PERFRM'] = ' '; // COUNTRY_VAT_PERFRM VARCHAR2(3) NOT NULL,
        $data['STATE_VAT_PERFRM'] = ' '; // STATE_VAT_PERFRM VARCHAR2(6) NOT NULL,
        $data['STATE_SHIP_FROM'] = ' '; // STATE_SHIP_FROM VARCHAR2(6) NOT NULL,
        $data['STATE_VAT_DEFAULT'] = ' '; // STATE_VAT_DEFAULT VARCHAR2(6) NOT NULL,
        $data['REQUESTOR_ID'] = ' '; // REQUESTOR_ID VARCHAR2(30) NOT NULL,
        $data['VAT_ENTRD_AMT'] = '0.00'; // VAT_ENTRD_AMT DECIMAL(26, 3) NOT NULL,
        $data['VAT_RECEIPT'] = ' '; // VAT_RECEIPT VARCHAR2(1) NOT NULL,
        $data['VAT_RGSTRN_SELLER'] = ' '; // VAT_RGSTRN_SELLER VARCHAR2(12) NOT NULL,
        $data['TRANS_DT'] = 'SYSDATE'; // TRANS_DT DATE,
        $data['USER_VCHR_CHAR1'] = ' '; // USER_VCHR_CHAR1 VARCHAR2(1) NOT NULL,
        $data['USER_VCHR_CHAR2'] = ' '; // USER_VCHR_CHAR2 VARCHAR2(1) NOT NULL,
        $data['USER_VCHR_DEC'] = '0.00'; // USER_VCHR_DEC DECIMAL(26, 3) NOT NULL,
        $data['USER_VCHR_DATE'] = 'SYSDATE'; // USER_VCHR_DATE DATE,
        $data['USER_VCHR_NUM1'] = '0'; // USER_VCHR_NUM1 SMALLINT NOT NULL,
        $data['USER_LINE_CHAR1'] = ' '; // USER_LINE_CHAR1 VARCHAR2(1) NOT NULL,
        $data['USER_SCHED_CHAR1'] = ' '; // USER_SCHED_CHAR1 VARCHAR2(1) NOT NULL,
        $data['WTHD_SW'] = ' '; // WTHD_SW VARCHAR2(1) NOT NULL,
        $data['WTHD_CD'] = ' '; // WTHD_CD VARCHAR2(5) NOT NULL,
        $data['LASTUPDOPRID'] = ' '; // LASTUPDOPRID VARCHAR2(30) NOT NULL,
        $data['LASTUPDDTTM'] = 'SYSDATE'; // LASTUPDDTTM DATE,
        $data['OPRID_ENTERED_BY'] = 'MASNGEXPENSES'; // OPRID_ENTERED_BY VARCHAR2(30) NOT NULL,
        $data['CREATED_DTTM'] = 'SYSDATE'; // CREATED_DTTM DATE,
        $data['PROCESSED_FLG'] = ' '; // PROCESSED_FLG VARCHAR2(1) NOT NULL,
        $data['ORIGIN'] = ' '; // ORIGIN VARCHAR2(3) NOT NULL
        
        return $data;        
        
    }
        
    // Crea el registro con los valores por defecto para la tabla: PS_MTI_VCDS_AP_TBL
    private function GetPS_MTI_VCDS_AP_TBLDefaultLine(){
        $data = Array();          
        $data['BUSINESS_UNIT'] = ' '; // BUSINESS_UNIT VARCHAR2(5) NOT NULL,
        $data['VCHR_BLD_KEY_C1'] = ' '; // VCHR_BLD_KEY_C1 VARCHAR2(25) NOT NULL,
        $data['VCHR_BLD_KEY_C2'] = ' '; // VCHR_BLD_KEY_C2 VARCHAR2(25) NOT NULL,
        $data['VCHR_BLD_KEY_N1'] = '0.00'; // VCHR_BLD_KEY_N1 DECIMAL(10) NOT NULL,
        $data['VCHR_BLD_KEY_N2'] = '0.00'; // VCHR_BLD_KEY_N2 DECIMAL(10) NOT NULL,
        $data['VOUCHER_ID'] = ' '; // VOUCHER_ID VARCHAR2(8) NOT NULL,
        $data['VOUCHER_LINE_NUM'] = '0'; // VOUCHER_LINE_NUM INTEGER NOT NULL,
        $data['DISTRIB_LINE_NUM'] = '0'; // DISTRIB_LINE_NUM INTEGER NOT NULL,
        $data['BUSINESS_UNIT_GL'] = ' '; // BUSINESS_UNIT_GL VARCHAR2(5) NOT NULL,
        $data['ACCOUNT'] = ' '; // ACCOUNT VARCHAR2(10) NOT NULL,
        $data['ALTACCT'] = ' '; // ALTACCT VARCHAR2(10) NOT NULL,
        $data['DEPTID'] = ' '; // DEPTID VARCHAR2(10) NOT NULL,
        $data['STATISTICS_CODE'] = ' '; // STATISTICS_CODE VARCHAR2(3) NOT NULL,
        $data['STATISTIC_AMOUNT'] = '0.00'; // STATISTIC_AMOUNT DECIMAL(15, 2) NOT NULL,
        $data['QTY_VCHR'] = '0.00'; // QTY_VCHR DECIMAL(15, 4) NOT NULL,
        $data['DESCR'] = ' '; // DESCR VARCHAR2(30) NOT NULL,
        $data['MERCHANDISE_AMT'] = '0.00'; // MERCHANDISE_AMT DECIMAL(26, 3) NOT NULL,
        $data['BUSINESS_UNIT_PO'] = ' '; // BUSINESS_UNIT_PO VARCHAR2(5) NOT NULL,
        $data['PO_ID'] = ' '; // PO_ID VARCHAR2(10) NOT NULL,
        $data['LINE_NBR'] = '0'; // LINE_NBR INTEGER NOT NULL,
        $data['SCHED_NBR'] = '0'; // SCHED_NBR SMALLINT NOT NULL,
        $data['PO_DIST_LINE_NUM'] = '0'; // PO_DIST_LINE_NUM INTEGER NOT NULL,
        $data['BUSINESS_UNIT_PC'] = ' '; // BUSINESS_UNIT_PC VARCHAR2(5) NOT NULL,
        $data['ACTIVITY_ID'] = ' '; // ACTIVITY_ID VARCHAR2(15) NOT NULL,
        $data['ANALYSIS_TYPE'] = ' '; // ANALYSIS_TYPE VARCHAR2(3) NOT NULL,
        $data['RESOURCE_TYPE'] = ' '; // RESOURCE_TYPE VARCHAR2(5) NOT NULL,
        $data['RESOURCE_CATEGORY'] = ' '; // RESOURCE_CATEGORY VARCHAR2(5) NOT NULL,
        $data['RESOURCE_SUB_CAT'] = ' '; // RESOURCE_SUB_CAT VARCHAR2(5) NOT NULL,
        $data['ASSET_FLG'] = ' '; // ASSET_FLG VARCHAR2(1) NOT NULL,
        $data['BUSINESS_UNIT_AM'] = ' '; // BUSINESS_UNIT_AM VARCHAR2(5) NOT NULL,
        $data['ASSET_ID'] = ' '; // ASSET_ID VARCHAR2(12) NOT NULL,
        $data['PROFILE_ID'] = ' '; // PROFILE_ID VARCHAR2(10) NOT NULL,
        $data['COST_TYPE'] = ' '; // COST_TYPE VARCHAR2(1) NOT NULL,
        $data['VAT_TXN_TYPE_CD'] = ' '; // VAT_TXN_TYPE_CD VARCHAR2(4) NOT NULL,
        $data['BUSINESS_UNIT_RECV'] = ' '; // BUSINESS_UNIT_RECV VARCHAR2(5) NOT NULL,
        $data['RECEIVER_ID'] = ' '; // RECEIVER_ID VARCHAR2(10) NOT NULL,
        $data['RECV_LN_NBR'] = '0'; // RECV_LN_NBR INTEGER NOT NULL,
        $data['RECV_SHIP_SEQ_NBR'] = '0'; // RECV_SHIP_SEQ_NBR SMALLINT NOT NULL,
        $data['RECV_DIST_LINE_NUM'] = '0'; // RECV_DIST_LINE_NUM INTEGER NOT NULL,
        $data['OPERATING_UNIT'] = ' '; // OPERATING_UNIT VARCHAR2(8) NOT NULL,
        $data['PRODUCT'] = ' '; // PRODUCT VARCHAR2(6) NOT NULL,
        $data['FUND_CODE'] = ' '; // FUND_CODE VARCHAR2(5) NOT NULL,
        $data['CLASS_FLD'] = ' '; // CLASS_FLD VARCHAR2(5) NOT NULL,
        $data['PROGRAM_CODE'] = ' '; // PROGRAM_CODE VARCHAR2(5) NOT NULL,
        $data['BUDGET_REF'] = ' '; // BUDGET_REF VARCHAR2(8) NOT NULL,
        $data['AFFILIATE'] = ' '; // AFFILIATE VARCHAR2(5) NOT NULL,
        $data['AFFILIATE_INTRA1'] = ' '; // AFFILIATE_INTRA1 VARCHAR2(10) NOT NULL,
        $data['AFFILIATE_INTRA2'] = ' '; // AFFILIATE_INTRA2 VARCHAR2(10) NOT NULL,
        $data['CHARTFIELD1'] = ' '; // CHARTFIELD1 VARCHAR2(10) NOT NULL,
        $data['CHARTFIELD2'] = ' '; // CHARTFIELD2 VARCHAR2(10) NOT NULL,
        $data['CHARTFIELD3'] = ' '; // CHARTFIELD3 VARCHAR2(10) NOT NULL,
        $data['PROJECT_ID'] = ' '; // PROJECT_ID VARCHAR2(15) NOT NULL,
        $data['BUDGET_DT'] = 'SYSDATE'; // BUDGET_DT DATE,
        $data['ENTRY_EVENT'] = ' '; // ENTRY_EVENT VARCHAR2(10) NOT NULL,
        $data['ECQUEUEINSTANCE'] = '0'; // ECQUEUEINSTANCE INTEGER NOT NULL,
        $data['ECTRANSID'] = ' '; // ECTRANSID VARCHAR2(15) NOT NULL,
        $data['JRNL_LN_REF'] = ' '; // JRNL_LN_REF VARCHAR2(10) NOT NULL,
        $data['VAT_APORT_CNTRL'] = ' '; // VAT_APORT_CNTRL VARCHAR2(1) NOT NULL,
        $data['USER_VCHR_CHAR1'] = ' '; // USER_VCHR_CHAR1 VARCHAR2(1) NOT NULL,
        $data['USER_VCHR_CHAR2'] = ' '; // USER_VCHR_CHAR2 VARCHAR2(1) NOT NULL,
        $data['USER_VCHR_DEC'] = '0.00'; // USER_VCHR_DEC DECIMAL(26, 3) NOT NULL,
        $data['USER_VCHR_DATE'] = 'SYSDATE'; // USER_VCHR_DATE DATE,
        $data['USER_VCHR_NUM1'] = '0'; // USER_VCHR_NUM1 SMALLINT NOT NULL,
        $data['USER_DIST_CHAR1'] = ' '; // USER_DIST_CHAR1 VARCHAR2(1) NOT NULL,
        $data['OPEN_ITEM_KEY'] = ' '; // OPEN_ITEM_KEY VARCHAR2(30) NOT NULL,
        $data['VAT_RECOVERY_PCT'] = '0.00'; // VAT_RECOVERY_PCT DECIMAL(5, 2) NOT NULL,
        $data['VAT_REBATE_PCT'] = '0.00'; // VAT_REBATE_PCT DECIMAL(5, 2) NOT NULL,
        $data['VAT_CALC_AMT'] = '0.00'; // VAT_CALC_AMT DECIMAL(26, 3) NOT NULL,
        $data['VAT_BASIS_AMT'] = '0.00'; // VAT_BASIS_AMT DECIMAL(26, 3) NOT NULL,
        $data['VAT_RCVRY_AMT'] = '0.00'; // VAT_RCVRY_AMT DECIMAL(26, 3) NOT NULL,
        $data['VAT_NRCVR_AMT'] = '0.00'; // VAT_NRCVR_AMT DECIMAL(26, 3) NOT NULL,
        $data['VAT_REBATE_AMT'] = '0.00'; // VAT_REBATE_AMT DECIMAL(26, 3) NOT NULL,
        $data['VAT_TRANS_AMT'] = '0.00'; // VAT_TRANS_AMT DECIMAL(26, 3) NOT NULL,
        $data['TAX_CD_VAT_PCT'] = '0.00'; // TAX_CD_VAT_PCT DECIMAL(7, 4) NOT NULL,
        $data['VAT_INV_AMT'] = '0.00'; // VAT_INV_AMT DECIMAL(26, 3) NOT NULL,
        $data['VAT_NONINV_AMT'] = '0.00'; // VAT_NONINV_AMT DECIMAL(26, 3) NOT NULL,
        $data['LASTUPDOPRID'] = ' '; // LASTUPDOPRID VARCHAR2(30) NOT NULL,
        $data['LASTUPDDTTM'] = 'SYSDATE'; // LASTUPDDTTM DATE,
        $data['OPRID_ENTERED_BY'] = 'MASNGEXPENSES'; // OPRID_ENTERED_BY VARCHAR2(30) NOT NULL,
        $data['CREATED_DTTM'] = 'SYSDATE'; // CREATED_DTTM DATE,
        $data['PROCESSED_FLG'] = ' '; // PROCESSED_FLG VARCHAR2(1) NOT NULL,
        $data['ORIGIN'] = ' '; // ORIGIN VARCHAR2(3) NOT NULL   
        return $data;
    }           
    
    //------------------------------------------------------------------
    //
    //      GL
    //
    //------------------------------------------------------------------    
    
    // Crea el registro con los valores por defecto para la tabla: MTI_JGNO_GL_TBL
    private function GetMTI_JGNO_GL_TBLDefaultLine(){
        $data = Array();  
        $data['BUSINESS_UNIT'] = ' '; // BUSINESS_UNIT VARCHAR2(5) NOT NULL,
        $data['TRANSACTION_ID'] = ' '; //    TRANSACTION_ID VARCHAR2(10) NOT NULL,
        $data['TRANSACTION_LINE'] = '0'; //    TRANSACTION_LINE SMALLINT NOT NULL,
        $data['LEDGER_GROUP'] = 'CONTABLE'; //    LEDGER_GROUP VARCHAR2(10) NOT NULL,
        $data['LEDGER'] = 'CONTABLE'; //    LEDGER VARCHAR2(10) NOT NULL,
        $data['ACCOUNTING_DT'] = 'SYSDATE'; //    ACCOUNTING_DT DATE NOT NULL,
        // Se cambio a solicitud de Roberto Manga, solia ser: GENERIC
        $data['APPL_JRNL_ID'] = 'EXP_GST'; //    APPL_JRNL_ID VARCHAR2(10) NOT NULL, 
        $data['BUSINESS_UNIT_GL'] = ' '; //    BUSINESS_UNIT_GL VARCHAR2(5) NOT NULL,
        $data['FISCAL_YEAR'] = '0'; //    FISCAL_YEAR SMALLINT NOT NULL,
        $data['ACCOUNTING_PERIOD'] = '0'; //    ACCOUNTING_PERIOD SMALLINT NOT NULL,
        $data['JOURNAL_ID'] = ' '; //    JOURNAL_ID VARCHAR2(10) NOT NULL,
        $data['JOURNAL_DATE'] = 'SYSDATE'; //    JOURNAL_DATE DATE,
        $data['JOURNAL_LINE'] = '0'; //    JOURNAL_LINE INTEGER NOT NULL,
        $data['ACCOUNT'] = ' '; //    ACCOUNT VARCHAR2(10) NOT NULL,
        $data['ALTACCT'] = ' '; //    ALTACCT VARCHAR2(10) NOT NULL,
        $data['OPERATING_UNIT'] = ' '; //    OPERATING_UNIT VARCHAR2(8) NOT NULL,
        $data['DEPTID'] = ' '; //    DEPTID VARCHAR2(10) NOT NULL,
        $data['PRODUCT'] = ' '; //    PRODUCT VARCHAR2(6) NOT NULL,
        $data['PROJECT_ID'] = ' '; //    PROJECT_ID VARCHAR2(15) NOT NULL,
        $data['AFFILIATE'] = ' '; //    AFFILIATE VARCHAR2(5) NOT NULL,
        $data['CURRENCY_CD'] = 'MXP'; //    CURRENCY_CD VARCHAR2(3) NOT NULL,
        $data['STATISTICS_CODE'] = ' '; //    STATISTICS_CODE VARCHAR2(3) NOT NULL,
        $data['FOREIGN_CURRENCY'] = 'MXP'; //    FOREIGN_CURRENCY VARCHAR2(3) NOT NULL,
        $data['RT_TYPE'] = ' '; //    RT_TYPE VARCHAR2(5) NOT NULL,
        $data['RATE_MULT'] = '0.00'; //    RATE_MULT DECIMAL(15, 8) NOT NULL,
        $data['RATE_DIV'] = '0.00'; //    RATE_DIV DECIMAL(15, 8) NOT NULL,
        $data['MONETARY_AMOUNT'] = '0.00'; //    MONETARY_AMOUNT DECIMAL(26, 3) NOT NULL,
        $data['FOREIGN_AMOUNT'] = '0.00'; //    FOREIGN_AMOUNT DECIMAL(26, 3) NOT NULL,
        $data['STATISTIC_AMOUNT'] = '0.00'; //    STATISTIC_AMOUNT DECIMAL(15, 2) NOT NULL,
        $data['MOVEMENT_FLAG'] = 'N'; //    MOVEMENT_FLAG VARCHAR2(1) NOT NULL,
        $data['DOC_TYPE'] = ' '; //    DOC_TYPE VARCHAR2(8) NOT NULL,
        $data['DOC_SEQ_NBR'] = ' '; //    DOC_SEQ_NBR VARCHAR2(12) NOT NULL,
        $data['DOC_SEQ_DATE'] = 'SYSDATE'; //    DOC_SEQ_DATE DATE,
        $data['LINE_DESCR'] = ' '; //    LINE_DESCR VARCHAR2(30) NOT NULL,
        $data['GL_DISTRIB_STATUS'] = 'N'; //    GL_DISTRIB_STATUS VARCHAR2(1) NOT NULL,
        $data['PROCESS_INSTANCE'] = '0.00'; //    PROCESS_INSTANCE DECIMAL(10) NOT NULL,
        $data['FUND_CODE'] = ' '; //    FUND_CODE VARCHAR2(5) NOT NULL,
        $data['CLASS_FLD'] = ' '; //    CLASS_FLD VARCHAR2(5) NOT NULL,
        $data['PROGRAM_CODE'] = ' '; //    PROGRAM_CODE VARCHAR2(5) NOT NULL,
        $data['BUDGET_REF'] = ' '; //    BUDGET_REF VARCHAR2(8) NOT NULL,
        $data['AFFILIATE_INTRA1'] = ' '; //    AFFILIATE_INTRA1 VARCHAR2(10) NOT NULL,
        $data['AFFILIATE_INTRA2'] = ' '; //    AFFILIATE_INTRA2 VARCHAR2(10) NOT NULL,
        $data['CHARTFIELD1'] = ' '; //    CHARTFIELD1 VARCHAR2(10) NOT NULL,
        $data['CHARTFIELD2'] = ' '; //    CHARTFIELD2 VARCHAR2(10) NOT NULL,
        $data['CHARTFIELD3'] = ' '; //    CHARTFIELD3 VARCHAR2(10) NOT NULL,
        $data['LASTUPDOPRID'] = ' '; //    LASTUPDOPRID VARCHAR2(30) NOT NULL,
        $data['LASTUPDDTTM'] = 'SYSDATE'; //    LASTUPDDTTM DATE,
        $data['OPRID_ENTERED_BY'] = 'MASNGEXPENSES'; //    OPRID_ENTERED_BY VARCHAR2(30) NOT NULL,
        $data['CREATED_DTTM'] = 'SYSDATE'; //    CREATED_DTTM DATE NOT NULL,
        $data['PROCESSED_FLG'] = ' '; //    PROCESSED_FLG VARCHAR2(1) NOT NULL,
        $data['ORIGIN'] = ' '; //    ORIGIN VARCHAR2(3) NOT NULL
        return $data;
    }      
 
}
?>
