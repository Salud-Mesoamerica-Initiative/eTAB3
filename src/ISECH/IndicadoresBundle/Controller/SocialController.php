<?php

namespace ISECH\IndicadoresBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Symfony\Component\HttpFoundation\Response;
use ISECH\IndicadoresBundle\Entity\ComentariosSala;
use ISECH\IndicadoresBundle\Entity\Social;
use ISECH\IndicadoresBundle\Entity\GrupoIndicadores;
use ISECH\IndicadoresBundle\Entity\User;
use ISECH\IndicadoresBundle\Entity\UsuarioGrupoIndicadores;
use Symfony\Component\HttpFoundation\Session\Session;
use ISECH\IndicadoresBundle\Entity\SalaAcciones;

class SocialController extends Controller {

    /**
     * @Route("/sala/get_comentarios/{idSala}/", name="sala_get_comentarios", options={"expose"=true})
     */
    public function getComentariosSala($idSala) {
        $session = new Session();
        $em = $this->getDoctrine()->getManager();
        $req = $this->getRequest();

                                    
        if ($req->get('vez') == 1 or $session->get('ultima_lectura_comentarios_sala') == ''){
            $comentarios = $em->createQuery('SELECT c, u FROM IndicadoresBundle:ComentariosSala c 
                LEFT JOIN c.usuario u 
                WHERE c.sala = :sala AND c.fecha > :fecha ORDER  BY c.fecha ASC')
            ->setParameter('sala', $idSala)->setParameter('fecha', '1970-01-01')->getResult();                        
        }
        else{            
            $comentarios = $em->createQuery('SELECT c, u FROM IndicadoresBundle:ComentariosSala c 
                LEFT JOIN c.usuario u  
                WHERE c.sala = :sala AND c.fecha > :fecha ORDER  BY c.fecha ASC')
            ->setParameter('sala', $idSala)->setParameter('fecha', $session->get('ultima_lectura_comentarios_sala'))->getResult();                        
        }

        
        $session->set('ultima_lectura_comentarios_sala', new \DateTime("now"));
        
        $ret = '';
        foreach($comentarios as $comentario){
            $u = $comentario->getUsuario();
            $photo  = $u->getPhoto() != "" ? $u->getPhoto() : $this->container->get('templating.helper.assets')->getUrl('bundles/indicadores/images/user.png');
            $name = $u->getFirstname().$u->getLastname() == "" ? $u->getUsername() : $u->getFirstname().' '.$u->getLastname();
            $ret .= '<li>
                        <div class="comment-main-level"> 
                            <!-- Avatar -->
                            <div class="comment-avatar"><img src="'.$photo.'"></div>                                               
                            <!-- Contenedor del Comentario -->
                            <div class="comment-box">
                                <div class="comment-head">
                                    <h6 class="comment-name"><a href="#">'.$name.'</a></h6>
                                    
                                    <i class="fa fa-calendar"><span>'.$comentario->getFecha()->format('d-M-Y H:i:s').'</span></i>
                                    
                                </div>
                                <div class="comment-content">
                                    '.$comentario->getComentario().'
                                </div>
                            </div>
                        </div>
                        
                    </li>';
        }

                

        
        $response = new Response($ret);

        return $response;
    }
    
    /**
     * @Route("/sala/{id}/set_comentario", name="sala_set_comentario", options={"expose"=true})
     */
    public function setComentarioSala(GrupoIndicadores $sala) {
        $session = new Session();
        $em = $this->getDoctrine()->getManager();
        $req = $this->getRequest();
        $comentario = new ComentariosSala();
        $ahora = new \DateTime("now");
        $ret = ""; $msg = "";
        if($req->get('chat-mensaje')!="")
        {
            $comentario->setComentario($req->get('chat-mensaje'));
            $comentario->setUsuario($this->getUser());
            $comentario->setFecha($ahora);
            $comentario->setSala($sala);
            
            $em->persist($comentario);
            $em->flush();

            $session->set('ultima_lectura_comentarios_sala', new \DateTime("now"));
            
            $ret = '<li>
                        <div class="comment-main-level"> 
                            <!-- Avatar -->
                            <div class="comment-avatar"></div>                                               
                            <!-- Contenedor del Comentario -->
                            <div class="comment-box">
                                <div class="comment-head">
                                    <h6 class="comment-name"><a href="#">'.$comentario->getUsuario().'</a></h6>
                                    
                                    <i class="fa fa-calendar"><span>'.$comentario->getFecha()->format('d-M-Y H:i:s').'</span></i>
                                    
                                </div>
                                <div class="comment-content">
                                    '.$comentario->getComentario().'
                                </div>
                            </div>
                        </div>
                        
                    </li>';
        }
        if($req->get("correo") == "1")
        {
            $dato = array(array
            (
                'token' =>md5(time()), 
                'sala' => $sala->getId()
            ));

            $qb = $em->createQueryBuilder();
            $qb->select('u');
            $qb->from('IndicadoresBundle:User', 'u');
            $qb->where($qb->expr()->in('u.id', $req->get('usuario_sala')));
            $users = $qb->getQuery()->getResult();

            foreach($users as $usuario)
            {       
                if($usuario->isEnabled())
                { 
                    $name = $usuario->getFirstname().$usuario->getLastname() == "" ? $usuario->getUsername() : $usuario->getFirstname().' '.$usuario->getLastname();                      
                    $array = array(array
                    (
                        'username' => $usuario->getUsername(), 
                        'email' => $usuario->getEmail(), 
                        'nombre' => $usuario->getFirstname(), 
                        'apellido' => $usuario->getLastname()
                    )); 
                    $documento1 = $this->container->getParameter('kernel.root_dir').'/../web/bundles/indicadores/images/logo_salud.png';
                    $message = \Swift_Message::newInstance()
                        ->attach(\Swift_Attachment::fromPath($documento1))
                        ->attach(\Swift_Attachment::fromPath($documento2))
                        ->setSubject('Sala eTAB')
                        ->setFrom('eTAB@SM2015.com.mx')
                        ->setTo($usuario->getEmail()) 
                        ->setBody($this->renderView('IndicadoresBundle:Page:sala.html.twig', array('dato' => $dato,'array' => $array)),"text/html");
                    $this->get('mailer')->send($message);
                    $msg.="se envio correo a: ".$name." (".$usuario->getEmail().")\n\n";
                }
            }            
        }

        if($req->get("usuarios_sin")!="")
        {
            $token  = md5(time());
            $dato = array(array
            (
                'token' =>$token, 
                'sala' => $sala->getId()
            ));
            
            $usuario = explode(",", $req->get("usuarios_sin"));

            for($i=0; $i<count($usuario);$i++)
            {       
                if(stripos($usuario[$i],"@"))
                { 
                    $array = array(array
                    (
                        'username' => "Temporal", 
                        'email' => $usuario[$i], 
                        'nombre' => "", 
                        'apellido' => ""
                    )); 
                    $documento1 = $this->container->getParameter('kernel.root_dir').'/../web/bundles/indicadores/images/logo_salud.png';
                    $message = \Swift_Message::newInstance()
                        ->attach(\Swift_Attachment::fromPath($documento1))
                        ->setSubject('Sala eTAB')
                        ->setFrom('eTAB@SM2015.com.mx')
                        ->setTo(trim($usuario[$i])) 
                        ->setBody($this->renderView('IndicadoresBundle:Page:sala.html.twig', array('dato' => $dato,'array' => $array)),"text/html");
                    $this->get('mailer')->send($message);
                    $msg.="se envio correo a: ".$usuario[$i]."\n\n";
                }
            }
            if($msg!="")
            {
                $social = new Social();
                $ahora = new \DateTime("now");
                
                $social->setToken($token);
                $social->setCreado($ahora);
                $social->setSala($sala);
                
                $em->persist($social);
                $em->flush();
            }    
        }

        $response = new Response(json_encode(array("mensaje" => $ret,"correo" => $msg)));

        return $response;
    }
    
    /**
     * @Route("/sala/get_usuarios/{idSala}/", name="sala_get_usuarios", options={"expose"=true})
     */
    public function getUsuariosSala($idSala) {
        $em = $this->getDoctrine()->getManager();
        
        $usuarios_asignados = $em->getRepository('IndicadoresBundle:UsuarioGrupoIndicadores')
                        ->findBy(array('grupoIndicadores' => $idSala));
        $usuarios_asignados_por_usuario_actual = $em->getRepository('IndicadoresBundle:UsuarioGrupoIndicadores')
                        ->findBy(array('usuarioAsigno'=>$this->getUser(),
                                        'grupoIndicadores' => $idSala));
        $usuarios_sala_por_usuario_actual = array(); 
        foreach ($usuarios_asignados_por_usuario_actual as $ua){
            $usuarios_sala_por_usuario_actual[] = $ua->getUsuario()->getId();
        }
        
        $usuarios_sala = array(); 
        foreach ($usuarios_asignados as $ua){
            $usuarios_sala[] = $ua->getUsuario()->getId();
        }
        
        $usuarios = $em->getRepository('IndicadoresBundle:User')
                        ->findBy(array(), array('username'=>'ASC'));
        
        $ret = '<SELECT id="usuarios_sala_" multiple="" class="form-control">';
        foreach ($usuarios as $u){
            if ($u->getId() != $this->getUser()->getId()){
                $name = $u->getFirstname().$u->getLastname() == "" ? $u->getUsername() : $u->getFirstname().' '.$u->getLastname();
                $selected = (in_array($u->getId(), $usuarios_sala)) ? 'selected="selected"': '';
                $disable = ($selected=='selected="selected"' and !in_array($u->getId(), $usuarios_sala_por_usuario_actual)) ? 'disabled="disabled"': '';
                $ret .= '<OPTION VALUE="'.$u->getId().'" '.$selected.' '.$disable.'>'.$name.'</OPTION>';
            }
        }
        $ret .= '</SELECT>';
        $response = new Response($ret);

        return $response;
    }
    
    /**
     * @Route("/sala/{id}/set_usuario/{id_usuario}/{accion}", name="sala_set_usuario", options={"expose"=true})
     */
    public function setUsuarioSala(GrupoIndicadores $sala, $id_usuario, $accion) {
        $em = $this->getDoctrine()->getManager();
        if ($accion == 'borrar'){
            $usuarios_asignados = $em->getRepository('IndicadoresBundle:UsuarioGrupoIndicadores')
                        ->findOneBy(array('usuario'=>$id_usuario,
                                        'grupoIndicadores' => $sala));
            $em->remove($usuarios_asignados);
        } else{
            $usuario = $em->find('IndicadoresBundle:User', $id_usuario);
            $usuarioSala = new UsuarioGrupoIndicadores();
            $usuarioSala->setGrupoIndicadores($sala);
            $usuarioSala->setUsuario($usuario);
            $usuarioSala->setUsuarioAsigno($this->getUser());
            $em->persist($usuarioSala);
        }
        
        $em->flush();
        
        return new Response();
    }

    /**
    * @Route("/publico/sala/{sala}/{token}", name="sala_publico", options={"expose"=true})
    */
    public function tokenAction($sala,$token)
    {
        $em = $this->getDoctrine()->getManager();
        $sa = $em->getRepository('IndicadoresBundle:Social')->getRuta($sala,$token);

        if (!$sa) 
            return $this->render('IndicadoresBundle:Page:error.html.twig', array(
                'error' => "No existe la sala: $sala",
                'bien' => "")); 
        else if($sa=="Error")   
            return $this->render('IndicadoresBundle:Page:error.html.twig', array(
                'error' => "El tiempo de este boletin ha expirado",
                'bien' => ""));
        else
        {   
            $sa['indicadores'] = $em->getRepository('IndicadoresBundle:GrupoIndicadores')
                    ->getIndicadoresSala($em->getRepository('IndicadoresBundle:GrupoIndicadores')->find($sa[0]['sala']));
            $indicadores = $em->getRepository("IndicadoresBundle:FichaTecnica")->getIndicadoresPublicos();        
            return $this->render('IndicadoresBundle:FichaTecnicaAdmin:tablero_public.html.twig', array(
                    'indicadores' => $indicadores,
                    'sala' => $sa
                ));
        }
        
    }

    /**
     * @Route("/sala/{id}/accion/borrar", name="accion_borrar", options={"expose"=true})
    */
    public function borrarAccion(SalaAcciones $id) {
        if (!$id) {
             return new Response("No existe el elemento");
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($id);
        $em->flush();
    
        return new Response("Elemento borrado");
    }
}
