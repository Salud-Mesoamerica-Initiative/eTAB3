graficoPastel = function(ubicacion, datos, colorChosen, categoryChoosen) 
{
    this.tipo = 'pastel';
    this.currentDatasetChart = datos;
    this.zona = ubicacion;
	this.color = colorChosen;
	this.category = categoryChoosen;
	
	var contexto=this;
    this.dibujar = function() 
	{
		var margin = {top: 30, right: 40, bottom: 75, left: 50},
		width = parseInt(d3.select('#'+this.zona+' .panel-body').style('width'), 10)
		width  = width - margin.left - margin.right-50,
		barPadding = 1;
		var height=parseInt(d3.select('#'+this.zona+' .panel-body').style('height'), 10)-150;
		if ($('#' + zona + '_icon_maximizar').hasClass('glyphicon glyphicon-zoom-out'))
			height=height-50;
		if(height<300||height>width)
		height=width*.65;
		
        $('#' + ubicacion + ' .grafico').html('');
       /* var vis = d3.select('#' + ubicacion + ' .grafico')
                .append("svg:svg")              //create the SVG element inside the <body>
                .data([datos])                  //associate our data with the document
                .attr("viewBox", '-5 0 440 310')
				.attr("style", 'width:96%')
                .attr("preserveAspectRatio", 'none')
                .append("svg:g")                //make a group to hold our pie chart
                .attr("transform", "translate(" + parseFloat(outerRadius + 30) + "," + outerRadius + ")")    //move the center of the pie chart from 0, 0 to radius, radius
                .attr("id", "ChartPlot")
                ;
		
        var arc = d3.svg.arc().outerRadius(outerRadius).innerRadius(innerRadius);

        // for animation
        var arcFinal = d3.svg.arc().innerRadius(innerRadiusFinal).outerRadius(outerRadius);
        var arcFinal3 = d3.svg.arc().innerRadius(innerRadiusFinal3).outerRadius(outerRadius);

        var pie = d3.layout.pie()
				         //this will create arc data for us given a list of values
                .value(function(d) {
            return parseFloat(d.measure);
        });    //we must tell it out to access the value of each element in our data array
		
        var arcs = vis.selectAll("g.slice")     //this selects all <g> elements with class slice (there aren't any yet)
                .data(pie)                          //associate the generated pie data (an array of arcs, each having startAngle, endAngle and value properties) 
                .enter()                            //this will create <g> elements for every "extra" data element that should be associated with a selection. The result is creating a <g> for every object in the data array
                .append("svg:g")                //create a group to hold each slice (we will have a <path> and a <text> element associated with each slice)
                .attr("class", "slice")    //allow us to style things in the slices (like text)
                .on("mouseover", mouseover)
                .on("mouseout", mouseout)
                ;
        arcs.on("dblclick", function(d, i) {
            descenderNivelDimension(ubicacion, d.data.category);
        });

        if (color_grafico == null)
            arcs.append("svg:path")
                    .attr("fill", function(d, i) {
                return colores_alertas(ubicacion, d.data.measure, i)
            });
        else
            arcs.append("svg:path")
                    .attr("fill", function(d, i) {
                return color_grafico;
            });

        arcs.attr("d", arc)     //this creates the actual SVG path using the associated data (pie) with the arc drawing function
			.append("svg:title") //mouseover title showing the figures
			.text(function(d) {
            return d.data.category + ": " + d.data.measure;
        });

        d3.selectAll("g.slice").selectAll("path")
                .transition()
                .duration(750)
                .delay(10)
                .attr("stroke", "white")
                .attr("stroke-width", 1.5)
                .attr("d", arcFinal)
                ;

        // Add a label to the larger arcs, translated to the arc centroid and rotated.
        // source: http://bl.ocks.org/1305337#index.html
        arcs.filter(function(d) {
            return d.endAngle - d.startAngle > .2;
        })
                .append("svg:text")
                .attr("dy", ".35em")
                .attr("text-anchor", "middle")
                .attr("transform", function(d) {
            return "translate(" + arcFinal.centroid(d) + ")rotate(" + angle(d) + ")";
        })
                //.text(function(d) { return formatAsPercentage(d.value); })
                .text(function(d) {
            return d.data.category+" : "+d.data.measure;
        })
		.attr('fill', '#ccc');

        // Computes the label angle of an arc, converting from radians to degrees.
        function angle(d) {
            var a = (d.startAngle + d.endAngle) * 90 / Math.PI - 90;
            return a > 90 ? a - 180 : a;
        }


        if (categoryChoosen != null)
            // Pie chart title			
            vis.append("svg:text")
                    .attr("dy", ".35em")
                    .attr("text-anchor", "middle")
                    .text("Datos de " + categoryChoosen)
                    .attr("class", "title")
                    ;

        function mouseover() {
            d3.select(this).select("path").transition()
                    .duration(750)
                    .attr("stroke", "red")
                    .attr("stroke-width", 2.5)
                    .attr("d", arcFinal3)
                    ;
        }

        function mouseout() {
            d3.select(this).select("path").transition()
                    .duration(750)
                    .attr("stroke", "white")
                    .attr("stroke-width", 1.5)
                    .attr("d", arcFinal)
                    ;
        }
		
		data = this.randomData();
		
		var svg = d3.select("#" + contexto.zona + ' .grafico')
			.append("svg:svg")
			.data([data])
			.attr("width", width + margin.left + margin.right)
			.attr("height", height + margin.top + margin.bottom)
			.append("svg:g")
			.attr("id", "ChartPlot")
			.attr("transform", "translate(" + width/1.65 + "," + height/1.5 + ")");
			
				
			
			
		this.draw( this.randomData(), (width + margin.left + margin.right),(height + margin.top + margin.bottom), width/2.8);*/
		$("#" + contexto.zona + ' .grafico').attr("id",contexto.zona + '_grafico')
		var pie = new d3pie(contexto.zona + '_grafico', 
		{
			"size": 
			{
				"canvasWidth": width + margin.left + margin.right
			},
			"data": 
			{
				"sortOrder": "value-desc",
				"content":
				this.formatPieData()
			},
			"labels": 
			{
				"inner": 
				{
					"hideWhenLessThanPercentage": 3
				},
				"mainLabel": 
				{
					"fontSize": 11
				},
				"percentage": {
					"color": "#ffffff",
					"decimalPlaces": 0
				},
				"value": {
					"color": "#adadad",
					"fontSize": 11
				},
				"lines": {
					"enabled": true
				}
			},
			"effects": 
			{
				"pullOutSegmentOnClick": {
					"effect": "linear",
					"speed": 400,
					"size": 8
				}
			},
			"misc": 
			{
				"gradient": {
					"enabled": true,
					"percentage": 100
				}
			},
			"callbacks": {
				onClickSegment:function(info) 
								{console.log(info);
									descenderNivelDimension(contexto.zona, info.data.label);
								}	
				}
		});
		
	};
	
	this.formatPieData = function()
	{
		//var data = contexto.currentDatasetChart.map(function(d,i)
		
		return contexto.currentDatasetChart.map(function(d,i)
		{ 
			return {"label":d.category, 
						"value":parseFloat(d.measure),
						 "color":colores_alertas(contexto.zona, d.measure, i)
			};
		});
	};
	this.draw = function( data, w, h, r)
	{
		var arc = d3.svg.arc().outerRadius(r).innerRadius(r);

        // for animation
        var arcFinal = d3.svg.arc().innerRadius(0).outerRadius(r);
        var arcFinal3 = d3.svg.arc().innerRadius(2).outerRadius(r+8);
		
		var vis = d3.select("#" + contexto.zona + ' .grafico')
			.append("svg:svg")              
			.data([data])                   
			.attr("width", w)           
			.attr("height", h)
			.append("svg:g")                
			.attr("transform", "translate(" + (r+(w/4.8)) + "," + (r+(h*.15)) + ")")    
		
		var arc = d3.svg.arc()              
			.outerRadius(r);
		
		var pie = d3.layout.pie()           
			.value(function(d) 
			{ 
				return d.value; 
			});    
		
		var arcs = vis.selectAll("g.slice")     
			.data(pie)                          
			.enter()                            
			.append("svg:g")                
			.attr("class", "slice")
			.on("mouseover", mouseover)
            .on("mouseout", mouseout);    
		
		var labels = vis.selectAll("text").data(pie(data)).enter().append("text");
		
		
		labels.attr("class", "value")
			.attr("transform", function(d) 
			{
				var dist=r+35;
				var winkel=(d.startAngle+d.endAngle)/2;
				var x=dist*Math.sin(winkel);
				var y=-dist*Math.cos(winkel);
				return "translate(" + x + "," + y + ")";
			})
			.attr("dy", "0.35em")
			.style("font-size","0.9em")
			
			.attr("text-anchor", "middle")
			.text(function(d)
			{
				return d.data.category;
			});
		
		// Die kleine Linien (Ticks) erzeugen und positionieren
		var ticks = vis.selectAll("line").data(pie(data)).enter().append("line");
		
		ticks.attr("x1", 0)
			.attr("x2", 0)
			.attr("y1", -r+0)
			.attr("y2", -r-10)
			.attr("stroke", "gray")
			.attr("transform", function(d) 
			{    	
				return "rotate(" + (d.startAngle+d.endAngle)/2 * (180/Math.PI) + ")";
			});
   
		arcs.append("svg:path")
			.attr("fill", function(d, i) 
			{ 
				return colores_alertas(contexto.zona, d.data.value, i); 
			}) 
			.attr("d", arc)     
			.append("svg:title")
			.text(function(d) 
			{
            	return d.data.label;
        	});
		
		arcs.append("svg:text")                                     
			.attr("transform", function(d) 
			{                   
				d.innerRadius = 0;
				d.outerRadius = r;
				return "translate(" + arc.centroid(d) + ")";        
			})
			.attr("text-anchor", "middle")   
			.style("font-size", "1em")                          
			.text(function(d, i) 
			{ 
				return data[i].value; 
			});
			        
		arcs.selectAll("path").on("click", function(d, i) 
		{
            descenderNivelDimension(contexto.zona, d.data.category);
        });	
		
		function mouseover() {
            d3.select(this).select("path").transition()
                    .duration(500)
                    .attr("stroke", "gray")
                    .attr("d", arcFinal3)
                    ;
        }

        function mouseout() {
            d3.select(this).select("path").transition()
                    .duration(500)
                    .attr("stroke", "white")
                    .attr("d", arcFinal)
                    ;
        }		
	};	
	
	
	this.transition = function(id, data, r) 
	{
		function arcTween(a) 
		{
			var i = d3.interpolate(this._current, a);
			this._current = i(0);
			return function(t) { return d3.svg.arc().outerRadius(r)(i(t));  };
		}
		d3.select("#"+id).selectAll("path").data(pie(data))
		.transition().duration(750).attrTween("d", arcTween);
	};
    
    this.ordenar = function(modo_orden, ordenar_por) 
	{ /*No hacer nada, el gráfico circular no se puede ordenar*/
        this.currentDatasetChart = ordenarArreglo(this.currentDatasetChart, ordenar_por, modo_orden);
        this.dibujar();
        $('#' + this.zona).attr('datasetPrincipal', JSON.stringify(this.currentDatasetChart));
    };
}