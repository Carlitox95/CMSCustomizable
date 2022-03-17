<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException; 
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Entity\Publicacion;
use App\Entity\Banner;


class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(): Response {
     //Obtengo el EntityManager
     $em= $this->getDoctrine()->getManager();
     //Obtengo las ultimas noticias
     $ultimasPublicaciones=$em->getRepository(Publicacion::class)->findByUltimasNoticias();   
     //Obtengo los Banners
     $banners=$em->getRepository(Banner::class)->findByActivos();    

        //Retorno la vista
        return $this->render('index.html.twig', 
            [
             'publicaciones' => $ultimasPublicaciones,
             'banners' => $banners,
            ]
        );
    }
}
