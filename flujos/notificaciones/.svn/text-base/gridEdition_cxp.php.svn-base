<?php
require_once '../../jqGridPHP/jq-config.php';
// include the jqGrid Class
require_once "../../jqGridPHP/php/jqGrid.php";
// include the driver class
require_once "../../jqGridPHP/php/jqGridPdo.php";
// Connection to the server

//$c="SELECT t_id, t_iniciador, t_owner, t_etapa_actual FROM tramites where t_id=".$_GET['id'];
$conn = new PDO(DB_DSN,DB_USER,DB_PASSWORD);
// Tell the db that we use utf-8
$conn->query("SET NAMES utf8");


// Create the jqGrid instance
$grid = new jqGridRender($conn);
// Write the SQL Query
if(isset($_GET['id_Solicitud']))
	$id_Solicitud=$_GET['id_Solicitud'];

$x=$_GET['id'];

$grid->SelectCommand = "SELECT svi_id, svi_tipo_viaje, svi_origen, svi_destino,  svi_medio, svi_kilometraje,  svi_horario, svi_fecha_salida, svi_noches_hospedaje, svi_monto_peaje, svi_nombre_hotel, svi_monto_hospedaje, svi_hotel  FROM sv_itinerario  where svi_solicitud={$id_Solicitud}";
//$grid->SelectCommand = "SELECT t_id, t_iniciador, t_owner, t_etapa_actual, t_etiqueta, sv_motivo, sv_id FROM sv_itinerario inner join solicitud_viaje on (sv_itinerario.svi_solicitud=solicitud_viaje.sv_id)  inner join tramites  on (tramites.t_id=solicitud_viaje.sv_tramite) where t_id={$x} ";
//$grid->SelectCommand = "SELECT t_id, t_iniciador, t_owner, t_etapa_actual FROM tramites where t_id={$x}";
// Set the table to where you add the data
$grid->table = 'sv_itinerario';


// Set output format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('grid_travel.php?id='.$_GET['id'].'&id_Solicitud='.$id_Solicitud);
$grid->addCol(array(
    "name"=>"Acciones",
    "formatter"=>"actions",
    "editable"=>false,
    "sortable"=>false,
    "resizable"=>false,
    "fixed"=>true,
    "width"=>50,
    "formatoptions"=>array("keys"=>true)
    ), "second");

$grid->setGridOptions(array(
    //"rowNum"=>10,
    "rowList"=>array(10,20,30),
    "sortname"=>"svi_id",
	"height"=>100,
	"width"=>900
));

$grid->setColProperty('svi_id', array("editable"=>false, "width"=>50,"hidden"=>true));
$grid->setColProperty('svi_tipo_viaje', array("editable"=>true,"width"=>32,"label"=>"Tipo","hidden"=>false,"edittype"=>"select","editoptions"=>array("value"=>"Nacional:Nacional;Extranjero:Extranjero")));
$grid->setColProperty('svi_origen', array("editable"=>false,"width"=>45,"label"=>"Origen"));
$grid->setColProperty('svi_destino', array("editable"=>false,"width"=>45,"rowpos"=>4,"colpos"=>5, "label"=>"Destino"));
$grid->setColProperty('svi_medio', array("editable"=>true,"width"=>30,"label"=>"Medio","edittype"=>"select","editoptions"=>array("value"=>"Terrestre:Terrestre;Aéreo:Aereo")));
//$grid->setColProperty('svi_medio', array("edittype"=>"select","editoptions"=>array("value"=>"Terrestre:Terrestre;Aéreo:Aéreo")));
$grid->setColProperty('svi_kilometraje', array("editable"=>true,"width"=>30,"thousandsSeparator"=>",","formatter"=>"numeric","label"=>"Km"));
$grid->setColProperty('svi_horario', array("editable"=>true,"width"=>67,"label"=>"Horario","edittype"=>"select","editoptions"=>array("value"=>"Cualquier horario:Cualquier horario;Mañana:Mañana;Tarde:Tarde;Noche:Noche")));
$grid->setColProperty('svi_fecha_salida', array("editable"=>true,"width"=>45, "label"=>"Salida"));
$grid->setColProperty('svi_noches_hospedaje', array("editable"=>true,"width"=>35, "label"=>"Noches"));
$grid->setColProperty('svi_monto_peaje', array("editable"=>true,"width"=>35, "label"=>"Peaje"));
$grid->setColProperty('svi_nombre_hotel', array("editable"=>true,"width"=>60, "label"=>"Hotel"));
$grid->setColProperty('svi_monto_hospedaje', array("editable"=>true,"width"=>40, "label"=>"$ Hotel"));
$grid->setColProperty('svi_hotel', array("editable"=>true,"width"=>35, "label"=>"Tipo Hab.","edittype"=>"select","editoptions"=>array("value"=>"1:Doble;0:Sencilla")));

 
 
//$grid->setColProperty("CustomerID",array("editoptions"=>array("value"=>"1:One;2:Two"))); 

$grid->setColProperty("svi_fecha_salida", array(
    "formatter"=>"date",
    "formatoptions"=>array("srcformat"=>"Y-m-d","newformat"=>"d/m/Y"),
    "search"=>false,
    "editable"=>true,
	 "editoptions"=>array("dataInit"=>
                "js:function(elm){setTimeout(function(){
                    jQuery(elm).datepicker({dateFormat:'dd/mm/yy'});
                    jQuery('.ui-datepicker').css({'font-size':'85%'});
                },200);}")
    )
);
/*$grid->setColProperty("BirthDate", array(
    "formatter"=>"date",
    "formatoptions"=>array("srcformat"=>"Y-m-d H:i:s","newformat"=>"d/m/Y"),
    "search"=>false,
    "editable"=>true
    )
);*/
/*$grid->setColProperty('BirthDate', 
        array("formatter"=>"date","formatoptions"=>array("srcformat"=>"Y-m-d H:i:s", "newformat"=>"d-m-Y")));*/

//**********Anexadoo ---- $grid->setSelect('FirstName', "SELECT FirstName, FirstName FROM employees");
// Set some grid options
/*$grid->setGridOptions(array(
    "rowNum"=>10,
    "rowList"=>array(10,20,30),
    "sortname"=>"tramites.t_id"
));*/

$grid->navigator = true;
$grid->setNavOptions('navigator', array("add"=>false,"edit"=>true,"del"=>false,"excel"=>false,"search"=>false,"refresh"=>false));
$grid->setNavOptions('edit', array("height"=>150,"dataheight"=>"auto"));

// Enjoy
$grid->renderGrid('#grid','#pager',true, null, null, true,true);

$conn = null;
?>
