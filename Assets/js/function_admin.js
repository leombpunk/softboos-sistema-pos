// alert("hola mundo!");
//codigo para las notificaciones
$(document).ready(function() {
    $.ajax({
        type: "GET",
        url: base_url+"Notificaciones/getAlertas/"+0,
        dataType: "json",
        success: function(response) {
            console.log(response);
            if (!response.alertas){
                $("#alertMessage").html("No se encontro ninguna notificación relacionada con la falta o exceso en el Inventario!");
                $("#alertHidden").removeAttr("hidden");
                $("#notifyNumber").attr("hidden",true);
            }
            else {
                $("#notifyNumber").removeAttr("hidden");
                $("#notifyNumber").html(parseInt(response.alertas));
            }
            notificacion();
            setInterval(notificacion,2000);
        },
        error: function(error){
            console.log(error);
        }
    })
})

function notificacion(){
    $("#bell_head").addClass("fa-bell").removeClass("fa-bell-o");
    setTimeout(function () {
        $("#bell_head").addClass("fa-bell-o").removeClass("fa-bell");
    }, 1000);

    // $("#bell_menu").addClass("fa-bell").removeClass("fa-bell-o");
    // setTimeout(function () {
    //     $("#bell_menu").addClass("fa-bell-o").removeClass("fa-bell");
    // }, 1000);
}