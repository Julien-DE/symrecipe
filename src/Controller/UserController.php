<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserPassworType;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{

    /**
     * This controller allow us to edit a user profil
     *
     * @param User $choosenUser
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/utilisateur/edition/{id}', name: 'user_edit', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_USER') and user  === choosenUser")]
    public function edit(User $choosenUser, Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $hasher): Response
    {


        $form = $this->createForm(UserType::class, $choosenUser);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($hasher->isPasswordValid($choosenUser, $form->getData()->getPlainPassword())) {

                $choosenUser = $form->getData();

                $manager->persist($choosenUser);
                $manager->flush();

                $this->addFlash(
                    'success',
                    'Les informations de votre compte ont bien été modifiées !'
                );

                return $this->redirectToRoute('app_recipe');
            } else {

                $this->addFlash(
                    'warning',
                    'Le mot de passe renseigné est incorrect.'
                );
            }
        }

        return $this->render('pages/user/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/utilisateur/edition-mot-de-passe/{id}', name: 'user_edit_password', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_USER') and user  === choosenUser")]
    public function editPassword(User $choosenUser, Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $hasher): Response
    {
        $form = $this->createForm(UserPassworType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($hasher->isPasswordValid($choosenUser, $form->getData()['PlainPassword'])) {
                $choosenUser->setUpdateAt(new \DateTimeImmutable());
                $choosenUser->setPassword(
                    $hasher->hashPassword(
                        $choosenUser,
                        $form->getData()['newPassword']
                    )
                );

                $manager->persist($choosenUser);
                $manager->flush();
                $this->addFlash(
                    'success',
                    'Votre mot de passe a bien été modifiées !'
                );

                return $this->redirectToRoute('app_recipe');
            } else {
                $this->addFlash(
                    'warning',
                    'Le mot de passe renseigné est incorrect.'
                );
            }
        }
        return $this->render(
            'pages/user/edit_password.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }
}
