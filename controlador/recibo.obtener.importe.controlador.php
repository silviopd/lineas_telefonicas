<?php

    require_once '../negocio/Recibo.clase.php';
    require_once '../util/funciones/Funciones.clase.php';

    try {
	$obj = new Recibo();
        $numero_linea = $_POST["p_linea"];
        $recibo = $_POST["p_recibo"];
        $resultado = $obj->cargarImporte($numero_linea,$recibo);
	Funciones::imprimeJSON(200, "", $resultado);
	
    } catch (Exception $exc) {
	Funciones::imprimeJSON(500, $exc->getMessage(), "");
	
    }
