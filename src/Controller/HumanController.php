<?php
namespace App\Controller;

use App\Entity\Human;
use App\Repository\HumanRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectRepository;
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


    #[Route('/list', name: 'list')]
    public function list(Request $request, ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();

        $objects = $em->getRepository(Human::class)->findAll();


        return $this->render('list.html.twig', [
            'objects' => $objects
        ]);
    }

    #[Route('/edit/new', name: 'new_human')]
    public function new (Request $request, ManagerRegistry $doctrine, SluggerInterface $slugger): Response
    {

        $human = new Human();

        $form = $this->createForm(HumanType::class, $human, [
        ]);



        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {

            $pictureFile = $form->get('picture')->getData();

            if ($pictureFile)
            {
                $originalFilename = pathinfo($pictureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $pictureFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try
                {
                    $pictureFile->move(
                        $this->getParameter('pictures_directory'),
                        $newFilename
                    );
                }
                catch (FileException $e)
                {
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

    #[Route('/edit/edit/{id}', name: 'edit_human')]
    public function edit(int $id, Request $request, ManagerRegistry $doctrine, SluggerInterface $slugger): Response
    {

        $em = $doctrine->getManager();
        $human = $em->getRepository(Human::class)->find($id);
        if ($human === null)
        {
            throw new Exception('Пользователь не найден');
        }

        $form = $this->createForm(HumanType::class, $human, [
            'currentHuman' => $human
        ]);


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {

            $pictureFile = $form->get('picture')->getData();

            if ($pictureFile)
            {
                $originalFilename = pathinfo($pictureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $pictureFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try
                {
                    $pictureFile->move(
                        $this->getParameter('pictures_directory'),
                        $newFilename
                    );
                }
                catch (FileException $e)
                {
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

    #[Route('/edit/delete/{id}', name: 'delete_human')]
    public function delete(int $id, Request $request, ManagerRegistry $doctrine, SluggerInterface $slugger): Response
    {
        $data = [];

        try
        {
            $em = $doctrine->getManager();
            $human = $em->getRepository(Human::class)->find($id);
            if ($human === null)
            {
                throw new Exception('Пользователь не найден');
            }
            $filesystem = new Filesystem();
            $path = $this->getParameter('pictures_directory') . '/' . $human->getPicture();
            $filesystem->remove(['file', $path, 'activity.log']);

            $em->remove($human);
            $em->flush();
            $data = ['data' => 'success'];
        }
        catch (Exception $e)
        {
            $data = ['error' => $e->getMessage()];
        }

        return new JsonResponse($data);
    }

    #[Route('/human/{id}', name: 'human_profile')]
    public function human_profile(int $id, Request $request, ManagerRegistry $doctrine): Response
    {

        $em = $doctrine->getManager();
        $human = $em->getRepository(Human::class)->find($id);
        if ($human === null)
        {
            throw new Exception('Пользователь не найден');
        }

        return $this->render('human.html.twig', [
            'human' => $human
        ]);

    }

    function get_user_tree(Human $human, ObjectRepository $em): array
    {

        $human_data = [
            'name' => $human->getFullname(),
        'class' => $human->getGender(),

            'extra' => [
        'id' => $human->getId()

            ]
        ];
        $marriage = [];
        $human_spouse = $em->findSpouse($human);
        if ($human_spouse)
        {
            $marriage['spouse'] = [
                    'name' => $human_spouse->getFullname(),

                    'class' => $human_spouse->getGender(),

                                    'extra' => [
                    'id' => $human_spouse->getId()


                            ]
                        ];
        }


        $children = $em->findChildren($human);
        foreach ($children as $child)
        {
            $marriage['children'][] = $this->get_user_tree($child, $em);
        }
        if (!isset($marriage['spouse']) && isset($marriage['children']))
        {
            $marriage['spouse'] = [
                    'name' => '?',
                    'class' => $human->getOppositeGender()
                ];
        }
        if ($marriage != [])
        {
            $human_data['marriages'] = [];
            $human_data['marriages'][] = $marriage;
        }


        return $human_data;


    }


    #[Route('/get_map_data/{id}', name: 'get_map_data')]
    public function getMapData(int $id, Request $request, ManagerRegistry $doctrine, SluggerInterface $slugger): Response
    {
        $data = [];


        try
        {
            $em = $doctrine->getManager()->getRepository(Human::class);
            $human = $em->find($id ?: 2);
            $ancestors = $em->findOldestInTreeIncluding($human);

            $human_data = $this->get_user_tree($ancestors[0], $em);



            $data[] = $human_data;
            // $data[] = $human_data;


        }
        catch (Exception $e)
        {
            $data = ['error' => $e->getMessage()];
        }

        return new JsonResponse($data);
    }








}