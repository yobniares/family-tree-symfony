<?php
namespace App\Controller;

use App\Entity\Human;
use Doctrine\Persistence\ManagerRegistry;
use Masterminds\HTML5\Exception;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\HumanType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\String\Slugger\SluggerInterface;
use DateTime;




class HumanController extends AbstractController
{


    #[Route('/list',name:'list')]
    public function list(Request $request, ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();

        $objects = $em->getRepository(Human::class)->findAll();


        return $this->render('list.html.twig', [
            'objects'=>$objects
        ]);
    }

    #[Route('/edit/new',name:'new_human')]
    public function new(Request $request, ManagerRegistry $doctrine, SluggerInterface $slugger): Response
    {

        $human = new Human();

$form = $this->createForm(HumanType::class, $human,[
]);



        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $pictureFile = $form->get('picture')->getData();
            
            if ($pictureFile) {
                $originalFilename = pathinfo($pictureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$pictureFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $pictureFile->move(
                        $this->getParameter('pictures_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    throw $e;
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $human->setPicture($newFilename);
                
            }

            // Save
$human->setDatetimeUpdated(new DateTime());

            $em = $doctrine->getManager();
            $em->persist($human);
            $em->flush();

            return $this->redirectToRoute('list');

        }

        return $this->render('edit/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/edit/edit/{id}',name:'edit_human')]
    public function edit(int $id,Request $request, ManagerRegistry $doctrine, SluggerInterface $slugger): Response
    {

        $em = $doctrine->getManager();
        $human = $em->getRepository(Human::class)->find($id);
        if($human === null){
            throw new Exception('Пользователь не найден');
        }

        $form = $this->createForm(HumanType::class, $human,[
            'currentHuman'=>$human
        ]);


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $pictureFile = $form->get('picture')->getData();
            
            if ($pictureFile) {
                $originalFilename = pathinfo($pictureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$pictureFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $pictureFile->move(
                        $this->getParameter('pictures_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    throw $e;
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $human->setPicture($newFilename);
            }

            // Save
$human->setDatetimeUpdated(new DateTime());

            $em = $doctrine->getManager();
            $em->persist($human);
            $em->flush();

            return $this->redirectToRoute('list');

        }

        return $this->render('edit/edit.html.twig', [
            'form' => $form->createView(),
            'human' => $human
        ]);
    }
    
    #[Route('/edit/delete/{id}',name:'delete_human')]
    public function delete(int $id, Request $request, ManagerRegistry $doctrine, SluggerInterface $slugger): Response
    {
        $data = [];

        try{
        $em = $doctrine->getManager();
        $human = $em->getRepository(Human::class)->find($id);
        if($human === null){
            throw new Exception('Пользователь не найден');
        }
        $filesystem = new Filesystem();
        $path = $this->getParameter('pictures_directory').'/'.$human->getPicture();
        $filesystem->remove(['file', $path, 'activity.log']);

        $em->remove($human);
        $em->flush();
        $data = ['data'=>'success'];
        }catch(Exception $e){
            $data=['error'=>$e->getMessage()];
        }

        return new JsonResponse($data);
    }

    #[Route('/get_map_data',name:'get_map_data')]
    public function getMapData(Request $request, ManagerRegistry $doctrine, SluggerInterface $slugger): Response
    {
        $data = [];


        try{
        $em = $doctrine->getManager()->getRepository(Human::class);
        $human_data = [];
            $ancestor = $em->findOldest();
            $human_data = [
                'name' => $ancestor->getFullname(),
                'class' => $ancestor->getGender(),
            ];
            $marriage = [];
            $ancestor_spouse = $em->findSpouse($ancestor);
            if($ancestor_spouse)
            $marriage['spouse'] = [
'name' => $ancestor_spouse->getFullname(),

'class' => $ancestor_spouse->getGender(),

                ];
            $ancestor_children = $em->findChildren($ancestor);
            foreach($ancestor_children as $child){
            $marriage['children'][] = [
                    'name' => $child->getFullname(),
                    'class' => $child->getGender(),

                ];

            }
            $human_data['marriages'] = [];
            $human_data['marriages'][] = $marriage;
// $human_data['marriages'][] = $marriage;


            $data[] = $human_data;
            // $data[] = $human_data;
        
        
        }catch(Exception $e){
            $data=['error'=>$e->getMessage()];
        }

        return new JsonResponse($data);
    }






















}