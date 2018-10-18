<?php

require_once '../datos/Conexion.clase.php';

class Pago extends Conexion {

    private $num_pago;
    private $fecha_pago;
    private $total;
    private $detalleVenta; //JSON

    function getNum_pago() {
        return $this->num_pago;
    }

    function getFecha_pago() {
        return $this->fecha_pago;
    }

    function getTotal() {
        return $this->total;
    }

    function getDetalleVenta() {
        return $this->detalleVenta;
    }

    function setNum_pago($num_pago) {
        $this->num_pago = $num_pago;
    }

    function setFecha_pago($fecha_pago) {
        $this->fecha_pago = $fecha_pago;
    }

    function setTotal($total) {
        $this->total = $total;
    }


    public function agregar() {
        $this->dblink->beginTransaction();
        try {
            $sql = "select numero+1 as nc from correlativo where tabla='pago'";
            $sentencia = $this->dblink->prepare($sql);
            $sentencia->execute();
            $resultado = $sentencia->fetch();

            if ($sentencia->rowCount()) {
                $nuevoNumeroVenta = $resultado["nc"];
                $this->setNum_pago($nuevoNumeroVenta);


                $sql = "
                        INSERT INTO pago_prestamo(
                        numero_pago, fecha_pago,total_pagado)
                        VALUES (:p_num_pago, :p_fecha_pago, :p_total);
                    ";

                //Preparar la sentencia
                $sentencia = $this->dblink->prepare($sql);

                //Asignar un valor a cada parametro
                $sentencia->bindParam(":p_num_pago", $this->getNum_pago());
                $sentencia->bindParam(":p_fecha_pago", $this->getFecha_pago());
                $sentencia->bindParam(":p_total", $this->getTotal());
                //Ejecutar la sentencia preparada
                $sentencia->execute();


                /* INSERTAR EN LA TABLA VENTA_DETALLE */
                $detalleVentaArray = json_decode($this->getDetalleVenta()); //Convertir de formato JSON a formato array


//                $item = 0;

                foreach ($detalleVentaArray as $key => $value) { //permite recorrer el array
                    
                    $sql = "
                           INSERT INTO pago_prestamo_cuota(
                            numero_pago, numero_prestamo,numero_cuota, importe_pagado)
                            VALUES (:p_numero_pago, :p_numero_recibo,:p_numero_cuota,, :p_importe);

                        ";


                    //Preparar la sentencia
                    $sentencia = $this->dblink->prepare($sql);


                    //Asignar un valor a cada parametro
                    $sentencia->bindParam(":p_numero_pago", $this->getNum_pago());
                    $sentencia->bindParam(":p_numero_recibo", $value->codigoprestamo);
                    $sentencia->bindParam(":p_numero_cuota", $value->codigocuota);
                    $sentencia->bindParam(":p_importe", $this->getTotal());

                    //Ejecutar la sentencia preparada
                    $sentencia->execute();


                    /* ACTUALIZAR EL STOCK DE CADA ARTICULO VENDIDO */
                    $sql = "UPDATE prestamo_cuota
                            SET saldo_cuota=importe_cuota-:p_saldo
                          WHERE numero_prestamo=:p_prestamo AND numero_cuota=:p_cuota";

                    $sentencia = $this->dblink->prepare($sql);
                    $sentencia->bindParam(":p_saldo", $value->importe);
                    $sentencia->bindParam(":p_prestamo", $value->codigoprestamo);
                    $sentencia->bindParam(":p_cuota", $value->codigocuota);
                    $sentencia->execute();
                    /* ACTUALIZAR EL STOCK DE CADA ARTICULO VENDIDO */
                }

                /* INSERTAR EN LA TABLA VENTA_DETALLE */


                //Actualizar el correlativo en +1
                $sql = "UPDATE correlativo set numero= numero + 1 WHERE tabla='pago' ;";
                $sentencia = $this->dblink->prepare($sql);
                $sentencia->execute();

                //Actualizar el correlativo segun el tipo de doc (BV/FA) y la serie
//                $sql = "update serie_comprobante set numero_documento = numero_documento + 1 where codigo_tipo_comprobante = :p_codigo_tipo_comprobante and numero_serie = :p_numero_serie";
//                $sentencia = $this->dblink->prepare($sql);
//                $sentencia->bindParam(":p_codigo_tipo_comprobante", $this->getCodigoTipoComprobante());
//                $sentencia->bindParam(":p_numero_serie", $this->getNumeroSerie());
//                $sentencia->execute();
                //Terminar la transacción
                $this->dblink->commit();

                return true;
            }
        } catch (Exception $exc) {
            $this->dblink->rollBack(); //Extornar toda la transacción
            throw $exc;
        }

        return false;
    }

    public function listar() {
        try {
            $sql = "SELECT 
  pago_prestamo_cuota.numero_pago, 
  pago_prestamo_cuota.numero_prestamo, 
  pago_prestamo_cuota.numero_cuota, 
  pago_prestamo.fecha_pago, 
  (cliente.apellidos ||' '|| cliente.nombres) as nombreCli, 
  producto.nombre, 
  prestamo.importe, 
  pago_prestamo_cuota.importe_pagado, 
  pago_prestamo.estado
FROM 
  public.cliente, 
  public.pago_prestamo, 
  public.pago_prestamo_cuota, 
  public.prestamo, 
  public.prestamo_cuota, 
  public.producto
WHERE 
  cliente.dni_cliente = prestamo.dni_cliente AND
  pago_prestamo.numero_pago = pago_prestamo_cuota.numero_pago AND
  prestamo.numero_prestamo = prestamo_cuota.numero_prestamo AND
  prestamo_cuota.numero_prestamo = pago_prestamo_cuota.numero_prestamo AND
  prestamo_cuota.numero_cuota = pago_prestamo_cuota.numero_cuota AND
  producto.codigo_producto = prestamo.codigo_producto;
";
            $sentencia = $this->dblink->prepare($sql);
            $sentencia->execute();

            $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);

            return $resultado;
        } catch (Exception $exc) {
            throw $exc;
        }
    }

    public function anular($numeroVenta) {
        $this->dblink->beginTransaction();
        try {
            $sql = "update pago set estado = 'A' where num_pago = :p_num_pago";
            $sentencia = $this->dblink->prepare($sql);
            $sentencia->bindParam(":p_num_pago", $numeroVenta);
            $sentencia->execute();
//            
            $sql = "select numero_recibo from pago_detalle where numero_pago = :p_numero_pago";
            $sentencia = $this->dblink->prepare($sql);
            $sentencia->bindParam(":p_numero_pago", $numeroVenta);
            $sentencia->execute();
            $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);
//            
            for ($i = 0; $i < count($resultado); $i++) {
                $sql = "update recibo set estado = 'P' where numero_recibo = :p_numero_recibo";
                $sentencia = $this->dblink->prepare($sql);
                $sentencia->bindParam(":p_numero_recibo", $resultado[$i]["numero_recibo"]);
                $sentencia->execute();
            }
//            
            //Terminar la transacción
            $this->dblink->commit();

            return true;
        } catch (Exception $exc) {
            $this->dblink->rollBack(); //Extornar toda la transacción
            throw $exc;
        }
    }
    
    public function listarVentaDetalle() {
        try {
            $sql = "SELECT 
            pago.num_pago,
            cliente.nombre, 
            recibo.numero_linea_telefonica, 
            recibo.numero_recibo,
            recibo.fecha_vencimiento_deuda, 
            recibo.importe
          FROM 
            public.cliente, 
            public.pago, 
            public.pago_detalle, 
            public.recibo, 
            public.linea_telefonica
          WHERE 
            cliente.dni = linea_telefonica.dni AND
            pago.num_pago = pago_detalle.numero_pago AND
            pago_detalle.numero_recibo = recibo.numero_recibo AND
            linea_telefonica.dni = recibo.dni AND
            linea_telefonica.numero_linea_telefonica = recibo.numero_linea_telefonica AND 
            pago.num_pago =:p_numero_venta";
            $sentencia = $this->dblink->prepare($sql);
            $sentencia->bindParam(":p_numero_venta", $numeroVenta);
            $sentencia->execute();

            $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);

            return $resultado;
        } catch (Exception $exc) {
            throw $exc;
        }
    }

}
