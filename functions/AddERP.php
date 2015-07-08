<?

#Alimentar linea de comandos al ERP
#require_once "../lib/php/constantes.php";
#require_once "$RUTA_A/functions/ConexionERP.php";
#require_once "$RUTA_A/Connections/fwk_db.php";

// Funcion para calcular el no. de tramite
function getTramiteID($TRAMITE_ID)
{
    // tramite_id puede ser -1 cuando es un anticipo relacionado que no existe. ap
    if($TRAMITE_ID<0){
        return $TRAMITE_ID;
    }
    
    return $TRAMITE_ID; // + '280000'; // de 100,000 en adelante son los tramites de prueba
}

function GuardaAnticipos($valida = false)
{
        #Leer todas los anticipos sencillos cuyo STATUS_ERP = 1
        $VConn = new conexion();

        $VSql = "SELECT
                    *
                 FROM
                     solicitud_viaje
                         inner join tramites on (t_id = sv_tramite)
                         inner join usuario on (t_iniciador = u_id)
                         inner join empresas on (u_empresa = e_id)
                         inner join cat_cecos on (cc_id = sv_ceco_paga)
                 WHERE
                     sv_status_erp = 1";

        $VRes = $VConn->consultar($VSql);
        $Filas = mysql_num_rows($VRes);
        $Datos = mysql_fetch_assoc($VRes);
        
        #Por cada anticipo se guarda en el ERP
        $conn = new ConexionERP();
        $conn->Conectar();

        for($i=0; $i<$Filas; $i++)
        {
            $SOLICITUD_VIAJE = mysql_result($VRes,$i,"sv_id");;
            #leer los datos por cada fila que saque el query e insertar una fila por cada registro...
            $TRAMITE_ID = mysql_result($VRes,$i,"t_id");;
            $CVE_EMPRESA = mysql_result($VRes,$i,"e_codigo");
            $TOTAL = mysql_result($VRes,$i,"sv_anticipo");
            $MONEDA = mysql_result($VRes,$i,"sv_divisa");      
            $TASA = mysql_result($VRes,$i,"sv_tasa");      
            $CentroCosto = mysql_result($VRes,$i,"cc_centrocostos");
            $FechaAutorizacion = mysql_result($VRes,$i,"t_fecha_cierre");
            $IdUsuario = mysql_result($VRes,$i,"t_iniciador");
            $ID_PRODUCTO = mysql_result($VRes,$i,"u_producto");
            $ID_VENDOR = mysql_result($VRes,$i,"u_proveedor");

            $TRAMITE_ID = getTramiteID($TRAMITE_ID);

            #asignar datos a las variables del anticipo
            $Anticipo = new Anticipo();
            $Anticipo->TRAMITE_ID = $TRAMITE_ID;
            $Anticipo->ID_VENDOR = $ID_VENDOR;
            $Anticipo->BUSINESS_UNIT = $CVE_EMPRESA;
            $Anticipo->ID_DEPTO = $CentroCosto;
            $Anticipo->ID_USUARIO = $IdUsuario;
            $Anticipo->ID_PRODUCTO = $ID_PRODUCTO;
            $Anticipo->FECHA = $FechaAutorizacion;            
            $Anticipo->MONTO = $TOTAL;
            $Anticipo->MONEDA = $MONEDA;
            $Anticipo->TASA = $TASA;

            if($valida){
                global $VALIDACIONES;
                $VALIDACIONES = $VALIDACIONES . "Anticipo: $TRAMITE_ID\n";
                $Anticipo->Valida();
                $VALIDACIONES = $VALIDACIONES . "------------------------------\n";
                
            } else {

                #Guardar el anticipo
                $conn->Registra_Anticipo($Anticipo);
                
                #Modificar el status_erp del tramite a enviado a ERP ($ESTATUS_ERP_ENVIADO = 2)
                $VUpdt = "UPDATE solicitud_viaje SET sv_status_erp  = 2 WHERE sv_id = $SOLICITUD_VIAJE";
                $VExecute = $VConn->insertar($VUpdt);
                
            }
        }
        $conn->Desconectar();
}

#funcion que guarda comprobaciones
function GuardaComprobaciones($valida = false)
{
        #Leer todas las comprobaciones sencillas cuyo STATUS_ERP = 1
        $VConn = new conexion();

        $VSql = "SELECT
                    *
                 FROM
                     comprobaciones
                         inner join tramites on (t_id = co_mi_tramite)
                         inner join usuario on (t_iniciador = u_id)
                         inner join empresas on (u_empresa = e_id)             
                         inner join cat_cecos on (cc_id = co_cc_clave)                                                            
                 WHERE
                     (co_tipo = 1 or co_tipo = 3) AND 
                     co_status_erp = 1";

        $VRes = $VConn->consultar($VSql);
        $Filas = mysql_num_rows($VRes);
        
        #Por cada comprobacion se guarda en el ERP un registro
        $conn = new ConexionERP();
        $conn->Conectar();

        for($i=0; $i<$Filas; $i++)
        {

            #tramite de la comprobacion
            $TRAMITE_ID = mysql_result($VRes,$i,"t_id");

            #leer los datos por cada fila que saque el query e insertar una fila por cada registro...
            $MONTO_TOTAL = mysql_result($VRes,$i,"co_total");
            $MONTO_TOTAL_APROBADO = mysql_result($VRes,$i,"co_total_aprobado");
            $BUSINESS_UNIT = mysql_result($VRes,$i,"e_codigo");
            $FECHA = mysql_result($VRes,$i,"t_fecha_cierre");
            $ID_USUARIO = mysql_result($VRes,$i,"t_iniciador");
            $ID_PRODUCTO = mysql_result($VRes,$i,"u_producto");
            $ID_VENDOR = mysql_result($VRes,$i,"u_proveedor");
            $ID_ANTICIPO_RELACIONADO = mysql_result($VRes,$i,"co_tramite");
            $TIPO = mysql_result($VRes,$i,"co_tipo");
            $ID_COMPROBACION = mysql_result($VRes,$i,"co_id");
            
            $MONTO_NO_APROBADO = $MONTO_TOTAL - $MONTO_TOTAL_APROBADO;
            
            #sacar los centros de costo a los cuales se guardaron los montos de la comprobacion
            $Ceco = "SELECT * FROM ceco_detalle INNER JOIN cat_cecos ON (ceco_detalle_ceco = cc_id) WHERE ceco_detalle_tramite = $TRAMITE_ID";
            //error_log($Ceco);
            $ResCeco = $VConn->consultar($Ceco);
            $NumCecosEnComprovacion = mysql_num_rows($ResCeco);
            
            #
            # convertimos los valores de los centros de costos a porcentajes
            #

            # 1. Sacamos el porcentaje por ceco.
            $porcentajePorCECO = Array();
            while($CentroCostos = mysql_fetch_assoc($ResCeco))
            {
                $MontoCeco = $CentroCostos["ceco_detalle_cantidad"];
                $Ceco = $CentroCostos["cc_centrocostos"];
                $porcentajePorCECO[$Ceco] = ($MontoCeco * 100) / $MONTO_TOTAL;
            }
            //error_log(print_r($porcentajePorCECO, True));
            
            # 2. Creamos una comprobacion por cada centro de costos
            # $BANDERA = false;
            $contadorCeco = 0;
            $TRAMITE_ID = getTramiteID($TRAMITE_ID);
            foreach ($porcentajePorCECO as $ceco => $porcentaje)
            {
                $TRAMITE_ID = $TRAMITE_ID + $contadorCeco;
                $contadorCeco = $contadorCeco + 1;

                # Saca los conceptos de la comprobacion
                $LineaComprobacion = "
                    SELECT
                        d.dc_id AS ID,
                        d.dc_concepto,
                        d.dc_monto,
                        d.dc_iva,
                        d.dc_total,
                        d.dc_porcentaje_iva,
                        c.dc_id AS IdConcepto,
                        c.cp_concepto,
                        c.cp_cuenta
                    FROM
                        detalle_comprobacion d,
                        cat_conceptos c
                    WHERE
                         d.dc_concepto = c.dc_id AND
                         dc_comprobacion = $ID_COMPROBACION";

                //array_push($arr, $ID_COMPROBACION);

                #crear el objeto de la clase de comprobacion
                $Comprobacion = new Comprobacion();
         
               //echo "<BR>";
                $ResLineas = $VConn->consultar($LineaComprobacion);
                $FilLineas = mysql_num_rows($ResLineas);

                #if(!$BANDERA)
                for($l=0; $l<$FilLineas; $l++)
                {
                    #$BANDERA = TRUE;
                    #se sacan los datos de los detalles de la comprobacion por cada comprobacion
                    $ID_CONCEPTO = mysql_result($ResLineas, $l, "IdConcepto");
                    $CONCEPTO = mysql_result($ResLineas, $l, "cp_concepto");
                    $CUENTA = mysql_result($ResLineas, $l, "cp_cuenta");
                    $MONTO_SUBTOTAL = mysql_result($ResLineas, $l, "dc_monto");
                    $MONTO_IVA = mysql_result($ResLineas, $l, "dc_iva");
                    $MONTO_TOTAL = mysql_result($ResLineas, $l, "dc_total");
                    $TASA_IVA = mysql_result($ResLineas, $l, "dc_porcentaje_iva");

                    $linea = new LineaComprobacion();
                    $linea->ID_CONCEPTO = $ID_CONCEPTO;
                    $linea->CONCEPTO = $CONCEPTO;
                    $linea->CUENTA = $CUENTA;
                    $linea->MONTO_SUBTOTAL = ($MONTO_SUBTOTAL * $porcentaje) / 100;
                    $linea->MONTO_IVA = ($MONTO_IVA * $porcentaje) / 100;
                    $linea->MONTO_TOTAL = $MONTO_SUBTOTAL + $MONTO_IVA;
                    $linea->TASA_IVA = $TASA_IVA / 100;
                    $Comprobacion->AgregaLinea($linea);
                }
                
                #asignar datos a variables de la comprobacion
                $Comprobacion->TIPO = $TIPO;
                $Comprobacion->TRAMITE_ID = $TRAMITE_ID;
                $Comprobacion->BUSINESS_UNIT = $BUSINESS_UNIT;
                $Comprobacion->ID_USUARIO = $ID_USUARIO;
                $Comprobacion->ID_PRODUCTO = $ID_PRODUCTO;
                $Comprobacion->ID_DEPTO = $ceco; 
                $Comprobacion->FECHA = $FECHA;
                $Comprobacion->MONTO_TOTAL = $MONTO_TOTAL;
                $Comprobacion->ID_VENDOR = $ID_VENDOR;
                $Comprobacion->ID_ANTICIPO_RELACIONADO = getTramiteID($ID_ANTICIPO_RELACIONADO);
                $Comprobacion->MONTO_NO_APROBADO = $MONTO_NO_APROBADO;
                
                if($valida){
                    global $VALIDACIONES;
                    $VALIDACIONES = $VALIDACIONES . "Comprobante: $TRAMITE_ID\n";
                    $Comprobacion->Valida();
                    $VALIDACIONES = $VALIDACIONES . "------------------------------\n";
                    
                } else {
                    
                    #Guardar la comprobacion
                    $conn->Registra_Comprobacion($Comprobacion);
                                        
                }                
                
            }
            
            if(!$valida){            
                #Modificar el status_erp del tramite a enviado a ERP ($ESTATUS_ERP_ENVIADO = 2)
                $VUpdt = "UPDATE comprobaciones SET co_status_erp  = 2 WHERE co_id = $ID_COMPROBACION";
                $VExecute = $VConn->insertar($VUpdt);
                #echo "Comprobacion $TRAMITE_ID realizada....";                            
            }
            
        }
        $conn->Desconectar();
}

function GuardaComprobacionesAmex($valida = false)
{
        #Leer todas las comprobaciones sencillas cuyo STATUS_ERP = 1 y co_tipo = 2 (AMEX)
        $VConn = new conexion();

        $VSql = "SELECT
                    *
                 FROM
                     comprobaciones
                         inner join tramites on (t_id = co_mi_tramite)
                         inner join usuario on (t_iniciador = u_id)
                         inner join empresas on (u_empresa = e_id)             
                         inner join cat_cecos on (cc_id = co_cc_clave)                                                            
                 WHERE
                     (co_tipo = 2) AND 
                     co_status_erp = 1";

        $VRes = $VConn->consultar($VSql);
        $Filas = mysql_num_rows($VRes);
        
        #Por cada comprobacion se guarda en el ERP un registro
        $conn = new ConexionERP();
        $conn->Conectar();

        for($i=0; $i<$Filas; $i++)
        {

            #tramite de la comprobacion
            $TRAMITE_ID = mysql_result($VRes,$i,"t_id");

            #leer los datos por cada fila que saque el query e insertar una fila por cada registro...
            $MONTO_TOTAL = mysql_result($VRes,$i,"co_total"); #monto total de la comprobacion
            $BUSINESS_UNIT = mysql_result($VRes,$i,"e_codigo");
            $FECHA = mysql_result($VRes,$i,"t_fecha_cierre");
            $ID_USUARIO = mysql_result($VRes,$i,"t_iniciador");
            $ID_PRODUCTO = mysql_result($VRes,$i,"u_producto");
            $ID_VENDOR = mysql_result($VRes,$i,"u_proveedor");
            $ID_ANTICIPO_RELACIONADO = mysql_result($VRes,$i,"co_tramite");
            $TIPO = mysql_result($VRes,$i,"co_tipo");
            $ID_COMPROBACION = mysql_result($VRes,$i,"co_id");
            
            
            #sacar los centros de costo a los cuales se guardaron los montos de la comprobacion
            $Ceco = "SELECT * FROM ceco_detalle INNER JOIN cat_cecos ON (ceco_detalle_ceco = cc_id) WHERE ceco_detalle_tramite = $TRAMITE_ID";
            $ResCeco = $VConn->consultar($Ceco);
            $NumCecosEnComprovacion = mysql_num_rows($ResCeco);
            
            #
            # convertimos los valores de los centros de costos a porcentajes
            #

            # 1. Sacamos el porcentaje por ceco.
            $porcentajePorCECO = Array();
            while($CentroCostos = mysql_fetch_assoc($ResCeco))
            {
                $MontoCeco = $CentroCostos["ceco_detalle_cantidad"];
                $Ceco = $CentroCostos["cc_centrocostos"];
                $porcentajePorCECO[$Ceco] = ($MontoCeco * 100) / $MONTO_TOTAL;
            }
            //error_log(print_r($porcentajePorCECO, True));
            
            # 2. Creamos una comprobacion por cada centro de costos
            # $BANDERA = false;
            $contadorCeco = 0;
            $TRAMITE_ID = getTramiteID($TRAMITE_ID);
            foreach ($porcentajePorCECO as $ceco => $porcentaje)
            {
                $TRAMITE_ID = $TRAMITE_ID + $contadorCeco;
                $contadorCeco = $contadorCeco + 1;

                # Saca los conceptos de la comprobacion
                $LineaComprobacion = "
                    SELECT
                        d.dc_id AS ID,
                        d.dc_concepto,
                        d.dc_monto,
                        d.dc_iva,
                        d.dc_total,
                        d.dc_porcentaje_iva,
                        c.dc_id AS IdConcepto,
                        c.cp_concepto,
                        c.cp_cuenta
                    FROM
                        detalle_comprobacion d,
                        cat_conceptos c
                    WHERE
                         d.dc_concepto = c.dc_id AND
                         dc_comprobacion = $ID_COMPROBACION";

                //array_push($arr, $ID_COMPROBACION);

                #crear el objeto de la clase de comprobacion
                $Comprobacion = new Comprobacion();
         
               //echo "<BR>";
                $ResLineas = $VConn->consultar($LineaComprobacion);
                $FilLineas = mysql_num_rows($ResLineas);

                #if(!$BANDERA)
                for($l=0; $l<$FilLineas; $l++)
                {
                    #$BANDERA = TRUE;
                    #se sacan los datos de los detalles de la comprobacion por cada comprobacion
                    $ID_CONCEPTO = mysql_result($ResLineas, $l, "IdConcepto");
                    $CONCEPTO = mysql_result($ResLineas, $l, "cp_concepto");
                    $CUENTA = mysql_result($ResLineas, $l, "cp_cuenta");
                    $MONTO_SUBTOTAL = mysql_result($ResLineas, $l, "dc_monto");
                    $MONTO_IVA = mysql_result($ResLineas, $l, "dc_iva");
                    $MONTO_TOTAL = mysql_result($ResLineas, $l, "dc_total");
                    $TASA_IVA = mysql_result($ResLineas, $l, "dc_porcentaje_iva");

                    $linea = new LineaComprobacion();
                    $linea->ID_CONCEPTO = $ID_CONCEPTO;
                    $linea->CONCEPTO = $CONCEPTO;
                    $linea->CUENTA = $CUENTA;
                    $linea->MONTO_SUBTOTAL = ($MONTO_SUBTOTAL * $porcentaje) / 100;
                    $linea->MONTO_IVA = ($MONTO_IVA * $porcentaje) / 100;
                    $linea->MONTO_TOTAL = $MONTO_SUBTOTAL + $MONTO_IVA;
                    $linea->TASA_IVA = $TASA_IVA / 100;
                    $Comprobacion->AgregaLinea($linea);
                }
                
                #asignar datos a variables de la comprobacion
                $Comprobacion->TIPO = $TIPO;
                $Comprobacion->TRAMITE_ID = $TRAMITE_ID;
                $Comprobacion->BUSINESS_UNIT = $BUSINESS_UNIT;
                $Comprobacion->ID_USUARIO = $ID_USUARIO;
                $Comprobacion->ID_PRODUCTO = $ID_PRODUCTO;
                $Comprobacion->ID_DEPTO = $ceco; 
                $Comprobacion->FECHA = $FECHA;
                $Comprobacion->MONTO_TOTAL = $MONTO_TOTAL;
                $Comprobacion->ID_VENDOR = $ID_VENDOR;
                //$Comprobacion->ID_ANTICIPO_RELACIONADO = getTramiteID($ID_ANTICIPO_RELACIONADO);
                
                if($valida){
                    global $VALIDACIONES;
                    $VALIDACIONES = $VALIDACIONES . "Comprobante Amex: $TRAMITE_ID\n";
                    $Comprobacion->Valida();
                    $VALIDACIONES = $VALIDACIONES . "------------------------------\n";
                    
                } else {
                    
                    #Guardar la comprobacion
                    $conn->Registra_ComprobacionAmex($Comprobacion);
                                        
                }                   
                            
            }
            
            if (!$valida){
            
                #Modificar el status_erp del tramite a enviado a ERP ($ESTATUS_ERP_ENVIADO = 2)
                $VUpdt = "UPDATE comprobaciones SET co_status_erp  = 2 WHERE co_id = $ID_COMPROBACION";
                $VExecute = $VConn->insertar($VUpdt);
                #echo "Comprobacion $TRAMITE_ID realizada....";                
                
            }
        }
        $conn->Desconectar();    

}

//
//  Corre la interface en modo real, envia los datos a PeopleSoft
//
function ExecutaInterfazPeopleSoft()
{
    GuardaAnticipos();
    GuardaComprobaciones();
    GuardaComprobacionesAmex();
}

// Corre la interface en modo validacion, solo revisa los tramites por errores
// sin guardarlos en PeopleSoft o modificar el status ERP del tramite
function ValidarInterfazPeopleSoft()
{
    GuardaAnticipos(true);
    GuardaComprobaciones(true);
    GuardaComprobacionesAmex(true);
}

?>
