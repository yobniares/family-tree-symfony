<?php
namespace App\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Human;



class MapController extends AbstractController
{
    #[Route('/map',name:'map')]
public function map(ManagerRegistry $doctrine): Response

    {
        $em = $doctrine->getManager();
        $human = $em->getRepository(Human::class)->find(2);


        return $this->render('map.html.twig', [
            "human" => $human

        ]);
    }

    #[Route('/map/{id}',name:'map_from_human')]
public function map_from_human(int $id,Request $request, ManagerRegistry $doctrine): Response

    {
        $em = $doctrine->getManager();
        $human = $em->getRepository(Human::class)->find($id);
        if ($human === null) {
            throw new \Exception('Пользователь не найден');
        }

        return $this->render('map.html.twig', [
            "human"=>$human
        ]);
    }



}