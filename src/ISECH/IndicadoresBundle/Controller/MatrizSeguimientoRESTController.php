<?php

namespace ISECH\IndicadoresBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\Get;
use ISECH\IndicadoresBundle\Entity\MatrizSeguimiento;
use ISECH\IndicadoresBundle\Entity\MatrizSeguimientoDato;


use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\Loader\ArrayLoader;

class MatrizSeguimientoRESTController extends Controller {

    
    public function MatrizPlaneacionAction()
    {        
        return $this->render('IndicadoresBundle:Matriz:planeacion.html.twig', array('admin_pool'    => $this->container->get('sonata.admin.pool')));
    }

    public function MatrizRealAction()
    {        
        return $this->render('IndicadoresBundle:Matriz:real.html.twig', array('admin_pool'    => $this->container->get('sonata.admin.pool')));
    }

    public function MatrizReporteAction()
    {        
        return $this->render('IndicadoresBundle:Matriz:reporte.html.twig', array('admin_pool'    => $this->container->get('sonata.admin.pool')));
    }

    public function listAction(){
        return $this->render('IndicadoresBundle:Matriz:reporte.html.twig', array('admin_pool'    => $this->container->get('sonata.admin.pool')));
    }
    /**
     * @Route("/matriz/planeacion", name="matriz_planeacion", options={"expose"=true})
     */
    public function planeacion(Request $request){
        $response = new Response();

        $anio = $request->query->get('anio');

        $em = $this->getDoctrine()->getEntityManager();

        $connection = $em->getConnection();
        $statement = $connection->prepare('SELECT distinct(id_desempeno) FROM matriz_seguimiento WHERE anio = :anio');
        $statement->bindValue('anio', $anio);
        $statement->execute();
        $matriz = $statement->fetchAll();

        if($matriz){
            $resp = array(); $indicadores_in = array();
            foreach ($matriz as $key => $value) {
                $value = (object) $value;
                $ind = $em->getRepository("IndicadoresBundle:MatrizIndicadoresDesempeno")->find($value->id_desempeno);
                if($ind){
                    
                    $indicador = array();
                    $indicadores_in[] = $value->id_desempeno;
                    $indicador['id'] = $ind->getId();
                    $indicador['nombre'] = $ind->getNombre();

                    $connection = $em->getConnection();
                    $statement = $connection->prepare("SELECT meta FROM matriz_seguimiento WHERE anio = '$anio' and id_desempeno = '".$value->id_desempeno."' ");
                    $statement->execute();
                    $meta = $statement->fetchAll();

                    $indicador['meta'] = $meta[0]["meta"];
                    
                    $indicators = array(); $i=0;
                    foreach($ind->getIndicators() as $indrs){
                        $indicators[$i] = array('id'=>$indrs->getId(), 'nombre'=>$indrs->getNombre());

                        $connection = $em->getConnection();
                        $statement = $connection->prepare("SELECT msd.mes, msd.planificado FROM matriz_seguimiento ms 
                            LEFT JOIN matriz_seguimiento_dato msd ON msd.id_matriz = ms.id   
                            WHERE ms.anio = '$anio' and ms.etab = false and ms.id_desempeno = '".$value->id_desempeno."' and indicador = '".$indrs->getId()."'");
                        $statement->execute();
                        $meses = $statement->fetchAll();

                        foreach ($meses as $km => $vm) {
                            $vm = (object) $vm;
                            $indicators[$i][$vm->mes] = $vm->planificado;
                        }
                        $i++;
                    }

                    $etab = array(); $i=0;
                    foreach($ind->getMatrizIndicadoresEtab() as $indrs){
                        $etab[$i] = array('id'=>$indrs->getId(), 'nombre'=>$indrs->getNombre());

                        $connection = $em->getConnection();
                        $statement = $connection->prepare("SELECT msd.mes, msd.planificado FROM matriz_seguimiento ms 
                            LEFT JOIN matriz_seguimiento_dato msd ON msd.id_matriz = ms.id   
                            WHERE ms.anio = '$anio' and ms.etab = true and ms.id_desempeno = '".$value->id_desempeno."' and indicador = '".$indrs->getId()."'");
                        $statement->execute();
                        $meses = $statement->fetchAll();
                        
                        foreach ($meses as $km => $vm) {
                            $vm = (object) $vm;
                            $etab[$i][$vm->mes] = $vm->planificado;
                        }
                        $i++;
                    }

                    $indicador['indicadores_etab'] = $etab;
                    $indicador['indicadores_relacion'] = $indicators;

                    $resp[] = $indicador;                    
                }
            }
           
            $indicadores_in = implode(",", $indicadores_in);
            $statement = $connection->prepare("SELECT id FROM matriz_indicadores_desempeno WHERE id not in($indicadores_in)");
            $statement->execute();
            $indicadores = $statement->fetchAll();            

            if($indicadores){
                foreach($indicadores as $indicator){
                    $indicador = array();
                    
                    $ind = $em->getRepository("IndicadoresBundle:MatrizIndicadoresDesempeno")->find($indicator["id"]);

                    $indicador['id'] = $ind->getId();
                    $indicador['nombre'] = $ind->getNombre();
                    
                    $indicators = array();
                    foreach($ind->getIndicators() as $indrs){
                        $indicators[] = array('id'=>$indrs->getId(), 'nombre'=>$indrs->getNombre());
                    }

                    $etab = array();
                    foreach($ind->getMatrizIndicadoresEtab() as $indrs){
                        $etab[] = array('id'=>$indrs->getId(), 'nombre'=>$indrs->getNombre());
                    }

                    $indicador['indicadores_etab'] = $etab;
                    $indicador['indicadores_relacion'] = $indicators;

                    $resp[] = $indicador;
                }
            }
            $resp = ["data" => $resp, "mensaje" => $this->get('translator')->trans('_cargado_correctamente'), "status" => 200];
        }else{
            $resp = ["data" => "false", "mensaje" => $this->get('translator')->trans('_ningun_dato_'), "status" => 404];
        }
        
        $response->setContent(json_encode($resp));

        return $response;
    }

    /**
     * @Route("/matriz/planeacion/crear", name="matriz_planeacion_crear", options={"expose"=true})
     */
    public function planeacionCrear(Request $request){
        $response = new Response();
        $resp = array();
        $anio = $request->query->get('anio');

        $em = $this->getDoctrine()->getManager();        
        $indicadores = $em->getRepository("IndicadoresBundle:MatrizIndicadoresDesempeno")->findBy(array(), array('id' => 'ASC'));
        if($indicadores){
            foreach($indicadores as $ind){
                $indicador = array();
            
                $indicador['id'] = $ind->getId();
                $indicador['nombre'] = $ind->getNombre();
                
                $indicators = array();
                foreach($ind->getIndicators() as $indrs){
                    $indicators[] = array('id'=>$indrs->getId(), 'nombre'=>$indrs->getNombre());
                }

                $etab = array();
                foreach($ind->getMatrizIndicadoresEtab() as $indrs){
                    $etab[] = array('id'=>$indrs->getId(), 'nombre'=>$indrs->getNombre());
                }

                $indicador['indicadores_etab'] = $etab;
                $indicador['indicadores_relacion'] = $indicators;

                $resp[] = $indicador;
            }
            $resp = ["data" => $resp  , "mensaje" => $this->get('translator')->trans('_cargado_correctamente'), "status" => 200];
        }else{
            $resp = ["data" => "false", "mensaje" => $this->get('translator')->trans('_ningun_dato_'), "status" => 404];
        }
        
        $response->setContent(json_encode($resp));

        return $response;
    }

    /**
     * @Route("/matriz/planeacion/guardar", name="matriz_planeacion_guardar", options={"expose"=true})
     */
    public function planeacionGuardar(Request $request){
        $response = new Response();
        $resp = array();
        $em = $this->getDoctrine()->getEntityManager(); 
        $bien = true;
        $anio = $request->request->get('anio');
        $matriz = $request->request->get('matriz');
        $existe = $em->getRepository('IndicadoresBundle:MatrizSeguimiento')->findBy(
            array(
                'anio' => $anio
            )
        );

        foreach ($matriz as $key => $value) {
            $value = (object) $value;
            if(isset($value->indicadores_etab))
            foreach ($value->indicadores_etab as $ke => $ve) {                                                            
                if(!$this->insertSeguiminetoDato($em, $existe, $value, $ke, $ve, $anio, 1))
                    $bien = false;                
            }
            if(isset($value->indicadores_relacion))
            foreach ($value->indicadores_relacion as $ke => $ve) {                                                            
                if(!$this->insertSeguiminetoDato($em, $existe, $value, $ke, $ve, $anio, 0))
                    $bien = false;                
            }
        }
        if($bien){
            $resp = ["data" => "true" , "mensaje" => $this->get('translator')->trans('_guardar_ok_'), "status" => 200];
        }else{
            $resp = ["data" => "false", "mensaje" => $this->get('translator')->trans('_guardar_no_ok_'), "status" => 404];
        }
        
        $response->setContent(json_encode($resp));

        return $response;
    }

    public function insertSeguiminetoDato($em, $existe, $value, $ke, $ve, $anio, $etab){
        $ve = (object) $ve; 
        try{
            if($existe){
                $seguimiento = $em->getRepository('IndicadoresBundle:MatrizSeguimiento')->findBy(
                    array(
                        'desempeno' => $value->id,
                        'anio' => $anio,
                        'indicador' => $ve->id,
                        'etab' => $etab
                    )
                );
                if($seguimiento)
                    $seguimiento = $em->getRepository('IndicadoresBundle:MatrizSeguimiento')->find($seguimiento[0]->getId());
                else{
                    $seguimiento = new MatrizSeguimiento();
                }
                        

                $seguimiento->setAnio($anio);
                $seguimiento->setEtab($etab);
                $seguimiento->setIndicador($ve->id);

                $desempeno = $em->getRepository('IndicadoresBundle:MatrizIndicadoresDesempeno')->find($value->id);

                $seguimiento->setDesempeno($desempeno);
                $seguimiento->setMeta($value->meta); 

                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($seguimiento);
                $em->flush();

                foreach ($ve as $k1 => $v1) {
                    if($k1 != "id" && $k1 != "nombre" && $k1 != '$$hashKey'){
                        $matrizDato = $em->getRepository('IndicadoresBundle:MatrizSeguimientoDato')->findBy(
                            array(
                                'matriz' => $seguimiento->getId(),
                                'mes' => $k1
                            )
                        );

                        if($matrizDato){
                            $matrizDato = $em->getRepository('IndicadoresBundle:MatrizSeguimientoDato')->find($matrizDato[0]->getId());
                        }
                        else{
                            $matrizDato = new MatrizSeguimientoDato();                    
                        }

                        $matrizDato->setMes($k1);
                        $matrizDato->setPlanificado($v1);
                        $matrizDato->setMatriz($seguimiento);

                        $em = $this->getDoctrine()->getEntityManager();
                        $em->persist($matrizDato);
                        $em->flush();
                    }
                }
            }
            return true;
        }catch(\Exception $e){
            var_dump($e->getMessage());
            return false;
        }
    }



    //REAL

    /**
     * @Route("/matriz/real", name="matriz_real", options={"expose"=true})
     */
    public function real(Request $request){
        $response = new Response();

        $anio = $request->query->get('anio');

        $em = $this->getDoctrine()->getEntityManager();

        $connection = $em->getConnection();
        $statement = $connection->prepare('SELECT distinct(id_desempeno) FROM matriz_seguimiento WHERE anio = :anio');
        $statement->bindValue('anio', $anio);
        $statement->execute();
        $matriz = $statement->fetchAll();

        if($matriz){
            $resp = array();
            foreach ($matriz as $key => $value) {
                $value = (object) $value;
                $ind = $em->getRepository("IndicadoresBundle:MatrizIndicadoresDesempeno")->find($value->id_desempeno);
                if($ind){
                    
                    $indicador = array();
                
                    $indicador['id'] = $ind->getId();
                    $indicador['nombre'] = $ind->getNombre();

                    $connection = $em->getConnection();
                    $statement = $connection->prepare("SELECT meta FROM matriz_seguimiento WHERE anio = '$anio' and id_desempeno = '".$value->id_desempeno."' ");
                    $statement->execute();
                    $meta = $statement->fetchAll();

                    $indicador['meta'] = $meta[0]["meta"];
                    
                    $indicators = array(); $i=0;
                    foreach($ind->getIndicators() as $indrs){
                        $indicators[$i] = array('id'=>$indrs->getId(), 'nombre'=>$indrs->getNombre());

                        $connection = $em->getConnection();
                        $statement = $connection->prepare("SELECT msd.mes, msd.planificado, msd.real FROM matriz_seguimiento ms 
                            LEFT JOIN matriz_seguimiento_dato msd ON msd.id_matriz = ms.id   
                            WHERE ms.anio = '$anio' and ms.etab = false and ms.id_desempeno = '".$value->id_desempeno."' and indicador = '".$indrs->getId()."'");
                        $statement->execute();
                        $meses = $statement->fetchAll();

                        foreach ($meses as $km => $vm) {
                            $vm = (object) $vm;
                            $indicators[$i][$vm->mes]["planificado"] = $vm->planificado;
                            $indicators[$i][$vm->mes]["real"] = $vm->real;
                        }
                        $i++;
                    }
                    // obtener los datos del etab
                    $etab = array(); $i=0;
                    
                    $fichaRepository = $em->getRepository('IndicadoresBundle:FichaTecnica');            
                    $errores = ""; $ci = 0;
                    foreach($ind->getMatrizIndicadoresEtab() as $indrs){
                        $ci++;
                        $etab[$i] = array('id'=>$indrs->getId(), 'nombre'=>$indrs->getNombre());

                        $connection = $em->getConnection();
                        $statement = $connection->prepare("SELECT msd.mes, msd.planificado, msd.real FROM matriz_seguimiento ms 
                            LEFT JOIN matriz_seguimiento_dato msd ON msd.id_matriz = ms.id   
                            WHERE ms.anio = '$anio' and ms.etab = true and ms.id_desempeno = '".$value->id_desempeno."' and indicador = '".$indrs->getId()."'");
                        $statement->execute();
                        $meses = $statement->fetchAll();
                        
                        foreach ($meses as $km => $vm) {
                            $vm = (object) $vm;
                            $etab[$i][$vm->mes]["planificado"] = $vm->planificado;

                            if($vm->real == '' || $vm->real == null){
                                try{
                                    $filtros = ["mes" => $vm->mes];
                                    $fichaTec = $fichaRepository->find($indrs->getId());
                                    $fichaRepository->crearIndicador($fichaTec, "mes", $filtros);
                                    $repFicha = $fichaRepository->calcularIndicador($fichaTec, "mes", $filtros, false, null, 1, 0);

                                    if(!$repFicha)
                                        $errores.= "<br>No se cargo linea: $ci mes: ".$vm->mes;
                                }
                                catch(\Exception $e){
                                    
                                }
                            }else{
                                $etab[$i][$vm->mes]["real"] = $vm->real;
                            }
                        }
                        $i++;
                    }

                    $indicador['indicadores_etab'] = $etab;
                    $indicador['indicadores_relacion'] = $indicators;

                    $resp[] = $indicador;                    
                }
            }
            $resp = ["data" => $resp, "mensaje" => $this->get('translator')->trans('_cargado_correctamente').$errores, "status" => 200];
        }else{
            $resp = ["data" => "false", "mensaje" => $this->get('translator')->trans('_ningun_dato_'), "status" => 404];
        }
        
        $response->setContent(json_encode($resp));

        return $response;
    }

    /**
     * @Route("/matriz/real/guardar", name="matriz_real_guardar", options={"expose"=true})
     */
    public function realGuardar(Request $request){
        $response = new Response();
        $resp = array();
        $em = $this->getDoctrine()->getEntityManager(); 
        $bien = true;
        $anio = $request->request->get('anio');
        $matriz = $request->request->get('matriz');
        $existe = $em->getRepository('IndicadoresBundle:MatrizSeguimiento')->findBy(
            array(
                'anio' => $anio
            )
        );

        foreach ($matriz as $key => $value) {
            $value = (object) $value; 

            if(isset($value->indicadores_etab))
            foreach ($value->indicadores_etab as $ke => $ve) {                                                            
                if(!$this->insertSeguiminetoRealDato($em, $existe, $value, $ke, $ve, $anio, 1))
                    $bien = false;                
            }
            
            if(isset($value->indicadores_relacion))
            foreach ($value->indicadores_relacion as $ke => $ve) {  
                                                                     
                if(!$this->insertSeguiminetoRealDato($em, $existe, $value, $ke, $ve, $anio, 0))
                    $bien = false;                
            }
        }
        if($bien){
            $resp = ["data" => "true" , "mensaje" => $this->get('translator')->trans('_guardar_ok_'), "status" => 200];
        }else{
            $resp = ["data" => "false", "mensaje" => $this->get('translator')->trans('_guardar_no_ok_'), "status" => 404];
        }
        
        $response->setContent(json_encode($resp));

        return $response;
    }

    public function insertSeguiminetoRealDato($em, $existe, $value, $ke, $ve, $anio, $etab){

        $ve = (object) $ve; 
        try{
            if($existe){         

                $seguimiento = $em->getRepository('IndicadoresBundle:MatrizSeguimiento')->findBy(
                    array(
                        'desempeno' => $value->id,
                        'anio' => $anio,
                        'indicador' => $ve->id,
                        'etab' => $etab
                    )
                );
                if($seguimiento)
                    $seguimiento = $em->getRepository('IndicadoresBundle:MatrizSeguimiento')->find($seguimiento[0]->getId());
                
                foreach ($ve as $k1 => $v1) {

                    if($k1 != "id" && $k1 != "nombre" && $k1 != '$$hashKey'){
                        if(isset($v1["real"])){
                            if($v1["real"] == "")
                                $v1["real"] = null;
                            
                            $matrizDato = $em->getRepository('IndicadoresBundle:MatrizSeguimientoDato')->findBy(
                                array(
                                    'matriz' => $seguimiento->getId(),
                                    'mes' => $k1
                                )
                            );

                            if($matrizDato){
                                $matrizDato = $em->getRepository('IndicadoresBundle:MatrizSeguimientoDato')->find($matrizDato[0]->getId());
                            }
                            else{
                                $matrizDato = new MatrizSeguimientoDato();                    
                            }  

                            $matrizDato->setReal($v1["real"]);

                            $em = $this->getDoctrine()->getEntityManager();
                            $em->persist($matrizDato);
                            $em->flush();
                        }
                    }
                }
            }
            return true;
        }catch(\Exception $e){
            var_dump($e->getMessage());
            return false;
        }
    }

    //REPORTE

    /**
     * @Route("/matriz/reporte", name="matriz_reporte", options={"expose"=true})
     */
    public function reporte(Request $request){
        $response = new Response();

        $anio = $request->query->get('anio');

        $em = $this->getDoctrine()->getEntityManager();

        $connection = $em->getConnection();
        $statement = $connection->prepare('SELECT distinct(id_desempeno) FROM matriz_seguimiento WHERE anio = :anio');
        $statement->bindValue('anio', $anio);
        $statement->execute();
        $matriz = $statement->fetchAll();

        if($matriz){
            $resp = array();
            foreach ($matriz as $key => $value) {
                $value = (object) $value;
                $ind = $em->getRepository("IndicadoresBundle:MatrizIndicadoresDesempeno")->find($value->id_desempeno);
                if($ind){
                    
                    $indicador = array();
                
                    $indicador['id'] = $ind->getId();
                    $indicador['nombre'] = $ind->getNombre();

                    $connection = $em->getConnection();
                    $statement = $connection->prepare("SELECT meta FROM matriz_seguimiento WHERE anio = '$anio' and id_desempeno = '".$value->id_desempeno."' ");
                    $statement->execute();
                    $meta = $statement->fetchAll();

                    $indicador['meta'] = $meta[0]["meta"];
                    
                    $indicators = array(); $i=0;
                    foreach($ind->getIndicators() as $indrs){
                        $indicators[$i] = array('id'=>$indrs->getId(), 'nombre'=>$indrs->getNombre());

                        $connection = $em->getConnection();
                        $statement = $connection->prepare("SELECT msd.mes, msd.planificado, msd.real FROM matriz_seguimiento ms 
                            LEFT JOIN matriz_seguimiento_dato msd ON msd.id_matriz = ms.id   
                            WHERE ms.anio = '$anio' and ms.etab = false and ms.id_desempeno = '".$value->id_desempeno."' and indicador = '".$indrs->getId()."'");
                        $statement->execute();
                        $meses = $statement->fetchAll();

                        foreach ($meses as $km => $vm) {
                            $vm = (object) $vm;
                            $indicators[$i][$vm->mes]["planificado"] = $vm->planificado;
                            $indicators[$i][$vm->mes]["real"] = $vm->real;
                        }
                        $i++;
                    }

                    $etab = array(); $i=0;
                    foreach($ind->getMatrizIndicadoresEtab() as $indrs){
                        $etab[$i] = array('id'=>$indrs->getId(), 'nombre'=>$indrs->getNombre());

                        $connection = $em->getConnection();
                        $statement = $connection->prepare("SELECT msd.mes, msd.planificado, msd.real FROM matriz_seguimiento ms 
                            LEFT JOIN matriz_seguimiento_dato msd ON msd.id_matriz = ms.id   
                            WHERE ms.anio = '$anio' and ms.etab = true and ms.id_desempeno = '".$value->id_desempeno."' and indicador = '".$indrs->getId()."'");
                        $statement->execute();
                        $meses = $statement->fetchAll();
                        
                        foreach ($meses as $km => $vm) {
                            $vm = (object) $vm;
                            $etab[$i][$vm->mes]["planificado"] = $vm->planificado;
                            $etab[$i][$vm->mes]["real"] = $vm->real;
                        }
                        $i++;
                    }

                    $indicador['indicadores_etab'] = $etab;
                    $indicador['indicadores_relacion'] = $indicators;

                    $resp[] = $indicador;                    
                }
            }
            $resp = ["data" => $resp, "mensaje" => $this->get('translator')->trans('_cargado_correctamente'), "status" => 200];
        }else{
            $resp = ["data" => "false", "mensaje" => $this->get('translator')->trans('_ningun_dato_'), "status" => 404];
        }
        
        $response->setContent(json_encode($resp));

        return $response;
    }
}

