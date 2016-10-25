var App = angular.module('App', ['ui.bootstrap','ngStorage'], function($interpolateProvider) {
    $interpolateProvider.startSymbol('<%');
    $interpolateProvider.endSymbol('%>');
});
//hce las peticiones a la api
App
.factory('Crud', function ($http) {
    return {
        lista: function (url, success, error) { 
            $http.get(url).success(success).error(error)
        },
        ver: function (url, id, success, error) {
            $http.get(url +'/' + id).success(success).error(error)
        },
        crear: function (url, data, type, success, error) {         
            $http.post(url, data,{headers: {'Content-Type': type }}).success(success).error(error)                  
        },
        editar: function (url, id, data, type, success, error) {
            $http.put(url +'/' + id, data,{headers: {'Content-Type': type }}).success(success).error(error)
        },
        consulta: function (url, success, error) {
            $http.get(url).success(success).error(error)
        },
        eliminar: function (url, id,  success, error) {
            $http.delete( url +'/' + id).success(success).error(error)
         }
    };
})      
// para subir archivos con angular con file de html
.directive('fileModel', function ($parse) {
    return {
        restrict: 'A',
        link: function(scope, element, attrs) {
            var model = $parse(attrs.fileModel);
            var modelSetter = model.assign;
 
            element.bind('change', function(){
                scope.$apply(function(){
                    modelSetter(scope, element[0].files[0]);
                });
            });
        }
    };
})
//exportar tabla a xls
.directive('exportarTabla', function(){
    return {
        restrict: 'A',
        link: function(scope, element, attrs){
            element.bind('click', function(evt){
                evt.preventDefault();
                var titulo = $("#tituloReporte").html();
                var excelData = "<table><tr><th colspan='15'><h1>" + titulo + " (MEXICO) <h1></th></tr></table>" + '<table class="table table-bordered table-striped"> <thead> <tr> <th></th><th>Color</th> <th>Límite inferior</th> <th>Límite superior</th> <th></th> <th></th> <th></th> <th>Informacion</th> </tr> </thead> <tbody> <tr> <th></th><td style="background:red">0 - 69.99</td> <td>0</td> <td>69.99</td> <th></th> <th></th> <th></th> <th style="background:darkgray">Real</th> </tr> <tr> <th></th><td style="background:#FFCC00">70 - 84.99</td> <td>70</td> <td>84.99</td> <th></th> <th></th> <th></th> <th style="background:cornflowerblue">Planificado</th> </tr> <tr> <th></th><td style="background:green">85 - 100</td> <td>85</td> <td>100</td> <th></th> <th></th> <th></th> <th style="color:#000; font-weight:900; text-shadow: 1px 1px 1px #000;">Status</th> </tr> </tbody> </table>';

                excelData += document.getElementById('exportable').innerHTML;
                var blob = new Blob([excelData], {type: "text/comma-separated-values;charset=utf-8"});
                saveAs(blob, titulo+".xls");               
            })
        }
    };
})
//imprimir un div que contenga la la clase imprimir
.directive('imprimirDiv', function () 
{
    return {
        restrict: 'A',
        link: function(scope, element, attrs) 
        {
            // evento que dispara el generador de impresion
            element.bind('click', function(evt)
            {    
                evt.preventDefault();  
                var elem = document.querySelector(attrs.imprimirDiv);  
                PrintElem(elem);
            });
            // obtener el área a imprimir y se extrae su contenido html
            function PrintElem(elem)
            {
                PrintWithIframe(angular.element(elem).html());
            }
            // generar el ddocumento a imprimir
            function PrintWithIframe(data) 
            {
                // comprobar que el contenedor de impresion no exista
                if (!angular.isUndefined(document.getElementById('printf'))) 
                {
                    // crear el contenedor para guardar el elemento a imprimir
                    var iframe = document.createElement('iframe');
                    iframe.setAttribute("id","printf"); 
                    iframe.setAttribute("style","display:none"); 
                    document.body.appendChild(iframe);  

                    var titulo = $("#tituloReporte").html();

                    var mywindow =  document.getElementById('printf');
                    mywindow.contentWindow.document.write('<html lang="es" ng-app="App">'
+' <head>'
+' <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />'
+' <meta name="charset" content="UTF-8">'
+' <meta name="viewport" content="wianioh=device-wianioh, initial-scale=1, maximum-scale=1">'
+' <meta name="apple-mobile-web-app-capable" content="yes">'
+' <title></title><link rel="stylesheet" href="'+attrs.ruta+'css/print.css"/>'
+' <title></title><link rel="stylesheet" href="'+attrs.ruta+'css/custom_bootstrap.min.css" media="screen"/>'
+' <meta name="viewport" content="initial-scale=1" />'
+' </head>'
+' <body ng-controller="MatrizCtrl">'
+' <h1>' + titulo + ' (MEXICO) <h1>'
+data
+' <script src="'+attrs.ruta+'js/angular/angular.js"></script>'
+' </body>'
+' </html>');

                    
                        setTimeout(function()
                        {
                            // lanzar la sentencia imprimir
                            mywindow.contentWindow.print();
                        },500);
                        setTimeout(function()
                        {
                            // remover el contenedor de impresion
                            document.body.removeChild(iframe);
                        },2000);  
                   
                }
                return true;
            }
        }
    };
})
// para utilizar el autocomplete de ui jquery
.directive('uiAutocomplete', function() {
    return {
        require: '?ngModel',
        link: function(scope, element, attrs, controller) {
            var getOptions = function() {
                return angular.extend({}, scope.$eval(attrs.uiAutocomplete));
            };
            var initAutocompleteWidget = function () {
                var opts = getOptions();
                element.autocomplete(opts);
                if (opts._renderItem) {
                    element.data("autocomplete")._renderItem = opts._renderItem;
                }
            };
            // Watch for changes to the directives options
            scope.$watch(initAutocompleteWidget, true);
        }
    };
})
//muestra como html el contenido de un string
.directive('toHtml', function() {
    return {
        restrict: 'A',
        link: function (scope, el, attrs) {
            el.html(scope.$eval(attrs.toHtml));
        }
    };
})
//crea en lista los textos html que biene separado por br
.directive('toUl', function() {
    return {
        restrict: 'A',
        link: function (scope, el, attrs) {
            var texto = scope.$eval(attrs.toUl);
            if(texto != '')
                if(texto.substring(texto.length-4,4) == "<br>")
                    texto = substring(0,texto.length-4);

            var ul = "";
            if(texto != ''){
                ul = "<li>";
                ul += texto.replace(/<br>/g,'</li><li>');
                ul += "</li>";
            }
            el.html(ul);
        }
    };
})
//crea el grafico de estrellas 
.directive('estrella', function() {
    return {
        restrict: 'A',
        link: function (scope, el, attrs) {
            
            var numero = attrs.estrella.replace(/\D/g,'');
            estrella = numero.substring(0,1);
            media = numero.substring(1,2);
            var star = "";
            for (var i = 0; i < estrella; i++) {
                star += "<i class='fa fa-star' style='color:orange;'></i>";
            };
            if(media>0){ 
                estrella++;
                star += '<i class="fa fa-star" style="color:#FFF;"></i><i class="fa fa-star-half" style="margin-left: -22.2px; margin-right: 11px; color:orange;"></i>';
            }
            for (var i = estrella; i < 6; i++) {
                star += "<i class='fa fa-star' style='color:#fff'></i>";
            };
            el.html(star);
        }
    };
})
//muestra y cambia el tooltip de los inputs con title
.directive('tooltip', function(){
    return {
        restrict: 'A',
        link: function(scope, element, attrs){
            $(element).hover(function(){
                // on mouseenter
                $(element).tooltip('show');
            }, function(){
                // on mouseleave
                $(element).tooltip('hide');
            });
        }
    };
})
.directive('stringToNumber', function() {
  return {
    require: 'ngModel',
    link: function(scope, element, attrs, ngModel) {
      ngModel.$parsers.push(function(value) {
        return '' + value;
      });
      ngModel.$formatters.push(function(value) {
        return parseFloat(value, 10);
      });
    }
  };
})

//regresa si un contenido es array
.filter('esArray',function(){
    return function(input){
        return angular.isArray(input) ? input.length : 1;        
    }
})

//controlador
.controller('MatrizCtrl',  function ($scope, $http, $localStorage, $window, $filter, Crud) {

    $scope.today = function() {
        $scope.anio = new Date();
    };
    $scope.today();

    $scope.clear = function () {
        $scope.anio = null;
    };

    $scope.open = function($event) {
        $scope.status.opened = true;
    };

    $scope.setDate = function(year, month, day) {
        $scope.anio = new Date(year, month, day);
    };

    $scope.dateOptions = {
        formatYear: 'yyyy',
        startingDay: 1,
        minMode: 'year'
    };

    $scope.formats = ['yyyy'];
    $scope.format = $scope.formats[0];

    $scope.status = {
        opened: false
    };

    $scope.tamanoHeight = $window.innerHeight / 1.5;
    $scope.$watch(function(){
        return window.innerHeight;
    }, function(value) {                             
        $scope.tamanoHeight = value / 1.5;          
    });

    $scope.$watch(function() { 
        return $scope.anio; 
    },function(value) { 
        var anio = $filter('date')(value, "yyyy");
        $scope.cargarCatalogo($scope.ruta + "?anio=" + anio,$scope.dato.matriz);
        $scope.intento = 0;                     
    });
    $scope.dato = {};
    $scope.intento = 0; $scope.noPlaneacion = false;
    $scope.meses = ["enero","febrero","marzo","abril","mayo","junio","julio","agosto","septiembre","octubre","noviembre","diciembre"];
    $scope.cargarCatalogo = function(url, modelo) { 
        $scope.cargando = true;
        Crud.lista(url, function (data) {
            
            if(data.status==200){                   
                $scope.intento = 0;
                $scope.dato.matriz = data.data;
                $scope.noPlaneacion = false;
                $scope.imprimir_mensaje(data.mensaje,'success');
            }
            else{
                $scope.noPlaneacion = true;
                $scope.imprimir_mensaje(data.mensaje,'danger');
            }
            $scope.cargando = false;
        },function (e) {                
            if($scope.intento<1)
            {
                $scope.cargarCatalogo(url, modelo);
                $scope.intento++;
            }
            else{
                $scope.cargando = false;
            }
        });
    };
    $scope.crearPlaneacion = function(url, anio) {
        var anio = $filter('date')(anio, "yyyy");
        $scope.cargarCatalogo(url + "?anio=" + anio,$scope.dato.matriz);
    }

    $scope.guardarPlaneacion = function(url, anio) {    
        var datos = $scope.dato;
        var anio = $filter('date')(anio, "yyyy");
        datos.anio = anio;
        $scope.cargando = true;
        Crud.crear(url, $.param(datos), 'application/x-www-form-urlencoded;charset=utf-8;', function (data) {                           
                if(data.status == 200 || data.status == 201){
                    $scope.imprimir_mensaje(data.mensaje,'success');                                                            
                }else{
                    $scope.imprimir_mensaje(data.mensaje,'warning');                            
                }
                $scope.cargando = false;
            },function (e) {
                console.log(e); 
                $scope.cargando = false;        
            }
        );  
    }

    $scope.exportarPlaneacion = function() {        
    }   

    $scope.imprimir_mensaje = function(mensaje,tipo,id){
        id = angular.isUndefined(id) ? "#feedback_bar" : "#result_factura_test" +  ", #" +id;
        
        $(id).html('<div class="alert alert-'+tipo+' alert-dismissable" >'+
                        '<i class="fa fa-'+tipo+'"></i> '+
                            '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>'+
                            '<b>Alert! </b>'+mensaje+
                    '</div>');
        setTimeout(function()
        {
            $(id).html('');         
        },6000);
    }
})