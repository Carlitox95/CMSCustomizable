<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException; 
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Menu;
use App\Form\MenuType;
use App\Entity\Publicacion;

class MenuController extends AbstractController
{
    
    
    /**
    * Require ROLE_ADMIN for only this controller method.
    * @Route("/menu", name="app_menu")
    * @IsGranted("ROLE_ADMIN")
    */
    public function index(): Response {
     //Obtengo el EntityManager
     $em= $this->getDoctrine()->getManager();
     //Obtengo todas las entidades
     $menus=$em->getRepository(Menu::class)->findBy([],['orden'=> 'ASC']);

        //Retorno a la vista
        return $this->render('menu/index.html.twig', 
            [
             'menus' => $menus,
            ]
        );
    }

    /**
     * 
     * @Route("/menu/obtener", name="app_menu_obtenerJson")
    */
    public function apiObtenerMenu (): Response {
     //Obtengo el Entity Manager
     $em=$this->getDoctrine()->getManager();
     //Obtengo todas las entidades
     $menus=$em->getRepository(Menu::class)->findBy([],['orden'=> 'ASC']);
     //Defino un array para almacenar los Menus
     $arrayMenus=[];
   
        //Itero sobre todos los menus
        foreach ($menus as $menu) {
         //Defino un array para el Menu actual
         $arrayUnMenu=[];
            
            //Iteramos sobre todas las publicaciones del Menu 
            foreach ($menu->getPublicaciones() as $publicacion) {
             //Defino un array de Publicacion
             $arrayPublicacion=array("id" => $publicacion->getId(),"nombre" => $publicacion->getTitulo());
             //Inserto la Publicacion al array del menu actual
             array_push($arrayUnMenu,$arrayPublicacion);
            }

         //Defino un array con el nombre del menu y sus publicaciones..
         $arrayMenu=array("id" => $menu->getId(),"nombre" => $menu->getNombre(),"publicaciones" =>$arrayUnMenu);
         //Inserto el array del menu actual al array de los Menus..
         array_push($arrayMenus,$arrayMenu);       
        }

     //Retorno el respose
     return new JsonResponse($arrayMenus);
    }





    /**
    * Require ROLE_ADMIN for only this controller method.
    * @Route("/menu/crear", name="app_menu_crear")
    * @IsGranted("ROLE_ADMIN")
    */
    public function crearMenu(Request $request,SluggerInterface $slugger): Response {
     //Creo la entidad
     $menu = new Menu(); 
     //Defino el Formulario
     $form = $this->createForm(MenuType::class,$menu);
     //Si se envia el formulario , existe un request
     $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
         //Obtengo el EntityManager
         $entityManager = $this->getDoctrine()->getManager();
         //Obtengo la entidad del formulario
         $menu=$form->getData(); 
         //Le doy persistencia 
         $entityManager->persist($menu);
         //Asiento los cambios en la base de datos
         $entityManager->flush();
         //Aviso
         $this->addFlash('exito','El Menu se creo exitosamente'); 
         //Redirecciono        
         return $this->redirectToRoute('app_menu');
        }             
      
        //Retorno la vista
        return $this->render('menu/nuevo.html.twig', 
            [             
             'form' => $form->createView(),             
            ]
        );   
    }


    /**
    * Require ROLE_ADMIN for only this controller method.
    * @Route("/menu/editar/{idMenu}", name="app_menu_editar")
    * @IsGranted("ROLE_ADMIN")
    */
    public function editarMenu($idMenu,Request $request,SluggerInterface $slugger): Response {
     //Obtengo el EntityManager
     $entityManager=$this->getDoctrine()->getManager();
     //Obtengo la entidad
     $menu=$entityManager->getRepository(Menu::class)->find($idMenu);   
     //Defino el Formulario
     $form = $this->createForm(MenuType::class,$menu);
     //Si se envia el formulario , existe un request
     $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
         //Obtengo la entidad del formulario
         $menu=$form->getData(); 
         //Le doy persistencia 
         $entityManager->persist($menu);
         //Asiento los cambios en la base de datos
         $entityManager->flush();
         //Aviso
         $this->addFlash('exito','El Menu se edito exitosamente'); 
         //Redirecciono        
         return $this->redirectToRoute('app_menu');
        }             
      
        //Retorno la vista
        return $this->render('menu/edit.html.twig', 
            [    
             'menu' => $menu,         
             'form' => $form->createView(),             
            ]
        );   
    }

    /**
    * Require ROLE_ADMIN for only this controller method.
    * 
    * @Route("/menu/eliminar/{idMenu}", name="app_menu_eliminar", methods={"GET","HEAD","POST"})
    * 
    * @IsGranted("ROLE_ADMIN")
    */
    //Funcion para eliminar un menu
    public function eliminarMenu($idMenu,Request $request,SluggerInterface $slugger): Response { 
     //Obtengo el EntityManager
     $em=$this->getDoctrine()->getManager();
     //Obtengo la entidad
     $menu=$em->getRepository(Menu::class)->find($idMenu); 
     //Le doy persistencia 
     $em->remove($menu);
     //Asiento los cambios en la base de datos
     $em->flush();       
     //Aviso
     $this->addFlash('exito','El menu se elimino exitosamente'); 
     //Redirecciono        
     return $this->redirectToRoute('app_menu');
    }






}
