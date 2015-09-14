<?php
namespace UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use UserBundle\Form\Type\RegistrationType;
use UserBundle\Form\Model\Registration;

class AccountController extends Controller
{
	public function registerAction()
	{
		$registration = new Registration();
		$form = $this->createForm(new RegistrationType(), $registration, array(
				'action' => $this->generateUrl('account_create'),
		));

		return $this->render(
				'UserBundle:Account:register.html.twig',
				array('form' => $form->createView())
		);
	}
	
	public function createAction(Request $request)
	{
		$form = $this->createForm(new RegistrationType(), new Registration());
	
		$form->handleRequest($request);
	
		if ($form->isValid()) {
			$registration = $form->getData();
			
			$user = $registration->getUser();
			$user->setRegistrationDate(new \DateTime());
			$user->setLastLogin(new \DateTime());
			
			$plainPassword = $user->getPassword();
			$encoder = $this->container->get('security.password_encoder');
			$encoded = $encoder->encodePassword($user, $plainPassword);
			$user->setPassword($encoded);
			
			$em = $this->getDoctrine()->getManager();
			$em->persist($user);
			$em->flush();
	
			return $this->redirectToRoute("app_index");
		}
	
		return $this->render(
				'UserBundle:Account:register.html.twig',
				array('form' => $form->createView())
		);
	}
}

