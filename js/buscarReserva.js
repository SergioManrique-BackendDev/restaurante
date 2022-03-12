function BuscarReserva(){
    var BuscarMethod = $("#select-buscar-method").val();
    var BuscarData = $("#input-buscar-data").val();
    $.post("/php/api.php", {'request' : 'buscar', 'data' : BuscarData, 'metodo' : BuscarMethod})
    .done(function(response){
        console.log(response);
        if(response['data'][0] && response['data'][1]){
            var ReservaData = response['data'][0];
            var UsuarioData = response['data'][1];
            $("#input-folio").val(ReservaData.folio);
            $("#input-nombre").val(UsuarioData.nombre);
            $("#input-apellido").val(UsuarioData.apellido);
            $("#input-fecha_nacimiento").val(UsuarioData.fecha_nacimiento);
            $("#input-correo").val(UsuarioData.correo);
            $("#input-telefono").val(UsuarioData.telefono);
            $("#input-rfc").val(UsuarioData.rfc);
            $("#input-nickname").val(UsuarioData.nickname);
            $("#input-numero_mesa").val(ReservaData.numero_mesa);
            $("#input-fecha_reserva").val(ReservaData.fecha);
            $("#select-restaurante").val(ReservaData.restaurante);
            $("#select-tipo_mesa").val(ReservaData.tipo_mesa);
            getHorarios();
            setTimeout(function(){
                $("#select-horario").val(ReservaData.hora_entrada);
                $("#select-horario").css("color", "initial");
            }, 250);
            if(ReservaData.factura == 1){
                $("#input-factura").prop("checked", true);
            } else {
                $("#input-factura").prop("checked", false);
            }
        }
    });
}

$(function(){

$("#btn-buscar-reserva").on("click", BuscarReserva);

})