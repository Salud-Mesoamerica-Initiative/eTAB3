var idIndicadorActivo;
google.load("visualization", "1", {packages: ["corechart", "charteditor"]});
$(document).ready(function() {
    $('#myTab a').click(function(e) {
        e.preventDefault();
        $(this).tab('show');
    });
    function ajax_states(){
        $(document).bind("ajaxStart.mine", function() {
            $('#div_carga').show();
        });
        $(document).bind("ajaxStop.mine", function() {
            $('#div_carga').hide();
        });
    }
    ajax_states(); 

    $('#export').on('click',function(e) {
       var t = $('.pvtTable');
        tableToExcel(t[0],'indicador', $('#titulo_header').attr('data-content').trim()+'.xls');
    });
    $('#export_grp').on('click',function(e) {    
        chart_div = document.getElementById('sql');
        chart_div.innerHTML = '<img src="' + getImgData() + '">';
        $('#myModalLabel2').html(trans.guardar_imagen);
        $('#myModal2').modal('show');
    });
    
    $('#ver_ficha').on('click',function(e) {
        if (idIndicadorActivo != null){ 
            $.get(Routing.generate('get_indicador_ficha',{id: idIndicadorActivo}), function(resp) {
                resp.replace('span12', 'span10');
                $('#fichaTecnicaContent').html(resp);
                $('#fichaTecnicaTitle').html($('#fichaTecnicaContent').find(".sonata-ba-view-title").find("h2").html());
                $('#fichaTecnicaContent').find(".sonata-ba-view-title").find("h2").remove();
                $('#fichaTecnicaContent').html('<table>' + $('#fichaTecnicaContent table').html() + '</table>');
                $('#fichaTecnica').modal('show');
            });
        }
    });
    
    $("#FiltroNoClasificados").searchFilter({targetSelector: ".indicador", charCount: 2});
    
    $('A.indicador').click(function() {
        var id_indicador = $(this).attr('data-id');
        var nombre_indicador = $(this).attr('data-name');
        cargar_indicador(id_indicador,nombre_indicador);
    });
});
function getImgData() {
    var chartArea = document.getElementsByTagName('svg')[0].parentNode;
    var svg = chartArea.innerHTML;
    var canvas = document.createElement('canvas');
    canvas.setAttribute('width', chartArea.offsetWidth);
    canvas.setAttribute('height', chartArea.offsetHeight);


    canvas.setAttribute(
        'style',
        'position: absolute; ' +
        'top: ' + (-chartArea.offsetHeight * 2) + 'px;' +
        'left: ' + (-chartArea.offsetWidth * 2) + 'px;');
    document.body.appendChild(canvas);
    canvg(canvas, svg);
    var imgData = canvas.toDataURL("image/png");
    canvas.parentNode.removeChild(canvas);
    return imgData;
}
function cargar_indicador(id_indicador,nombre_indicador){
    var renderers = $.extend($.pivotUtilities.renderers,$.pivotUtilities.gchart_renderers);
                    
    $.getJSON(Routing.generate('get_datos_indicador', {id: id_indicador}), function(mps) {
        $("#opcion_exportar").attr("style","display:");
        $("#output").pivotUI(mps, {
            renderers: renderers,
            menuLimit: 500,
            unusedAttrsVertical: false
        }, false, 'es');
        $('#titulo_header').attr('data-content', nombre_indicador);            
        idIndicadorActivo = id_indicador;
    });
}

var tableToExcel = (function() {
  var uri = 'data:application/vnd.ms-excel;base64,'
    , template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>'
    , base64 = function(s) { return window.btoa(unescape(encodeURIComponent(s))) }
    , format = function(s, c) { return s.replace(/{(\w+)}/g, function(m, p) { return c[p]; }) }
  return function(table, name, filename) {
    if (table !== undefined){        
        if (!table.nodeType) table = document.getElementById(table);
        var ctx = {worksheet: name || 'Worksheet', table: table.innerHTML};
        document.getElementById("dlink").href = uri + base64(format(template, ctx));
        document.getElementById("dlink").download = filename;
        document.getElementById("dlink").click();
    }
}
})();