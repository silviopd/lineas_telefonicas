<?php

require_once '../datos/Conexion.clase.php';

class Recibo extends Conexion {

    public function cargarRecibo($numero_linea) {
        try {
            $sql = "select numero_prestamo,numero_cuota,(numero_cuota||' '||fecha_vencimiento_cuota||' '||saldo_cuota)as descripcion from prestamo_cuota where numero_prestamo=:p_linea";

            $sentencia = $this->dblink->prepare($sql);
            $sentencia->bindParam(":p_linea", $numero_linea);
            $sentencia->execute();

            $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);
            return $resultado;
        } catch (Exception $exc) {
            throw $exc;
        }
    }

    public function cargarImporte($numero_linea,$recibo) {
        try {
            $sql = " select numero_prestamo,numero_cuota,(numero_cuota||' '||fecha_vencimiento_cuota||' '||saldo_cuota)as descripcion,saldo_cuota as importe from prestamo_cuota where numero_prestamo=:p_linea and numero_cuota=:p_recibo";

            $sentencia = $this->dblink->prepare($sql);
            $sentencia->bindParam(":p_linea", $numero_linea);
            $sentencia->bindParam(":p_recibo", $recibo);
            $sentencia->execute();

            $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);
            return $resultado;
        } catch (Exception $exc) {
            throw $exc;
        }
    }
}
