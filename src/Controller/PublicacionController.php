<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException; 
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Entity\Publicacion;
use App\Form\PublicacionType;
use App\Entity\Imagen;
use App\Entity\Archivo;

class PublicacionController extends AbstractController
{
   
   /**
   * Require ROLE_ADMIN for only this controller method.
   * @Route("/publicacion", name="app_publicacion")
   * @IsGranted("ROLE_ADMIN")
   */
   public function index(): Response {
    //Obtengo el EntityManager
    $em= $this->getDoctrine()->getManager();
    //Obtengo todas las entidades
    $publicaciones=$em->getRepository(Publicacion::class)->findBy([],['id'=> 'DESC']);  
    

    //Retorno la vista
      return $this->render('publicacion/index.html.twig', 
         [
          'publicaciones' => $publicaciones,          
         ]
      );
   }


   /**    
   * @Route("/publicacion/listar", name="app_publicaciones")    
   */
   //Funcion que lista las publicaciones
   public function listarNoticias(): Response {
    //Obtengo el EntityManager
    $em= $this->getDoctrine()->getManager();
    //Obtengo todas las entidades
    $publicaciones=$em->getRepository(Publicacion::class)->findBy(['flagNoticia' => '1'],['id'=> 'DESC']);

      //Retorno la vista
      return $this->render('publicacion/listarPublicaciones.html.twig', 
         [
          'publicaciones' => $publicaciones,
         ]
      );
   }



   /**
   * @Route("/publicacion/ver/{idPublicacion}", name="app_publicacion_ver")
   */
   //Funcion para visualizar una publicacion
   public function verPublicacion($idPublicacion): Response {
    //Obtengo el EntityManager
    $em = $this ->getDoctrine()->getManager();     
    //Obtengo la entidad
    $publicacion=$em->getRepository(Publicacion::class)->find($idPublicacion);
    
      //Retorno a la vista
      return $this->render('publicacion/verPublicacion.html.twig', 
         [
          'publicacion' => $publicacion,             
         ]
      );
   }

   /**
   * Require ROLE_ADMIN for only this controller method.
   *
   * @Route("/publicacion/nueva", name="app_publicacion_nueva")
   *
   * @IsGranted("ROLE_ADMIN")
   */
   //Funcion para crear una nueva publicacion
   public function nuevaPublicacion(Request $request,SluggerInterface $slugger): Response {
    //Creo la entidad
    $publicacion = new Publicacion(); 
    //Defino el Formulario
    $form = $this->createForm(PublicacionType::class,$publicacion);
    //Si se envia el formulario , existe un request
    $form->handleRequest($request);

      //Si se disparo el formulario y es valido
      if ($form->isSubmitted() && $form->isValid()) {
       //Obtengo el EntityManager
       $entityManager = $this->getDoctrine()->getManager();

       //Obtengo la entidad del formulario
       $publicacion= $form->getData(); 
       //Le doy persistencia a
       $entityManager->persist($publicacion);
       //Asiento los cambios en la base de datos
       $entityManager->flush();  

       //Obtengo la imagen que subi 
       $imagenSubida=$form->get('imagenes')->getData();       
       $this->publicacionAdjuntarImagen($publicacion,$imagenSubida,$slugger);
       //Obtengo los archivos que subi
       $archivoSubido=$form->get('archivos')->getData(); 
       $this->publicacionAdjuntarArchivo($publicacion,$archivoSubido,$slugger);
              

       //Le doy persistencia a la imagen
       $entityManager->persist($publicacion);
       //Asiento los cambios en la base de datos
       $entityManager->flush();
       //Aviso
       $this->addFlash('exito','La Publicacion se creo exitosamente'); 
       //Redirecciono        
       return $this->redirectToRoute('app_publicacion');
      }             
      
      //Retorno la vista
      return $this->render('publicacion/nueva.html.twig', 
         [             
          'form' => $form->createView(),             
         ]
      );   
   }

   /**
   * Require ROLE_ADMIN for only this controller method.
   * 
   * @Route("/publicacion/editar/{idPublicacion}", name="app_publicacion_editar", methods={"GET","HEAD","POST"})
   * 
   * @IsGranted("ROLE_ADMIN")
   */
   //Funcion para editar una publicacion
   public function editarPublicacion($idPublicacion,Request $request,SluggerInterface $slugger): Response {
    //Obtengo el EntityManager
    $entityManager=$this->getDoctrine()->getManager();
    //Obtengo la entidad
    $publicacion=$entityManager->getRepository(Publicacion::class)->find($idPublicacion);   
    //Defino el Formulario
    $form = $this->createForm(PublicacionType::class,$publicacion);
    //Si se envia el formulario , existe un request
    $form->handleRequest($request);
 
      //Si se disparo el formulario y es valido
      if ($form->isSubmitted() && $form->isValid()) {
       //Obtengo el EntityManager
       $entityManager = $this->getDoctrine()->getManager();
       //Obtengo la entidad del formulario
       $publicacion= $form->getData(); 
       //Obtengo la imagen que subi 
       $imagenSubida= $form->get('imagenes')->getData();
          
         
       //Obtengo la imagen que subi 
       $imagenSubida=$form->get('imagenes')->getData();       
       $this->publicacionAdjuntarImagen($publicacion,$imagenSubida,$slugger);
       //Obtengo los archivos que subi
       $archivoSubido=$form->get('archivos')->getData(); 
       $this->publicacionAdjuntarArchivo($publicacion,$archivoSubido,$slugger);
       //Le doy persistencia 
       $entityManager->persist($publicacion);
       //Asiento los cambios en la base de datos
       $entityManager->flush();  

       //Aviso
       $this->addFlash('exito','La Publicacion se edito exitosamente'); 
       //Redirecciono        
       return $this->redirectToRoute('app_publicacion');
      }             
      
      //Retorno la vista
      return $this->render('publicacion/editar.html.twig', 
         [             
          'form' => $form->createView(),             
         ]
      );   
   }

   /**
   * Require ROLE_ADMIN for only this controller method.
   * 
   * @Route("/publicacion/eliminar/{idPublicacion}", name="app_publicacion_eliminar", methods={"GET","HEAD","POST"})
   * 
   * @IsGranted("ROLE_ADMIN")
   */
   //Funcion para editar una publicacion
   public function eliminarPublicacion($idPublicacion,Request $request,SluggerInterface $slugger): Response { 
    //Obtengo el EntityManager
    $em=$this->getDoctrine()->getManager();
    //Obtengo la entidad
    $publicacion=$em->getRepository(Publicacion::class)->find($idPublicacion); 

    //Elimino las imagenes
    $this->eliminarImagenesPublicacion($publicacion);
    //Elimino los archivos
    $this->eliminarArchivosPublicacion($publicacion);
    //Le doy persistencia 
    $em->remove($publicacion);
    //Asiento los cambios en la base de datos
    $em->flush();       
    //Aviso
    $this->addFlash('exito','La Publicacion se elimino exitosamente'); 
    //Redirecciono        
    return $this->redirectToRoute('app_publicacion');
   }
    

   /**
   * Require ROLE_ADMIN for only this controller method.
   * 
   * @Route("/publicacion/eliminarImagen/{idImagen}", name="app_publicacion_imagen_eliminar", methods={"GET","HEAD","POST"})
   * 
   * @IsGranted("ROLE_ADMIN")
   */
   //Funcion para eliminar una imagen de una publicacion
   public function eliminarImagenPublicacion($idImagen) {
    //Obtengo el EntityManager
    $em=$this->getDoctrine()->getManager();
    //Obtengo la entidad
    $imagen=$em->getRepository(Imagen::class)->find($idImagen); 
    //Obtengo 
    $publicacion=$imagen->getPublicacion();

    //Elimino fisicamente el archivo
    $urlImagen=$imagen->getUrl();
    //Elimino la imagen Fisicamente en el FileSistem
    unlink($urlImagen);
    //Elimino logicamente la imagen de la entidad
    $publicacion->removeImagen($imagen);
    //Le doy persistencia 
    $em->remove($imagen);
    $em->persist($publicacion);
    //Asiento los cambios en la base de datos
    $em->flush();
    //Aviso
    $this->addFlash('exito','Se elimino la imagen de la publicacion'); 
    //Redirecciono        
    return $this->redirectToRoute('app_publicacion');
   }

   /**
   * Require ROLE_ADMIN for only this controller method.
   * 
   * @Route("/publicacion/eliminarArchivo/{idArchivo}", name="app_publicacion_archivo_eliminar", methods={"GET","HEAD","POST"})
   * 
   * @IsGranted("ROLE_ADMIN")
   */
   //Funcion para eliminar un archivo de una publicacion
   public function eliminarArchivoPublicacion($idArchivo) {
    //Obtengo el EntityManager
    $em=$this->getDoctrine()->getManager();
    //Obtengo la entidad
    $archivo=$em->getRepository(Archivo::class)->find($idArchivo); 
    //Obtengo 
    $publicacion=$archivo->getPublicacion();

    //Elimino fisicamente el archivo
    $urlArchivo=$archivo->getUrl();
    //Elimino Fisicamente en el FileSistem
    unlink($urlArchivo);
    //Elimino logicamente 
    $publicacion->removeArchivo($archivo);
    //Le doy persistencia 
    $em->remove($archivo);
    $em->persist($publicacion);
    //Asiento los cambios en la base de datos
    $em->flush();
    //Aviso
    $this->addFlash('exito','Se elimino el archivo de la publicacion'); 
    //Redirecciono        
    return $this->redirectToRoute('app_publicacion');
   }

   //Funcion para eliminar las imagenes de una publicacion
   private function eliminarImagenesPublicacion($publicacion) {
    //Obtengo el EntityManager
    $em=$this->getDoctrine()->getManager();
    //Borro las imagenes de la publicacion
    $imagenes=$publicacion->getImagenes();
      //Itero sobre todas las imagenes
      foreach ($imagenes as $imagen) {
       //Elimino fisicamente el archivo
       $urlImagen=$imagen->getUrl();
       //Elimino la imagen Fisicamente en el FileSistem
       unlink($urlImagen);
       //Elimino logicamente la imagen de la entidad
       $publicacion->removeImagen($imagen);
       $em->remove($imagen);
      }
    //Le doy persistencia 
    $em->persist($publicacion);
    //Asiento los cambios en la base de datos
    $em->flush();
    //Retorno la entidad
    return $publicacion;
   }

   //Funcion para eliminar los archivos de una publicacion
   private function eliminarArchivosPublicacion($publicacion) {
    //Obtengo el EntityManager
    $em=$this->getDoctrine()->getManager();
    //Borro los archivos de la publicacion
    $archivos=$publicacion->getArchivos();
      //Itero sobre todos los archivos
      foreach ($archivos as $archivo) {
       //Elimino fisicamente el archivo
       $urlArchivo=$archivo->getUrl();
       //Elimino Fisicamente en el FileSistem
       unlink($urlArchivo);
       //Elimino logicamente 
       $publicacion->removeArchivo($archivo);
       $em->remove($archivo);
      }
    //Le doy persistencia 
    $em->persist($publicacion);
    //Asiento los cambios en la base de datos
    $em->flush();
    //Retorno la entidad
    return $publicacion;
   }
   
   //Funcion para Adjuntar una imagen a una publicacion
   private function publicacionAdjuntarImagen($publicacion,$imagenSubida,SluggerInterface $slugger) {
    //Obtengo el EntityManager
    $entityManager = $this->getDoctrine()->getManager();

      //Si existe la imagen la trabajo para guardarla
      if ($imagenSubida) {
       //Obtengo el Nombre Original de la Imagen para incluir de forma seguro el nombre de archivo en la URL
       $nombreOriginalImagen= pathinfo($imagenSubida->getClientOriginalName(), PATHINFO_FILENAME); 
       //Es necesario para incluir de forma segura el nombre del archivo como parte de la URL (todo minusculas)
       $nombreSeguroArchivo=strtolower($slugger->slug($nombreOriginalImagen));
       //Defino la URL completa de mi imagen subida
       $urlImagen='img/publicaciones/'.$publicacion->getId().'/'.$nombreSeguroArchivo.'.'.$imagenSubida->guessExtension();

         try {
          //Muevo el archivo al directorio donde los almaceno
          $imagenSubida->move($this->getParameter('imagenes_directory')."/".$publicacion->getId(),$urlImagen);
          //Creo la nueva entidad imagen..
          $imagen = new Imagen(); 
          //Asigno los datos a la imagen
          $imagen->setUrl($urlImagen);
          $imagen->setPublicacion($publicacion);
          $imagen->setNombre($nombreOriginalImagen);
          //Inserto mi imagen a la publicacion
          $publicacion->addImagen($imagen);
          //Le doy persistencia 
          $entityManager->persist($imagen);  
         } 
         catch (FileException $e) {
          $this->addFlash('aviso','Error al cargar la imagen');
          //Redirecciono al listado          
          return $this->redirectToRoute('app_publicacion');
         } 

       //retorno la publicacion    
       return $publicacion;        
      }
   }
   
   //Funcion para Adjuntar Archivos a una Publicacion
   private function publicacionAdjuntarArchivo($publicacion,$archivoSubido,SluggerInterface $slugger) {
    //Obtengo el EntityManager
    $entityManager = $this->getDoctrine()->getManager();

      //Si existe la imagen la trabajo para guardarla
      if ($archivoSubido) {
       //Obtengo el Nombre Original de la Imagen para incluir de forma seguro el nombre de archivo en la URL
       $nombreOriginalArchivo= pathinfo($archivoSubido->getClientOriginalName(), PATHINFO_FILENAME); 
       //Es necesario para incluir de forma segura el nombre del archivo como parte de la URL (todo minusculas)
       $nombreSeguroArchivo=strtolower($slugger->slug($nombreOriginalArchivo));
       //Defino la URL completa de mi imagen subida
       $urlArchivo='archivos/publicaciones/'.$publicacion->getId().'/'.$nombreSeguroArchivo.'.'.$archivoSubido->guessExtension();

         try {
          //Muevo el archivo al directorio donde los almaceno
          $archivoSubido->move($this->getParameter('archivos_directory')."/".$publicacion->getId(),$urlArchivo);
          //Creo la nueva entidad..
          $archivo = new Archivo(); 
          //Asigno los datos a la imagen
          $archivo->setUrl($urlArchivo);
          $archivo->setPublicacion($publicacion);
          $archivo->setNombre($nombreOriginalArchivo);
          //Inserto mi archivo a la publicacion
          $publicacion->addArchivo($archivo);
          //Le doy persistencia 
          $entityManager->persist($archivo);   
         } 
         catch (FileException $e) {
          $this->addFlash('aviso','Error al cargar la imagen');
          //Redirecciono al listado          
          return $this->redirectToRoute('app_publicacion');
         } 
       //retorno la publicacion    
       return $publicacion;        
      }
   }





}
