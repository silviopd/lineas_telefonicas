<?php


require_once '../negocio/Pago.clase.php';
require_once '../util/funciones/Funciones.clase.php';

//if (! isset($_POST["p_datosFormulario"]) ){
//    Funciones::imprimeJSON(500, "Faltan parametros", "");
//    exit();
//}

$datosFormulario = $_POST["p_datosFormulario"];

//Convertir todos los datos que llegan concatenados a un array
parse_str($datosFormulario, $datosFormularioArray);


//echo '<pre>';
//print_r($datosFormulario);
//echo '</pre>';


try {
    $objVenta = new Pago();
    $objVenta->setFecha_pago( $datosFormularioArray["txtfec"]);
    $objVenta->setTotal( $datosFormularioArray["txtimporte"]);
        
    
    
    $resultado = $objVenta->agregar();
    
    if ($resultado == true){
        Funciones::imprimeJSON(200, "La venta ha sido registrada correctamente", "");
    }
    
} catch (Exception $exc) {
    Funciones::imprimeJSON(500, $exc->getMessage(), "");
}



