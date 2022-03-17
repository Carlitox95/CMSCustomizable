<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\Usuario;
use App\Form\UsuarioType;


class SecurityController extends AbstractController {

    private $passwordHasher;

    //Funcion para hashear la contraseña
    public function __construct(UserPasswordHasherInterface $passwordHasher) {
     $this->passwordHasher = $passwordHasher;
    }

    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response {
        
        //Si ya estoy logueado, voy a la home
        if ($this->getUser()) {
         return $this->redirectToRoute('index');
        }

        //Obtengo los errores
        $error = $authenticationUtils->getLastAuthenticationError();
        //Obtengo el ultimo usuario logueado
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout() {
     //throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');

     //Obtengo los errores
     $error = $authenticationUtils->getLastAuthenticationError();
     //Obtengo el ultimo usuario logueado
     $lastUsername = $authenticationUtils->getLastUsername();

     return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/registrarme", name="app_registro")
     */
    public function registro(Request $request): Response {
     //Definimos el usuario
     $user = new Usuario();
     //Obtenemos el formulario
     $form = $this->createForm(UsuarioType::class, $user);
     //Obtengo el request
     $form->handleRequest($request);

        //Si el formulario se disparo y es valido
        if ($form->isSubmitted() && $form->isValid()) {
         $user->setRoles(['ROLE_USER']);
         $user->setPassword($this->passwordHasher->hashPassword($user, $user->getPassword()));
         $entityManager = $this->getDoctrine()->getManager();
         $entityManager->persist($user);
         $entityManager->flush();
         return $this->redirectToRoute('app_login');
        }

        //Retorno la vista
        return $this->render('security/registro.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
    





}
