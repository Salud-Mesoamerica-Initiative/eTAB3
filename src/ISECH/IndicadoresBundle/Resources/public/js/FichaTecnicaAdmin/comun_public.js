var panZoom="",contenedor=""; 
var formatAsPercentage = d3.format("%"),
        formatAsPercentage1Dec = d3.format(".1%"),
        formatPercent = d3.format("%"),
        formatAsInteger = d3.format(","),
        fsec = d3.time.format("%S s"),
        fmin = d3.time.format("%M m"),
        fhou = d3.time.format("%H h"),
        fwee = d3.time.format("%a"),
        fdat = d3.time.format("%d d"),
        fmon = d3.time.format("%b")
        ;

var zona = 1;
var max_zonas = 3;

var color = d3.scale.category20();    //builtin range of colors

function colores_alertas(zona, indice, i) 
{

    var rangos_alertas = JSON.parse($('#' + zona + ' .titulo_indicador').attr('rangos_alertas'));
    if (rangos_alertas.length === 0)
        return color(i);
    else 
	{
        for (ii = 0; ii < rangos_alertas.length; ii++) 
		{
            if (indice <= rangos_alertas[ii].limite_sup)
                return rangos_alertas[ii].color;
        }
        return 'lightblue';
    }
}
var graficos=Array();
function dibujarGraficoPrincipal(zona, tipo) 
{
    	//en el caso de que se recuperen tipos de graficos null
	//se asignan directamente a tipo columnas
	if (tipo == null)
		tipo = "columnas";
	
    $('#' + zona + ' .dimension').html($('#' + zona + ' .dimensiones option:selected').html());
    cerrarMenus();
    var grafico = crearGraficoObj(zona, tipo);
	graficos[zona] = grafico;
    $('#' + zona + ' .titulo').show();
    grafico.dibujar();
    //aplicarFormato();

    var datasetPrincipal = JSON.parse($('#' + zona).attr('datasetPrincipal'));
    construir_tabla_datos(zona, datasetPrincipal);
	
	if ($('#' + zona + '_icon_maximizar').hasClass('glyphicon glyphicon-zoom-out')||getCookieS("zoom"+zona)=='1')
	{
		$('#' + zona + '_icon_maximizar').addClass('glyphicon glyphicon-zoom-out');		
	}
}
var tam=0,h1=0,w1=0;
function dibujarGrafico(zona, dimension) 
{
    if (dimension === null)
        return;
	$('#' + zona + ' .filtros_dimensiones').attr('data-id',zona);	
    var filtro = $('#' + zona + ' .filtros_dimensiones').attr('data');
    //validar las entradas de las fechas
    var patron = /^\d{4}-\d{2}$/;
    var filtrofecha = {};
    
    if($('#fechainicio'+zona).val().match(patron) && $('#fechainicio'+zona).val() != ''
       && $('#fechafin'+zona).val().match(patron) && $('#fechafin'+zona).val() != ''
       && $('#filtro_por_fecha'+zona).is(':checked') == true)
    {
    	var min = $('#fechainicio'+zona).val().split('-');
        var max = $('#fechafin'+zona).val().split('-');
        filtrofecha = {mesmin : min[1],aniomin : min[0],mesmax:max[1],aniomax:max[0]};
    }
    $('#' + zona).removeAttr('datasetPrincipal_bk');
	
    var ruta=ruta_sala_publica.split('/');  
    $.getJSON(Routing.generate('indicador_datos_public',{id: $('#' + zona + ' .titulo_indicador').attr('data-id'), dimension: dimension, token: ruta[0], sala:ruta[1] }),
    {filtro: filtro, ver_sql: false, filtrofecha : filtrofecha},
    function(resp) {
    	///////crear la cadena con el rango de datos
    	////
    	mensajerango = "";
    	val = resp.datos[0];
        if (typeof val !='undefined')
        {
			if (typeof val.min_anio != 'undefined')
			{
				if (typeof val.min_mes == 'undefined')
					mensajerango += " ["+trans.de+" "+val.min_anio;
				else
					mensajerango += " [" + trans.de + " 01/"+("0" + val.min_mes).slice(-2)+"/"+val.min_anio;
				
				val = resp.datos[resp.datos.length-1];
				if (typeof val.min_mes == 'undefined')
					mensajerango += " "+trans.a+" "+val.max_anio+"] ";
				else
					mensajerango += " "+trans.a+" 01/"+("0" + val.max_mes).slice(-2)+"/"+val.max_anio+"] ";
				
				$('#' + zona + ' .titulo_indicador').html($('#' + zona + ' .titulo_indicador').attr('nombre') );
				$('#' + zona + ' .panel-footer').find(".pull-left").remove();
				$('#' + zona + ' .panel-footer').append("<span class='pull-left'>"+mensajerango+"</span>");                	    	
				$('#'+zona).attr("rangoanio" , resp.datos[0].min_anio+":"+resp.datos[resp.datos.length-1].max_anio);
				//$('#fechainicio'+zona).datepicker("option", { yearRange : $('#'+zona).attr("rangoanio") , disabled : false});
				//$('#fechafin'+zona).datepicker("option",  { yearRange : $('#'+zona).attr("rangoanio") , disabled : false});
				$('label[for='+$('#filtro_por_fecha'+zona).attr("id")+']').text(trans.filtro_fecha+" - Rango disponible("+$('#'+zona).attr("rangoanio")+")");
					//$('#filtro_por_fecha'+zona).text(trans.filtro_fecha+" - Rango disponible("+$('#'+zona).attr("rangoanio")+")");
				$('#filtro_por_fecha'+zona).removeAttr("disabled");
			}
			else
			{
				//$('#fechainicio'+zona).datepicker("option", "disabled", true);
				//$('#fechafin'+zona).datepicker("option", "disabled", true);
				$('#filtro_por_fecha'+zona).attr("disabled","disabled"); 
			}
    	
            var datos = JSON.stringify(resp.datos);
            $('#' + zona).attr('datasetPrincipal_bk', datos);
			
            if (typeof $('#' + zona).attr('orden') !== 'undefined'
                    && $('#' + zona).attr('orden') !== null
                    && $('#' + zona).attr('orden') !== '')
            {
                if ($('#' + zona).attr('orden-aplicado') !== 'true') 
				{
                    var ordenobj = JSON.parse($('#' + zona).attr('orden'));
                    datos = JSON.stringify(ordenarArreglo(resp.datos, ordenobj[0].tipo, ordenobj[0].modo));
                    $('DIV.zona_actual').attr('orden-aplicado', 'true');
                }
            }

            $('#' + zona).attr('datasetPrincipal', datos);

            dibujarGraficoPrincipal(zona, $('#' + zona + ' .tipo_grafico_principal').val());
            controles_filtros(zona);
        }
        else
        {
			var $dimension = $('#' + zona + ' .dimensiones option:selected');
			var indice = $('#' + zona + ' .dimensiones option').index($dimension);
			
			$('#' + zona + ' .dimensiones option').eq(indice-1).prop("disabled",false);
			$dimension.attr('disabled',true);
			$('#' + zona + ' .dimensiones option').eq(indice-1).prop("selected",true);
			
			$filtro = $('#' + zona + ' .filtros_dimensiones'),
			filtros_obj = jQuery.parseJSON($filtro.attr('data'));
		
			var nivel ="";
			$.each(filtros_obj, function(i, obj) 
			{
				nivel=i;
			});
		
			dibujar_breadcum(zona,nivel-1);
			
			
            alert("No se encontraron datos con ese filtro");
        }
    });
	
	if($('#' + zona + ' .filtros_dimensiones').text()!="")
		$('#' + zona + ' .filtros_dimensiones').show();
	else
		$('#' + zona + ' .filtros_dimensiones').hide();

}
function dibujarControles(zona, datos) 
{
	//////agregar un nuevo atributo con el nombre del indicador
	
    $('#' + zona + ' .titulo_indicador').html(datos.nombre_indicador)
            .attr('data-unidad-medida', datos.unidad_medida)
            .attr('nombre', datos.nombre_indicador)
            .attr('formula', datos.formula)
            .attr('data-id', datos.id_indicador)
            .attr('filtro-elementos', '')
            .attr('rangos_alertas', JSON.stringify(datos.rangos));
	var rangos_alertas = datos.rangos;
	
	myConfig='<ul class="dropdown-menu" role="menu" id="myConfig'+zona+'">' +
					'<li><a class="ver_tabla_datos" data-id="'+zona+'"><i class="glyphicon glyphicon-list-alt" ></i> ' + trans.tabla_datos + ' </a></li>' +
			'</ul>';
				
	image='<ul class="opciones_dimension dropdown-menu" role="menu" id="'+ zona +'_image" ></ul>';
	
	//inicio Botones 
	botones='<button class="btn btn-info myOption" data-toggle="modal" data-target="#myOption'+ zona +'" title="' + trans.opciones_grafico + '" data-id="'+zona+'">' +
				'<i class="glyphicon glyphicon-signal"></i>' +
			'</button>';	
	if (rangos_alertas.length > 0) 
	{
	botones+='<button class="btn btn-info myAlert" data-toggle="modal" data-target="#myAlert'+ zona +'"  title="' + trans.alertas_indicador + '" data-id="'+zona+'">' +
				'<i class="glyphicon glyphicon-exclamation-sign"></i>' +
			'</button>';
	}	
	
	botones+='<button class="btn btn-info myFilter" data-toggle="modal" data-target="#myFilter'+ zona +'" title="' + trans.dimension_opciones + '" data-id="'+zona+'">' +
				'<i class="glyphicon glyphicon-filter"></i>' +
			'</button>';
			
	botones+='<div class="btn-group div_toimage"><button class="btn btn-info dropdown-toggle toimage" id="'+ zona +'_toimage" data-toggle="dropdown" title="' + trans.exportar_imagen + '" data-id="'+zona+'">' +
				'<i class="glyphicon glyphicon-camera"></i> <span class="caret"></span>' +
			'</button>'+image+'</div>';
			
	botones+='<div class="btn-group"><button class="btn btn-info dropdown-toggle myConfig" data-toggle="dropdown" title="' + trans.opciones + '" data-id="'+zona+'">' +
				'<i class="glyphicon glyphicon-info-sign"></i> <span class="caret"></span>' +
			'</button>'+myConfig+'</div>';
			
	
	botones+='<button class="btn btn-info myRefresh" id="'+ zona +'_refresh" data-toggle="dropdown" title="' + trans.refrescar + '" data-id="'+zona+'">' +
				'<i class="glyphicon glyphicon-refresh"></i>' +
			'</button>';
	
	botones+='<button class="btn btn-info myMax" data-toggle="dropdown" title="' + trans.maximizar + '" id="'+ zona+'_maximizar" data-id="'+zona+'">' + 
				'<i class="glyphicon glyphicon-zoom-in" id= "'+ zona +'_icon_maximizar"></i>' +
			'</button>';
			
	botones+='<button class="btn btn-info quitar_indicador" id="'+ zona +'_quitar" data-toggle="dropdown" title="' + trans.quitar_indicador + '" data-id="'+zona+'">' +
				'<i class="glyphicon glyphicon-remove-sign"></i>' +
			'</button>';
	
	
	$('#' + zona + ' .controles').append('<div class="btn-group">'+botones+'</div>');
	
	
	//info boton
	hn=($(window).height()/1.5)+'px';
	
	var combo_ordenar_por_medida ='<label class="control-label required col-lg-8">'+ trans.ordenar_y + ":</label>"+
			"<SELECT class='ordenar_medida form-control' data-id='"+zona+"'>" +
				"<OPTION VALUE='-1'></OPTION>" +
				"<OPTION VALUE='desc'>" + trans.descendente + "</OPTION>" +
				"<OPTION VALUE='asc'>" + trans.ascendente + "</OPTION>" +
            "</SELECT>";
	var combo_ordenar_por_dimension ='<label class="control-label required col-lg-8">'+ trans.ordenar_x + ":</label>"+ 
			"<SELECT class='ordenar_dimension form-control' data-id='"+zona+"'>" +
				"<OPTION VALUE='-1'></OPTION>" +
				"<OPTION VALUE='desc'>" + trans.descendente + "</OPTION>" +
				"<OPTION VALUE='asc'>" + trans.ascendente + "</OPTION>" +
            "</SELECT>";
	var combo_tipo_grafico ='<label class="control-label required col-lg-8">'+ trans.tipo_grafico + ":</label>"+ 
			"<SELECT class='tipo_grafico_principal form-control' data-id='"+zona+"'></SELECT>";
	var opciones_indicador="";
	if (rangos_alertas.length > 0) 
	{
        opciones_indicador += '<label class="control-label required col-lg-8">'+trans.max_escala_y +
                ":</label> <SELECT class='max_y form-control' data-id='"+zona+"'>" +
                "<OPTION VALUE='indicador' selected='selected'>" + trans.max_indicador + "</OPTION>" +
                "<OPTION VALUE='rango_alertas'>" + trans.max_rango_alertas + "</OPTION>" +
                "</SELECT>";
    } 
	botonesinfo='<div id="myOption'+ zona +'" class="modal fade" style="z-index:999999999">'+
				  '<div class="modal-dialog">'+
					'<div class="modal-content">'+
					  	'<div class="modal-header">'+
							'<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>'+
							'<h3 id="myModalLabel2">' + trans.opciones_grafico + '</h3>'+
						'</div>'+
						'<div class="modal-body" style="max-height:'+hn+'; max-width:100%; overflow:auto;">'+
							'<div class="form-group">' + combo_ordenar_por_medida + '</div>' +
							'<div class="form-group">' + combo_ordenar_por_dimension + '</div>' +
							'<div class="form-group">' + combo_tipo_grafico + '</div>'+
							'<div class="form-group">' + opciones_indicador + '</div>'+
						'</div>'+
						'<div class="modal-footer">'+
						'<button class="btn btn-warning" data-dismiss="modal" aria-hidden="true">Cerrar</button>'+
						'</div>'+
					'</div>'+
				  '</div>'+
				'</div>';
	$('#' + zona + ' .controles').append(botonesinfo);
	var 
	alertas = '<TABLE class="table table-bordered table-striped">' +
				'<THEAD>' +
					'<TR>' +
						'<TH>' + trans.color + '</TH>' +
						'<TH>' + trans.limite_inf + '</TH>' +
						'<TH>' + trans.limite_sup + '</TH>' +
						'<TH>' + trans.comentario + '</TH>' +
					'</TR>' +
				'</THEAD>' +
            '<TBODY>';	
	var max_rango = 0;
    $.each(rangos_alertas, function(i, rango) 
	{
        var comentario_rango = '';
        if (rango.comentario === null)
            comentario_rango = '';
        else
            comentario_rango = rango.comentario
        
		alertas += '<TR>' +
						'<TD bgcolor="' + rango.color + '"></TD>' +
						'<TD>' + rango.limite_inf + '</TD>' +
						'<TD>' + rango.limite_sup + '</TD>' +
						'<TD>' + comentario_rango + '</TD>' +
					'</TR>';
        max_rango = rango.limite_sup;
    });
    $('#' + zona + ' .titulo_indicador').attr('data-max_rango', max_rango);
    alertas += '<TR>'+
					'<TD bgcolor="lightblue"></TD>' +
            		'<TD colspan="3">' + trans.rango_no_especificado + '</TD>'+
    		   '</TR>'+
			'</TBODY>'+
			'</TABLE>';
    $('#' + zona + ' .alertas').html('');
    $('#' + zona + ' .grafico').html('');
    	
	botonesinfo='<div id="myAlert'+ zona +'" class="modal fade" style="z-index:999999999">'+
				  '<div class="modal-dialog">'+
					'<div class="modal-content">'+
					  	'<div class="modal-header">'+
							'<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>'+
							'<h3 id="myModalLabel2">' + trans.alertas_indicador + '</h3>'+
						'</div>'+
						'<div class="modal-body" style="max-height:'+hn+'; max-width:100%; overflow:auto;">'+
							 alertas+							
						'</div>'+
						'<div class="modal-footer">'+
						'<button class="btn btn-warning" data-dismiss="modal" aria-hidden="true">Cerrar</button>'+
						'</div>'+
					'</div>'+
				  '</div>'+
				'</div>';
	$('#' + zona + ' .controles').append(botonesinfo);					
	
	var combo_dimensiones ='<label class="control-label required col-lg-8">'+ trans.cambiar_dimension + ":</label>"+ 
			"<SELECT class='dimensiones form-control' name='dimensiones' data-id='"+zona+"'>";
			$.each(datos.dimensiones, function(codigo, datosDimension) 
			{
				combo_dimensiones += "<option value='" + codigo + "' data-escala='" + datosDimension.escala +
						"' data-x='" + datosDimension.origenX +
						"' data-y='" + datosDimension.origenY +
						"' data-graficos='" + JSON.stringify(datosDimension.graficos) + "'>" + datosDimension.descripcion + "</option>";
			});
			combo_dimensiones += "</SELECT>";	
	
	var filtro_fecha = '<input type="checkbox" class="filtro_por_fecha" id="filtro_por_fecha'+zona+'" data-id="'+zona+'" />'+
					   '<label for="filtro_por_fecha'+zona+'">' + trans.filtro_fecha +'</label><br/>'+
					   '<div id="div_rango_fechas'+zona+'" class="form-horizontal" style="display:none">'+
						   '<label class="control-label required col-lg-2"> '+ trans.desde +" </label>"+
						   "<INPUT class='valores_filtro fecha_desde form-control' id='fechainicio"+zona+"' type='month' style='width:220px;' />"+
						   "<label class='control-label required col-lg-2'>" + trans.hasta +"</label>"+ 
						   "<INPUT class='valores_filtro fecha_hasta form-control' id='fechafin"+zona+"' type='month'  style='width:220px;'/>" + 
						   '<input type="button" class="btn btn-success btn_filtrar_fecha" data-id="'+zona+'" id="btn_filtrar_fecha'+zona+'" value="' + trans.filtro_fecha + ' "/>'+
					   '</div>';
  
	filters='<div role="menu" >' +
					'<div class="form-group"><a >' + combo_dimensiones + '</a></div>' +
					'<div class="form-group"><a >' + filtro_fecha + '</a></div>' +
					'<div class="form-group lista_datos_dimension"></div>' +
				'</div>';
	botonesinfo='<div id="myFilter'+ zona +'" class="modal fade" style="z-index:999999999">'+
				  '<div class="modal-dialog">'+
					'<div class="modal-content">'+
					  	'<div class="modal-header">'+
							'<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>'+
							'<h3 id="myModalLabel2">' + trans.dimension_opciones + '</h3>'+
						'</div>'+
						'<div class="modal-body" style="max-height:'+hn+'; max-width:100%; overflow:auto;">'+
							 filters+							
						'</div>'+
						'<div class="modal-footer">'+
						'<button class="btn btn-warning" data-dismiss="modal" aria-hidden="true">Cerrar</button>'+
						'</div>'+
					'</div>'+
				  '</div>'+
				'</div>';
	$('#' + zona + ' .controles').append(botonesinfo);					
		
		var variable_="",data_="";
		var origen=datos.origen_dato_;
		for(data in origen)
		{
			variable_+='<table class="table table-bordered table-striped"><tr><th colspan=2><h3 class="popover-title">Variable '+((data*1)+1)+'</h3></th></tr><tr><th width="20%">'+
			trans.last_confiable+'</th><td>'+origen[data].origen_dato_confiabilidad+'</td></tr><tr><th>'+
			trans.last_nombre+' </th><td>'+origen[data].origen_dato_nombre+'</td></tr><tr><th>'+
			trans.last_fuente+' </th><td>'+origen[data].origen_dato_fuente+'</td></tr><tr><th>'+			
			trans.last_origen+' </th><td>'+origen[data].origen_dato_origen+'</td></tr><tr><th>'+
			trans.last_conexion+' </th><td>'+origen[data].origen_dato_conexion+'</td></tr><tr><th>'+
			trans.last_responsable+' </th><td>'+origen[data].origen_dato_responsable+'</td></tr></table>';
		}
		
		data_=('<table class="table table-bordered table-striped"><tr><th>'+
		trans.indicador+' </th><td>'+datos.nombre_indicador+'</td></tr><tr><th>'+
		trans.last_reading+' </th><td>'+datos.ultima_lectura+' (dd/mm/aaaa hh:mm:ss)</td></tr><tr><th>'+
		trans.last_update+' </th><td>'+datos.origen_dato_actualizacion+' (dd/mm/aaaa hh:mm:ss)</td></tr><tr><th colspan=2>'+
		variable_
		+'</th></tr></table>');
		
	botonesinfo='<div id="myNote'+ zona +'" class="modal fade" style="z-index:999999999">'+
				  '<div class="modal-dialog">'+
					'<div class="modal-content">'+
					  	'<div class="modal-header">'+
							'<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>'+
							'<h3 id="myModalLabel2">' + trans.notas_lectura + '</h3>'+
						'</div>'+
						'<div class="modal-body" style="max-height:'+hn+'; max-width:100%; overflow:auto;">'+
							'<div id="'+ zona +'_minota" class="col-lg-12">'+data_+'</div>'+
						'</div>'+
						'<div class="modal-footer">'+
						'<button class="btn btn-warning" data-dismiss="modal" aria-hidden="true">Cerrar</button>'+
						'</div>'+
					'</div>'+
				  '</div>'+
				'</div>';
		
	$('#' + zona + ' .controles').append(botonesinfo);
	//fin info boton
	
	//fin botones 
  $(document.body).keyup(function()
  {
	  	if (typeof (event) == "undefined")
    		var tecla = 27;
		else
		var tecla = (event.keyCode) ? event.keyCode : event.which ;
		if ($('#' + zona + '_icon_maximizar').hasClass('glyphicon glyphicon-zoom-out')||getCookieS("zoom"+zona)=='1')
		if (tecla == 27)
		{
			minimizar(zona,contenedor)
		}
  });
  setTiposGraficos(zona);
}

function aplicarFormato() {
    d3.selectAll(".axis path, .axis line")
            .attr('fill', 'none')
            .attr('stroke', 'black');
    d3.selectAll(".line")
            .attr('fill', 'none')
            .attr('stroke-width', '2px');
    d3.selectAll(".slice")
            .attr('font-size', '11pt')
            .attr('font-family', 'sans-serif');
    d3.selectAll(".axis text")
            .attr('font-family', 'sans-serif')
            .attr('font-size', '9pt');
    d3.selectAll(".background").attr('fill', 'none');

    d3.selectAll(".x g text").attr("transform", "rotate(45)").attr('x', 7).attr('y', 10).attr('text-anchor', 'start');
}
function crearGraficoObj(zona, tipo) {
    var grafico;
    var datasetPrincipal = JSON.parse($('#' + zona).attr('datasetPrincipal'));
    if (tipo == 'pastel')
        grafico = new graficoPastel(zona, datasetPrincipal);
    else if (tipo == 'columnas')
        grafico = new graficoColumnas(zona, datasetPrincipal);
    else if (tipo == 'lineas')
        grafico = new graficoLineas(zona, datasetPrincipal);
    else if (tipo == 'mapa')
        grafico = new graficoMapa(zona, datasetPrincipal);
    else if (tipo == 'gauge')
	{
    	if (datasetPrincipal[1] != null)
    	grafico = new graficoColumnas(zona, datasetPrincipal);
    	else
        grafico = new graficoGauge(zona, datasetPrincipal);
	}
    else if (tipo == 'lineargauge' /*termometro*/)
    {
    	if (datasetPrincipal[1] != null)
    	grafico = new graficoColumnas(zona, datasetPrincipal);
    	else
        grafico = new graficoTermometro(zona, datasetPrincipal);
    }
    return grafico;
}

function filtroRuta(filtros_obj) {
    var ruta = '';
    //var filtros_obj = );
    var cant_obj = filtros_obj.length;
    $.each(filtros_obj, function(i, obj) {
        if (i == (cant_obj - 1))
            ruta += obj.etiqueta + ': ' + obj.valor;
        else
            ruta += '<A data="' + i + '">' + obj.etiqueta + ': ' + obj.valor + '</A> / ';
    });
    return ruta;
}
function dibujar_breadcum(zona,nivel)
{
	var ultimo,
		ruta = '',
		$filtro = $('#' + zona + ' .filtros_dimensiones'),
		filtros_obj = jQuery.parseJSON($filtro.attr('data'));
	
    var nuevo_filtro = [{}];
    $.each(filtros_obj, function(i, obj) 
	{
        if (i <= nivel) 
		{
            nuevo_filtro[i] = obj;
            if (i == nivel)
                ruta +='<li>'+obj.etiqueta + ': ' + obj.valor+'</li> ';
            else
                ruta += '<a href="#" data="' + i + '">' + obj.etiqueta + ': ' + obj.valor + '</a> / ';
        }
        else 
		{
            // Los que estén a la derecha del seleccionado deben volver al control de dimensiones                        
            //$('#' + zona + ' .dimensiones').append("<OPTION VALUE='" + obj.codigo + "'>" + obj.etiqueta + "</OPTION>");
			$('#' + zona + ' .dimensiones').children('option[value="' + obj.codigo + '"]').attr("disabled",false);
            if (i == parseInt(nivel) + 1)
                primero = obj.codigo;
        }
    });	
    //El primer elemento 
    $('#' + zona + ' .dimensiones').children('option[value="' + primero + '"]').attr("disabled",false).prop("selected",true);

    $filtro.html(ruta);
    $filtro.attr('data', JSON.stringify(nuevo_filtro));
}
function ascenderNivelDimension(zona, nivel) 
{
    dibujar_breadcum(zona,nivel);

    dibujarGrafico(zona, $('#' + zona + ' .dimensiones').val());
    $('#' + zona).attr('orden', null);
    $('#' + zona + ' .ordenar_dimension').children('option[value="-1"]').prop("selected",true);
    $('#' + zona + ' .ordenar_medida').children('option[value="-1"]').prop("selected",true);	
}

function descenderNivelDimension(zona, category) 
{

	var $dimension = $('#' + zona + ' .dimensiones option:selected');
	var indice = $('#' + zona + ' .dimensiones option').index($dimension);
	
   	if ($('#' + zona + ' .dimensiones option').length-1 == indice) 
	{
        alert(trans.no_mas_niveles);
        return;
    }
    
	
    var $filtro = $('#' + zona + ' .filtros_dimensiones');
    var separador1 = '',
        separador2 = '';
	$('#' + zona).attr("style","width:100%; height:100%");
    // Construir la cadena de filtros
    var filtros = $filtro.attr('data');
    var filtro_a_agregar = '{"codigo":"' + $dimension.val() + '",' +
            '"etiqueta":"' + $dimension.html() + '",' +
            '"valor":"' + category + '"' +
            "}";

    if (filtros != '' && filtros != null)
        separador1 += ',';
    else
        filtros = '[';

    filtros = filtros.replace(']', '');
    $filtro.attr('data', filtros + separador1 + filtro_a_agregar + ']');

    var ruta = filtroRuta(jQuery.parseJSON($filtro.attr('data')));
    $filtro.html(ruta);
		
	$('#' + zona + ' .dimensiones option').eq(indice+1).prop("selected",true);
	//$('#' + zona + ' .dimensiones option:selected').next().attr("selected","selected");
	dibujarGrafico(zona, $('#' + zona + ' .dimensiones option:selected').val());
	// Deshabilitamos options que causan conflicto con la consulta
	$dimension.attr('disabled',true);
    $('#' + zona).attr('orden', null);
    $('#' + zona + ' .ordenar_dimension').children('option[value="-1"]').prop("selected",true);
    $('#' + zona + '.ordenar_medida').children('option[value="-1"]').prop("selected",true);
	
}

function ordenarDatos(zona, ordenar_por, modo_orden) {
    if (modo_orden === '-1')
        return;
    if (ordenar_por === 'dimension')
        $('#' + zona + ' .ordenar_medida').children('option[value="-1"]').prop("selected",true);
    else
        $('#' + zona + ' .ordenar_dimension').children('option[value="-1"]').prop("selected",true);

    cerrarMenus();
    $('#' + zona).attr('orden', '[{"tipo":"' + ordenar_por + '", "modo": "' + modo_orden + '"}]');
    var grafico = crearGraficoObj(zona, $('#' + zona + ' .tipo_grafico_principal').val());
    grafico.ordenar(modo_orden, ordenar_por);
    var datasetPrincipal = JSON.parse($('#' + zona).attr('datasetPrincipal'));
    construir_tabla_datos(zona, datasetPrincipal);
    aplicarFormato();
}

function aplicarFiltro(zona) {
    
    //datasetPrincipal = datasetPrincipal_bk;
    $('#' + zona).attr('datasetPrincipal', $('#' + zona).attr('datasetPrincipal_bk'));
    var elementos = '';
    var datasetPrincipal = JSON.parse($('#' + zona).attr('datasetPrincipal'));

    $('#' + zona + ' .panel-body input:checked').each(function() 
	{
        elementos += $(this).val() + '&';
    });
	
	var desde="",hasta="";
	
    $.post(Routing.generate('indicador_datos_filtrar'),
    {
		datos: datasetPrincipal, desde: desde, hasta: hasta, elementos: elementos
	},
    function(resp) 
	{
        $('#' + zona).attr('datasetPrincipal', JSON.stringify(resp.datos));
        dibujarGraficoPrincipal(zona, $('#' + zona + ' .tipo_grafico_principal').val());
    }, 'json');
    $('#' + zona).attr('orden', '');
    $('#' + zona + ' .titulo_indicador').attr('filtro-elementos', '');
}

function controles_filtros(zona) {
    var datasetPrincipal = JSON.parse($('#' + zona).attr('datasetPrincipal'));

    var lista_datos_dimension1 = '<div class=""><a style="margin-left:"5px" class="filtro_elementos"><input type="button" class="btn btn-info aplicar_filtro" data-id="'+zona+'" value="' + trans.filtrar + '" />' +
            '&nbsp;<input type="button" class="btn btn-danger quitar_filtro" value="' + trans.quitar_filtro + '" data-id="'+zona+'"/></a></div>';
			
	lista_datos_dimension = '<ul class="list-group">';
    
	$.each(datasetPrincipal, function(i, dato) 
	{
        lista_datos_dimension += '<li class="list-group-item"><label class="forcheckbox" for="categorias_a_mostrar' + zona + i + '" ><input type="checkbox" id="categorias_a_mostrar' + zona + i + '" ' +
                'name="categorias_a_mostrar[]" value="' + dato.category + '" /> ' + dato.category + '</label></li>';
    });
	
    lista_datos_dimension += '</ul>';
	lista_group=lista_datos_dimension1+'<div class=""><hr><div class="col-lg-12">'+
    '<div class="panel panel-default">'+
        '<div class="panel-heading"><label>' + trans.filtrar_por_elemento + '</label></div>'+
        '<div class="panel-body" style=" max-height:250px; overflow:auto; ">'+
		lista_datos_dimension+
        '</div>'+
    '</div>'+
'</div></div>';

    $('#' + zona + ' .lista_datos_dimension').html(lista_group);       
    
    // Corrige un error de bootstrap para permitir usar controles dentro de un dropdown
    $('.dropdown-menu SELECT, .dropdown-menu LABEL, .dropdown-menu INPUT').on("click",function(event) 
	{
        $(this).focus();
        event.stopPropagation();
    });    
    //Corrige un error de bootstrap para que funcione un menu dropdown en tabletas
    $('body').on('touchstart.dropdown', '.dropdown-menu', function(e) 
	{
        e.stopPropagation();
    }); 
    
    $('#' + zona + ' .aplicar_filtro').on('click',function(e)
	{
		zona=$(this).attr("data-id");
        aplicarFiltro(zona);
    });
    $('#' + zona + ' .quitar_filtro').on('click',function(e)
	{
		zona=$(this).attr("data-id");
        $('#' + zona + ' .filtro_desde').val('');
        $('#' + zona + ' .filtro_hasta').val('');
        $('#' + zona + ' .panel-body input:checked').each(function() 
		{
            $(this).attr('checked', false);
        });
        //datasetPrincipal = datasetPrincipal_bk;
        $('#' + zona).attr('datasetPrincipal', $('#' + zona).attr('datasetPrincipal_bk'))
        dibujarGraficoPrincipal(zona, $('#' + zona + ' .tipo_grafico_principal').val());
    });
    if (typeof $('#'+zona+' .titulo_indicador').attr('filtro-elementos')!=='undefined'&&$('#'+zona+' .titulo_indicador').attr('filtro-elementos') !== '') 
	{
		
        var filtroElementos = $('#' + zona + ' .titulo_indicador').attr('filtro-elementos').split(',');
        for (var j = 0; j < filtroElementos.length; j++) 
		{
            $('#' + zona + ' .capa_dimension_valores input[value="' + filtroElementos[j] + '"]').attr('checked', true);
        }
        aplicarFiltro(zona);
    }
}

function cerrarMenus() {
    $('.open').each(function(i, nodo) {
        $(nodo).removeClass('open');
    })
}

function construir_tabla_datos(zona, datos) {
    var tabla_datos = '<TABLE class="table" >';
    $.each(datos, function(i, fila) {
        if (i === 0) {
            // Los nombres de los campos
            tabla_datos += '<THEAD><TR>';
            for (var campo in fila) {

                if (campo === 'category')
                    campo = $('#' + zona + ' .dimension').html();
                else if (campo === 'measure')
                    campo = trans.indicador + ' (' + $('#' + zona + ' .titulo_indicador').attr('formula') + ')';
                tabla_datos += '<TH>' + campo.toUpperCase() + '</TH>';
            }
            tabla_datos += '</TR></THEAD><TBODY>';
        }

        //los datos
        tabla_datos += '<TR>';
        for (var i in fila)
            tabla_datos += '<TD>' + fila[i] + '</TD>';
        tabla_datos += '</TR>';

    });
    tabla_datos += '</TBODY></TABLE>';

    $('#' + zona + ' .info').html(tabla_datos);
}


function setTiposGraficos(zona) {
	var tipos_graficos = '';
    var graficos = jQuery.parseJSON($('#' + zona + ' .dimensiones option:selected').attr('data-graficos'));
    $.each(graficos, function(i, grafico) {
        tipos_graficos += "<OPTION VALUE='" + grafico.codigo + "'>" + grafico.descripcion + "</OPTION>";
    });
    $('#' + zona + ' .tipo_grafico_principal').html(tipos_graficos);
}

function alternar_favorito(zona, id_indicador) {
    //Revisar si ya es favorito
    var es_favorito;
    ($('#fav-' + id_indicador).length === 0) ? es_favorito = false : es_favorito = true;
    var cant_favoritos = parseInt($('#cantidad_favoritos').html());
    cant_favoritos = (es_favorito) ? cant_favoritos - 1 : cant_favoritos + 1;
    $('#cantidad_favoritos').html(cant_favoritos);

    if (es_favorito) 
	{
        $('#' + zona + ' .agregar_como_favorito').html('<i class="glyphicon glyphicon-star"></i>' + trans.agregar_favorito);
        $('#li_fav-' + id_indicador).remove();
    } 
	else 
	{
        $('#' + zona + ' .agregar_como_favorito').html('<i class=" glyphicon glyphicon-star-empty"></i>' + trans.quitar_favoritos);
        $('#listado-favoritos').append("<li id='li_fav-" + id_indicador + "'><A data-id='" + id_indicador + "' " +
                "id='fav-" + id_indicador + "' " +
                "data-unidad-medida='" + $('#' + zona + ' .titulo_indicador').attr('data-unidad-medida') + "'>" +
                $('#' + zona + ' .titulo_indicador').html() +
                "</A></li>");

        $('#fav-' + id_indicador).click(function() {
            $('#' + zona + ' .controles').html('');
            $('#' + zona + ' .filtros_dimensiones').html('').attr('data', '');

            recuperarDimensiones(id_indicador);
        });
    }
    $.get(Routing.generate('indicador_favorito'),
            {id: $('#' + zona + ' .titulo_indicador').attr('data-id'), es_favorito: es_favorito}
    );
}

function limpiarZona(zona) {
    $('#' + zona + ' .controles').html('');
    $('#' + zona + ' .filtros_dimensiones').attr('data', '');
    $('#' + zona + ' .filtros_dimensiones').html('');
    $('#' + zona).attr('orden', null);
}

function limpiarZona2(zona) {
    limpiarZona(zona);
    $('#' + zona + ' .titulo_indicador')
            .html('')
            .attr('data-unidad-medida', '')
            .attr('formula', '')
            .attr('rangos_alertas', '')
            .attr('data-id', '')
            .attr('data-max_rango', '')
    $('#' + zona).attr('datasetprincipal', '')
            .attr('datasetprincipal_bk', '');
    $('#' + zona + ' .grafico').html('');
    $('#' + zona + ' .dimension').html('');
    $('#' + zona + ' .controlesDimension').html('');
    $('#' + zona + ' .titulo').hide();
}

function recuperarDimensiones(id_indicador, datos) 
{
	var zona_g = $('DIV.zona_actual').attr('id');
    limpiarZona(zona_g);
	var ruta=ruta_sala_publica.split('/');
    $.getJSON(
			Routing.generate('indicador_dimensiones_public',{id:id_indicador, token:ruta[0], sala:ruta[1]}),
    function(resp) {
        //Construir el campo con las dimensiones disponibles

        var meta=0;
        if (resp.resultado === 'ok') 
		{
            if (resp.dimensiones == '') 
			{
                alert(trans.no_graficos_asignados);
            } 
			else 
			{				
                dibujarControles(zona_g, resp);
				meta=resp.meta;
				if(meta==null)
				meta=0;
                if (datos !== null) 
				{
                    if (JSON.stringify(datos.filtro) !== '""') 
					{
                        var $filtro = $('#' + zona_g + ' .filtros_dimensiones');
                        $filtro.attr('data', datos.filtro);
                        filtro_obj = jQuery.parseJSON($filtro.attr('data'));
                        var ruta = filtroRuta(filtro_obj);
                        $filtro.html(ruta);

                        for (i = 0; i < filtro_obj.length; i++) 
						{
                            $('#' + zona_g + ' .dimensiones')
                                    .children('option[value=' + filtro_obj[i].codigo + ']')
                                    .remove();
                        }

                        /*$('#' + zona_g + ' .filtros_dimensiones A').on('click',function(e){
                            ascenderNivelDimension(zona_g, $(this).attr('data'));
                        });*/
                    }
                    $('#' + zona_g + ' .titulo_indicador').attr('data-id', datos.idIndicador);
                    $('#' + zona_g).attr('orden', datos.orden);
                    $('#' + zona_g).attr('orden-aplicado', 'false');
                    $('#' + zona_g + ' .dimensiones').val(datos.dimension);
                    $('#' + zona_g + ' .filtro_desde').val(datos.filtroPosicionDesde);
                    $('#' + zona_g + ' .filtro_hasta').val(datos.filtroPosicionHasta);
                    $('#' + zona_g + ' .titulo_indicador').attr('filtro-elementos', datos.filtroElementos);
                    $('#' + zona_g + ' .tipo_grafico_principal').val(datos.tipoGrafico);					
					
                }
                dibujarGrafico(zona_g, $('#' + zona_g + ' .dimensiones').val());
            }
			$("#"+zona_g).removeClass('zona_actual');
			$('#' + zona_g + ' .panel-footer').find("#"+zona_g+"meta").remove();
			$('#' + zona_g + ' .panel-footer').append("<span class='pull-right' data-id='"+meta+"' id='"+zona_g+"meta'><strong>-</strong> Meta: "+meta+"</span>.");
        }
    });	
}

function ordenarArreglo(datos, ordenar_por, modo_orden) {
    if (ordenar_por === 'dimension')
        var datos_ordenados = datos.sort(
                (modo_orden === 'asc') ?
                function(a, b) {
                    return d3.ascending((isNaN(a.category)) ? a.category : parseFloat(a.category), (isNaN(b.category)) ? b.category : parseFloat(b.category));
                } :
                function(a, b) {
                    return d3.descending((isNaN(a.category)) ? a.category : parseFloat(a.category), (isNaN(b.category)) ? b.category : parseFloat(b.category));
                }
        );
    else
        var datos_ordenados = datos.sort(
                (modo_orden === 'asc') ? function(a, b) {
            return d3.ascending(parseFloat(a.measure), parseFloat(b.measure));
        } :
                function(a, b) {
                    return d3.descending(parseFloat(a.measure), parseFloat(b.measure));
                }
        );
    return datos_ordenados;
}
function getCookieS(cname)
{
	var name = cname + "=";
	var ca = document.cookie.split(';');
	for(var i=0; i<ca.length; i++) 
	{
		var c = ca[i].trim();
		if (c.indexOf(name)==0) return c.substring(name.length,c.length);
	}
	return "";
}


function acciones_button()
{
	  $("body").on('click','button.toimage',function(e)
		{
			zona=$(this).attr("data-id");
			
            $("#"+zona+"_image").html('<li><a id="'+zona+'esvg"><i class="ion ion-images"></i> SVG</a></li><li> <a id="'+zona+'epng"><i class="ion ion-image"></i> PNG</a></li>');
			
			// se obtiene el uniqid que genera en auto symfony al create			
			$("svg").attr({ version: '1.1' , xmlns:"http://www.w3.org/2000/svg"});
			var valor = $("#"+zona+" .grafico").html();
			
			valor=window.btoa( valor );
			
			var img = document.createElement("img");
			img.setAttribute( "src", "data:image/svg+xml;base64," +valor) ;
			titulo=$('#' + zona+' .titulo').find("span").text();   
			img.onload =new function()
			{
				var a = document.getElementById(zona+"esvg");
				a.download = titulo+".svg";
				a.href = "data:image/svg+xml;base64," +valor;		  
								
				img.style.display = "none";								
				
			};
			
			var image = img;
			var canvas = document.createElement("canvas");
			
			canvas.width = image.width;
			canvas.height = image.height;
			if (canvas.getContext) 
			{
				image.onload=new function () 
				{
					var ctx = canvas.getContext("2d");               
					ctx.clearRect(0, 0, canvas.width, canvas.height);
					ctx.drawImage(image, 0, 0);
					var p = document.getElementById(zona+"epng")
					p.download = titulo+".png";
					p.href = canvas.toDataURL("image/png");	
				
			  	};
			}
			
        });
        $('body').on("change",'.filtro_por_fecha',function()
		{
			zona=$(this).attr("data-id");
			if (this.checked == true)
			{
				$('#div_rango_fechas'+zona).css({'display':'inline'});
			}
			else
			{
				$('#div_rango_fechas'+zona).css({'display':'none'});
			}
		});
    
    $('body').on("click",'.btn_filtrar_fecha',function()
	{
		zona=$(this).attr("data-id");
        if ($('#fechainicio'+zona).val() != '' && $('#fechafin'+zona).val() != '')
        {
            setTiposGraficos(zona);
            if ($('#' + zona + ' .tipo_grafico_principal').val() != null) 
			{
                $('#' + zona + ' .ordenar_dimension').children('option[value="-1"]').prop("selected",true);
                $('#' + zona + ' .ordenar_medida').children('option[value="-1"]').prop("selected",true);
                dibujarGrafico(zona, $('#' + zona + ' .dimensiones').val());
                $('#' + zona).attr('orden', null);
            }
        }
    });

  	$('body').on('click','.myRefresh',function(e)
	{
		zona=$(this).attr("data-id");
		$(".area_grafico").removeClass('zona_actual');
		$("#"+(zona)).addClass('zona_actual');
    	recuperarDimensiones($('#' + zona + ' .titulo_indicador').attr('data-id'),null);
	});
   
	//({title: trans.ultima_lectura, content: trans.ultima_lectura_exp});
	
   $('body').on('click','.myMax',function(e)
   {
	   zona=$(this).attr("data-id");	   
  	if($(this).hasClass('glyphicon glyphicon-zoom-out')||getCookieS("zoom"+zona)=='1')
	{
		document.cookie="zoom"+zona+"=0";				
	   	minimizar(zona,contenedor);
	}
  	else
  	{	
		document.cookie="zoom"+zona+"=1";	
		if (typeof (event) == "undefined")
    		var tecla = 0;
		else
		var tecla = (event.keyCode) ? event.keyCode : event.which ;
		if (tecla != 27)
		{
			$("body").addClass("bodysinscroll");
			
			$('#' + zona + '_icon_maximizar').removeClass('glyphicon glyphicon-zoom-in');
			$('#' + zona + '_icon_maximizar').addClass('glyphicon glyphicon-zoom-out');
			$('#'+zona).parent().attr('id','_maximizado');
			$('#' + zona + '_quitar').attr("style","display:none");	
			contenedor = document.createElement('div');
			$(contenedor).attr('alt',$('#' + zona).index());
			$(contenedor).addClass("row");
			$(contenedor).css({'position':'absolute',
							   'left':'15px','top':'0px',
							   'zIndex':'99999999' ,
							   'width':'100%',
							   'height':'100%',
							   'background-color':'#F4F3FA',
							   'overflow':''});
							   
			$(contenedor).attr('id','contenedor_maximizado');
			$(contenedor).append($('#' + zona));					
			
			$(document.body).append($(contenedor));
			
			$(contenedor).fadeIn('slow',function()
			{
				$('#'+zona +' .panel-body').animate({height:$(window).height()/1.109 , width: $(window).width()-1});
			});
			aplicarFiltro(zona);
			
		}
	}
  });
  
  $('body').on("change",' .max_y',function() {
		zona=$(this).attr("data-id");
        dibujarGraficoPrincipal(zona, $('#' + zona + ' .tipo_grafico_principal').val());
    });
	
    $('body').on("change",' .ordenar_medida',function() {
		zona=$(this).attr("data-id");
        ordenarDatos(zona, 'medida', $(this).val());
    });

    $('body').on("change",' .ordenar_dimension',function() {
		zona=$(this).attr("data-id");
        ordenarDatos(zona, 'dimension', $(this).val());
    });
    

    $('body').on("change",' .dimensiones',function() 
	{
		zona=$(this).attr("data-id");
        setTiposGraficos(zona);
        if ($('#' + zona + ' .tipo_grafico_principal').val() != null) {
            $('#' + zona + ' .ordenar_dimension').children('option[value="-1"]').prop("selected",true);
            $('#' + zona + ' .ordenar_medida').children('option[value="-1"]').prop("selected",true);
            dibujarGrafico(zona, $(this).val());
            $('#' + zona).attr('orden', null);
        }
    });

    $('body').on("change",' .tipo_grafico_principal',function() {
		zona=$(this).attr("data-id");
        dibujarGraficoPrincipal(zona, $(this).val());
    });
	
    $('body').on('click',' .agregar_como_favorito',function(e){
		zona=$(this).attr("data-id");	
        alternar_favorito(zona, $(this).attr('data-indicador'));
        cerrarMenus();
    });
//    $('#' + zona + ' .zoom').on('click',function(e){
//        $('#' + zona ).toggleClass('zona_maximizada');
//    });
    $('body').on('click',' .quitar_indicador',function(e){
		zona=$(this).attr("data-id");	
        limpiarZona2(zona);		
		$("#"+zona).remove();
    });
    $('#' + zona + ' .info').hide();
    $('body').on('click','.ver_tabla_datos',function(e){
		zona=$(this).attr("data-id");	
        $('#myModalLabel2').html();
        $('#sql').html($('#' + zona + ' .info').html());
        $('#sql table').dataTable({
            "bJQueryUI": true,
            "sDom": '<"H"Tfr>t<"F"ip>',
            "oTableTools": {
                "sSwfPath": "/bundles/indicadores/js/DataTables/media/swf/copy_csv_xls_pdf.swf",
                "aButtons": [
                    {
                        "sExtends": "collection",
                        "sButtonText": trans.exportar,
                        "aButtons": [{
                                "sExtends": "csv",
                                "sTitle": $('#' + zona + ' .titulo_indicador').html()
                            }, {
                                "sExtends": "xls",
                                "sTitle": $('#' + zona + ' .titulo_indicador').html()
                            }, {
                                "sExtends": "pdf",
                                "sTitle": $('#' + zona + ' .titulo_indicador').html()
                            }]
                    }
                ]
            },
            "oLanguage": {
                "sLengthMenu": "Display _MENU_ records per page",
                "sZeroRecords": trans.nada_encontrado,
                "sInfo": trans.mostrando_n_de_n,
                "sInfoEmpty": trans.mostrando_0,
                "sInfoFiltered": trans.filtrados_de
            }
        });
        $('#myModal2').modal('show');
        //cerrarMenus();
    });

    $('body').on('click',' .ver_sql',function(e){
		zona=$(this).attr("data-id");
        var filtro = $('#' + zona + ' .filtros_dimensiones').attr('data');
        var dimension = $('#' + zona + ' .dimensiones').val();

        $.getJSON(Routing.generate('indicador_ver_sql',
                {id: $('#' + zona + ' .titulo_indicador').attr('data-id'), dimension: dimension}),
        {filtro: filtro, ver_sql: true},
        function(resp) {
            $('#myModalLabel2').html($('#' + zona + ' .titulo_indicador').html());
            $('#sql').html(resp.datos);
            $('#myModal2').modal('show');
        });
    });

    $('body').on('click','ver_imagen',function(e){
		zona=$(this).attr("data-id");
        var html = '<H5 style="text-align:center;">' + $('#' + zona + ' .titulo_indicador').html() +
                ' (por ' + $('#' + zona + ' .dimension').html() + ')</H5>' +
                '<H6 >' + $('#' + zona + ' .filtros_dimensiones').html() + '</H6>' +
                '<svg id="ChartPlot" width="95%" viewBox="-5 0 450 360" preserveAspectRatio="none">' + d3.select('#' + zona + ' svg').html() + '"</svg>' +
                $('#sql').html('<canvas id="canvasGrp" width="400" height="350"></canvas>');

        var canvas = document.getElementById("canvasGrp");

        rasterizeHTML.drawHTML(html, canvas);
        $('#myModalLabel2').html(trans.guardar_imagen);
        $('#myModal2').modal('show');
    });

    $('body' ).on('click','.ver_ficha_tecnica',function(e){
		zona=$(this).attr("data-id");	
        $.get(Routing.generate('get_indicador_ficha',{id: $('#' + zona + ' .titulo_indicador').attr('data-id')}),
        function(resp) 
		{
			
            $('#myModalLabel2').html($('#' + zona+' .titulo').find("span").text());
            $('#sql').html(resp);
            //Dejar solo el código html de la tabla, quitar todo lo demás

            $('#sql').html('<table class="table table-bordered table-striped">' + $('#sql table').html() + '</table>');
            $('#sql .sonata-ba-view-title').remove();
            $('#sql table').append('<thead><TR><TH>Campo</TH><TH>Descripcion</TH></TR></thead>');
            $('#sql table').dataTable({
                "bFilter": false,
                "bSort": false,
                "sDom": '<"H"T>',
                "bInfo": false,
                "iDisplayLength": 30,
                "oTableTools": {
                    "sSwfPath": "/bundles/indicadores/js/DataTables/media/swf/copy_csv_xls_pdf.swf",
                    "aButtons": [
                        {
                            "sExtends": "collection",
                            "sButtonText": trans.exportar,
                            "aButtons": [{
                                    "sExtends": "xls",
                                    "sTitle": $('#' + zona + ' .titulo_indicador').html()
                                }, {
                                    "sExtends": "pdf",
                                    "sTitle": $('#' + zona + ' .titulo_indicador').html()
                                }]
                        }
                    ]
                },
            });
            $('#sql .DTTT_container').css('float', 'left');
            $('#myModal2').modal('show');
        }, 'html');
    });
	$('body').on('click','.filtros_dimensiones A',function(e)
	{
		event.preventDefault();
        ascenderNivelDimension($(this).parent().attr('data-id'), $(this).attr('data'));
    });
}


$(window).on('resize', resizeGraficos); 

function resizeGraficos() 
{
	if(Object.keys(graficos).length>0)
	$(".area_grafico").each(function(){
		graficos[$(this).attr("id")].dibujar();
	});
}

function number_format(number, decimals, dec_point, thousands_sep) 
{    
    var n = !isFinite(+number) ? 0 : +number, 
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function (n, prec) {
            var k = Math.pow(10, prec);
            return '' + Math.round(n * k) / k;
        };
    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
}

function utf8_encode(argString) 
{
  if (argString === null || typeof argString === 'undefined') {
    return '';
  }

  var string = (argString + ''); // .replace(/\r\n/g, "\n").replace(/\r/g, "\n");
  var utftext = '',
    start, end, stringl = 0;

  start = end = 0;
  stringl = string.length;
  for (var n = 0; n < stringl; n++) {
    var c1 = string.charCodeAt(n);
    var enc = null;

    if (c1 < 128) {
      end++;
    } else if (c1 > 127 && c1 < 2048) {
      enc = String.fromCharCode(
        (c1 >> 6) | 192, (c1 & 63) | 128
      );
    } else if ((c1 & 0xF800) != 0xD800) {
      enc = String.fromCharCode(
        (c1 >> 12) | 224, ((c1 >> 6) & 63) | 128, (c1 & 63) | 128
      );
    } else { // surrogate pairs
      if ((c1 & 0xFC00) != 0xD800) {
        throw new RangeError('Unmatched trail surrogate at ' + n);
      }
      var c2 = string.charCodeAt(++n);
      if ((c2 & 0xFC00) != 0xDC00) {
        throw new RangeError('Unmatched lead surrogate at ' + (n - 1));
      }
      c1 = ((c1 & 0x3FF) << 10) + (c2 & 0x3FF) + 0x10000;
      enc = String.fromCharCode(
        (c1 >> 18) | 240, ((c1 >> 12) & 63) | 128, ((c1 >> 6) & 63) | 128, (c1 & 63) | 128
      );
    }
    if (enc !== null) {
      if (end > start) {
        utftext += string.slice(start, end);
      }
      utftext += enc;
      start = end = n + 1;
    }
  }

  if (end > start) {
    utftext += string.slice(start, stringl);
  }

  return utftext;
}