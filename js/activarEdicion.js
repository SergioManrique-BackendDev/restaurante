$(function(){

$("#input-edit").on("change", function(){
    if($("#input-edit").is(":checked")){
        $("#input-fecha_reserva")
        .add("#select-restaurante")
        .add("#select-tipo_mesa")
        .add("#select-horario")
        .add("#input-factura")
        .add("#btn-editar-reserva")
        .prop("disabled", "");
    } else {
        $("#input-fecha_reserva")
        .add("#select-restaurante")
        .add("#select-tipo_mesa")
        .add("#select-horario")
        .add("#input-factura")
        .add("#btn-editar-reserva")
        .prop("disabled", "true");
    }
})

})