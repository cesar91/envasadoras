<?php
require_once("$RUTA_A/lib/php/constantes.php");
require_once "$RUTA_A/Connections/fwk_db.php";

class Hotel extends conexion {

	protected $h_hotel_id;
	protected $rst_rstHotel;

	public function __construct() {
		parent::__construct();
		$this->h_hotel_id = "";
	}
	
	public function Get_dato($nombre) {
		return(mysql_result($this->rst_rstHotel, 0, $nombre));
	}
	
	public function agregarHotel($svi_id, $divisaHotel, $sComentario, $sTipoHotel, $sCostoNoche, $sHotel, $sIva, $sLlegada, $sMontoH_pesos, $sCiudad, $sNoches, $sNo_reservacion, $sSalida, $sSubtotal, $sTotal, $sBandera){
		$query = sprintf("INSERT INTO hotel(
				svi_id,
				h_hotel_id,
				div_id,
				h_comentarios,
				h_tipo_hotel,
				h_costo_noche,
				h_nombre_hotel,
				h_iva,
				h_fecha_llegada,
				h_total_pesos,
				h_ciudad,
				h_noches,
				h_no_reservacion,
				h_fecha_salida,
				h_subtotal,
				h_total,
				h_cotizacion_inicial)
			VALUES(
				'%s',
				default,
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				 %s)",
				$svi_id, $divisaHotel, $sComentario, $sTipoHotel, $sCostoNoche, $sHotel, $sIva, $sLlegada, $sMontoH_pesos, $sCiudad, $sNoches, $sNo_reservacion, $sSalida, $sSubtotal, $sTotal, $sBandera);
		//error_log("--->>Insercion de Hoteles: ".$query);
		$this->h_hotel_id=parent::insertar($query);
		return($this->h_hotel_id);
	}
	
	public function borraHotelesporTramite($itinerario){
		$query=sprintf("DELETE FROM hotel WHERE svi_id = %s", $itinerario);
		parent::ejecutar($query);
	}
}
?>