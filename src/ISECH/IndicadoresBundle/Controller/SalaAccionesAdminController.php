<?php

namespace ISECH\IndicadoresBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use ISECH\IndicadoresBundle\Entity\GrupoIndicadores;
use ISECH\IndicadoresBundle\Entity\SalaAcciones;

class SalaAccionesAdminController extends Controller
{
    /**
     * @Route("/sala/{id}/acciones/list", name="sala_acciones_custom_list", options={"expose"=true})
     */
    public function customListAction(GrupoIndicadores $sala)
    {        
        $em = $this->getDoctrine()->getManager();
        $usuario = $this->getUser();
      
        $acciones = $em->getRepository("IndicadoresBundle:SalaAcciones")
                        ->findBy(array('sala'=>$sala, 
                            'usuario'=>$usuario), 
                             array('fecha'=> 'ASC'));
        
        $resp = '<br/><br/>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr class="sonata-ba-list-field-header">
                            <TH>Fecha</TH>
                            <TH>Usuario</TH>
                            <TH>Acciones</TH>
                            <TH>Observaciones</TH>
                            <TH>Responsables</TH>
                            <TH></TH>
                        </tr>
                    </thead>
                    <tbody>';                
        foreach ($acciones as $acc){
            $resp .=  '<TR id="accion-'.$acc->getId().'">'.
                            '<TD data-sort="'.$acc->getFecha()->format('YmdHms').'">'.$acc->getFecha()->format('d/m/Y H:m:s').'</TD>'.
                            '<TD>'.$acc->getUsuario().'</TD>'.
                            '<TD>'.$acc->getAcciones(). '</TD>'.
                            '<TD>'.$acc->getObservaciones() . '</TD>' . 
                            '<TD>'.$acc->getResponsables() . '</TD>'. 
                            '<TD><a class="btn btn-danger" onclick="borrarAccion('.$acc->getId().')"><i class="glyphicon glyphicon-trash" ></i></a></TD>'.                            
                    '</TR>';
        }
        $resp .= '</tbody>
                </table>';
        return new Response($resp);
    }
    
    /**
     * @Route("/sala/{id}/accion/guardar", name="accion_guardar", options={"expose"=true})
    */
    public function guardarAccion(GrupoIndicadores $sala) {
        $em = $this->getDoctrine()->getManager();
        $req = $this->getRequest();        
        $usuario = $this->getUser();
        $resp = array();

        $datos = json_decode($req->get('datos'));
        
        $salaAcciones = new SalaAcciones();
        $salaAcciones->setSala($sala);
        $salaAcciones->setAcciones($datos->acciones);
        $salaAcciones->setObservaciones($datos->observaciones);
        $salaAcciones->setResponsables($datos->responsables);
        $salaAcciones->setFecha(new \DateTime());
        $salaAcciones->setUsuario($usuario);
        
        $em->getConnection()->beginTransaction();
        try {
            $em->persist($salaAcciones);
            $em->flush();
            $resp['estado'] = 'ok';
            $em->getConnection()->commit();

            // guardar el log 
            $util = new \ISECH\IndicadoresBundle\Util\Util();
            $util->logUsuario($em, $req, $usuario, "Accion sala", "SalaAcciones");
        } catch (Exception $e) {
            $em->getConnection()->rollback();
            $em->close();
            $resp['estado'] = 'error';
            throw $e;
        }
        
        return new Response(json_encode($resp));
    }
}
