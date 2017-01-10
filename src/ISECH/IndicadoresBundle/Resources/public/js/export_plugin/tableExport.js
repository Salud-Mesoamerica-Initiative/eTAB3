
(function($){
    $.fn.extend({
        tableExport: function(options) {
            var defaults = {
                separator: ',',
                ignoreColumn: [],
                type:'csv',
                pdfFontSize:12,
                pdfLeftMargin:20,
                escape:'true',
                htmlContent:'false',
                consoleLog:'false'
            };

            var options = $.extend(defaults, options);
            var el = this;

            if(defaults.type == 'csv'){

                    var tdData ="";
                    $(el).find('tbody').find('tr').each(function() {
                    tdData += "\n";

                            $(this).find('td').each(function(index,data) {
                                var colspan = $(this).attr('colspan') ? $(this).attr('colspan') : 0;
                                
                                tdData += '"'+ parseString($(this)) + '"'+ defaults.separator;
                                
                                for(i=0; i < (colspan - 1) ; i++){
                                    tdData += '""'+ defaults.separator;
                                }
                            });
                            tdData = $.trim(tdData);
                            tdData = $.trim(tdData).substring(0, tdData.length);
                    });

                    if(defaults.consoleLog == 'true'){
                            console.log(tdData);
                    }
                    return tdData;

            }else if(defaults.type == 'xml'){

                    var xml = '<?xml version="1.0" encoding="utf-8"?>';
                    xml += '<indicadores>';

                    // Row Vs Column
                    var rowCount=1;
                    var indicador = "";
                    var encabezado = [];
                    $(el).find('tbody').find('tr').each(function() {
                        
                        var tipo = $(this).attr('tipo');
                        if (tipo == "indicador"){
                            
                            var nombre = $(this).find('td:not(:empty):first').html();
                            if (indicador == ""){
                                xml += '<indicador_item nombre="'+nombre+'">';
                            }else{
                                    xml += '</indicador><indicador nombre="'+nombre+'">';
                            }
                            indicador = nombre;
                            
                        }else if (tipo == "encabezado"){
                            
                            encabezado = [];
                            $(this).find('td').each(function(index,data) {
                                encabezado[encabezado.length] = parseString($(this));
                            });
                        }else if(tipo == "contenido"){
                            xml += "<valores>";
                            $(this).find('td').each(function(index,data) {
                                xml += '<valor nombre="'+encabezado[index]+'">'+parseString($(this))+"</valor>";
                            });
                            xml += '</valores>';
                        }
                    });					
                    xml += '</indicador_item></indicadores>'

                    if(defaults.consoleLog == 'true'){
                            console.log(xml);
                    }
                    
                    return xml;

            }else if(defaults.type == 'excel'){
                    //console.log($(this).html());
                    var excel="<table>";					
                    $(el).find('tbody').find('tr').each(function() {
                            excel += "<tr>";
                            $(this).find('td').each(function(index,data) {
                                
                                var colspan = $(this).attr('colspan') ? 'colspan='+$(this).attr('colspan') : '';
                                
                                excel += "<td "+colspan+" >"+parseString($(this))+"</td>";
                                
                            });															
                            excel += '</tr>';
                    });					
                    excel += '</table>'

                    if(defaults.consoleLog == 'true'){
                            console.log(excel);
                    }

                    var excelFile = "<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:x='urn:schemas-microsoft-com:office:excel' xmlns='http://www.w3.org/TR/REC-html40'>";
                    excelFile += "<head>";
                    excelFile += "<!--[if gte mso 9]>";
                    excelFile += "<xml>";
                    excelFile += "<x:ExcelWorkbook>";
                    excelFile += "<x:ExcelWorksheets>";
                    excelFile += "<x:ExcelWorksheet>";
                    excelFile += "<x:Name>";
                    excelFile += "{worksheet}";
                    excelFile += "</x:Name>";
                    excelFile += "<x:WorksheetOptions>";
                    excelFile += "<x:DisplayGridlines/>";
                    excelFile += "</x:WorksheetOptions>";
                    excelFile += "</x:ExcelWorksheet>";
                    excelFile += "</x:ExcelWorksheets>";
                    excelFile += "</x:ExcelWorkbook>";
                    excelFile += "</xml>";
                    excelFile += "<![endif]-->";
                    excelFile += "</head>";
                    excelFile += "<body>";
                    excelFile += excel;
                    excelFile += "</body>";
                    excelFile += "</html>";
                    
                    return excelFile;

                    //var base64data = "base64," + $.base64.encode(excelFile);
                    //window.open('data:application/vnd.ms-excel;filename=reporte.xls;' + base64data);

            }else if(defaults.type == 'pdf'){

                    var doc = new jsPDF('p','mm', 'a4', true);
                    doc.setFontSize(defaults.pdfFontSize);

                    // Header
                    var startColPosition=defaults.pdfLeftMargin;				

                    // Row Vs Column
                    var startRowPosition = 20; 
                    var page = 1;
                    var rowPosition=0;
                    var columnas = 0;
                    
                    $(el).find('tbody').find('tr').each(function(index,data) {

                        var tipo = $(this).attr('tipo');
                        
                        rowCalc = index+1;

                        if (rowCalc % 26 == 0){
                                doc.addPage();
                                page++;
                                startRowPosition=startRowPosition+10;
                        }
                    
                        rowPosition=(startRowPosition + (rowCalc * 10)) - ((page -1) * 280);

                        $(this).find('td').each(function(index,data) {
                            if (index === 0 && tipo === "indicador"){
                                columnas = $(this).attr('colspan') ? $(this).attr('colspan') : 0;        
                            }
                            var colPosition = startColPosition+ (index * Math.round(200/columnas));
                            var texto = parseString($(this));
                            
                            while(texto.length > 0){
                                if (texto.length>90){
                                var cadena = texto.substring(0,90);
                                var indexcorte = cadena.lastIndexOf(" ");
                                }else{
                                    cadena = texto;
                                    indexcorte = texto.length;
                                }
                                cadena = texto.substring(0,indexcorte);
                                texto = texto.substring(indexcorte);
                                doc.text(colPosition,rowPosition, cadena);
                            }
                            
                        });															

                    });					

                    // Output as Data URI
                    //doc.output('datauri');

                doc.save('reporte.pdf');
            }


            function parseString(data){

                    if(defaults.htmlContent == 'true'){
                            content_data = data.html().trim();
                    }else{
                            content_data = data.text().trim();
                    }

                    if(defaults.escape == 'true'){
                            content_data = escape(content_data);
                    }

                    return content_data;
            }

    }
    });
})(jQuery);
        
