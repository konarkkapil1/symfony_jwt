<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Firebase\JWT\JWT;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
        if(empty($password) || empty($email)){
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

    /**
     * @Route("/login", name="login", methods={"POST"})
     */
    public function login(Request $request, UserRepository $userRepository, UserPasswordEncoderInterface $userPasswordEncoder){
        $email = $request->get("email");
        $password = $request->get("password");

        if(empty($email) || empty($password)){
            return $this->json([
                "error" => "email and password are required"
            ]);
        }

        $user = $userRepository->findOneBy([
            "email" => $email
        ]);

        if(!$user || !$userPasswordEncoder->isPasswordValid($user, $password)){
            return new JsonResponse([
                "error" => "Invalid credentials"
            ], Response::HTTP_UNAUTHORIZED);
        }

        $payload = [
            "user" => $user->getUsername(),
            "exp" => (new \DateTime())->modify("+5 minutes")->getTimestamp()
        ];

        $jwt = JWT::encode($payload, $this->getParameter('jwt_secret'), 'HS256');

        $newUser = [
            "id" => $user->getId(),
            "email" => $user->getEmail(),
        ];
        return $this->json([
            'message' => "Success",
            "user" => $newUser,
            'token' => sprintf('Bearer %s', $jwt)
        ]);

    }
}
