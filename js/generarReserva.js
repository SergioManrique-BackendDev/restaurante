$(function(){

$("#btn-generar-reserva").on("click", function(){
    if(
        $("#input-nombre").val() !== "" &&
        $("#input-apellido").val() !== "" &&
        $("#input-fecha_nacimiento").val() !== "" &&
        $("#input-correo").val() !== "" &&
        $("#input-telefono").val() !== "" &&
        $("#input-rfc").val() !== "" &&
        $("#input-nickname").val() !== "" &&
        $("#input-fecha_reserva").val() !== "" &&
        $("#select-restaurante").val() !== 0 &&
        $("#select-tipo_mesa").val() !== 0 &&
        $("#select-horario").val() !== null
    ){        
        $(".form-control").each(function(){
            $(this).removeClass(["border", "border-danger"])
        });
        $("#error-popup").addClass("d-none");
        var UserData = {
            'Nombre' : $("#input-nombre").val(),
            'Apellido' : $("#input-apellido").val(),
            'Fecha_nacimiento' : $("#input-fecha_nacimiento").val(),
            'Correo' : $("#input-correo").val(),
            'Telefono' : $("#input-telefono").val(),
            'RFC' : $("#input-rfc").val(),
            'Nickname' : $("#input-nickname").val(),
            'Fecha_reserva' : $("#input-fecha_reserva").val(),
            'Restaurante' : $("#select-restaurante").val(),
            'Tipo_mesa' : $("#select-tipo_mesa").val(),
            'Hora' : $("#select-horario").val(),
            'Factura' : $("#input-factura").is(":checked")
        };
        var UserDataJson = JSON.stringify(UserData);
        $.post("/php/api.php", {'request' : 'crear_reserva', 'data' : UserDataJson})
        .done(function(response){
            $("#error-popup").children().html(response[1]);
            if(response[0] == 1){
                $("#error-popup").removeClass(["d-none", "border-warning", "bg-warning"]);
                $("#error-popup").addClass(["border-success", "bg-success"]);
            } else {
                $("#error-popup").removeClass(["d-none", "border-success", "bg-success"]);
                $("#error-popup").addClass(["border-warning", "bg-warning"]);
            }
        })

    } else {
        $(".form-control").each(function(){
            if($(this).val() === "" || $(this).val() === 0 || $(this).val() === "0"|| $(this).val() === null){
                $(this).addClass(["border", "border-danger"])
            } else {
                $(this).removeClass(["border", "border-danger"])
            }
            $("#error-popup").children().html("Deben rellenarse todos los campos obligatorios");
            $("#error-popup").removeClass(["d-none", "border-success", "bg-success"]);
            $("#error-popup").addClass(["border-warning", "bg-warning"]);
        })
    }
})

});