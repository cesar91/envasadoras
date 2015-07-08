<?php

    include_once 'ConexionERP.php';
    
    $TRAMITE_ID = 101364;
     
    function TestOracle(){
        
        $conn = new ConexionERP();
        $conn->Conectar();
        $conn->PruebaConexion();
        $conn->Desconectar();        

    } 
    
    // Funcion de prueba para regresar el siguiente tramite
    function getNextTramiteID(){
        global $TRAMITE_ID;
        $TRAMITE_ID = $TRAMITE_ID + 1;
        return $TRAMITE_ID;
    }
    
    // Funcion de prueba para crear un anticipo con su comprobacion
    function CreateAnticipoConComprobacionExcedente1($bu, $depto, $usuario, $fecha, $monto_anticipo, $concepto1, $subtotal1, $iva1, $tasa1){
        
        $anticipo = new Anticipo();
        $anticipo->TRAMITE_ID = getNextTramiteID();
        $anticipo->BUSINESS_UNIT = $bu;
        $anticipo->ID_DEPTO = $depto;
        $anticipo->ID_USUARIO = $usuario['vendor'];
        $anticipo->ID_PRODUCTO = $usuario['producto'];
        $anticipo->ID_VENDOR = $usuario['vendor'];
        $anticipo->FECHA = $fecha;
        $anticipo->MONTO = $monto_anticipo;  
        
        $comprobacion = new Comprobacion();
        $comprobacion->TRAMITE_ID = getNextTramiteID();
        $comprobacion->ID_ANTICIPO_RELACIONADO = $anticipo->TRAMITE_ID;
        $comprobacion->BUSINESS_UNIT = $bu;
        $comprobacion->ID_DEPTO =  $depto;
        $comprobacion->ID_USUARIO = $usuario['vendor'];
        $comprobacion->ID_PRODUCTO = $usuario['producto'];
        $comprobacion->ID_VENDOR = $usuario['vendor'];   
        $comprobacion->FECHA = $fecha;
        
        $comprobacion->AgregaLineaDeConcepto($concepto1['nombre'], $concepto1['cuenta'], $subtotal1, $iva1, $tasa1);   
        
        $conn = new ConexionERP();
        $conn->Conectar();
        error_log('');         
        error_log('// --------- ANTICIPO'); 
        $conn->Registra_Anticipo($anticipo);
        error_log('// --------- COMPROBACION');         
        $conn->Registra_Comprobacion($comprobacion);        
        $conn->Desconectar();          
    }
    
    // Funcion de prueba para crear un anticipo con su comprobacion
    function CreateAnticipoConComprobacionMonedaExtranjera1($bu, $depto, $usuario, $fecha, $moneda, $concepto1, $subtotal1, $iva1, $tasa1){
        
        $anticipo = new Anticipo();
        $anticipo->TRAMITE_ID = getNextTramiteID();
        $anticipo->BUSINESS_UNIT = $bu;
        $anticipo->ID_DEPTO = $depto;
        $anticipo->ID_USUARIO = $usuario['vendor'];
        $anticipo->ID_PRODUCTO = $usuario['producto'];
        $anticipo->ID_VENDOR = $usuario['vendor'];
        $anticipo->FECHA = $fecha;
        $anticipo->MONTO = $subtotal1 + $iva1;  
        $anticipo->MONEDA = $moneda;  
        
        $comprobacion = new Comprobacion();
        $comprobacion->TRAMITE_ID = getNextTramiteID();
        $comprobacion->ID_ANTICIPO_RELACIONADO = $anticipo->TRAMITE_ID;
        $comprobacion->BUSINESS_UNIT = $bu;
        $comprobacion->ID_DEPTO =  $depto;
        $comprobacion->ID_USUARIO = $usuario['vendor'];
        $comprobacion->ID_PRODUCTO = $usuario['producto'];
        $comprobacion->ID_VENDOR = $usuario['vendor'];   
        $comprobacion->FECHA = $fecha;
        $comprobacion->MONEDA = $moneda;          
        
        $comprobacion->AgregaLineaDeConcepto($concepto1['nombre'], $concepto1['cuenta'], $subtotal1, $iva1, $tasa1);   
        
        $conn = new ConexionERP();
        $conn->Conectar();
        error_log('');         
        error_log('// --------- ANTICIPO'); 
        $conn->Registra_Anticipo($anticipo);
        error_log('// --------- COMPROBACION');         
        $conn->Registra_Comprobacion($comprobacion);        
        $conn->Desconectar();          
    }    
 
 
     // Funcion de prueba para crear un anticipo con su comprobacion
    function CreateAnticipoDistintoConComprobacion1($bu, $depto, $usuario, $fecha, $monto_anticipo, $concepto1, $subtotal1, $iva1, $tasa1){
        
        $anticipo = new Anticipo();
        $anticipo->TRAMITE_ID = getNextTramiteID();
        $anticipo->BUSINESS_UNIT = $bu;
        $anticipo->ID_DEPTO = $depto;
        $anticipo->ID_USUARIO = $usuario['vendor'];
        $anticipo->ID_PRODUCTO = $usuario['producto'];
        $anticipo->ID_VENDOR = $usuario['vendor'];
        $anticipo->FECHA = $fecha;
        $anticipo->MONTO = $monto_anticipo;  
        
        $comprobacion = new Comprobacion();
        $comprobacion->TRAMITE_ID = getNextTramiteID();
        $comprobacion->ID_ANTICIPO_RELACIONADO = $anticipo->TRAMITE_ID;
        $comprobacion->BUSINESS_UNIT = $bu;
        $comprobacion->ID_DEPTO =  $depto;
        $comprobacion->ID_USUARIO = $usuario['vendor'];
        $comprobacion->ID_PRODUCTO = $usuario['producto'];
        $comprobacion->ID_VENDOR = $usuario['vendor'];   
        $comprobacion->FECHA = $fecha;
        
        $comprobacion->AgregaLineaDeConcepto($concepto1['nombre'], $concepto1['cuenta'], $subtotal1, $iva1, $tasa1);   
        
        $conn = new ConexionERP();
        $conn->Conectar();
        error_log('');         
        error_log('// --------- ANTICIPO'); 
        $conn->Registra_Anticipo($anticipo);
        error_log('// --------- COMPROBACION');         
        $conn->Registra_Comprobacion($comprobacion);        
        $conn->Desconectar();          
    }
    
    // Funcion de prueba para crear un anticipo con su comprobacion
    function CreateAnticipoConComprobacionTUA1($bu, $depto, $usuario, $fecha, $concepto1, $subtotal1, $iva1, $tasa1, $tua){
        
        $anticipo = new Anticipo();
        $anticipo->TRAMITE_ID = getNextTramiteID();
        $anticipo->BUSINESS_UNIT = $bu;
        $anticipo->ID_DEPTO = $depto;
        $anticipo->ID_USUARIO = $usuario['vendor'];
        $anticipo->ID_PRODUCTO = $usuario['producto'];
        $anticipo->ID_VENDOR = $usuario['vendor'];
        $anticipo->FECHA = $fecha;
        $anticipo->MONTO = $subtotal1+$tua + $iva1;  
        
        $comprobacion = new Comprobacion();
        $comprobacion->TRAMITE_ID = getNextTramiteID();
        $comprobacion->ID_ANTICIPO_RELACIONADO = $anticipo->TRAMITE_ID;
        $comprobacion->BUSINESS_UNIT = $bu;
        $comprobacion->ID_DEPTO =  $depto;
        $comprobacion->ID_USUARIO = $usuario['vendor'];
        $comprobacion->ID_PRODUCTO = $usuario['producto'];
        $comprobacion->ID_VENDOR = $usuario['vendor'];   
        $comprobacion->FECHA = $fecha;
        
        $comprobacion->AgregaLineaDeConcepto($concepto1['nombre'], $concepto1['cuenta'], $subtotal1+$tua, $iva1, $tasa1);   
        
        $conn = new ConexionERP();
        $conn->Conectar();
        error_log('');         
        error_log('// --------- ANTICIPO'); 
        $conn->Registra_Anticipo($anticipo);
        error_log('// --------- COMPROBACION');         
        $conn->Registra_Comprobacion($comprobacion);        
        $conn->Desconectar();          
    }    
    
      
    // Funcion de prueba para crear un anticipo con su comprobacion
    function CreateAnticipoConComprobacion1($bu, $depto, $usuario, $fecha, $concepto1, $subtotal1, $iva1, $tasa1){
        
        $anticipo = new Anticipo();
        $anticipo->TRAMITE_ID = getNextTramiteID();
        $anticipo->BUSINESS_UNIT = $bu;
        $anticipo->ID_DEPTO = $depto;
        $anticipo->ID_USUARIO = $usuario['vendor'];
        $anticipo->ID_PRODUCTO = $usuario['producto'];
        $anticipo->ID_VENDOR = $usuario['vendor'];
        $anticipo->FECHA = $fecha;
        $anticipo->MONTO = $subtotal1 + $iva1;  
        
        $comprobacion = new Comprobacion();
        $comprobacion->TRAMITE_ID = getNextTramiteID();
        $comprobacion->ID_ANTICIPO_RELACIONADO = $anticipo->TRAMITE_ID;
        $comprobacion->BUSINESS_UNIT = $bu;
        $comprobacion->ID_DEPTO =  $depto;
        $comprobacion->ID_USUARIO = $usuario['vendor'];
        $comprobacion->ID_PRODUCTO = $usuario['producto'];
        $comprobacion->ID_VENDOR = $usuario['vendor'];   
        $comprobacion->FECHA = $fecha;
        
        $comprobacion->AgregaLineaDeConcepto($concepto1['nombre'], $concepto1['cuenta'], $subtotal1, $iva1, $tasa1);   
        
        $conn = new ConexionERP();
        $conn->Conectar();
        error_log('');         
        error_log('// --------- ANTICIPO'); 
        $conn->Registra_Anticipo($anticipo);
        error_log('// --------- COMPROBACION');         
        $conn->Registra_Comprobacion($comprobacion);        
        $conn->Desconectar();          
    }

    function CreateAnticipoConComprobacion2($bu, $depto, $usuario, $fecha, $concepto1, $subtotal1, $iva1, $tasa1,
                                                                            $concepto2, $subtotal2, $iva2, $tasa2){        
        $anticipo = new Anticipo();
        $anticipo->TRAMITE_ID = getNextTramiteID();
        $anticipo->BUSINESS_UNIT = $bu;
        $anticipo->ID_DEPTO = $depto;
        $anticipo->ID_USUARIO = $usuario['vendor'];
        $anticipo->ID_PRODUCTO = $usuario['producto'];
        $anticipo->ID_VENDOR = $usuario['vendor'];
        $anticipo->FECHA = $fecha;
        $anticipo->MONTO = $subtotal1 + $iva1 + $subtotal2 + $iva2;
        
        $comprobacion = new Comprobacion();
        $comprobacion->TRAMITE_ID = getNextTramiteID();
        $comprobacion->ID_ANTICIPO_RELACIONADO = $anticipo->TRAMITE_ID;
        $comprobacion->BUSINESS_UNIT = $bu;
        $comprobacion->ID_DEPTO =  $depto;
        $comprobacion->ID_USUARIO = $usuario['vendor'];
        $comprobacion->ID_PRODUCTO = $usuario['producto'];
        $comprobacion->ID_VENDOR = $usuario['vendor'];  
        $comprobacion->FECHA = $fecha;
        
        $comprobacion->AgregaLineaDeConcepto($concepto1['nombre'], $concepto1['cuenta'], $subtotal1, $iva1, $tasa1);
        $comprobacion->AgregaLineaDeConcepto($concepto2['nombre'], $concepto2['cuenta'], $subtotal2, $iva2, $tasa2);                
        
        $conn = new ConexionERP();
        $conn->Conectar();
        error_log('');         
        error_log('// --------- ANTICIPO'); 
        $conn->Registra_Anticipo($anticipo);
        error_log('// --------- COMPROBACION');         
        $conn->Registra_Comprobacion($comprobacion);        
        $conn->Desconectar();           
    }
     
    function CreateAnticipoConComprobacion3($bu, $depto, $usuario, $fecha, $concepto1, $subtotal1, $iva1, $tasa1,
                                                                            $concepto2, $subtotal2, $iva2, $tasa2,
                                                                            $concepto3, $subtotal3, $iva3, $tasa3){    
        
        $anticipo = new Anticipo();
        $anticipo->TRAMITE_ID = getNextTramiteID();
        $anticipo->BUSINESS_UNIT = $bu;
        $anticipo->ID_DEPTO = $depto;
        $anticipo->ID_USUARIO = $usuario['vendor'];
        $anticipo->ID_PRODUCTO = $usuario['producto'];
        $anticipo->ID_VENDOR = $usuario['vendor'];
        $anticipo->FECHA = $fecha;
        $anticipo->MONTO = $subtotal1 + $iva1 + $subtotal2 + $iva2 + $subtotal3 + $iva3;
        
        $comprobacion = new Comprobacion();
        $comprobacion->TRAMITE_ID = getNextTramiteID();
        $comprobacion->ID_ANTICIPO_RELACIONADO = $anticipo->TRAMITE_ID;
        $comprobacion->BUSINESS_UNIT = $bu;
        $comprobacion->ID_DEPTO =  $depto;
        $comprobacion->ID_USUARIO = $usuario['vendor'];
        $comprobacion->ID_PRODUCTO = $usuario['producto'];
        $comprobacion->ID_VENDOR = $usuario['vendor'];   
        $comprobacion->FECHA = $fecha;
        
        $comprobacion->AgregaLineaDeConcepto($concepto1['nombre'], $concepto1['cuenta'], $subtotal1, $iva1, $tasa1);
        $comprobacion->AgregaLineaDeConcepto($concepto2['nombre'], $concepto2['cuenta'], $subtotal2, $iva2, $tasa2);  
        $comprobacion->AgregaLineaDeConcepto($concepto3['nombre'], $concepto3['cuenta'], $subtotal3, $iva3, $tasa3);  
        
        $conn = new ConexionERP();
        $conn->Conectar();
        error_log('');         
        error_log('// --------- ANTICIPO'); 
        $conn->Registra_Anticipo($anticipo);
        error_log('// --------- COMPROBACION');         
        $conn->Registra_Comprobacion($comprobacion);        
        $conn->Desconectar();          
    }     
    
    
    
    function CreateAnticipoConComprobacion3_2Cecos($bu, $depto1, $deptoPorcentaje1, $depto2, $deptoPorcentaje2, 
                                                                       $usuario, $fecha, $concepto1, $subtotal1, $iva1, $tasa1,
                                                                                          $concepto2, $subtotal2, $iva2, $tasa2,
                                                                                           $concepto3, $subtotal3, $iva3, $tasa3){    
        $anticipo = new Anticipo();
        $anticipo->TRAMITE_ID = getNextTramiteID();
        $anticipo->BUSINESS_UNIT = $bu;
        $anticipo->ID_DEPTO = $depto1;
        $anticipo->ID_USUARIO = $usuario['vendor'];
        $anticipo->ID_PRODUCTO = $usuario['producto'];
        $anticipo->ID_VENDOR = $usuario['vendor'];
        $anticipo->FECHA = $fecha;
        $anticipo->MONTO = $subtotal1 + $iva1 + $subtotal2 + $iva2 + $subtotal3 + $iva3;
        
        // depto1
        $comprobacion1 = new Comprobacion();
        $comprobacion1->TRAMITE_ID = getNextTramiteID();
        $comprobacion1->ID_ANTICIPO_RELACIONADO = $anticipo->TRAMITE_ID;
        $comprobacion1->BUSINESS_UNIT = $bu;
        $comprobacion1->ID_DEPTO =  $depto1;
        $comprobacion1->ID_USUARIO = $usuario['vendor'];
        $comprobacion1->ID_PRODUCTO = $usuario['producto'];
        $comprobacion1->ID_VENDOR = $usuario['vendor'];   
        $comprobacion1->FECHA = $fecha;
        
        $comprobacion1->AgregaLineaDeConcepto($concepto1['nombre'], $concepto1['cuenta'], round($subtotal1*$deptoPorcentaje1,2), round($iva1*$deptoPorcentaje1,2), round($tasa1*$deptoPorcentaje1,2));
        $comprobacion1->AgregaLineaDeConcepto($concepto2['nombre'], $concepto2['cuenta'], round($subtotal2*$deptoPorcentaje1,2), round($iva2*$deptoPorcentaje1,2), round($tasa2*$deptoPorcentaje1,2));  
        $comprobacion1->AgregaLineaDeConcepto($concepto3['nombre'], $concepto3['cuenta'], round($subtotal3*$deptoPorcentaje1,2), round($iva3*$deptoPorcentaje1,2), round($tasa3*$deptoPorcentaje1,2));  
        
        // depto2
        $comprobacion2 = new Comprobacion();
        $comprobacion2->TRAMITE_ID = getNextTramiteID();
        $comprobacion2->ID_ANTICIPO_RELACIONADO = $anticipo->TRAMITE_ID;
        $comprobacion2->BUSINESS_UNIT = $bu;
        $comprobacion2->ID_DEPTO =  $depto2;
        $comprobacion2->ID_USUARIO = $usuario['vendor'];
        $comprobacion2->ID_PRODUCTO = $usuario['producto'];
        $comprobacion2->ID_VENDOR = $usuario['vendor'];   
        $comprobacion2->FECHA = $fecha;
        
        $comprobacion2->AgregaLineaDeConcepto($concepto1['nombre'], $concepto1['cuenta'], round($subtotal1*$deptoPorcentaje2,2), round($iva1*$deptoPorcentaje2,2), round($tasa1*$deptoPorcentaje2,2));
        $comprobacion2->AgregaLineaDeConcepto($concepto2['nombre'], $concepto2['cuenta'], round($subtotal2*$deptoPorcentaje2,2), round($iva2*$deptoPorcentaje2,2), round($tasa2*$deptoPorcentaje2,2));  
        $comprobacion2->AgregaLineaDeConcepto($concepto3['nombre'], $concepto3['cuenta'], round($subtotal3*$deptoPorcentaje2,2), round($iva3*$deptoPorcentaje2,2), round($tasa3*$deptoPorcentaje2,2));  
        
        $conn = new ConexionERP();
        $conn->Conectar();
        error_log('');         
        error_log('// --------- ANTICIPO'); 
        $conn->Registra_Anticipo($anticipo);
        error_log('// --------- COMPROBACION 1');         
        $conn->Registra_Comprobacion($comprobacion1);        
        error_log('// --------- COMPROBACION 2');         
        $conn->Registra_Comprobacion($comprobacion2);  
        $conn->Desconectar();          
    }       
    
    
    // Funcion de prueba para crear un reembolso
    function CreateReembolso1($bu, $depto, $usuario, $fecha, $concepto1, $subtotal1, $iva1, $tasa1){
        
        $comprobacion = new Comprobacion();
        $comprobacion->TRAMITE_ID = getNextTramiteID();
        $comprobacion->BUSINESS_UNIT = $bu;
        $comprobacion->ID_DEPTO =  $depto;
        $comprobacion->ID_USUARIO = $usuario['vendor'];
        $comprobacion->ID_PRODUCTO = $usuario['producto'];
        $comprobacion->ID_VENDOR = $usuario['vendor']; 
        $comprobacion->FECHA = $fecha;
        
        $comprobacion->AgregaLineaDeConcepto($concepto1['nombre'], $concepto1['cuenta'], $subtotal1, $iva1, $tasa1);
        
        $conn = new ConexionERP();
        $conn->Conectar();
        error_log('');         
        error_log('// --------- REEMBOLSO');         
        $conn->Registra_Comprobacion($comprobacion);        
        $conn->Desconectar();          
    }    
    
    // Funcion de prueba para crear un reembolso
    function CreateReembolso2($bu, $depto, $usuario, $fecha, $concepto1, $subtotal1, $iva1, $tasa1, 
                                                                $concepto2, $subtotal2, $iva2, $tasa2){

        $comprobacion = new Comprobacion();
        $comprobacion->TRAMITE_ID = getNextTramiteID();
        $comprobacion->BUSINESS_UNIT = $bu;
        $comprobacion->ID_DEPTO =  $depto;
        $comprobacion->ID_USUARIO = $usuario['vendor'];
        $comprobacion->ID_PRODUCTO = $usuario['producto'];
        $comprobacion->ID_VENDOR = $usuario['vendor'];  
        $comprobacion->FECHA = $fecha;
        
        $comprobacion->AgregaLineaDeConcepto($concepto1['nombre'], $concepto1['cuenta'], $subtotal1, $iva1, $tasa1);    
        $comprobacion->AgregaLineaDeConcepto($concepto2['nombre'], $concepto2['cuenta'], $subtotal2, $iva2, $tasa2); 
        
        $conn = new ConexionERP();
        $conn->Conectar();
        error_log('');         
        error_log('// --------- REEMBOLSO');         
        $conn->Registra_Comprobacion($comprobacion);        
        $conn->Desconectar();          
    }   
    
    // Funcion de prueba para crear un reembolso
    function CreateReembolso3($bu, $depto, $usuario, $fecha, $concepto1, $subtotal1, $iva1, $tasa1, 
                                                                $concepto2, $subtotal2, $iva2, $tasa2,
                                                                    $concepto3, $subtotal3, $iva3, $tasa3){
        
        $comprobacion = new Comprobacion();
        $comprobacion->TRAMITE_ID = getNextTramiteID();
        $comprobacion->BUSINESS_UNIT = $bu;
        $comprobacion->ID_DEPTO =  $depto;
        $comprobacion->ID_USUARIO = $usuario['vendor'];
        $comprobacion->ID_PRODUCTO = $usuario['producto'];
        $comprobacion->ID_VENDOR = $usuario['vendor'];  
        $comprobacion->FECHA = $fecha;
        
        $comprobacion->AgregaLineaDeConcepto($concepto1['nombre'], $concepto1['cuenta'], $subtotal1, $iva1, $tasa1);    
        $comprobacion->AgregaLineaDeConcepto($concepto2['nombre'], $concepto2['cuenta'], $subtotal2, $iva2, $tasa2); 
        $comprobacion->AgregaLineaDeConcepto($concepto3['nombre'], $concepto3['cuenta'], $subtotal3, $iva3, $tasa3);   
        
        $conn = new ConexionERP();
        $conn->Conectar();
        error_log('');         
        error_log('// --------- REEMBOLSO');         
        $conn->Registra_Comprobacion($comprobacion);        
        $conn->Desconectar();          
    }           
    

    
    // Funcion de prueba para crear un ComprobacionAmex
    function CreateComprobacionAmexMonedaExtranjera1($bu, $depto, $usuario, $fecha, $moneda, $concepto1, $subtotal1, $iva1, $tasa1){
        
        $comprobacion = new Comprobacion();
        $comprobacion->TRAMITE_ID = getNextTramiteID();
        $comprobacion->BUSINESS_UNIT = $bu;
        $comprobacion->ID_DEPTO =  $depto;
        $comprobacion->ID_USUARIO = $usuario['vendor'];
        $comprobacion->ID_PRODUCTO = $usuario['producto'];
        $comprobacion->ID_VENDOR = $usuario['vendor'];            
        $comprobacion->FECHA = $fecha;
        $comprobacion->MONEDA = $moneda;         
        
        $comprobacion->AgregaLineaDeConcepto($concepto1['nombre'], $concepto1['cuenta'], $subtotal1, $iva1, $tasa1); 
        
        $conn = new ConexionERP();
        $conn->Conectar();
        error_log('');         
        error_log('// --------- CreateComprobacionAmexMonedaExtranjera1');         
        $conn->Registra_ComprobacionAmex($comprobacion);        
        $conn->Desconectar();          
    }        
    
    // Funcion de prueba para crear un ComprobacionAmex
    function CreateComprobacionAmexMonedaExtranjera2($bu, $depto, $usuario, $fecha,  $moneda, $concepto1, $subtotal1, $iva1, $tasa1, 
                                                                    $concepto2, $subtotal2, $iva2, $tasa2){
        
        $comprobacion = new Comprobacion();
        $comprobacion->TRAMITE_ID = getNextTramiteID();
        $comprobacion->BUSINESS_UNIT = $bu;
        $comprobacion->ID_DEPTO =  $depto;
        $comprobacion->ID_USUARIO = $usuario['vendor'];
        $comprobacion->ID_PRODUCTO = $usuario['producto'];
        $comprobacion->ID_VENDOR = $usuario['vendor'];  
        $comprobacion->FECHA = $fecha;
        $comprobacion->MONEDA = $moneda;           
        
        $comprobacion->AgregaLineaDeConcepto($concepto1['nombre'], $concepto1['cuenta'], $subtotal1, $iva1, $tasa1);    
        $comprobacion->AgregaLineaDeConcepto($concepto2['nombre'], $concepto2['cuenta'], $subtotal2, $iva2, $tasa2);
        
        $conn = new ConexionERP();
        $conn->Conectar();
        error_log('');         
        error_log('// --------- CreateComprobacionAmexMonedaExtranjera2');         
        $conn->Registra_ComprobacionAmex($comprobacion);        
        $conn->Desconectar();          
    }       
    
    // Funcion de prueba para crear un ComprobacionAmex
    function CreateComprobacionAmex1($bu, $depto, $usuario, $fecha, $concepto1, $subtotal1, $iva1, $tasa1){
        
        $comprobacion = new Comprobacion();
        $comprobacion->TRAMITE_ID = getNextTramiteID();
        $comprobacion->BUSINESS_UNIT = $bu;
        $comprobacion->ID_DEPTO =  $depto;
        $comprobacion->ID_USUARIO = $usuario['vendor'];
        $comprobacion->ID_PRODUCTO = $usuario['producto'];
        $comprobacion->ID_VENDOR = $usuario['vendor'];   
        $comprobacion->FECHA = $fecha;
        
        $comprobacion->AgregaLineaDeConcepto($concepto1['nombre'], $concepto1['cuenta'], $subtotal1, $iva1, $tasa1); 
        
        $conn = new ConexionERP();
        $conn->Conectar();
        error_log('');         
        error_log('// --------- ComprobacionAmex');         
        $conn->Registra_ComprobacionAmex($comprobacion);        
        $conn->Desconectar();          
    }    
    
    // Funcion de prueba para crear un ComprobacionAmex
    function CreateComprobacionAmex2($bu, $depto, $usuario, $fecha, $concepto1, $subtotal1, $iva1, $tasa1, 
                                                                    $concepto2, $subtotal2, $iva2, $tasa2){
        
        $comprobacion = new Comprobacion();
        $comprobacion->TRAMITE_ID = getNextTramiteID();
        $comprobacion->BUSINESS_UNIT = $bu;
        $comprobacion->ID_DEPTO =  $depto;
        $comprobacion->ID_USUARIO = $usuario['vendor'];
        $comprobacion->ID_PRODUCTO = $usuario['producto'];
        $comprobacion->ID_VENDOR = $usuario['vendor'];  
        $comprobacion->FECHA = $fecha;
        
        $comprobacion->AgregaLineaDeConcepto($concepto1['nombre'], $concepto1['cuenta'], $subtotal1, $iva1, $tasa1);    
        $comprobacion->AgregaLineaDeConcepto($concepto2['nombre'], $concepto2['cuenta'], $subtotal2, $iva2, $tasa2);
        
        $conn = new ConexionERP();
        $conn->Conectar();
        error_log('');         
        error_log('// --------- ComprobacionAmex');         
        $conn->Registra_ComprobacionAmex($comprobacion);        
        $conn->Desconectar();          
    }   
    
    // Funcion de prueba para crear un ComprobacionAmex
    function CreateComprobacionAmex3($bu, $depto, $usuario, $fecha, $concepto1, $subtotal1, $iva1, $tasa1, 
                                                                    $concepto2, $subtotal2, $iva2, $tasa2,
                                                                    $concepto3, $subtotal3, $iva3, $tasa3){
        
        $comprobacion = new Comprobacion();
        $comprobacion->TRAMITE_ID = getNextTramiteID();
        $comprobacion->BUSINESS_UNIT = $bu;
        $comprobacion->ID_DEPTO =  $depto;
        $comprobacion->ID_USUARIO = $usuario['vendor'];
        $comprobacion->ID_PRODUCTO = $usuario['producto'];
        $comprobacion->ID_VENDOR = $usuario['vendor'];    
        $comprobacion->FECHA = $fecha;
        
        $comprobacion->AgregaLineaDeConcepto($concepto1['nombre'], $concepto1['cuenta'], $subtotal1, $iva1, $tasa1);    
        $comprobacion->AgregaLineaDeConcepto($concepto2['nombre'], $concepto2['cuenta'], $subtotal2, $iva2, $tasa2); 
        $comprobacion->AgregaLineaDeConcepto($concepto3['nombre'], $concepto3['cuenta'], $subtotal3, $iva3, $tasa3);    
        
        $conn = new ConexionERP();
        $conn->Conectar();
        error_log('');         
        error_log('// --------- ComprobacionAmex');         
        $conn->Registra_ComprobacionAmex($comprobacion);        
        $conn->Desconectar();          
    }         
    
    function LimpiaBase(){
        $conn = new ConexionERP();
        $conn->Conectar(); 
        $conn->LimpiaBase();        
        $conn->Desconectar();          
    }       
    
    // Crea 5 anticipos, 5 comprobaciones, 5 reembolsos y 5 comprobaciones Amex
    function PruebaCaso1(){
        
        $bu = 'GMDEM';
        $depto = 'DAD012'; // Gestión Corporativa Marti
        $fecha = '2011-01-14';
        $usuario = array("vendor" => "0000010869", "producto" => "DD0063"); // Fabiola Mancilla
        
        $alimentacion = array("nombre" => "ALIMENTACION", "cuenta" => "6011110103");
        $hospedaje    = array("nombre" => "HOSPEDAJE",    "cuenta" => "6011110101");
        $transportes  = array("nombre" => "TRANSPORTES",  "cuenta" => "6011110102");
        $pasajes      = array("nombre" => "PASAJES",      "cuenta" => "6011110104");         
        $banamex1     = array("nombre" => "BANAMEX 7837059", "cuenta" => "1011210104");  
        $banamex2     = array("nombre" => "BANAMEX 7869341", "cuenta" => "1011210102");          
                
        CreateAnticipoConComprobacion1($bu, $depto, $usuario, $fecha, $pasajes,  '181.46', '29.04', '0.16');
        CreateAnticipoConComprobacion2($bu, $depto, $usuario, $fecha, $pasajes,  '2750.00', '440.00', '0.16',
                                                                      $alimentacion,  '1034.48', '165.52', '0.16');
        CreateAnticipoConComprobacion3($bu, $depto, $usuario, $fecha, $pasajes,  '2750.00', '440.00', '0.16',
                                                                      $alimentacion,  '1034.48', '165.52', '0.16',
                                                                      $hospedaje,  '7327.59', '1172.41', '8500.00');
        CreateAnticipoConComprobacion1($bu, $depto, $usuario, $fecha, $pasajes,  '650.46', '0.00', '0.00');
        CreateAnticipoConComprobacion2($bu, $depto, $usuario, $fecha, $pasajes,  '100.00', '0.00', '0.00',
                                                                      $alimentacion,  '1034.48', '165.52', '0.16');
        CreateAnticipoConComprobacion2($bu, $depto, $usuario, $fecha, $pasajes,  '100.00', '0.00', '0.00',
                                                                      $alimentacion,  '1896.55', '303.45', '0.16');
        CreateAnticipoConComprobacion2($bu, $depto, $usuario, $fecha, $pasajes,  '100.00', '16.00', '0.16',
                                                                      $banamex1, '400.00', '0.00', '0.00');
                                                                                        
        CreateReembolso1($bu, $depto, $usuario, $fecha, $pasajes,  '200.00', '0.00', '0.00');                                                                                          
        CreateReembolso2($bu, $depto, $usuario, $fecha, $pasajes,  '350.00', '38.50', '0.00',
                                                        $alimentacion,  '1896.55', '303.45', '0.16'); 
        CreateReembolso3($bu, $depto, $usuario, $fecha, $pasajes,  '2750.00', '440.00', '0.16',
                                                        $alimentacion,  '3000.00', '160.00', '0.16',
                                                        $hospedaje,  '7327.59', '1172.41', '0.16');
                                                                            
        CreateComprobacionAmex1($bu, $depto, $usuario, $fecha, $pasajes,  '200.00', '0.00', '0.00');                                                                                          
        CreateComprobacionAmex2($bu, $depto, $usuario, $fecha, $pasajes,  '350.00', '38.50', '0.00',
                                                               $alimentacion,  '1896.55', '303.45', '0.16');    
        CreateComprobacionAmex3($bu, $depto, $usuario, $fecha, $pasajes,  '350.00', '38.50', '0.00',
                                                               $alimentacion,  '1896.55', '303.45', '0.16',
                                                               $hospedaje,  '4560.00', '729.60', '0.16');                                                                                                                                                      
        CreateComprobacionAmex1($bu, $depto, $usuario, $fecha, $pasajes,  '1200.00', '0.00', '0.00');
        CreateComprobacionAmex1($bu, $depto, $usuario, $fecha, $pasajes,  '800.00', '88.00', '0.11');
        CreateComprobacionAmex1($bu, $depto, $usuario, $fecha, $pasajes,  '181.46', '29.04', '0.16');
        CreateComprobacionAmex2($bu, $depto, $usuario, $fecha, $pasajes,  '2750.00', '440.00', '0.16',
                                                               $alimentacion,  '1034.48', '165.52', '0.16');        
    }
     
    function PruebaConMonedaExtranjera(){
        
        $bu = 'GMDEM';
        $depto = 'DAD012'; // Gestión Corporativa Marti
        $fecha = '2011-05-18';
        $usuario = array("vendor" => "0000010869", "producto" => "DD0063"); // Fabiola Mancilla
        
        $alimentacion = array("nombre" => "ALIMENTACION", "cuenta" => "6011110103");
        $hospedaje    = array("nombre" => "HOSPEDAJE",    "cuenta" => "6011110101");
        $transportes  = array("nombre" => "TRANSPORTES",  "cuenta" => "6011110102");
        $pasajes      = array("nombre" => "PASAJES",      "cuenta" => "6011110104");         
        $banamex1     = array("nombre" => "BANAMEX 7837059", "cuenta" => "1011210104");  
        $banamex2     = array("nombre" => "BANAMEX 7869341", "cuenta" => "1011210102");     
        
        // conceptos de caja chica
        $mantenimiento = array("nombre" => "MANTTO. TIENDA", "cuenta" => "6010710102");
        $fumigacion    = array("nombre" => "FUMIGACION",     "cuenta" => "6010710102");
        $limpiezacrist = array("nombre" => "LIMP. CRISTALES", "cuenta" => "6010710102");

        CreateAnticipoConComprobacionMonedaExtranjera1($bu, $depto, $usuario, $fecha, "USD", $pasajes,  '181.46', '29.04', '0.16');        
        //CreateAnticipoConComprobacionMonedaExtranjera1($bu, $depto, $usuario, $fecha, "EUR", $pasajes,  '181.46', '29.04', '0.16');           
    }
    
    // GDEM (Cada una con un usuario distinto y manejar 3 deptos)
    function PruebaFuncionalesConSoporteGMDEM(){
        
        $bu = 'GMDEM';
        $depto = 'DAD012'; // Gestión Corporativa Marti
        $fecha = '2011-03-22';

        $depto1 = 'DAD001'; // Dirección de operaciones
        $depto2 = 'DAD002'; // Dirección Comercial        
        $depto3 = 'DAD003'; // Ventas Institucionales 
        
        /*
        GMDEM	0000010555	Y	CAVB 46061	CAVB 46061-001	CASTILLO VALENCIA BERNABE
        GMDEM	0000010556	Y	MAGJ50121	MAGJ50121-001	MAGAÑA GORDILLO  JOSE
        GMDEM	0000010557	Y	MESV70091	MESV70091-001	MELENDEZ SUAREZ VICTOR MANUEL
        GMDEM	0000010558	Y	BASF81004	BASF81004-001	BARON SUAREZ FRANCISCO
        GMDEM	0000010559	Y	BURS57062	BURS57062-001	BUENROSTRO ROMERO SANTIAGO
        GMDEM	0000010560	Y	CAPM54082	CAPM54082-001	CHAVEZ PEREZ MARIO
        GMDEM	0000010561	Y	DAMJ63011	DAMJ63011-001	DAVALOS MARTINEZ JUAN PEDRO
        GMDEM	0000010562	Y	DEER590508	DEER590508-001	DELGADILLO ESCOBAR RENE
        GMDEM	0000010563	Y	EIFL66062	EIFL66062-001	ENRIQUEZ FUENTES JOSE LUIS
        GMDEM	0000010564	Y	EANJ64110	EANJ64110-001	ESCAMILLA NUÑEZ JORGE
        GMDEM	0000010565	Y	ITM801201	ITM801201-001	IMPULSORA DE TRASPORTES
         
        GMDEM	DD0116	A	Roberto Mejía Díaz
        GMDEM	DD0117	A	Maria del Carmen Jaime
        GMDEM	DD0118	A	Alder Gómez Gama
        GMDEM	DD0119	A	Tomás Bayod Hernández
        GMDEM	DD0120	A	Eduardo Sánchez Portillo
        GMDEM	DD0121	A	Miguel Lozano Herrera
        GMDEM	DD0122	A	Rafael Obed Galeana Salazar
        GMDEM	DD0123	A	Humberto de Jesús Avalos
        GMDEM	DD0124	A	Valeria Carpinacci
        GMDEM	DD0125	A	Eduardo Galicia Chávez
        GMDEM	DD0126	A	Alfonso Vázquez Paredes Arroyo
        */
        
        $usuario1 = array("vendor" => "0000010555", "producto" => "DD0116"); 
        $usuario2 = array("vendor" => "0000010556", "producto" => "DD0117"); 
        $usuario3 = array("vendor" => "0000010557", "producto" => "DD0118"); 
        $usuario4 = array("vendor" => "0000010558", "producto" => "DD0119"); 
        $usuario5 = array("vendor" => "0000010559", "producto" => "DD0120"); 
        $usuario6 = array("vendor" => "0000010560", "producto" => "DD0121"); 
        $usuario7 = array("vendor" => "0000010561", "producto" => "DD0122"); 
        $usuario8 = array("vendor" => "0000010562", "producto" => "DD0123"); 
        $usuario9 = array("vendor" => "0000010563", "producto" => "DD0124"); 
        $usuario10 = array("vendor" => "0000010564", "producto" => "DD0125"); 
        $usuario11 = array("vendor" => "0000010565", "producto" => "DD0126"); 
        
        $usuario_noencontrado = array("vendor" => "9999999999", "producto" => "DD0063"); // Fabiola Mancilla 

        $alimentacion = array("nombre" => "ALIMENTACION", "cuenta" => "6011110103");
        $hospedaje    = array("nombre" => "HOSPEDAJE",    "cuenta" => "6011110101");
        $transportes  = array("nombre" => "TRANSPORTES",  "cuenta" => "6011110102");
        $pasajes      = array("nombre" => "PASAJES",      "cuenta" => "6011110104");                

        // conceptos de caja chica
        $mantenimiento = array("nombre" => "MANTTO. TIENDA", "cuenta" => "6010710102");
        $fumigacion    = array("nombre" => "FUMIGACION",     "cuenta" => "6010710102");
        $limpiezacrist = array("nombre" => "LIMP. CRISTALES", "cuenta" => "6010710102");


        //CASO 1
        //Anticipo Sencillo
        //Comprobacion Sencillo
        error_log("");
        error_log("-- CASO 1.1 / AP");
        //CreateAnticipoConComprobacion1($bu, $depto, $usuario1, '2011-03-28', $alimentacion, '1160.00', '0.00', '0.00');  // 1450      
        CreateAnticipoConComprobacionTUA1($bu, $depto, $usuario1, '2011-03-28', $alimentacion, '1000.00', '160.00', '0.16', '100.00');

//        error_log("");
//        error_log("-- CASO 1.1.1 / AP");
//        CreateAnticipoConComprobacion1($bu, $depto, $usuario1, '2010-12-01', $alimentacion, '350.00', '0.00', '0.00');  // 1450   

        //CASO 2 - Autorizacion Adicional de Presupuesto.
        //Anticipo Sencillo
        //Comprobacion Sencillo
//        error_log("");        
//        error_log("-- CASO 1.2 / AP");
//        CreateAnticipoConComprobacion1($bu, $depto, $usuario2, '2010-12-02', $hospedaje, '16216.21', '1783.79', '0.11'); // 18000 al 11
                 
        //CASO 3
        //Anticipo Sencillo
        //Comprobacion con Excedente
//        error_log("");        
//        error_log("-- CASO 1.3 / AP");        
//        CreateAnticipoConComprobacionExcedente1($bu, $depto, $usuario3, $fecha, '500.00', $alimentacion, '482.76', '77.24', '0.16');  
        

        //CASO 4
        //Anticipo Sencillo
        //Comprobacion 3 Lineas
//        error_log("");        
//        error_log("-- CASO 1.4 / AP");        
//        CreateAnticipoConComprobacion3($bu, $depto, $usuario4, $fecha, $pasajes,  '2750.00', '440.00', '0.16',
//                                                                       $alimentacion,  '1034.48', '165.52', '0.16',
//                                                                       $hospedaje,  '7327.59', '1172.41', '0.16');

        //CASO 5
        //Anticipo Sencillo
        //Comprobacion 3 Lineas divido en 2 cecos
//        $deptoPorcentaje1 = '.60';
//        $deptoPorcentaje2 = '.40';
//        error_log("");        
//        error_log("-- CASO 1.5 / AP");          
//        CreateAnticipoConComprobacion3_2Cecos($bu, $depto1, $deptoPorcentaje1, $depto2, $deptoPorcentaje2, 
//                                                                           $usuario5, $fecha, $pasajes,  '2750.00', '440.00', '0.16',
//                                                                                               $alimentacion,  '1034.48', '165.52', '0.16',
//                                                                                               $hospedaje,  '7327.59', '1172.41', '0.16');


        //CASO 6
        //Anticipo Sencillo en Dolares
        //Comprobacion Sencillo en Dolares   
//        error_log("");        
//        error_log("-- CASO 1.6 / AP");              
//        CreateAnticipoConComprobacionMonedaExtranjera1($bu, $depto, $usuario6, $fecha, "USD", $pasajes, '150.00', '0.00', '0.00');        

        //CASO 7
        //Anticipo Sencillo en Euros
        //Comprobacion Sencillo en Euros
//        error_log("");        
//        error_log("-- CASO 1.7 / AP");         
//        CreateAnticipoConComprobacionMonedaExtranjera1($bu, $depto, $usuario7, $fecha, "EUR", $pasajes, '150.00', '0.00', '0.00');           


        //CASO 8
        //Comprobacion Amex Sencilla - 1 Concepto
//        error_log("");        
//        error_log("-- CASO 1.8 / GL");                 
//        CreateComprobacionAmex1($bu, $depto, $usuario8, '2011-03-22', $pasajes,  '200.00', '0.00', '0.00'); 

        //CASO 9
        //Comprobacion Amex Sencilla - 3 Concepto (Usando 2 tasas de IVA)
//        error_log("");        
//        error_log("-- CASO 1.9 / GL");                 
//        CreateComprobacionAmex3($bu, $depto, $usuario9, $fecha, $pasajes,  '350.00', '38.50', '0.11',
//                                                               $alimentacion,  '1896.55', '303.45', '0.16',
//                                                               $hospedaje,  '4560.00', '729.60', '0.16');
                                                               
//        // CASO 10
//        //Comprobacion Amex Sencilla - 1 Concepto en Moneda Extranjera
//        error_log("");        
//        error_log("-- CASO 1.10 / GL");                 
//        CreateComprobacionAmexMonedaExtranjera1($bu, $depto, $usuario10, $fecha, 'USD', $pasajes,  '50.00', '0.00', '0.00'); 
                                                                    
        //CASO 11
        //Reembolso Caja Chica - 1 Concepto Caja Chica (AP)
//        error_log("");        
//        error_log("-- CASO 1.11 / GL");                 
//        CreateReembolso1($bu, $depto, $usuario11, $fecha, $mantenimiento,  '200.00', '32.00', '0.16');  
        
        //CASO 12 - Anticipo Mayor a Comprobacion
//        error_log("");        
//        error_log("-- CASO 1.12 / AP");            
//        CreateAnticipoDistintoConComprobacion1($bu, $depto, $usuario1, $fecha, '350.00', $alimentacion, '181.46', '29.04', '0.16');  
        
        //CASO 13 - Anticipo Menor a Comprobacion
//        error_log("");        
//        error_log("-- CASO 1.13 / AP");            
//        CreateAnticipoDistintoConComprobacion1($bu, $depto, $usuario1, $fecha, '100.00', $alimentacion, '181.46', '29.04', '0.16');    
        
        // A peticion de Gio
        
        // CASO 14
        //Comprobacion Amex Sencilla - 1 Concepto en Moneda Extranjera
//        error_log("");        
//        error_log("-- CASO 1.14 / GL");                 
//        CreateComprobacionAmexMonedaExtranjera1($bu, $depto, $usuario8, $fecha, 'USD', $alimentacion,  '150.00', '0.00', '0.00'); 
        
        // CASO 15
        //Comprobacion Amex Sencilla - 2 Conceptos en Moneda Extranjera    
//        error_log("");        
//        error_log("-- CASO 1.15 / GL");                  
//        CreateComprobacionAmexMonedaExtranjera2($bu, $depto, $usuario9, $fecha, 'USD', $pasajes,  '357.00', '0.00', '0.00',
//                                                                     $alimentacion,  '1540.00', '00.00', '0.00');           
                                                                     
        // CASO 16
        //Comprobacion Amex Sencilla - 2 Conceptos en Moneda Extranjera    
//        error_log("");        
//        error_log("-- CASO 1.16 / GL");                  
//        CreateComprobacionAmexMonedaExtranjera2($bu, $depto, $usuario10, $fecha, 'USD', $pasajes,  '1090.00', '0.00', '0.00',
//                                                                     $hospedaje,  '2509.00', '00.00', '0.00');        
                                                                     
        // CASO 17
        //Comprobacion Amex Sencilla - 2 Conceptos en Moneda Extranjera    
//        error_log("");        
//        error_log("-- CASO 1.17 / GL");                  
//        CreateComprobacionAmexMonedaExtranjera2($bu, $depto, $usuario11, $fecha, 'USD', $alimentacion,  '890.00', '0.00', '0.00',
//                                                                     $hospedaje,  '1599.00', '00.00', '0.00');                                                                       
    }     
     
    // GMAIF
    function PruebaFuncionalesConSoporteGMAIF(){
        
        $bu = 'GMAIF';
        $depto = 'AIF001'; 
        $fecha = '2011-01-14';

        $usuario1 = array("vendor" => "0000000010", "producto" => "DD0118"); // Alejandro Joaquin Marti Garcia
        $usuario2 = array("vendor" => "0000000011", "producto" => "DD0216"); // Carlos EMilio Gomez Andonaegui
        $usuario_noencontrado = array("vendor" => "999999999", "producto" => "DD0063"); // Fabiola Mancilla 
                 
        $alimentacion = array("nombre" => "ALIMENTACION", "cuenta" => "6011110103");
        $hospedaje    = array("nombre" => "HOSPEDAJE",    "cuenta" => "6011110101");
        $transportes  = array("nombre" => "TRANSPORTES",  "cuenta" => "6011110102");
        $pasajes      = array("nombre" => "PASAJES",      "cuenta" => "6011110104");                

        // conceptos de caja chica
        $mantenimiento = array("nombre" => "MANTTO. TIENDA", "cuenta" => "6010710102");
        $fumigacion    = array("nombre" => "FUMIGACION",     "cuenta" => "6010710102");
        $limpiezacrist = array("nombre" => "LIMP. CRISTALES", "cuenta" => "6010710102");


        //CASO 1
        //Anticipo Sencillo
        //Comprobacion Sencillo
        error_log("");
        error_log("-- CASO 2.1 / AP");
        CreateAnticipoConComprobacion1($bu, $depto, $usuario1, $fecha, $alimentacion, '181.46', '29.04', '0.16');        

        //CASO 4
        //Anticipo Sencillo
        //Comprobacion 3 Lineas
        error_log("");        
        error_log("-- CASO 2.2 / AP");        
        CreateAnticipoConComprobacion3($bu, $depto, $usuario2, $fecha, $pasajes,  '2750.00', '440.00', '0.16',
                                                                       $alimentacion,  '1034.48', '165.52', '0.16',
                                                                       $hospedaje,  '7327.59', '1172.41', '0.16');
        //CASO 6
        //Anticipo Sencillo en Dolares
        //Comprobacion Sencillo en Dolares   
        error_log("");        
        error_log("-- CASO 2.3 / AP");              
        CreateAnticipoConComprobacionMonedaExtranjera1($bu, $depto, $usuario1, $fecha, "USD", $pasajes,  '150.00', '0.00', '0.00');        


        //CASO 12
        error_log("");        
        error_log("-- CASO 2.4");                 
        //Anticipo con Error - Proveedor no encontrado
        //Comprobacion con Error - Proveedor no encontrado  
        //CreateAnticipoConComprobacion1($bu, $depto, $usuario_noencontrado, $fecha, $alimentacion, '181.46', '29.04', '0.16'); 
    }              
     
    // GMAIL
    function PruebaFuncionalesConSoporteGMAIL(){
        
        $bu = 'GMAIL';
        $depto = 'AIL001'; 
        $fecha = '2011-01-14';

        $usuario1 = array("vendor" => "0000000001", "producto" => "DD0118"); // Ramon Martinez Bello
        $usuario2 = array("vendor" => "0000000002", "producto" => "DD0216"); // Rosa Elena Nolasco Luna
        $usuario_noencontrado = array("vendor" => "999999999", "producto" => "DD0063"); // Fabiola Mancilla 
                 
        $alimentacion = array("nombre" => "ALIMENTACION", "cuenta" => "6011110103");
        $hospedaje    = array("nombre" => "HOSPEDAJE",    "cuenta" => "6011110101");
        $transportes  = array("nombre" => "TRANSPORTES",  "cuenta" => "6011110102");
        $pasajes      = array("nombre" => "PASAJES",      "cuenta" => "6011110104");                

        // conceptos de caja chica
        $mantenimiento = array("nombre" => "MANTTO. TIENDA", "cuenta" => "6010710102");
        $fumigacion    = array("nombre" => "FUMIGACION",     "cuenta" => "6010710102");
        $limpiezacrist = array("nombre" => "LIMP. CRISTALES", "cuenta" => "6010710102");


        //CASO 1
        //Anticipo Sencillo
        //Comprobacion Sencillo
        error_log("");
        error_log("-- CASO 3.1 / AP");
        CreateAnticipoConComprobacion1($bu, $depto, $usuario1, $fecha, $alimentacion, '181.46', '29.04', '0.16');        

        //CASO 4
        //Anticipo Sencillo
        //Comprobacion 3 Lineas
        error_log("");        
        error_log("-- CASO 3.2 / AP");        
        CreateAnticipoConComprobacion1($bu, $depto, $usuario1, $fecha, $pasajes,  '2750.00', '440.00', '0.16',
                                                                       $alimentacion,  '1034.48', '165.52', '0.16',
                                                                       $hospedaje,  '7327.59', '1172.41', '0.16');
        //CASO 6
        //Anticipo Sencillo en Dolares
        //Comprobacion Sencillo en Dolares   
        error_log("");        
        error_log("-- CASO 3.3 / AP");              
        CreateAnticipoConComprobacionMonedaExtranjera1($bu, $depto, $usuario1, $fecha, "USD", $pasajes,  '150.00', '00.00', '0.0');        


        //CASO 12
        error_log("");        
        error_log("-- CASO 3.4");                 
        //Anticipo con Error - Proveedor no encontrado
        //Comprobacion con Error - Proveedor no encontrado  
        //CreateAnticipoConComprobacion1($bu, $depto, $usuario_noencontrado, $fecha, $alimentacion, '181.46', '29.04', '0.16'); 
    }      
     
    // GMSAM
    function PruebaFuncionalesConSoporteGMSAM(){
        
        $bu = 'GMSAM';
        $depto = 'SAM001'; // Gestión Corporativa Marti
        $fecha = '2011-01-14';

        $usuario1 = array("vendor" => "0000000008", "producto" => "DD0118"); // Cecilia Beatriz Calvo Gomez
        $usuario2 = array("vendor" => "0000000009", "producto" => "DD0216"); // Notaria 140 y 236 S.C.
        $usuario_noencontrado = array("vendor" => "11111", "producto" => "DD0063"); // Fabiola Mancilla 
        
        $alimentacion = array("nombre" => "ALIMENTACION", "cuenta" => "6011110103");
        $hospedaje    = array("nombre" => "HOSPEDAJE",    "cuenta" => "6011110101");
        $transportes  = array("nombre" => "TRANSPORTES",  "cuenta" => "6011110102");
        $pasajes      = array("nombre" => "PASAJES",      "cuenta" => "6011110104");                

        // conceptos de caja chica
        $mantenimiento = array("nombre" => "MANTTO. TIENDA", "cuenta" => "6010710102");
        $fumigacion    = array("nombre" => "FUMIGACION",     "cuenta" => "6010710102");
        $limpiezacrist = array("nombre" => "LIMP. CRISTALES", "cuenta" => "6010710102");

        //CASO 1
        //Anticipo Sencillo
        //Comprobacion Sencillo
        error_log("");
        error_log("-- CASO 4.1 / AP");
        CreateAnticipoConComprobacion1($bu, $depto, $usuario1, $fecha, $alimentacion, '181.46', '29.04', '0.16');        

        //CASO 4
        //Anticipo Sencillo
        //Comprobacion 3 Lineas
        error_log("");        
        error_log("-- CASO 4.2 / AP");        
        CreateAnticipoConComprobacion3($bu, $depto, $usuario2, $fecha, $pasajes,  '2750.00', '440.00', '0.16',
                                                                       $alimentacion,  '1034.48', '165.52', '0.16',
                                                                       $hospedaje,  '7327.59', '1172.41', '0.16');
        //CASO 6
        //Anticipo Sencillo en Dolares
        //Comprobacion Sencillo en Dolares   
        error_log("");        
        error_log("-- CASO 4.3 / AP");              
        CreateAnticipoConComprobacionMonedaExtranjera1($bu, $depto, $usuario1, $fecha, "USD", $pasajes,  '150.00', '0.00', '0.00');        


        //CASO 12
        error_log("");        
        error_log("-- CASO 4.4");                 
        //Anticipo con Error - Proveedor no encontrado
        //Comprobacion con Error - Proveedor no encontrado  
        //CreateAnticipoConComprobacion1($bu, $depto, $usuario_noencontrado, $fecha, $alimentacion, '181.46', '29.04', '0.16'); 
    }          
     
    //LimpiaBase(); 
     
    //TestOracle();     
    //PruebaCaso1();
    
    PruebaConMonedaExtranjera();
    
    //PruebaFuncionalesConSoporteGMDEM();
    //PruebaFuncionalesConSoporteGMAIF();
    //PruebaFuncionalesConSoporteGMAIL();
    //PruebaFuncionalesConSoporteGMSAM();
    
    /*
    for ($i = 0; $i<20; $i++){
        PruebaCaso1();
    }*/
        
?>
