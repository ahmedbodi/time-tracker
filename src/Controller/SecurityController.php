<?php

namespace App\Controller;

use App\Form\Security\LoginType;
use App\Form\Security\RegisterType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{
	/**
	 * @Route("/login", name="login")
	 */
	public function login(AuthenticationUtils $authenticationUtils)
	{
		if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED'))
		{
			return $this->redirect($this->generateUrl('homepage'));
		}

		// Get one login error if there is one
		$error = $authenticationUtils->getLastAuthenticationError();

		// last username ebtered by the user
		$username = $authenticationUtils->getLastUsername();

		// Get Login Form
		$form = $this->createForm(LoginType::class, [
			'_username' => $username,
		]);

		return $this->render('user/security/login.html.twig', ['form' => $form->createView(), 'error' => $error]);
	}

	/**
	 * @Route("/logout", name="logout")
	 */
	public function logout()
	{
	}

	/**
	 * @Route("/register", name="register")
	 */
	public function register(Request $request, UserPasswordEncoderInterface $encoder)
	{
		$form = $this->createForm(RegisterType::class);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$user = $form->getData();
			$user->setPassword($encoder->encodePassword($user, $user->getPassword()));

			$this->getDoctrine()->getManager()->persist($user);
			$this->getDoctrine()->getManager()->flush();
			$this->addFlash('success', 'Successfully Registered');
			return $this->redirect($this->generateUrl('homepage'));
		}
		return $this->render('user/security/register.html.twig', ['form' => $form->createView()]);
	}
}
