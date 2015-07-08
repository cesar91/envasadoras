<?php
//FUNCIONES Y UTILERIAS DEL FRAMEWORK

//DOWNLOAD FILES.

function downloadFile($downloadPath,$filename){
	// File Download Script
	// by James Warkentin
	// This script is provided without warrenty of any kind, nor may the author be held liable for
	// for any problems which arise as a result of the use of this script.
	// Modify this line to indicate the location of the files you want people to be able to download
	// This path must not contain a trailing slash.  ie.  /temp/files/download

	$download_path = $downloadPath;



	/* ###################################################################################*/
	/*                     DO NOT MODIFY THE SCRIPT BEYOND THIS POINT                     */
	/* ###################################################################################*/

	//$filename = $_GET['filename'];

	// Detect missing filename
	if(!$filename) die("Debe especificar un archivo.");

	// Make sure we can't download files above the current directory location.
	if(eregi("\.\.", $filename)) die("Lo siento no puede descargar ese archivo");
	$file = str_replace("..", "", $filename);

	// Make sure we can't download .ht control files.
	if(eregi("\.ht.+", $filename)) die("Lo siento no puede descargar ese archivo");

	// Combine the download path and the filename to create the full path to the file.
	$file = $download_path.$file;
	//echo $file;
	// Test to ensure that the file exists.
	if(!file_exists($file)) die("Lo sentimos, el archivo no existe");

	// Extract the type of file which will be sent to the browser as a header
	$type = filetype($file);

	// Get a date and timestamp
	$today = date("F j, Y, g:i a");
	$time = time();

	// Send file headers
	header("Content-type: $type");
	header("Content-Disposition: attachment;filename=$filename");
	header('Pragma: no-cache');
	header('Expires: 0');

	// Send the file contents.
	readfile($file);
}

function exportxls2file($export, $nombre,$head,$fname){
    $fields = mysql_num_fields ( $export );
    $header="";
    $data="";
	for ( $i = 0; $i < $fields; $i++ )
	{
		$header .= mysql_field_name( $export , $i ) . ",";
	}
	while( $row = mysql_fetch_row( $export ) )
	{
		$line = '';
		foreach( $row as $value )
		{
			if ( ( !isset( $value ) ) || ( $value == "" ) )
			{
				$value = ",";
			}
			else
			{
				$value = str_replace( '"' , '""' , $value );
				$value = '"' . $value . '"' . ",";
			}
			$line .= $value;
		}
		$data .= trim( $line ) . "\n";
	}
	//$data = str_replace( "\r" , "" , $data );

	if ( $data == "" )
	{
		$data = "\n(0) Registros encontrados!\n";
	}

	$fp = fopen($fname,'w');
	fwrite($fp,"$head\n$header\n$data");
	fclose($fp);
}

function exportxls($export, $nombre,$head){
    $fields = mysql_num_fields ( $export );
    $header="";
    $data="";

  	//error_log(".....".$nombre);
  	//error_log(".....".$head);
  	if($head == "REPORTE DE GASTOS"){
  		$csv_sep = ",";
  		$header ="";
  		$csv_end = "
  		";
  		//cabeceras
  		$header .=
  		"FOLIO".$csv_sep.  		
  		"TIPO DE COMPROBACION".$csv_sep.  		
  		"FECHA DE COMPROBACION".$csv_sep.  		
  		"NUMERO DE EMPLEADO".$csv_sep.
  		"NOMBRE DEL EMPLEADO".$csv_sep.
  		"EMPRESA".$csv_sep.
  		"DOCUMENTO".$csv_sep.
  		"FECHA DE SOLICITUD".$csv_sep.
  		"FOLIO DE SOLICITUD".$csv_sep.
  		"FECHA DE AUTORIZACION DE COMPROBACION".$csv_sep.
  		"MOTIVO".$csv_sep.
  		"ANTICIPO COMPROBADO".$csv_sep.
  		"CENTRO DE COSTOS".$csv_sep.
  		"CONCEPTO".$csv_sep.
  		"CUENTA CONTABLE".$csv_sep.
  		"IMPORTE SIN IVA".$csv_sep.
  		"PROPINA".$csv_sep.
  		"IMPUESTO SOBRE HOSPEDAJE".$csv_sep.
  		"IVA".$csv_sep.
  		"MONTO TOTAL".$csv_sep.
  		"MONEDA".$csv_sep.
  		"RFC".$csv_sep.
  		"TRANSACCION".$csv_sep.
  		"NUMERO DE TARJETA".$csv_sep.
  		"RUTA DE AUTORIZACION".$csv_sep."";
	}else{
    	for ( $i = 0; $i < $fields; $i++ )
		{
			$header .= mysql_field_name( $export , $i ) . ",";
		}
    
  	}
  		
	while( $row = mysql_fetch_row( $export ) )
	{
		$line = '';
		foreach( $row as $value )
		{
			if ( ( !isset( $value ) ) || ( $value == "" ) )
			{
				$value = ",";
			}
			else
			{
				$value = str_replace( '"' , '""' , $value );
				$value = '"' . $value . '"' . ",";
			}
			$line .= $value;
		}
		$data .= trim( $line ) . "\n";
	}
	//$data = str_replace( "\r" , "" , $data );

	if ( $data == "" )
	{
		$data = "\n(0) Registros encontrados!\n";
	}

	header("Content-type: application/octet-stream");
	header("Content-Disposition: attachment; filename=".$nombre.".csv");
	header("Pragma: no-cache");
	header("Pragma: public");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Expires: 0");
	print "$head\n$header\n$data";



}

function exportxlsarray($export, $headerarray, $nombre,$head){
    $fields = count ( $headerarray);
    $header="";
    $data="";
	for ( $i = 0; $i < $fields; $i++ )
	{
		$header .= $headerarray[$i]. ",";
	}
	$rows=0;
	$row=array();
	foreach( $export as $row)
	{
		$line = '';

		foreach( $row as $value )
		{
			if ( ( !isset( $value ) ) || ( $value == "" ) )
			{
				$value = ",";
			}
			else
			{
				$value = str_replace( '"' , '""' , $value );
				$value = '"' . $value . '"' . ",";
			}
			$line .= $value;
		}
		$data .= trim( $line ) . "\n";
	}
	//$data = str_replace( "\r" , "" , $data );

	if ( $data == "" )
	{
		$data = "\n(0) Registros encontrados!\n";
	}

	header("Content-type: application/octet-stream");
	header("Content-Disposition: attachment; filename=".$nombre.".csv");
	header("Pragma: no-cache");
	header("Pragma: public");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Expires: 0");
	print "$head\n$header\n$data";



}


?>