<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ISECH\IndicadoresBundle\Controller;

use FOS\UserBundle\Controller\SecurityController;

use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AdminSecurityController extends SecurityController
{
    /**
     * {@inheritdoc}
     */
    public function loginAction()
    {
        $request = $this->container->get('request');
        /* @var $request \Symfony\Component\HttpFoundation\Request */
        $session = $request->getSession();
        /* @var $session \Symfony\Component\HttpFoundation\Session */

        // get the error if any (works with forward and redirect -- see below)
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) 
		{
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } 
		else if ($session  !== null && $session->has(SecurityContext::AUTHENTICATION_ERROR)) 
		{
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        } 
		else 
		{
            $error = '';
        }

        if ($error) 
		{
            // TODO: this is a potential security risk (see http://trac.symfony-project.org/ticket/9523)
            $error = $error->getMessage();
        }
		
        // last username entered by the user
        $lastUsername = (null === $session) ? '' : $session->get(SecurityContext::LAST_USERNAME);

        $csrfToken = $this->container->has('form.csrf_provider')
            ? $this->container->get('form.csrf_provider')->generateCsrfToken('authenticate')
            : null;

        if ($this->container->get('security.context')->isGranted('ROLE_ADMIN')) 
		{
            $refererUri = $request->server->get('HTTP_REFERER');
			
			if($refererUri!="")
				$refererUri = $request->getUri();
			else
				$refererUri = $this->container->get('router')->generate('_inicio');
				
            return new RedirectResponse($refererUri);
        }

			
        return $this->container->get('templating')->renderResponse('IndicadoresBundle:Security:login.html.'.$this->container->getParameter('fos_user.template.engine'), array(
            'last_username' => $lastUsername,
            'error'         => $error,
            'csrf_token'    => $csrfToken,
            'base_template' => $this->container->get('sonata.admin.pool')->getTemplate('layout'),
            'admin_pool'    => $this->container->get('sonata.admin.pool')
        ));
    }
}
