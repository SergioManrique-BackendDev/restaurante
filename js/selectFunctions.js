function getHorarios(){
    if(
        $("#select-restaurante").val() && $("#select-restaurante").val() != 0 &&
        $("#select-tipo_mesa").val() && $("#select-tipo_mesa").val() != 0 &&
        $("#input-fecha_reserva").val() && $("#input-fecha_reserva").val() != ""
    ){
        $.post("/php/api.php", {'request' : 'horarios_disponibles', 'restaurante' : $("#select-restaurante").val(), 'tipo_mesa' : $("#select-tipo_mesa").val(), 'fecha' : $("#input-fecha_reserva").val()})
        .done(function(response){
            $("#select-horario").html("");
            response['data'].forEach(function(hora){
                var SelectOption = "<option value='" + hora + "'>" + hora + "</option>";
                $("#select-horario").append(SelectOption);
            })
        });
    }
}

$(function(){

$.post("/php/api.php", {'request':'restaurantes'})
.done(function(response){
    response['data'].forEach(function(restaurante){
        var SelectOption = "<option value='" + restaurante.id + "'>" + restaurante.nombre + "</option>";
        $("#select-restaurante").append(SelectOption);
    });
});

$.post("/php/api.php", {'request':'tipos_mesa'})
.done(function(response){
    response['data'].forEach(function(tipo_mesa){
        var SelectOption = "<option value='" + tipo_mesa.id + "'>" + tipo_mesa.tipo + "</option>";
        $("#select-tipo_mesa").append(SelectOption);
    });
});


$("#select-restaurante").add("#select-tipo_mesa").add("#input-fecha_reserva").on("change", getHorarios);

})