<?php

namespace ISECH\IndicadoresBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
//use Symfony\Component\Console\Input\ArrayInput;

class TableroController extends Controller
{
	public function tableroAction()
    {
		$array=$this->ParametrosAction();
		return $this->render('IndicadoresBundle:FichaTecnicaAdmin:tablero.html.twig', $array );
	}
	public function tableroPublicoAction()
    {
		$array=$this->ParametrosAction();
		return $this->render('IndicadoresBundle:FichaTecnicaAdmin:tablero_public.html.twig', $array );
	}
    public function ParametrosAction()
    {
        $em = $this->getDoctrine()->getManager();
        $clasificacionUso = $em->getRepository("IndicadoresBundle:ClasificacionUso")->findAll();

        //Luego agregar un método para obtener la clasificacion de uso por defecto del usuario
        $usuario = $this->getUser();
        if ($usuario->getClasificacionUso()) {
            $clasificacionUsoPorDefecto = $usuario->getClasificacionUso();
        } else {
            $clasificacionUsoPorDefecto = $clasificacionUso[0];
        }
        $categorias = $em->getRepository("IndicadoresBundle:ClasificacionTecnica")->findBy(array('clasificacionUso' => $clasificacionUsoPorDefecto));

        //Salas por usuario
        $usuarioSalas = array();
        if (($usuario->hasRole('ROLE_SUPER_ADMIN'))){
            foreach ($em->createQuery('SELECT g FROM IndicadoresBundle:GrupoIndicadores g ORDER BY g.nombre ASC')->getResult() as $sala) {
                $usuarioSalas[$sala->getId()] = $sala;
            } 
        }else{
           foreach ($usuario->getGruposIndicadores() as $sala) {
                $usuarioSalas[$sala->getGrupoIndicadores()->getId()] = $sala->getGrupoIndicadores();
            } 
        }
		
		$salasXusuario=array();
		$i=0;
		foreach ($usuarioSalas as $sala) {
            $salasXusuario[$i]['datos_sala'] = $sala;
            $salasXusuario[$i]['indicadores_sala'] = $em->getRepository('IndicadoresBundle:GrupoIndicadores')
                    ->getIndicadoresSala($sala);
            $i++;
        }
        //Salas asignadas al grupo al que pertenece el usuario
		$salasXgrupoTemp=array();
        foreach ($usuario->getGroups() as $grp){
            foreach ($grp->getSalas() as $sala){
                $usuarioSalas[$sala->getId()] = $sala;
				$salasXgrupoTemp[]=$sala;
            }
        }
        $salasXgrupo=array();
		$i=0;
		foreach ($salasXgrupo as $sala) {
            $salasXgrupo[$i]['datos_sala'] = $sala;
            $salasXgrupo[$i]['indicadores_sala'] = $em->getRepository('IndicadoresBundle:GrupoIndicadores')
                    ->getIndicadoresSala($sala);
            $i++;
        }
		
        $i = 0;
        $salas = array();
        foreach ($usuarioSalas as $sala) {
            $salas[$i]['datos_sala'] = $sala;
            $salas[$i]['indicadores_sala'] = $em->getRepository('IndicadoresBundle:GrupoIndicadores')
                    ->getIndicadoresSala($sala);
            $i++;
        }

        //Indicadores asignados por usuario
        $usuarioIndicadores = ($usuario->hasRole('ROLE_SUPER_ADMIN')) ?
                //$em->getRepository("IndicadoresBundle:FichaTecnica")->findAll() :
                $this->get('doctrine')->getManager()->createQuery('SELECT c FROM IndicadoresBundle:FichaTecnica c ORDER BY c.nombre ASC')->getResult() :
                $usuario->getIndicadores();
        //Indicadores asignadas al grupo al que pertenece el usuario
        $indicadoresPorGrupo = array();
        foreach ($usuario->getGroups() as $grp){            
            foreach ($grp->getIndicadores() as $indicadores_grupo){
                $indicadoresPorGrupo[] = $indicadores_grupo;
            }
        }
        
        $indicadores_por_usuario = array();
        $indicadores_clasificados = array();
        foreach ($usuarioIndicadores as $ind) {
            $indicadores_por_usuario[] = $ind->getId();
        }
        
        foreach ($indicadoresPorGrupo as $ind){
            $indicadores_por_usuario[] = $ind->getId();
        }
        
        $categorias_indicador = array();
        foreach ($categorias as $cat) {
            $categorias_indicador[$cat->getId()]['cat'] = $cat;
            $categorias_indicador[$cat->getId()]['indicadores'] = array();
            $indicadores_por_categoria = $cat->getIndicadores();
            foreach ($indicadores_por_categoria as $ind) {
                if (in_array($ind->getId(), $indicadores_por_usuario)) {
                    $categorias_indicador[$cat->getId()]['indicadores'][] = $ind;
                    $indicadores_clasificados[] = $ind->getId();
                }
            }
        }

        $indicadores_no_clasificados = array();
        foreach ($usuarioIndicadores as $ind) {
            if (!in_array($ind->getId(), $indicadores_clasificados)) {
                $indicadores_no_clasificados[] = $ind;
            }
        }
        foreach ($indicadoresPorGrupo as $ind) {
            if (!in_array($ind->getId(), $indicadores_clasificados)) {
                $indicadores_no_clasificados[] = $ind;
            }
        }
		
		return array(
                    'categorias' => $categorias_indicador,
                    'clasificacionUso' => $clasificacionUso,
                    'salas' => $salas,
					'salasXusuario' => $salasXusuario,
					'salasXgrupo' => $salasXgrupo,
					'admin_pool' => $this->get('sonata.admin.pool'),
                    'indicadores_no_clasificados' => $indicadores_no_clasificados);		        
    }    
}
