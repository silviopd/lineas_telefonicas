<?php

require_once '../negocio/Pago.clase.php';
require_once '../util/funciones/Funciones.clase.php';



$objVenta = new Pago();
try {
    
    $resultado = $objVenta->listar();
    
    Funciones::imprimeJSON(200, "", $resultado);
    
} catch (Exception $exc) {
    Funciones::imprimeJSON(500, $exc->getMessage(), "");
}


