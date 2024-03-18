<?php

namespace App\Controller\ModifyUserController;

use App\Entity\User;
use App\Form\UserModifyType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route(path:'user/', name: 'user_')]
class UserModifyController extends AbstractController
{

    #[Route(path: 'list', name: 'list', methods: ['GET', 'POST'])]
    public function list(EntityManagerInterface $entityManager): Response
    {
        // Je récupère la liste des utilisateurs dans la base de données
        $users = $entityManager->getRepository(User::class)->findAll();

        return $this->render('User/userList.html.twig', ['users' => $users]);
    }


    #[Route(path: 'list/desac/{id}', name: 'desac', methods: ['GET', 'POST'])]
    public function desac(EntityManagerInterface $entityManager,int $id): Response
    {
        // Je récupère l'utilisateur avec son id
        $users = $entityManager->getRepository(User::class)->find($id);
        //Quand je clique sur le bouton de la page /user/list, je desactive l'utilisateur avec son id  (regarde la page userList.html.twig)
        $users->setActif(true);
        $entityManager->persist($users);
        $entityManager->flush();
        return $this->redirectToRoute('user_list', ['users' => $users]);
    }






    #[Route(path: 'update/{id}', name: 'updateUser', methods: ['GET', 'POST'])]
    public function updateUser(Request $request,EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher, SluggerInterface $slugger,int $id): Response
    {
        // Je récupère l'utilisateur avec son id
        $user = $entityManager->getRepository(User::class)->find($id);
        // Je récupère le formulaire avec le user en paramètre
        $form = $this->createForm(UserModifyType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
// Si dans le form l'input picture_file est rempli
            if ($form->get('picture_file')->getData() instanceof UploadedFile) {
                //alors on récupère les données
                $pictureFile = $form->get('picture_file')->getData();
                // puis on le renomme avec les pseudo et on lui met un id unique et l'extension
                $fileName = $slugger->slug($user->getPseudo()) . '-' . uniqid() . '.' . $pictureFile->guessExtension();
                // on le met dans le dossier public uploads (picture_dir : c'est le parametre de config, regarde le fichier services.yaml)
                $pictureFile->move($this->getParameter('picture_dir'), $fileName);

                // on vérifie s'il y a une image
                if (!empty($user->getPicture())) {
                    //  c'est la fonction unlink qui supprime l'image (en gros en testant si l'image existe pour éviter d'avoir 20000 images dans le dossier public/uploads)
                    $picturePath = $this->getParameter('picture_dir') . '/' . $user->getPicture();
                    if (file_exists($picturePath)) {
                        unlink($picturePath);
                    }
                }
                // on rajoute le nouveau nom de l'image dans la bdd
                $user->setPicture($fileName);
            }
            // on hash le mot de passe
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();
            // dd($user);
            return $this->redirectToRoute('home_home');


        }
        // }

        return $this->render('User/UserModify.html.twig', ['form' => $form->createView(), 'user' => $user]);
    }

    #[Route(path:'detail', name:'detail')]
    public function detail()
    {

        return $this->redirectToRoute('home_home');

    }

// TEST POUR RECUP LES FICHIERS

}