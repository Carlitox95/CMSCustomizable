<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException; 
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Entity\Banner;
use App\Form\BannerType;
use App\Form\BannerPublicacionType;
use App\Entity\Imagen;



class BannerController extends AbstractController
{   
   /**
   * Require ROLE_ADMIN for only this controller method.
   * @Route("/banner", name="app_banner")
   * @IsGranted("ROLE_ADMIN")
   */
   public function index(): Response {
    //Obtengo el EntityManager
    $em= $this->getDoctrine()->getManager();
    //Obtengo todas las entidades
    $banners=$em->getRepository(Banner::class)->findBy([],['id'=> 'DESC']);

      //Retorno la vista
      return $this->render('banner/index.html.twig', 
         [
          'banners' => $banners,
         ]
      );
   }

   /**
   * Require ROLE_ADMIN for only this controller method.
   *
   * @Route("/banner/nuevo", name="app_banner_nuevo")
   *
   * @IsGranted("ROLE_ADMIN")
   */
   //Funcion para crear una nueva publicacion
   public function nuevoBanner(Request $request,SluggerInterface $slugger): Response {
    //Creo la entidad
    $banner = new Banner(); 
    //Defino el Formulario
    $form = $this->createForm(BannerType::class,$banner);
    //Si se envia el formulario , existe un request
    $form->handleRequest($request);

      //Si se disparo el formulario y es valido
      if ($form->isSubmitted() && $form->isValid()) {
       //Obtengo el EntityManager
       $entityManager = $this->getDoctrine()->getManager();
       //Obtengo la entidad del formulario
       $banner= $form->getData(); 
       //Obtengo la imagen que subi 
       $imagenSubida= $form->get('imagen')->getData();
          
         
       //Le doy persistencia 
       $entityManager->persist($banner);
       //Asiento los cambios en la base de datos
       $entityManager->flush();
            
         //Si existe la imagen la trabajo para guardarla
         if ($imagenSubida) {
          //Obtengo el Nombre Original de la Imagen para incluir de forma seguro el nombre de archivo en la URL
          $nombreOriginalImagen= pathinfo($imagenSubida->getClientOriginalName(), PATHINFO_FILENAME); 
          //Es necesario para incluir de forma segura el nombre del archivo como parte de la URL (todo minusculas)
          $nombreSeguroArchivo=strtolower($slugger->slug($nombreOriginalImagen));
          //Defino la URL completa de mi imagen subida
          $urlImagen='img/banners/'.$banner->getId().'/'.$nombreSeguroArchivo.'.'.$imagenSubida->guessExtension();

            try {
             //Muevo el archivo al directorio donde los almaceno
             $imagenSubida->move($this->getParameter('banners_directory')."/".$banner->getId(),$urlImagen);
             //Creo la nueva entidad imagen..
             $imagen = new Imagen(); 
             //Asigno los datos a la imagen
             $imagen->setUrl($urlImagen);                
             $imagen->setNombre($nombreOriginalImagen);
             //Inserto mi imagen a las imagenes de la publicacion
             $banner->setImagen($imagen);
             //Le doy persistencia a la imagen
             $entityManager->persist($imagen);
             //Aviso
             $this->addFlash('exito','Se ha cargado la imagen correctamente y se creo el banner');
            } 
            catch (FileException $e) {
             $this->addFlash('aviso','Error al cargar la imagen');
             //Redirecciono al listado          
             return $this->redirectToRoute('app_banner');
            }             
         }

       //Le doy persistencia a la imagen
       $entityManager->persist($banner);
       //Asiento los cambios en la base de datos
       $entityManager->flush();
       //Aviso
       $this->addFlash('exito','El banner se creo exitosamente'); 
       //Redirecciono        
       return $this->redirectToRoute('app_banner');
      }             
      
      //Retorno la vista
      return $this->render('banner/nuevo.html.twig', 
         [             
          'form' => $form->createView(),             
         ]
      );   
   }


   /**
   * Require ROLE_ADMIN for only this controller method.
   * 
   * @Route("/banner/eliminar/{idBanner}", name="app_banner_eliminar", methods={"GET","HEAD","POST"})
   * 
   * @IsGranted("ROLE_ADMIN")
   */
   //Funcion para eliminar un Banner
   public function eliminarBanner($idBanner,Request $request,SluggerInterface $slugger): Response {
    //Obtengo el EntityManager
    $em=$this->getDoctrine()->getManager();
    //Obtengo la entidad
    $banner=$em->getRepository(Banner::class)->find($idBanner); 
    //Elimino la imagen
    $this->eliminarImagenBanner($banner);
    //Le doy persistencia 
    $em->remove($banner);
    //Asiento los cambios en la base de datos
    $em->flush();       
    //Aviso
    $this->addFlash('exito','El Banner se elimino exitosamente'); 
    //Redirecciono        
    return $this->redirectToRoute('app_banner');
   }


   
   /**
   * Require ROLE_ADMIN for only this controller method.
   * 
   * @Route("/banner/cambiarEstado/{idBanner}", name="app_banner_cambiar_estado", methods={"GET","HEAD","POST"})
   * 
   * @IsGranted("ROLE_ADMIN")
   */
   //Funcion para cambiar el estado de los turnos
   public function modificarEstadoBanner($idBanner,Request $request,SluggerInterface $slugger): Response {
    //Obtengo el EntityManager
    $em=$this->getDoctrine()->getManager();
    //Obtengo la entidad
    $banner=$em->getRepository(Banner::class)->find($idBanner); 
    
      //Cambio el estado
      if ($banner->getActivo() == 1) {
       $banner->setActivo(false);
      }
      else {
       $banner->setActivo(true);
      }

    //Le doy persistencia 
    $em->persist($banner);
    //Asiento los cambios en la base de datos
    $em->flush();       
    //Aviso
    $this->addFlash('exito','El Banner cambio de estado correctamente'); 
    //Redirecciono        
    return $this->redirectToRoute('app_banner');
   }
   
   
   
   

   /**
    * Require ROLE_ADMIN for only this controller method.
    * @Route("/banner/enlazar/{idBanner}", name="app_banner_enlazar")
    * @IsGranted("ROLE_ADMIN")
    */
   public function enlazarBannerPublicacion($idBanner,Request $request,SluggerInterface $slugger): Response {
    //Obtengo el EntityManager
    $entityManager=$this->getDoctrine()->getManager();
    //Obtengo la entidad
    $banner=$entityManager->getRepository(Banner::class)->find($idBanner);   
    //Defino el Formulario
    $form = $this->createForm(BannerPublicacionType::class,$banner);
    //Si se envia el formulario , existe un request
    $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
       //Obtengo la entidad del formulario
       $banner=$form->getData(); 
       //Le doy persistencia 
       $entityManager->persist($banner);
       //Asiento los cambios en la base de datos
       $entityManager->flush();
       //Aviso
       $this->addFlash('exito','El Banner se edito exitosamente'); 
       //Redirecciono        
       return $this->redirectToRoute('app_banner');
      }             
      
      //Retorno la vista
      return $this->render('banner/editar.html.twig', 
         [   
          'banner' => $banner,          
          'form' => $form->createView(),             
         ]
      );   
   }







   //Funcion que elimina una imagen del banner
   private function eliminarImagenBanner($banner) {
    //Obtengo el EntityManager
    $em=$this->getDoctrine()->getManager();
    //Borro la imagen
    $imagen=$banner->getImagen();
    //Elimino fisicamente el archivo
    $urlImagen=$imagen->getUrl();    
    unlink($urlImagen);
    //Elimino logicamente la imagen de la entidad
    $banner->setImagen(null);
    //Le doy persistencia 
    $em->persist($banner);
    //Asiento los cambios en la base de datos
    $em->flush();
    //Retorno la entidad
    return $banner;
   }





}
