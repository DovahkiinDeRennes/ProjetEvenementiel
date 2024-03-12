<?php

namespace App\Controller\ModifyUserController;

use App\Entity\User;
use App\Form\UserModifyType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path:'user/', name: 'user_')]
class UserModifyController extends AbstractController
{




    #[Route(path: 'update/{id}', name: 'updateUser', methods: ['GET', 'POST'])]
public function updateUser(Request $request,EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher,int $id): Response
{
$user = $entityManager->getRepository(User::class)->find($id);
$form = $this->createForm(UserModifyType::class, $user);
$form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

        $user->setPassword(
            $userPasswordHasher->hashPassword(
                $user,
                $form->get('password')->getData()
            )
        );

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('user_updateUser', ['id' => $user->getId()]);
    }
    // }

    return $this->render('User/UserModify.html.twig', ['form' => $form->createView(), 'user' => $user]);
}

}