<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException; 
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use App\Form\UsuarioType;
use App\Form\UsuarioRolesType;
use App\Form\UsuarioCambiarPasswordType;
use App\Form\UsuarioCambiarMailType;
use App\Entity\Usuario;

class UsuarioController extends AbstractController
{ 

    /**    
    * @Route("/perfil", name="app_usuario")    
    */
    public function index(): Response {

        //Retorno la vista unicamente
        return $this->render('usuario/index.html.twig', 
            [
             'controller_name' => 'UsuarioController',
            ]
        );
    }

    /**
     * @Route("/usuarios/editarMail/", name="app_usuario_editar_mail", methods={"GET","HEAD","POST"})
     */
    //Funcion para editar los datos de una persona
    public function editarMailUsuario(Request $request): Response {
     //Obtengo el EntityManager
     $em = $this ->getDoctrine()->getManager();     
     //Obtengo el usuario logueado
     $usuarioLogueado=$this->get('security.token_storage')->getToken()->getUser();
     //Obtengo mi usuario de la BD
     $usuario=$em->getRepository(Usuario::class)->find($usuarioLogueado->getId());       
     //Defino el Formulario
     $form = $this->createForm(UsuarioCambiarMailType::class, $usuario);  
     //Si se envia el formulario , existe un request
     $form->handleRequest($request);
       
      //Si se disparo el formulario y es valido
      if ($form->isSubmitted() && $form->isValid()) {
       //Obtengo el dato del formulario
       $usuario = $form->getData();
       //Obtengo el EntityManager
       $entityManager = $this->getDoctrine()->getManager();
       //Le doy persistencia
       $entityManager->persist($usuario);
       //Asiento los cambios en la base de datos
       $entityManager->flush();

      //Redirecciono al listado de personas
       return $this->redirectToRoute('app_usuario');
      }
      
      return $this->render('usuario/editar.html.twig', [
       'form' => $form->createView(),
       'usuario' => $usuario,
      ]);
    }

    /**
     * @Route("/usuarios/cambiarPassword/", name="app_usuario_editar_password", methods={"GET","HEAD","POST"})
     */
    //Funcion para editar los datos de una persona
    public function editarPasswordUsuario(Request $request): Response {
     //Obtengo el EntityManager
     $em = $this ->getDoctrine()->getManager();     
     //Obtengo el usuario logueado
     $usuarioLogueado=$this->get('security.token_storage')->getToken()->getUser();
     //Obtengo mi usuario de la BD
     $usuario=$em->getRepository(Usuario::class)->find($usuarioLogueado->getId());
     //Defino el Formulario
     $form = $this->createForm(UsuarioCambiarPasswordType::class, $usuario);  
     //Si se envia el formulario , existe un request
     $form->handleRequest($request);
       
      //Si se disparo el formulario y es valido
      if ($form->isSubmitted() && $form->isValid()) {
       //Obtengo el alumno del formulario
       $usuario = $form->getData();
       //Obtengo el EntityManager
       $entityManager = $this->getDoctrine()->getManager();
       //Le doy persistencia a la persona nueva
       $entityManager->persist($usuario);
       //Asiento los cambios en la base de datos
       $entityManager->flush();

       //Redirecciono al listado de personas
       return $this->redirectToRoute('app_usuario');
      }
        
      //Retorno a la vista
      return $this->render('usuario/editar.html.twig', [
       'form' => $form->createView(),
       'usuario' => $usuario,
      ]);
    }


    /**
      * Require ROLE_ADMIN for only this controller method.
      *
      * @Route("/usuarios", name="app_usuario_abm")
      *
      * @IsGranted("ROLE_ADMIN")
      */
    //Funcion que muestra el panel de Administracion de los Usuarios
    public function abmUsuarios(): Response {
     //Obtengo el EntityManager
     $em = $this ->getDoctrine()->getManager();     
     //Obtengo mi usuario de la BD
     $usuarios=$em->getRepository(Usuario::class)->findAll();

      //Retorno a la vista
      return $this->render('usuario/panelUsuarios.twig', [
       'usuarios' => $usuarios,
      ]);          
    }


    /**
      * Require ROLE_ADMIN for only this controller method.
      *
      * @Route("/usuarios/editar/{idUsuario}", name="app_usuario_abm_editar" , methods={"GET","HEAD","POST"})
      *
      * @IsGranted("ROLE_ADMIN")
      */
    //Funcion que me permite editar un usuario
    public function abmEditarUsuario($idUsuario,Request $request): Response {
     ///Obtengo el EntityManager
     $entityManager=$this->getDoctrine()->getManager();
     //Obtengo el usuario seleccionado
     $usuario=$entityManager->getRepository(Usuario::class)->find($idUsuario);   
     //Defino el Formulario
     $form = $this->createForm(UsuarioRolesType::class, $usuario);
     //Si se envia el formulario , existe un request
     $form->handleRequest($request); 
       
      //Si se disparo el formulario y es valido
      if ($form->isSubmitted() && $form->isValid()) {
       //Obtengo el alumno del formulario
       $usuario = $form->getData();
       //Obtengo el EntityManager
       $entityManager = $this->getDoctrine()->getManager();
       //Le doy persistencia a la persona nueva
       $entityManager->persist($usuario);
       //Asiento los cambios en la base de datos
       $entityManager->flush();
       //Redirecciono al listado de personas
       return $this->redirectToRoute('app_usuario_abm');
      }
        
    //Retorno a la vista
    return $this->render('usuario/panelEditarUsuario.html.twig', [
     'form' => $form->createView(),
     'usuario' => $usuario,
    ]);
  }    


}
