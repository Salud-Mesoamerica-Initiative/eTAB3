<?php

namespace ISECH\IndicadoresBundle\Listener;

use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Router;
use Symfony\Component\HttpFoundation\RedirectResponse;


class LogUsuario implements LogoutSuccessHandlerInterface
{
  private $router;
  /** @var \Symfony\Component\Security\Core\SecurityContext */
  private $securityContext;
  
  /** @var \Doctrine\ORM\EntityManager */
  private $em;
  
  /**
   * Constructor
   * 
   * @param SecurityContext $securityContext
   * @param Doctrine        $doctrine
   */
  public function __construct(SecurityContext $securityContext, Doctrine $doctrine, Router $router)
  {
    $this->securityContext = $securityContext;
    $this->em              = $doctrine->getEntityManager();
    $this->router          = $router;
  }
  
  /**
   * checar el inicio de session.
   * 
   * @param InteractiveLoginEvent $event
   */
  public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
  {      
    $request = $event->getRequest();
    $user    = $event->getAuthenticationToken()->getUser();    
    
    // guardar el log 
    $util = new \ISECH\IndicadoresBundle\Util\Util();
    $util->logUsuario($this->em, $request, $user, "Login", "User");
  }
  /**
   * checar el cierre de session.
   * 
   * @param Request $request
   */
  public function onLogoutSuccess(Request $request) {
     $user = $this->securityContext->getToken()->getUser();

     // guardar el log 
    $util = new \ISECH\IndicadoresBundle\Util\Util();
    $util->logUsuario($this->em, $request, $user, "Logout", "User");
   
    $response = new RedirectResponse($this->router->generate("_inicio"));
    return $response;
  }
}
