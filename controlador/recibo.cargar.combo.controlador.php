<?php

    require_once '../negocio/Recibo.clase.php';
    require_once '../util/funciones/Funciones.clase.php';

    try {
	$obj = new Recibo();
        $numero_linea = $_POST["p_linea"];
        $resultado = $obj->cargarRecibo($numero_linea);
	Funciones::imprimeJSON(200, "", $resultado);
	
    } catch (Exception $exc) {
	Funciones::imprimeJSON(500, $exc->getMessage(), "");
	
    }
