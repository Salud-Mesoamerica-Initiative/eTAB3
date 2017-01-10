<?php

namespace ISECH\IndicadoresBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class CluesController extends Controller
{
    /**
     * @Route("/admin/clues/filtro_inicial", name="filtro_inicial", options={"expose"=true})
     */
	public function filtro_inicial()
    {
		$em = $this->getDoctrine()->getManager();
		
		$sql = 'SELECT id, descripcion as jurisdiccion
		FROM ctl_jurisdicciones';
	
		$result["jurisdiccion"]=$em->getConnection()->executeQuery($sql)->fetchAll();
		
		$sql = 'SELECT distinct( tipo_unidad )
		FROM ctl_clues';
		
		$result["tipo"]=$em->getConnection()->executeQuery($sql)->fetchAll();
		return new Response(json_encode($result));
	}
	
	/**
     * @Route("/admin/clues/municipio", name="municipio", options={"expose"=true})
     */
	public function municipio()
    {
		$id = $this->getRequest()->get('id');
		
		$em = $this->getDoctrine()->getManager();
				
		$sql = "SELECT distinct m.id, m.descripcion
		FROM ctl_clues c
		LEFT JOIN ctl_municipios m on m.id=c.municipio
		WHERE c.jurisdiccion='$id'";
		
		$result=$em->getConnection()->executeQuery($sql)->fetchAll();
		return new Response(json_encode($result));
	}
	
	/**
     * @Route("/admin/clues/localidad", name="localidad", options={"expose"=true})
     */
	public function localidad()
    {
		$id = $this->getRequest()->get('id');
		$em = $this->getDoctrine()->getManager();
		
		$sql = "SELECT distinct l.id, l.descripcion
		FROM ctl_clues c
		LEFT JOIN ctl_localidades l on l.id=c.localidad
		WHERE c.municipio='$id'";
		
		$result=$em->getConnection()->executeQuery($sql)->fetchAll();
		return new Response(json_encode($result));
	}
	
	/**
     * @Route("/admin/clues/clues", name="clues", options={"expose"=true})
     */
	public function clues()
    {
		$j = $this->getRequest()->get('j');
		$m = $this->getRequest()->get('m');
		$l = $this->getRequest()->get('l');
		$t = $this->getRequest()->get('t');
		
		$q = strtoupper($this->getRequest()->get('q'));
		
		if($j!="")$j=" AND c.jurisdiccion='$j'";
		if($m!="")$m=" AND c.municipio='$m'";
		if($l!="")$l=" AND c.localidad='$l'";
		if($t!="")$t=" AND c.tipo_unidad='$t'";
		
		if($q!="")$q=" AND UPPER(c.descripcion) like '%$q%'";
		
		$em = $this->getDoctrine()->getManager();
		
		$user = $this->container->get('security.context')->getToken()->getUser()->getId();
		
		$sql = "SELECT distinct c.id, c.descripcion, c.tipo_unidad, j.descripcion as jurisdiccion, m.descripcion as municipio, l.descripcion as localidad
		FROM ctl_clues c
		LEFT JOIN ctl_jurisdicciones j on j.id=c.jurisdiccion
		LEFT JOIN ctl_municipios m on m.id=c.municipio
		LEFT JOIN ctl_localidades l on l.id=c.localidad
		WHERE 1=1 $j $m $l $t $q";
		
		$result["datos"]=$em->getConnection()->executeQuery($sql)->fetchAll();
		
		$sql = "SELECT ctlclues_id
		FROM user_ctlclues		
		WHERE user_id='$user'";
		$clues=$em->getConnection()->executeQuery($sql)->fetchAll();
		$ctl=array();
		foreach($clues as $c)
			$ctl[]=$c["ctlclues_id"];
			
		$string="";
		if($ctl)$string=" ".implode(",",$ctl);
		$result["check"]=$string;		
		
		return new Response(json_encode($result));
	}
}
