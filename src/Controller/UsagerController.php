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
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

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
        $usager = $usagerRepository->findOneByEmail($user->getUserIdentifier());

        return $this->render('usager/index.html.twig', [
            'usager' => $user,
            'nombre_commande' => count($usager->getCommande())
        ]);
    }

    /**
     * @Route("/new", name="app_usager_new", methods={"GET", "POST"})
     */
    public function new(Request $request, UsagerRepository $usagerRepository, EntityManagerInterface $entityManager,
                        UserPasswordHasherInterface $passwordHasher, AuthenticationUtils $authenticationUtils): Response
    {

        $usager = new Usager();
        $form = $this->createForm(UsagerType::class, $usager);
        $form->handleRequest($request);

        $formConnexion = $this->createFormBuilder($usager)
            ->add('email',EmailType::class)
            ->add('password', PasswordType::class)
            ->add('save', SubmitType::class)
            ->getForm()
            ;

        if($formConnexion->isSubmitted() && $formConnexion->isValid()) {
            var_dump('test');
            if ($this->getUser()) {
                return $this->redirectToRoute('accueil_usager', ['usager' => $this->getUser()]);
             }
    
            // get the login error if there is one
            $error = $authenticationUtils->getLastAuthenticationError();
            // last username entered by the user
            $lastUsername = $authenticationUtils->getLastUsername();
    
            return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
        }

        

        if ($form->isSubmitted() && $form->isValid()) {

            

            // Encoder le mot de passe qui est en clair pour l’instant
            $hashedPassword = $passwordHasher->hashPassword($usager, $usager->getPassword());
            $usager->setPassword($hashedPassword);

            // Définir le rôle de l’usager qui va être créé
            $usager->setRoles(["ROLE_CLIENT"]);

            $usagers = $usagerRepository->findAll();
            if (in_array($usager->getUserIdentifier(), $usagers)){
                return $this->redirectToRoute('accueil_page');
            }

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
            'formConnexion' => $formConnexion
        ]);
    }

    
}
