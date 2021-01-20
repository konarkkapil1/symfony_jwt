<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


/**
 * Class AuthController
 * @Route("/auth", name="auth.")
 */
class AuthController extends AbstractController
{
    /**
     * @Route("/auth", name="auth")
     */
    public function index(): Response
    {
        return $this->render('auth/index.html.twig', [
            'controller_name' => 'AuthController',
        ]);
    }

    /**
     * @Route("/register", name="register", methods={"POST"})
     */
    public function register(Request $request, UserPasswordEncoderInterface $userPasswordEncoder){
        $password = $request->get('password');
        $email = $request->get('email');
        if(empty($password) || $email){
            return $this->json([
                "error" => "email and password are required"
            ]);
        }
        $user = new User();
        $user->setPassword($userPasswordEncoder->encodePassword($user, $password));
        $user->setEmail($email);

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        return $this->json([
            "user" => $user->getEmail()
        ]);
    }
}
