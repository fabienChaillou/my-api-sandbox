<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
//    /**
//     * @Route("/register", name="security_register")
//     */
//    public function register(Request $request, UserPasswordEncoderInterface $encoder)
//    {
//        $em = $this->getDoctrine()->getManager();
//
//        $username = $request->request->get('username');
//        $password = $request->request->get('password');
//        $roles = $request->request->get('roles');
//
//        if (!$roles) {
//            $roles = json_encode([]);
//        }
//
//        $user = new User($username);
//        $user->setPassword($encoder->encodePassword($user, $password));
//        $user->setRoles($roles);
//        $em->persist($user);
//        $em->flush();
//
//        return new Response(sprintf('User %s successfully created', $user->getUsername()));
//    }

    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
         if ($this->getUser()) {
             return $this->redirectToRoute('swagger_ui');
         }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
    }
}
