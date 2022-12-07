<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


class MapController extends AbstractController
{
    #[Route('/map',name:'map')]
    public function map(): Response
    {

        return $this->render('map.html.twig', [
            
        ]);
    }
}