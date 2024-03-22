<?php

namespace App\Controller\ModifyUserController;

use App\Entity\Sortie;
use App\Entity\User;
use App\Form\UserModifyType;
use App\Repository\ParticipantRepository;
use App\Repository\UserRepository;
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

        // Vérifie si l'utilisateur a le rôle 'ROLE_ADMIN'
        if ($this->isGranted('ROLE_ADMIN')) {
            $userId = $this->getUser()->getId();
            $users = $entityManager->getRepository(User::class)->findAll();

            // Je crée un tableau pour stocker les utilisateurs à afficher
            $filteredUsers = [];

            // Je parcours chaque utilisateur pour filtrer ceux ayant un nom égal à "Delete"
            foreach ($users as $user) {
                if ($user->getNom() !== 'Deleted' && $user->getId() !== $userId) {
                    // J'ajoute l'utilisateur au tableau des utilisateurs à afficher
                    $filteredUsers[] = $user;
                }
            }

            return $this->render('User/userList.html.twig', ['users' => $filteredUsers]);
        }

        // Si l'utilisateur n'a pas le rôle 'ROLE_ADMIN', affiche une autre vue
        return $this->render('User/non.html.twig');
    }








    #[Route(path: 'list/desac/{id}', name: 'desac', methods: ['GET', 'POST'])]
    public function desac(EntityManagerInterface $entityManager,int $id): Response
    {

        if ($this->isGranted('ROLE_ADMIN')) {

            // Je récupère l'utilisateur avec son id
            $users = $entityManager->getRepository(User::class)->find($id);
            //Quand je clique sur le bouton de la page /user/list, je desactive l'utilisateur avec son id  (regarde la page userList.html.twig)
            $users->setActif(true);
            $entityManager->persist($users);
            $entityManager->flush();
            return $this->redirectToRoute('user_list', ['users' => $users]);
        }
        return $this->render('User/non.html.twig');
    }

    #[Route(path: 'list/supprime/{id}', name: 'supprime', methods: ['GET', 'POST'])]
    public function supprime(EntityManagerInterface $entityManager,int $id): Response
    {

        if ($this->isGranted('ROLE_ADMIN')) {
            // Je récupère l'utilisateur avec son id
            $users = $entityManager->getRepository(User::class)->find($id);
            $anom = 'Deleted';
            $users->setActif(true);
            $users->setNom($anom);
            $users->setPrenom($anom);
            $users->setPassword($anom);
            $users->setTelephone($anom);
            $users->setPicture($anom);

            //Quand je clique sur le bouton de la page /user/list, je desactive l'utilisateur avec son id  (regarde la page userList.html.twig)


            // $findSortieByUser = $entityManager->getRepository(Sortie::class)->findBy(['organisateur' => $users]);
            //  foreach ($findSortieByUser as $sortie) {
            //  $entityManager->remove($sortie);
            //}
            $entityManager->persist($users);
            $entityManager->flush();
            return $this->redirectToRoute('user_list', ['users' => $users]);
        }
        return $this->render('User/non.html.twig');
    }






    #[Route(path: 'update/{id}', name: 'updateUser', methods: ['GET', 'POST'])]
    public function updateUser(Request $request,EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher, SluggerInterface $slugger,int $id): Response
    {
        if (!$this->isGranted('ROLE_BANNED')) {
            //Je récupère l'utilisateur connecté
            $userId = $this->getUser()->getId();
            $userConnected = $entityManager->getRepository(User::class)->find($userId);

            // Je récupère l'utilisateur avec son id
            $user = $entityManager->getRepository(User::class)->find($id);

            //On ne peut modifier le profile que si c'est notre profil OU admin
            if ($userConnected == $user or $this->isGranted('ROLE_ADMIN')) {
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

            } else {
                return $this->render('/User/non.html.twig');
            }

        }else {
                // Redirection vers la page de connexion si l'utilisateur est banni
                return $this->render('/User/actif.html.twig');
        }

    }

    #[Route(path:'profil/{id}', name:'profil')]
    public function detail(int $id, UserRepository $userRepository): Response
    {
        if (!$this->isGranted('ROLE_BANNED')) {
            $user = $userRepository->find($id);
            return $this->render('/User/profil.html.twig', [
                'user' => $user
            ]);
        }else {
            // Redirection vers la page de connexion si l'utilisateur est banni
            return $this->render('/User/actif.html.twig');
        }
    }

// TEST POUR RECUP LES FICHIERS

}