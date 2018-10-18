/*INICIO: BUSQUEDA DE CLIENTES*/
$("#txtnombrecliente").autocomplete({
    source: "../controlador/cliente.autocompletar.controlador.php",
    minLength: 1, //Filtrar desde que colocamos 2 o mas caracteres
    focus: f_enfocar_registro_cliente,
    select: f_seleccionar_registro_cliente
});

function f_enfocar_registro_cliente(event, ui) {
    var registro = ui.item.value;
    $("#txtcodigocliente").val(registro.nombre);
    event.preventDefault();
}
/*FIN: BUSQUEDA DE CLIENTES*/


/*INICIO: LLENAR CLIENTE CON ENTER*/
function f_seleccionar_registro_cliente(event, ui) {
    var registro = ui.item.value;
    $("#txtnombrecliente").val(registro.nombre);
    $("#txtcodigocliente").val(registro.dni);
    $("#txtdireccioncliente").val(registro.direccion);
    $("#txttelefonocliente").val(registro.telefono);

    cargarComboNumero("#cbonumero", "seleccione",registro.dni);


    event.preventDefault();
}
/*FIN: LLENAR CLIENTE CON ENTER*/