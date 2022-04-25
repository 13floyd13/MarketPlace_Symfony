<?php

namespace App\Controller;

use App\Entity\Usager;
use App\Form\UsagerType;
use App\Repository\UsagerRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @Route("/usager")
 */
class UsagerController extends AbstractController
{
    /**
     * @Route("/", name="app_usager_index", methods={"GET"})
     */
    public function index(UsagerRepository $usagerRepository): Response
    {
        /*return $this->render('usager/index.html.twig', [
            'usagers' => $usagerRepository->findAll(),
        ]);*/
        $user = $this->getUser();
        return $this->render('usager/index.html.twig', [
            'usagers' => $user
        ]);
    }

    /**
     * @Route("/new", name="app_usager_new", methods={"GET", "POST"})
     */
    public function new(Request $request, UsagerRepository $usagerRepository, EntityManagerInterface $entityManager,
                        UserPasswordHasherInterface $passwordHasher): Response
    {
        $usager = new Usager();
        $form = $this->createForm(UsagerType::class, $usager);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Encoder le mot de passe qui est en clair pour l’instant
            $hashedPassword = $passwordHasher->hashPassword($usager, $usager->getPassword());
            $usager->setPassword($hashedPassword);
            // Définir le rôle de l’usager qui va être créé
            $usager->setRoles(["ROLE_CLIENT"]);
            // Faire persister l’usager en BD
            $entityManager->persist($usager);
            $entityManager->flush();
            // Après l’inscription, rediriger vers l’authentification
            return $this->redirectToRoute('app_login');

            //$usagerRepository->add($usager);
            //return $this->redirectToRoute('app_usager_index', [], Response::HTTP_SEE_OTHER);
        }

        //return $this->redirectToRoute('accueil_usager');
        return $this->renderForm('usager/new.html.twig', [
            'usager' => $usager,
            'form' => $form,
        ]);
    }

    
}
