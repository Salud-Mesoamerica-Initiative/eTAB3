<?php

namespace ISECH\IndicadoresBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use ISECH\IndicadoresBundle\Entity\OrigenDatos;
use ISECH\IndicadoresBundle\Entity\ReporteActualizacion;

//use Symfony\Component\Console\Input\ArrayInput;

class OrigenDatosLoadController extends Controller
{
    public function batchActionCrearPivoteIsRelevant(array $normalizedSelectedIds, $allEntitiesSelected)
    {
        return $this->batchActionMergeIsRelevant($normalizedSelectedIds, $allEntitiesSelected, true);
    }

    public function batchActionCrearPivote(ProxyQueryInterface $selectedModelQuery)
    {
        return $this->batchActionMerge($selectedModelQuery, true);
    }

    public function batchActionMergeIsRelevant(array $normalizedSelectedIds, $allEntitiesSelected, $esPivote=false)
    {
        $em = $this->getDoctrine()->getManager();
        $parameterBag = $this->get('request')->request;
        $selecciones = $parameterBag->get('idx');
        // Verificar que los orígenes esten configurados
        foreach ($selecciones as $id_origen) {
            $origenDato = $em->find('IndicadoresBundle:OrigenDatos', $id_origen);
            if ($origenDato->getEsCatalogo())
                return $this->get('translator')->trans('fusion.no_catalogos');
            if ($origenDato->getEsFusionado())
                return $this->get('translator')->trans('fusion.no_fusionados');
            $configurado = $em->getRepository('IndicadoresBundle:OrigenDatos')->estaConfigurado($origenDato);
            if (!$configurado)
                return $origenDato->getNombre() . ': ' . $this->get('translator')->trans('origen_no_configurado');
        }
        if (count($selecciones) > 1)
            return true;
        else
            return $this->get('translator')->trans('fusion.selecione_2_o_mas_elementos');
    }

    public function batchActionMerge(ProxyQueryInterface $selectedModelQuery, $esPivote = false)
    {
        $selecciones = $this->getRequest()->get('idx');
        $em = $this->getDoctrine()->getManager();

        //Obtener la información de los campos de cada origen
        $origen_campos = array();
        foreach ($selecciones as $k => $origen) {
            $origenDato[$k] = $em->find('IndicadoresBundle:OrigenDatos', $origen);
            foreach ($origenDato[$k]->getCampos() as $campo) {
                //La llave para considerar campo comun será el mismo tipo y significado
                $llave = $campo->getSignificado()->getId() . '-' . $campo->getTipoCampo()->getId();
                $origen_campos[$origen][$llave]['id'] = $campo->getId();
                $origen_campos[$origen][$llave]['nombre'] = $campo->getNombre();
                $origen_campos[$origen][$llave]['significado'] = $campo->getSignificado()->getCodigo();
                $origen_campos[$origen][$llave]['idSignificado'] = $campo->getSignificado()->getId();
                $origen_campos[$origen][$llave]['idTipo'] = $campo->getTipoCampo()->getId();
            }
        }

        //Determinar los campos comunes (con igual significado)
        $aux = $origen_campos;
        $campos_comunes = array_shift($aux);
        foreach ($aux as $a) {
            $campos_comunes = array_intersect_key($campos_comunes, $a);
        }

        //Dejar solo los campos que son comunes
        foreach ($origen_campos as $k => $campos) {
            $origen_campos[$k] = array_intersect_key($campos, $campos_comunes);
        }

        // Ordenar y darle la estructura para mostar en la plantilla
        $campos_ord = array();
        foreach ($campos_comunes as $sig_tipo => $c) {
            $campos_ord[$sig_tipo]['value']['nombre'] = $c['significado'];
            $campos_ord[$sig_tipo]['value']['idSignificado'] = $c['idSignificado'];
            $campos_ord[$sig_tipo]['value']['idTipo'] = $c['idTipo'];

            $campos_ord[$sig_tipo]['nombre'] = $c['significado'];
            $campos_ord[$sig_tipo]['value']['origenes_a_Fusionar'] = '';
            foreach ($origen_campos as $origen => $campos) {
                $campos_ord[$sig_tipo]['datos'][$origen] = $campos[$sig_tipo];
            }
            $campos_ord[$sig_tipo]['value']['origenes_a_Fusionar'] = trim($campos_ord[$sig_tipo]['value']['origenes_a_Fusionar'], ',');
            $campos_ord[$sig_tipo]['value'] = json_encode($campos_ord[$sig_tipo]['value']);
        }

        return $this->render('IndicadoresBundle:OrigenDatosAdmin:merge_selection.html.twig', array('origen_dato' => $origenDato,
                    'campos' => $campos_ord,
                    'es_pivote' => $esPivote
        ));
    }

    public function batchActionLoadDataIsRelevant($idx = null)
    {
        $em = $this->getDoctrine()->getManager();
        $parameterBag = $this->get('request')->request;

        if (!$parameterBag->get('all_elements')) {
            $selecciones = ($idx == null) ? $parameterBag->get('idx'): $idx;
            // Verificar que los orígenes esten configurados
            foreach ($selecciones as $id_origen) {
                $origenDato = $em->find('IndicadoresBundle:OrigenDatos', $id_origen);
                $configurado = $em->getRepository('IndicadoresBundle:OrigenDatos')->estaConfigurado($origenDato);
                if (!$configurado)
                    return $origenDato->getNombre() . ': ' . $this->get('translator')->trans('origen_no_configurado');
            }
        } else {
            $origenes = $em->getRepository('IndicadoresBundle:OrigenDatos')->findAll();
            foreach ($origenes as $origen) {
                $configurado = $em->getRepository('IndicadoresBundle:OrigenDatos')->estaConfigurado($origen);
                if (!$configurado)
                    return $origen->getNombre() . ': ' . $this->get('translator')->trans('origen_no_configurado');
            }
        }

        return true;
    }

    public function batchActionLoadData($idx = null)
    {
		$limites = NULL;
        //Mardar a la cola de carga de datos cada origen seleccionado
        $parameterBag = $this->get('request')->request;
        $em = $this->getDoctrine()->getManager();
		
        if (!$parameterBag->get('all_elements'))
            $selecciones = ($idx == null) ? $parameterBag->get('idx'): $idx;
        else
            $selecciones = $em->getRepository('IndicadoresBundle:OrigenDatos')->findAll();
		
        foreach ($selecciones as $origen) {

            if (!$parameterBag->get('all_elements'))
                $origenDato = $em->find('IndicadoresBundle:OrigenDatos', $origen);
            else
                $origenDato = $origen;
            
            // Recuperar el nombre y significado de los campos del origen de datos
            $campos_sig = array();
            $campos = $origenDato->getCampos();
            foreach ($campos as $campo) {
                $campos_sig[$campo->getNombre()] = $campo->getSignificado()->getCodigo();
            }
            
            if($origenDato->getActualizacionIncremental()) {
                $limites = NULL;

                // Obtener todos los campos del origen de datos
                // es necesario para la carga incremental
                $objsCampos = $origenDato->getCampos();
                $ordenTiempo = $this->container->getParameter('tiempo');
                $campos = array();

                foreach($objsCampos as $campo) {
                    $campos[$campo->getSignificado()->getCodigo()] = $campo->getNombre();
                }
                
                $maxCampoSuperior = '';
                $maxCampoInferior = '';
                $sqlCampoSuperior = '';
                $sqlCampoInferior = '';

                foreach ($ordenTiempo as $tiempo) {
                    if(empty($maxCampoSuperior)) {
                        if(array_key_exists($tiempo, $campos)) {
                            $maxCampoSuperior = $tiempo;
                            $sqlCampoSuperior = $campos[$tiempo];
                        }
                    }

                    if(array_key_exists($tiempo, $campos)) {
                        $maxCampoInferior = $tiempo;
                        $sqlCampoInferior = $campos[$tiempo];
                    }
                }
                
                $sql = 'SELECT 
                            MAX(CAST (datos->\''.$maxCampoSuperior.'\' AS INTEGER)) AS val_superior,
                            MAX(CAST (datos->\''.$maxCampoInferior.'\' AS INTEGER)) AS val_inferior
                        FROM fila_origen_dato WHERE id_origen_dato = '.$origen;

                $result = $this->getDoctrine()->getManager()->getConnection()->executeQuery($sql)->fetchAll();

                if(!empty($result)) {
                    $maxValorSuperior = $result[0]['val_superior'];
                    $maxValorInferior = $result[0]['val_inferior'];

                    if($maxCampoSuperior == 'anio' && $maxCampoInferior == 'id_mes' && $maxValorInferior == 12) {
                        //$maxValorSuperior++;
                        $maxValorInferior = 0;
                    }

                    if($maxCampoSuperior == $maxCampoInferior) {
                        $maxCampoInferior = null;
                        $maxValorInferior = null;
                    }

                    $limites = array(
                        'campoSuperior'=>$sqlCampoSuperior,
                        'valorSuperior'=>$maxValorSuperior,
                        'campoInferior'=>$sqlCampoInferior,
                        'valorInferior'=>$maxValorInferior,
                    );
                }
            }
            
            $msg = array('id_origen_dato' => $origen, 
                        'sql' => $origenDato->getSentenciaSql(),
                        'campos_significados' => $campos_sig,
                        'es_incremental'=>$origenDato->getActualizacionIncremental(),
                        'limites' => $limites);

            $ahora = new \DateTime("now");
            foreach ($origenDato->getVariables() as $var) {
                foreach ($var->getIndicadores() as $ind) {
                    $ind->setUltimaLectura($ahora);
                }
            }
            $em->flush();
            $carga_directa = $origenDato->getEsCatalogo();
            // No mandar a la cola de carga los que son catálogos, Se cargarán directamente
            if ($carga_directa) {
                $mess = $em->getRepository('IndicadoresBundle:OrigenDatos')->cargarCatalogo($origenDato);
                if ($mess !== true) {
                    //$this->addFlash('sonata_flash_error', $mess);

                    // Crear el registro para el reporte de actualizacion
                    $reporteActualizacion = new ReporteActualizacion;

                    $reporteActualizacion->setOrigenDatos($origenDato);
                    $reporteActualizacion->setEstatusAct($em->find('IndicadoresBundle:EstatusActualizacion', 2));
                    $reporteActualizacion->setFecha(new \DateTime('now'));
                    $reporteActualizacion->setReporte($mess);

                    $em->persist($reporteActualizacion);
                    $em->flush();
					
                } else {
                    // Crear el registro para el reporte de actualizacion
                    $reporteActualizacion = new ReporteActualizacion;

                    $reporteActualizacion->setOrigenDatos($origenDato);
                    $reporteActualizacion->setEstatusAct($em->find('IndicadoresBundle:EstatusActualizacion', 1));
                    $reporteActualizacion->setFecha(new \DateTime('now'));
                    $reporteActualizacion->setReporte('Actualizacion correcta');

                    $em->persist($reporteActualizacion);
                    $em->flush();
                }
            } else
			{
                $this->get('old_sound_rabbit_mq.cargar_origen_datos_producer')
                        ->publish(serialize($msg));
			}
        }
		return $this->get('translator')->trans('flash_batch_load_data_success');
    }



	/**
     * @Route("/admin/origendatos/load_data_ajax", name="load_data_ajax", options={"expose"=true})
     */
	public function load_data_ajax()
    {	
		try 
		{
            $id = $this->getRequest()->get('id');
			$origen = $this->getDoctrine()->getManager()->find('IndicadoresBundle:OrigenDatos', $id);
			$valid = $this->batchActionLoadDataIsRelevant(array($id));
			if($valid === true)  {

                //probar borrar todo antes de insertar                
                $sql = "SELECT table_name FROM information_schema.tables WHERE table_name LIKE 'tmp_ind%'";
                $em = $this->getDoctrine()->getManager();
                $stmt = $em->getConnection()->prepare($sql);
                $stmt->execute();
                $tablas_temp = $stmt->fetchAll();
                foreach ($tablas_temp as $key => $value) {
                    $dl = "DROP TABLE ".$value["table_name"];
                    $stmtd = $em->getConnection()->prepare($dl);
                    $stmtd->execute();
                }
                
                $sql = "";
                
                if($origen->getActualizacionIncremental() == true || $origen->getActualizacionIncremental() == 1) {
                    $sql = "";
                } else {
                    $sql = "DELETE FROM fila_origen_dato WHERE id_origen_dato='$id';";
                }

                $stmt = $em->getConnection()->prepare($sql);
                $stmt->execute();
				return new Response(json_encode(array("save"=>true,"message"=>$this->batchActionLoadData(array($id)))));
            }
			else 
			{
				return new Response(json_encode(array("save"=>false,"message"=>$this->get('translator')->trans('origen_no_configurado'))));
			}
        } catch (\Doctrine\DBAL\DBALException $e) 
		{
            return new Response(json_encode(array("save"=>false,"message"=>$e->getMessage())));
        }        
    }
}
