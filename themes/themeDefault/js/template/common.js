$(document).ready(function(){
    $(function () {
        $("[rel='tooltip']").tooltip();
    });


	$('#simuladorTab').click(function (e) {
	  ga('send', '/simulador-minicreditos');
	});

	//iniciamos pretty
	$("a[rel^='prettyPhoto']").prettyPhoto({horizontal_padding: 0,social_tools: false,deeplinking: false,  allow_resize: false });
	// Decimos que todas los links con ese rel, se habran en una ventana nueva 
	$("a[rel^='nueva_ventana']").click(function(){window.open($(this).attr('href'));return false;});

	$('.carousel').carousel({
    	interval: 3000
    })


	$('.div_under').on('mouseenter', function(e) {
		
		var div_under_height = $(this).height() + 40;

		var div_under_full_id = $(this).attr('id');
		var div_under_id = div_under_full_id.substring(9);

        $('#over_div'+div_under_id).stop().animate(
						{height : div_under_height+'px'},
						200);

    })
    $('.div_under').on('mouseleave', function(e) {

    	var div_under_full_id = $(this).attr('id');
		var div_under_id = div_under_full_id.substring(9);

        $('#over_div'+div_under_id).stop().animate(
						{height : '0px'},
						200);
    })

	
	$(".accordion-toggle").click(function(){
		/*igualarAltura();*/
		if($(this).hasClass('accordion-toggle-on'))
		{
	        $(this).removeClass('accordion-toggle-on');
	    }
	    else
	    {
	    	/* Eliminamos la clase "accordion-toggle-on" a todos los hijos con el mismo padre que el pulsado */

	    	$(this).parent(['.accordion-heading']).parent(['.span6']).parent(['.row']).parent(['.accordion-group']).children(['.row']).children(['.span6']).children(['.accordion-heading']).children([".accordion-toggle"]).removeClass('accordion-toggle-on');
	    	

	        $(this).addClass('accordion-toggle-on'); 
	    }
	});

	$('.numericInput').keypress(validateNumber);

    // comprobaciÃ³n cookies
    function setCookie(c_name,value,exdays)
    {
        var exdate=new Date();
        exdate.setDate(exdate.getDate() + exdays);
        var c_value=escape(value) + ((exdays==null) ? "" : "; expires="+exdate.toUTCString()) + "; path=/";
        document.cookie=c_name + "=" + c_value;
    }

    function readCookie(name) {
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for(var i=0;i < ca.length;i++) {
            var c = ca[i];
            while (c.charAt(0)==' ') c = c.substring(1,c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
        }
        return null;
    }
    var $ca_infoaccept = jQuery('#closeCookies');
    var cookieaccept = readCookie('cookieaccept');
    if(!(cookieaccept == "yes")){
        $ca_infoaccept.click(function(){
            setCookie("cookieaccept","yes",365);
            jQuery.post('http://www.formulacapitalfranquicia.es/', 'set_cookie=1', function(){});
            $("#alerta-cookies").fadeOut("slow");
        });
    } else{
        $("#alerta-cookies").css('display','none');
    }
    // fin comprobaciÃ³n cookies
});



function validateNumber(event) {
    var key = window.event ? event.keyCode : event.which;

    if (event.keyCode == 8 || event.keyCode == 46
     || event.keyCode == 37 || event.keyCode == 39) {
        return true;
    }
    else if ( key < 48 || key > 57 ) {
        return false;
    }
    else return true;
};