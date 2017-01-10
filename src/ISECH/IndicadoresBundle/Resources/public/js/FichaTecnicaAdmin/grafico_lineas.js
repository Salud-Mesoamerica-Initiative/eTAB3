graficoLineas = function(ubicacion, datos, colorChosen, categoryChoosen) 
{
    this.tipo = 'lineas';
	this.currentDatasetChart = datos;
    this.zona = ubicacion;
	this.color = colorChosen;
	this.category = categoryChoosen;
	
	var contexto=this;
	this.dibujar = function() 
	{
		var margin = {top: 50, right: 40, bottom: 75, left: 70},
		width = parseInt(d3.select('#'+this.zona+' .panel-body').style('width'), 10)
		width  = width - margin.left - margin.right-50,
		barPadding = 1;
		var height=parseInt(d3.select('#'+this.zona+' .panel-body').style('height'), 10)-150;
		if ($('#' + zona + '_icon_maximizar').hasClass('glyphicon glyphicon-zoom-out'))
			height=height-50;
		if(height<300||height>width)
		height=width*.65;				
	
		var xScale = d3.scale.ordinal()
            .domain(contexto.currentDatasetChart.map(function(d) 
			{
				return d.category;
			}))
            .rangeRoundBands([0, width], 1);
		
		var max_y=100;
		var meta=0;
		
		var long = $('#' + contexto.zona + ' .titulo_indicador').attr('data-unidad-medida');
		texto="";
		if(long=="%")
			texto="Porcentaje";
		
		var datasetPrincipal_bk = JSON.parse($('#' + this.zona).attr('datasetPrincipal_bk'));
		var	max_indicador = d3.max(datasetPrincipal_bk, function(d) 
		{
			return parseFloat(d.measure);
		});
		
		
		
		// Algoritmo de Akira para establecer eje y
		if($('#' + this.zona + '_max_y_manual').val()!=""){
			//El valor maximo de y lo indica el usuario
			max_y = parseFloat($('#' + this.zona + '_max_y_manual').val());
			// Verficamos que el valor introducido manualmente no sea menor que el maximo valor del indicador
			if(max_y<max_indicador || isNaN(max_y)){
				max_y = max_indicador;
			}
			
		}else{
			if($('#' + this.zona + ' .max_y').val()=="indicador"){
				//El maximo valor de y lo indica el indicador mas alto
				max_y = max_indicador;
			}
			else{
				//El maximo valor de y lo da el maximo valor de la alerta
				var max_alerta = $('#' + this.zona + ' .titulo_indicador').attr('data-max_rango');
				
				if(max_alerta !=""){
					max_y =  max_alerta;					
					// Verficamos que el max_alerta no sea menor que el maximo valor del indicador
					if(max_y<max_indicador){
						max_y = max_indicador;
					}
				}
				else{
					//Si no tiene alerta ponemos el maximo valor del indicador
					max_y = max_indicador;
				}
			}
		}
		
		// Tiene meta?
		if (parseFloat($('#' + this.zona + 'meta').attr("data-id"))>0)
		{
			// Verificamos que la meta no sea mayor a mi maximo valor de y
			var meta=$('#' + this.zona + 'meta').attr("data-id");
	
			if(max_y < meta){
				max_y = meta;
			}			
		}	
		
		
		var yScale = d3.scale.linear()
			.domain([0, max_y])
			.range([height, 0]);
	
		var yAxis = d3.svg.axis().scale(yScale).orient("left").ticks(10);
		var xAxis = d3.svg.axis().scale(xScale).orient("bottom");
			
		var line = d3.svg.line()			
			.x(function(d, i) 
			{
				return xScale(d.category);
			})
			.y(function(d) 
			{
				return yScale(parseFloat(d.measure));
			});
		
		var line2 = d3.svg.line()			
			.x(function(d, i) 
			{
				return xScale(d.category);
			})
			.y(function(d) 
			{
				return yScale(0);
			});

        $('#' + this.zona + ' .grafico').html('');
		
		var svg = d3.select("#" + contexto.zona + ' .grafico')
			.append("svg")
			//.datum(contexto.currentDatasetChart)
			.attr("width", width + margin.left + margin.right)
			.attr("height", height + margin.top + margin.bottom)
			.append("g")
			.attr("id", "ChartPlot")
			.attr("transform", "translate(" + margin.left + "," + margin.top + ")");
			
		svg.append("svg:path")	
			.attr("d", line2(contexto.currentDatasetChart))		
			.transition().duration(500).delay(20)
			.ease("cubic")
			.attr("d", line(contexto.currentDatasetChart))
			.style("stroke",function()
			{
				return "steelblue";
			})
			.style("fill","none")
			.style("stroke-width","1.5")
			;
		
		var datacircleGroup=svg.append("svg:g");
		var circles=datacircleGroup.selectAll("data-point").data(contexto.currentDatasetChart)
								  
		circles.enter()
			   .append("svg:circle")
			   .attr("class","dot")
			   .attr("fill", "white") 
			   .style("stroke-width","1.5")
			   .attr("stroke", function(d, i) 
				{
					return colores_alertas(contexto.zona, d.measure, i)
				})
				.on("mouseover",function(d)
				{
					d3.select(this)
					.attr("r",5)
					.transition().duration(750)
					.attr("r",10)
					.attr("stroke","ligth-gray")
					.attr("fill", function(d, i) 
					{
						return colores_alertas(contexto.zona, d.measure, i)
					})
					;
				})
				.on("mouseout",function(d)
				{
					d3.select(this)
					.transition().duration(750)
					.attr("r",5)
					.attr("fill", "white") 
				   .style("stroke-width","1.5")
				   .attr("stroke", function(d, i) 
					{
						return colores_alertas(contexto.zona, d.measure, i)
					})
					;
				})
				.attr("cx", line.x())
				.attr("cy", height)
				.transition().duration(500).delay(20)
				.ease("cubic")
				.attr("cx", line.x())								
				.attr("cy", line.y())
				.attr("r", 5)
				;
				
        svg.selectAll(".dot")
			.append("title")
			.text(function(d) 
			{
				return d.category + ": " + d.measure;
			});
				
        svg.append("g")
			.attr("class", "y axis")
			.call(yAxis);       
		
		svg.append("text")
			.attr("class","axis-label")
			.attr("transform", "rotate(0)")
			.attr("y", 0)
			.attr("x", 15)
			.attr("dy", "-2.2em")	  
			.text(long+" "+texto)
			.style("text-anchor", "end")
			.style("font-family", "Arial, Helvetica, sans-serif")
			.style("font-size", "7pt");
			
		svg.append("g")
			.attr("class", "x axis")
			.attr("transform", "translate(0," + height + ")")
			.call(xAxis)		  
			.selectAll("text")			
			.attr('x', 7).attr('y', 10)
			.attr('text-anchor', 'start')
			.style("font-family", "Arial, Helvetica, sans-serif")
			.attr('style', '')
			.style("font-size", "12pt")			
			.attr("transform", "rotate(30)"); 
		
		var ylabel=$('#' + contexto.zona + ' .dimensiones option:selected').text();
		svg.append("text")
			.attr("class","axis-label")
			.attr("transform", "rotate(-90)")
			.attr("y", width+margin.left)
			.attr("x",-((height-margin.top)-ylabel.length))
			.attr("dy", "-4.2em")	  
			.text(ylabel)
			.style("text-anchor", "end")
			.style("font-family", "Arial, Helvetica, sans-serif")
			.style("font-size", "10pt");               
				
		if(meta>0)
		{
			svg.append("line")
				.attr("x1", 5)
				.attr("y1", height-((height*meta)/max_y))
				.attr("x2", width)
				.attr("y2", height-((height*meta)/max_y))
				.attr("stroke-width", 1)
				.style("stroke-dasharray",("5","5"))
				.attr("stroke", "green");	
		}
		
		svg.selectAll(".axis line, .axis path")
			.style("fill", "none")
			.style("stroke", "#000")
			.style("font-family", "Arial, Helvetica, sans-serif")
			.style("stroke-width", "1px");
		
		var plot = svg
		.append("g")
		
		plot.selectAll("text")
			.data(contexto.currentDatasetChart)
			.enter()
			.append("text")
			.text(function(d) 
			{
				return number_format(d.measure,2);
			})
			
			.attr('x', function(d,i){return (i)*(width/contexto.currentDatasetChart.length)+(width/contexto.currentDatasetChart.length)/2;})
			.attr('y',height)
			.transition().duration(500).delay(20)
			.attr('y', function(d){a= yScale(parseFloat(d.measure))+15; if(a<0) a=0; return a;})
			.attr('text-anchor', 'middle')
			.style("font-family", "Arial, Helvetica, sans-serif")
			.attr('font-size', function()
			{ 
				var size=(width/contexto.currentDatasetChart.length)/2;

				if(size>50) size=14;
				if(size<50&&size>30) size=12;
				if(size<30&&size>10) size=10;
				if(size<10) size=0;
				
				return size;
			})
			.attr('fill', '#000');
		
		/*plot.append("path")
			.attr("class", "line")
			.attr("d", line)
			.attr("stroke", 'steelblue');*/
				
        svg.selectAll(".dot").on("click", function(d, i) {
            descenderNivelDimension(contexto.zona, d.category);
        });
    };
    this.ordenar = function(modo_orden, ordenar_por) 
	{
        this.currentDatasetChart = ordenarArreglo(this.currentDatasetChart, ordenar_por, modo_orden);
        this.dibujar();
        $('#' + this.zona).attr('datasetPrincipal', JSON.stringify(this.currentDatasetChart));

    };
}