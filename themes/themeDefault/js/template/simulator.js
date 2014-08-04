$(document).ready(function(){
	//Anula en comportamiento del enter dentro del input para no enviar al pulsarlo
	$('input').keypress(function(e){
		if(e.which == 13){
			return false;
		}
	});
	
});
/*
function datosSIMU_ajax() {
	$.ajax({
		type: "GET",
		url: "/simulador_module/simulador_module/formularioDatosJSON",
		data: '',
		contentType: "application/json; charset=utf-8",
		dataType: "json",
		complete: function(data) {
			var datosSIMU = JSON.parse(data.responseText);  
			console.log(datosSIMU);
			enviarDatosSimulador_ajax(datosSIMU);			
		}
	});
}; 

function enviarDatosSimulador_ajax(datosSIMU) { // funciÃ³n que envÃ­a los datos del SIMULADOR fuera
	console.log(datosSIMU);
	//alert(datosSIMU);
};
*/
function configura_ajax() {
	$.ajax({
		type: "GET",
		url: "/simulador_module/simulador_module/simulador",
		data: '',
		contentType: "application/json; charset=utf-8",
		dataType: "json",
		complete: function(data) {
			var datos = JSON.parse(data.responseText);  
			simulador(datos);
		}
	});
}; 

//SIMULADOR
function simulador(datos) {
/*
	//	console.log(datos);

	    var valSlider1a = new Array();
	    var valSlider1b = new Array();
	    var valSlider2a = new Array();
	    var valSlider2b = new Array();

		var c1a = 0;
		var c1b = 0;
		var c2a = 0;
		var c2b = 0;
   	 	for (var c=0;c<datos.length;c++)
    	{
    		if(valSlider1a.indexOf(datos[c].cantidad) == -1){ // Si no existe el importe
    			sw1 = true;
    		}

    		if(valSlider2a.indexOf(datos[c].dias) == -1){ // Si no existe el plazo
    			sw2 = true;
    		}
    	}
    	

	valSlider1a[c1a] = datos[c].cantidad;
    valSlider2a[c2a] = datos[c].dias;
    c1a++;
    c2a++;


    	console.log(valSlider1a);
    	console.log(valSlider2a);
    	

	    var cantidad 	= new Array();
    	var dias 		= new Array();
    	var interes 	= new Array();

   	 	for (var c=0;c<datos.length;c++)
    	{
       		cantidad[c] = datos[c].cantidad;
       		dias[c]		= datos[c].dias;
       		interes[c]	= datos[c].interes;
    	}

    	//console.log(cantidad);
    	//console.log(dias);
    	//console.log(interes);
*/
    	var valSlider1 	= [50, 100, 150, 200, 250, 300, 400, 500];
		var valSlider2a = [15, 30];
		var valSlider2b = [40, 45];
		var valSlider2 	= valSlider2a.concat(valSlider2b);

		if($('#importeMin').length){


			$("#slider1").slider({
				animate: 	"slow",
				range: 		"min",
				stepValues: valSlider1, 
				value:  	valSlider1[0],
				min: 		valSlider1[0],
				max:  		valSlider1[valSlider1.length - 1], //valSlider1[6],
		    	slide: function( event, ui ) {
					if($("#slider1").slider('value') < valSlider1[valSlider1.length - 1] && $("#slider2").slider('value') >= valSlider2b[0]){ // Si el importe es A cambia al plazo A
						$("#slider2").slider('value', valSlider2a[valSlider2a.length-1]);
						colorBarraPlazos("red");
					}else if($("#slider1").slider('value') >= valSlider1[valSlider1.length - 1] && $("#slider2").slider('value') < valSlider2b[0]){ // Si el importe es B cambia al plazo B
						$("#slider2").slider('value', valSlider2b[0]);
						colorBarraPlazos("transparent");
					}

					actualizarValor( $("#slider1").slider('value'), $("#slider2").slider('value'));

					var stepValues = $(this).slider("option", "stepValues"),
						distance = [],
						minDistance = $(this).slider("option", "max"),
						minI;
					$.each(stepValues, function(i, val) {
						distance[i] = Math.abs( ui.value - val );
						if ( distance[i] < minDistance ) {
							minDistance = distance[i];
							minI = i;
						}
					});
					if ( minDistance ) {
						$(this).slider("value", stepValues[ minI ]);
						return false;
					}
				},
				change: function( event, ui ) {	
					if($("#slider1").slider('value') < valSlider1[valSlider1.length - 1] && $("#slider2").slider('value') >= valSlider2b[0]){ // Si el importe es A cambia al plazo A
						$("#slider2").slider('value', valSlider2a[valSlider2a.length-1]);
						colorBarraPlazos("red");
					}else if($("#slider1").slider('value') >= valSlider1[valSlider1.length - 1] && $("#slider2").slider('value') < valSlider2b[0]){ // Si el importe es B cambia al plazo B
						$("#slider2").slider('value', valSlider2b[0]);
						colorBarraPlazos("transparent");
					}
					actualizarValor( $("#slider1").slider('value'), $("#slider2").slider('value'));
				}
			});	

			$("#slider2").slider({
				animate: 	"slow",
				range: 		"min",				
				stepValues: valSlider2, 
				values: 	valSlider2[0],
				min:   		valSlider2[0],
				max:  		valSlider2b[valSlider2b.length - 1],
		    	slide: function( event, ui ) {
					if($("#slider2").slider('value') <= valSlider2a[valSlider2a.length-1] &&  $("#slider1").slider('value') == valSlider1[valSlider1.length-1]){ // Si el plazo es A y el importe es B cambia al importe A
						$("#slider1").slider('value', valSlider1[valSlider1.length-1-1]);
						colorBarraPlazos("red");
					}else if($("#slider2").slider('value') >= valSlider2b[0] &&  $("#slider1").slider('value') < valSlider1[valSlider1.length-1]){ // Si el plazo es B y el importe es A cambia al importe B
						$("#slider1").slider('value', valSlider1[valSlider1.length-1]);
						colorBarraPlazos("transparent");
					}

					calcularFecha($("#slider2").slider('value'));
					actualizarValor($("#slider1").slider('value'), $("#slider2").slider('value'));

					var stepValues = $(this).slider("option", "stepValues"),
						distance = [],
						minDistance = $(this).slider("option", "max"),
						minI;
					$.each(stepValues, function(i, val) {
						distance[i] = Math.abs( ui.value - val );
						if ( distance[i] < minDistance ) {
							minDistance = distance[i];
							minI = i;
						}
					});
					if ( minDistance ) {
						$(this).slider("value", stepValues[ minI ]);
						return false;
					}
				},
				change: function( event, ui ) {	
					if($("#slider2").slider('value') <= valSlider2a[valSlider2a.length-1] &&  $("#slider1").slider('value') == valSlider1[valSlider1.length-1]){ // Si el plazo es A y el importe es B cambia al importe A
						$("#slider1").slider('value', valSlider1[valSlider1.length-1-1]);
						colorBarraPlazos("red");
					}else if($("#slider2").slider('value') >= valSlider2b[0] &&  $("#slider1").slider('value') < valSlider1[valSlider1.length-1]){ // Si el plazo es B y el importe es A cambia al importe B
						$("#slider1").slider('value', valSlider1[valSlider1.length-1]);
						colorBarraPlazos("transparent");
					}
					calcularFecha($("#slider2").slider('value'));
					actualizarValor( $("#slider1").slider('value'), $("#slider2").slider('value'));
				}
			});

			actualizarValor();
			calcularFecha($("#slider2").slider('value'));

			// Inputs (Van al valor superior mÃ¡s cercano)
			$("#valorSlider1").change(function () {
				input1 = valSlider1[0];
				for (var i=0;i<(valSlider1.length-1);i++){
					if(this.value>valSlider1[i]){
						input1 = valSlider1[i+1];
					}
				}
				$("#slider1").slider( "value" , parseInt( input1 ));

				if(input1 < valSlider1[valSlider1.length - 1] && $("#slider2").slider('value') >= valSlider2b[0]){ // Si el importe es A cambia al plazo A
					$("#slider2").slider('value', valSlider2a[valSlider2a.length-1]);
					colorBarraPlazos("red");
				}else if(input1 >= valSlider1[valSlider1.length - 1] && $("#slider2").slider('value') < valSlider2b[0]){ // Si el importe es B cambia al plazo B
					$("#slider2").slider('value', valSlider2b[0]);
					colorBarraPlazos("transparent");
				}

				actualizarValor();
			});

			$("#valorSlider2").change(function () {
				input2 = valSlider2[0];
				for (var i=0;i<(valSlider2.length-1);i++){
					if(this.value>valSlider2[i]){
						input2 = valSlider2[i+1];
					}
				}
				$("#slider2").slider( "value" , parseInt( input2 ));
				
				if(input2 <= valSlider2a[valSlider2a.length-1] &&  $("#slider1").slider('value') == valSlider1[valSlider1.length-1]){ // Si el plazo es A y el importe es B cambia al importe A
					$("#slider1").slider('value', valSlider1[valSlider1.length-1-1]);
					colorBarraPlazos("red");
				}else if(input2 >= valSlider2b[0] &&  $("#slider1").slider('value') < valSlider1[valSlider1.length-1]){ // Si el plazo es B y el importe es A cambia al importe B
					$("#slider1").slider('value', valSlider1[valSlider1.length-1]);
					colorBarraPlazos("transparent");
				}
				actualizarValor();
				calcularFecha($("#slider2").slider('value'));
			});

			// Botones
			$('#btnMenosSlider1').click(function() {		
				posSlider1 = valSlider1.indexOf($("#slider1").slider('value'));
				if(valSlider1[posSlider1] > valSlider1[0]){
					$("#slider1").slider('value', valSlider1[posSlider1-1]);
				}
				// Si el importe es A y el plazo es B cambia al plazo A
				if(valSlider1[posSlider1-1] < valSlider1[valSlider1.length - 1] && $("#slider2").slider('value') >= valSlider2b[0]){
					$("#slider2").slider('value', valSlider2a[valSlider2a.length-1]);
					colorBarraPlazos("red");
				}

				actualizarValor();
			});

			$('#btnMasSlider1').click(function() {
				posSlider1 = valSlider1.indexOf($("#slider1").slider('value'));
				if(valSlider1[posSlider1] < valSlider1[valSlider1.length-1]){
					$("#slider1").slider('value', valSlider1[posSlider1+1]);
				}
				// Si el importe es B y el plazo es A cambia al plazo B
				if(valSlider1[posSlider1+1] >= valSlider1[valSlider1.length - 1] && $("#slider2").slider('value') < valSlider2b[0]){
					$("#slider2").slider('value', valSlider2b[0]);
					colorBarraPlazos("transparent");
				}

				actualizarValor();
			});

			$('#btnMenosSlider2').click(function() {
				posSlider2 = valSlider2.indexOf($("#slider2").slider('value'));
				if(valSlider2[posSlider2] > valSlider2[0]){
					$("#slider2").slider('value', valSlider2[posSlider2-1]);
				}
				// Si el plazo es A y el importe es B cambia al importe A
				if(valSlider2[posSlider2-1] <= valSlider2a[valSlider2a.length-1] &&  $("#slider1").slider('value') == valSlider1[valSlider1.length-1]){
					$("#slider1").slider('value', valSlider1[valSlider1.length-1-1]);
					colorBarraPlazos("red");
				}

				actualizarValor();
				calcularFecha($("#slider2").slider('value'));
			});

			$('#btnMasSlider2').click(function() {
				posSlider2 = valSlider2.indexOf($("#slider2").slider('value'));
				if(valSlider2[posSlider2] < valSlider2[valSlider2.length-1]){
					$("#slider2").slider('value', valSlider2[posSlider2+1]);
				}
				// Si el plazo es B y el importe es A cambia al importe B
				if(valSlider2[posSlider2+1] >= valSlider2b[0] &&  $("#slider1").slider('value') < valSlider1[valSlider1.length-1]){
					$("#slider1").slider('value', valSlider1[valSlider1.length-1]);
					colorBarraPlazos("transparent");
				}

				actualizarValor();
				calcularFecha($("#slider2").slider('value'));
			});

			
		}	

		function actualizarValor () {
			importe = $("#slider1").slider( "value" );
			plazo 	= $("#slider2").slider( "value" );

			if(importe == 50 && plazo == 15){
				intereses 	= 29;				
			}else if(importe == 50 && plazo == 30){
				intereses 	= 39;				
			}else if(importe == 100 && plazo == 15){
				intereses 	= 25;				
			}else if(importe == 100 && plazo == 30){
				intereses 	= 40;				
			}else if(importe == 150 && plazo == 15){
				intereses 	= 23;				
			}else if(importe == 150 && plazo == 30){
				intereses 	= 33;				
			}else if(importe == 200 && plazo == 15){
				intereses 	= 22.5;				
			}else if(importe == 200 && plazo == 30){
				intereses 	= 32.5;		
			}else if(importe == 250 && plazo == 15){
				intereses 	= 22.1;				
			}else if(importe == 250 && plazo == 30){
				intereses 	= 32;			
			}else if(importe == 300 && plazo == 15){
				intereses 	= 23.2;				
			}else if(importe == 300 && plazo == 30){
				intereses 	= 31.5;				
			}else if(importe == 400 && plazo == 15){
				intereses 	= 21.3;				
			}else if(importe == 400 && plazo == 30){
				intereses 	= 30;					
			}else if(importe == 500 && plazo == 40){
				intereses 	= 35;				
			}else if(importe == 500 && plazo == 45){
				intereses 	= 38;				
			}
			
			interes = (importe * intereses / 100);				
			total 	= Math.round(importe + interes);

	    	$("#valorDatos1").text(importe);
			$("#valorDatos4").text(interes); // interÃ©s
		    $("#valorDatos5").text(total); // total

			$("#valorSlider1").val(importe);
			$("#valorSlider2").val(plazo);

			$("#importeSIM").val(importe);
		    $("#interesSIM").val(interes);
		    $("#totalSIM").val(total);
	    }

		function calcularFecha (dias) {
			fecha=new Date();
	   		day=fecha.getDate();
	   		month=fecha.getMonth()+1; //mes 0 - 11 (+1)
	   		year=fecha.getFullYear();
	    	//Obtenemos los milisegundos desde media noche del 1/1/1970
    		tiempo=fecha.getTime();
    		//Calculamos los milisegundos sobre la fecha que hay que sumar o restar...
    		milisegundos=parseInt(dias*24*60*60*1000);
    		//Modificamos la fecha actual
    		total=fecha.setTime(tiempo+milisegundos);
    		day=fecha.getDate();
    		day = (day < 10) ? '0' + day : day;
    		month=fecha.getMonth()+1;
    		month = (month < 10) ? '0' + month : month;
    		year=fecha.getFullYear();

    		$("#valorDatos2").text( day + "/" + month + "/" + year );
    		$("#fecha_vencimientoSIM").val( day + "/" + month + "/" + year );
    		$("#plazoSIM").val( dias );
		}

		function colorBarraPlazos (color){
			$('head').append('<style>#simulador_herramienta #cajaSlider2 .infSlider .slider.ui-widget-content::before{background:'+color+' !important;}</style>');
		}

};	//FIN SIMULADOR

