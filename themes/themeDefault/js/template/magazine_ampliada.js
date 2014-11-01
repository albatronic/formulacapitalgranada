$(function()
{
    $("#tareas li").click(function()
    {
        //alert($(this).text());
        var ancho_estrellas = $(this).text() * 20;

        $("#votos").attr("value", $(this).text());

        $(".votos_actuales").css("width", ancho_estrellas + "%");

    });
});


