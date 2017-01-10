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
			"callbacks": 
			{
				onClickSegment:function(info) 
				{
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
		
    this.ordenar = function(modo_orden, ordenar_por) 
	{ /*No hacer nada, el gráfico circular no se puede ordenar*/
        this.currentDatasetChart = ordenarArreglo(this.currentDatasetChart, ordenar_por, modo_orden);
        this.dibujar();
        $('#' + this.zona).attr('datasetPrincipal', JSON.stringify(this.currentDatasetChart));
    };
}