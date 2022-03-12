$(function(){

$("#btn-editar-reserva").on("click", function(){
    if($("#input-folio").val() !== ""){
        var DataReserva = {
            'Folio' : $("#input-folio").val(),
            'Fecha_reserva' : $("#input-fecha_reserva").val(),
            'Restaurante' : $("#select-restaurante").val(),
            'Tipo_mesa' : $("#select-tipo_mesa").val(),
            'Hora' : $("#select-horario").val(),
            'Factura' : $("#input-factura").is(":checked")
        };
        var DataReservaJson = JSON.stringify(DataReserva);
        $.post("/php/api.php", {'request' : 'editar_reserva', 'data' : DataReservaJson})
        .done(function(response){
            console.log(response);
            $("#error-popup").children().html(response['data']);
            if(response['success'] == 1){
                $("#error-popup").removeClass(["d-none", "border-warning", "bg-warning"]);
                $("#error-popup").addClass(["border-success", "bg-success"]);
                BuscarReserva();
            } else {
                $("#error-popup").removeClass(["d-none", "border-success", "bg-success"]);
                $("#error-popup").addClass(["border-warning", "bg-warning"]);
            }
        })
    }
});

})