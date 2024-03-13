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




    #[Route(path: 'update/{id}', name: 'updateUser', methods: ['GET', 'POST'])]
public function updateUser(Request $request,EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher, SluggerInterface $slugger,int $id): Response
{
$user = $entityManager->getRepository(User::class)->find($id);
$form = $this->createForm(UserModifyType::class, $user);
$form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

        if ($form->get('picture_file')->getData() instanceof UploadedFile) {
            $pictureFile = $form->get('picture_file')->getData();
            $fileName = $slugger->slug($user->getPseudo()) . '-' . uniqid() . '.' . $pictureFile->guessExtension();
            $pictureFile->move($this->getParameter('picture_dir'), $fileName);

            if (!empty($user->getPicture())) {
                $picturePath = $this->getParameter('picture_dir') . '/' . $user->getPicture();
                if (file_exists($picturePath)) {
                    unlink($picturePath);
                }
            }

            $user->setPicture($fileName);
        }
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

}