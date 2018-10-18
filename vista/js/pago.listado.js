$("#btnagregar").click(function(){
    document.location.href = "pago.vista.php";
});

$("#btnfiltrar").click(function(){
    listar();
});


function listar(){
    
    $.post
    (
        "../controlador/pago.listado.controlador.php",
        {
        }
    ).done(function(resultado){
        var datosJSON = resultado;
        
        if (datosJSON.estado===200){
            var html = "";

            html += '<small>';
            html += '<table id="tabla-listado" class="table table-bordered table-striped">';
            html += '<thead>';
            html += '<tr style="background-color: #ededed; height:25px;">';
            
            html += '<th style="text-align: center" style="font-size:13px">OPCIONES</th>';
            html += '<th style="font-size:13px">N.Pago</th>';
            html += '<th style="font-size:13px">N.Prestamo</th>';
            html += '<th style="font-size:13px">N.Cuota</th>';
            html += '<th style="font-size:13px">Fecha</th>';
            html += '<th style="font-size:13px">Nombre</th>';
            html += '<th style="font-size:13px">Cliente</th>';
            html += '<th style="font-size:13px">Importe</th>';
            html += '<th style="font-size:13px">Importe Pagado</th>';
            html += '<th style="font-size:13px">Estado</th>';
            
            html += '</tr>';
            html += '</thead>';
            html += '<tbody>';

            //Detalle
            $.each(datosJSON.datos, function(i,item) {
                
                if (item.estado === "E"){
                    html += '<tr>';
                    html += '<td align="center">';
                    html += '<button type="button" class="btn btn-danger btn-xs" onclick="anular(' + item.numero_pago + ')"><i class="fa fa-close"></i></button>';
                   
                }else{                    
                    html += '<tr style="text-decoration:line-through; color:red">';
                    html += '<td align="center">';
                }
                
                
                html += '<td align="center" style="font-size:12px">'+item.numero_pago+'</td>';
                html += '<td style="font-size:12px">'+item.numero_prestamo+'</td>';
                html += '<td style="font-size:12px">'+item.numero_cuota+'</td>';
                html += '<td style="font-size:12px">'+item.fecha_pago+'</td>';
                html += '<td style="font-size:12px">'+item.nombreCli+'</td>';
                html += '<td style="font-size:12px">'+item.nombre+'</td>';
                html += '<td style="font-size:12px">'+item.importe+'</td>';
                html += '<td style="font-size:12px">'+item.importe_pagado+'</td>';
                html += '<td style="font-size:12px">'+item.estado+'</td>';
                html += '</tr>';
            });

            html += '</tbody>';
            html += '</table>';
            html += '</small>';
            
            $("#listado").html(html);
            
            $('#tabla-listado').dataTable({
               "aaSorting": [[1, "asc"]],
                "sScrollX": "100%",
                "sScrollXInner": "150%",
                "bScrollCollapse": true,
                "bPaginate": true,
                "bProcessing": true
            });
            
            
            
	}else{
            swal("Mensaje del sistema", resultado , "warning");
        }
        
    }).fail(function(error){
        var datosJSON = $.parseJSON( error.responseText );
        swal("Error", datosJSON.mensaje , "error"); 
    });
    
}


$(document).ready(function(){
    listar();
});


function anular(numeroVenta){
   swal({
            title: "Confirme",
            text: "¿Esta seguro de anular la venta seleccionada?",

            showCancelButton: true,
            confirmButtonColor: '#d93f1f',
            confirmButtonText: 'Si',
            cancelButtonText: "No",
            closeOnConfirm: false,
            closeOnCancel: true,
            imageUrl: "../imagenes/eliminar.png"
	},
	function(isConfirm){
            if (isConfirm){
                $.post(
                    "../controlador/pago.anular.controlador.php",
                    {
                        p_numero_venta: numeroVenta
                    }
                    ).done(function(resultado){
                        var datosJSON = resultado;   
                        if (datosJSON.estado===200){ //ok
                            listar();
                            swal("Exito", datosJSON.mensaje , "success");
                        }

                    }).fail(function(error){
                        var datosJSON = $.parseJSON( error.responseText );
                        swal("Error", datosJSON.mensaje , "error");
                    });
                
            }
	});
   
}

function informacion(numeroVenta) {
    $.post
            (
                    "../controlador/pago.listar.detalle.controlador.php",
                    {
                        numeroVenta: numeroVenta
                    }
            ).done(function (resultado) {
        var datosJSON = resultado;

        if (datosJSON.estado === 200) {
            var html = "";
            html += '<small>';
            html += '<table id="tabla-listado" class="table table-bordered table-striped">';
            html += '<thead>';
            html += '<tbody>';
            $.each(datosJSON.datos, function (i, item) {
                html += '<tr>';
                html += '<td style="font-size:12px; vertical-align:middle; text-align: right">' + item.numero_linea_telefonica + '</td>';
                html += '<td style="font-size:12px; vertical-align:middle; text-align: right">' + item.numero_recibo + '</td>';
                html += '<td style="font-size:12px; vertical-align:middle; text-align: right">' + item.fecha_vencimiento_deuda + '</td>';
                html += '<td style="font-size:12px; vertical-align:middle; text-align: right">' + item.importe + '</td>';
                html += '</tr>';

                $("#txtclientemodal").val(item.nombre);
            });
            html += '</tbody>';
            html += '</table>';
            html += '</small>';

            $("#detalleventa-informacion").html(html);
        }
    }).fail(function (error) {
        var datosJSON = $.parseJSON(error.responseText);
        swal("Error", datosJSON.mensaje, "error");
    });
}

$("input[name='rbtipo']").change(function () {
    if ($("input[name='rbtipo']:checked").val() == 2) {
        $("#txtfecha1").prop("disabled", false);
        $("#txtfecha2").prop("disabled", false);
    } else {
        $("#txtfecha1").prop("disabled", true);
        $("#txtfecha2").prop("disabled", true);
    }
});