<?php

require_once '../datos/Conexion.clase.php';

class Linea_Telefonica extends Conexion {
    

    public function cargarNumero($dni){
        try {
            $sql = "select numero_prestamo,(numero_prestamo||' '|| fecha_prestamo ||' '|| importe)as descripcion from prestamo where dni_cliente=:p_dni and estado='E'";
            $sentencia = $this->dblink->prepare($sql);
             $sentencia->bindParam(":p_dni", $dni);
            $sentencia->execute();
            $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);
            return $resultado;
        } catch (Exception $exc) {
            throw $exc;
        }
    }

    
}
