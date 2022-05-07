<?php

namespace App\Controller;

use App\Entity\Usager;
use App\Form\UsagerConnexion;
use App\Form\UsagerType;
use App\Repository\UsagerRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Route("/usager")
 */
class UsagerController extends AbstractController
{
    /**
     * Route("/", name="app_usager_index", methods={"GET"})
     */
    public function index(UsagerRepository $usagerRepository): Response
    {

        $user = $this->getUser();
        $usager = $usagerRepository->findOneByEmail($user->getUserIdentifier());

        return $this->render('usager/index.html.twig', [
            'usager' => $user,
            'nombre_commande' => count($usager->getCommande())
        ]);
    }

    /**
     * Route("/new", name="app_usager_new", methods={"GET", "POST"})
     */
    public function new(Request $request, UsagerRepository $usagerRepository, EntityManagerInterface $entityManager,
                        UserPasswordHasherInterface $passwordHasher, AuthenticationUtils $authenticationUtils): Response
    {


        $usager = new Usager();

        //formulaire pour l'inscription
        $form = $this->createForm(UsagerType::class, $usager);
        $form->handleRequest($request);

        // formulaire pour la connexion
        $formConnexion = $this->createForm(UsagerConnexion::class, $usager, [
            'action' => $this->generateUrl('app_login'),
            'method' => 'POST',
        ]);
        $formConnexion->handleRequest($request);

        /*$formConnexion = $this->createFormBuilder($usager)
            ->add('email',EmailType::class)
            ->add('password', PasswordType::class)
            ->add('save', SubmitType::class)
            ->setAction($this->generateUrl('app_login'))
            ->setMethod('POST')
            ->getForm()
            ;*/

        // gestion du formulaire de connexion
        if($formConnexion->isSubmitted() && $formConnexion->isValid()) {
            if ($this->getUser()) {
                return $this->redirectToRoute('accueil_usager', ['usager' => $this->getUser()]);
             }
    
            // get the login error if there is one
            $error = $authenticationUtils->getLastAuthenticationError();
            // last username entered by the user
            $lastUsername = $authenticationUtils->getLastUsername();
    
            return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
        }

        
        // gestion du formulaire d'inscription
        if ($form->isSubmitted() && $form->isValid()) {

            // Encoder le mot de passe qui est en clair pour l’instant
            $hashedPassword = $passwordHasher->hashPassword($usager, $usager->getPassword());
            $usager->setPassword($hashedPassword);

            // Définir le rôle de l’usager qui va être créé
            $usager->setRoles(["ROLE_CLIENT"]);

            //vérification si l'utilisateur existe déja avant de l'inscrire
            //vérification sur l'email
            $usagers = $usagerRepository->findAll();
            foreach ($usagers as $u){
                if ($u->getUserIdentifier() === $usager->getUserIdentifier()) {

                    //création d'un méssage flash d'erreur si l'utilisateur existe déja
                    $this->addFlash(
                        "info",
                        "Compte déja existant"
                    );
                    return $this->redirectToRoute('inscription_usager');
                }
            }
            
            // Faire persister l’usager en BD
            $entityManager->persist($usager);
            $entityManager->flush();
            
            // Après l’inscription, rediriger vers l’authentification
            return $this->redirectToRoute('app_login');

        }

        //return $this->redirectToRoute('accueil_usager');
        return $this->renderForm('usager/new.html.twig', [
            'usager' => $usager,
            'form' => $form,
            'formConnexion' => $formConnexion
        ]);
    }

    //fonction accessible uniquement par l'admin pour avoir la liste des utilisateurs sur le backoffice
    public function gestionUsager(UsagerRepository $usagerRepository) {

        if ($this->isGranted('ROLE_ADMIN')) {
            $usagers = $usagerRepository->findAll();
            return $this->renderForm('administration/gestion.html.twig', [
                'usagers' => $usagers
            ]);
        } 
    }

    // fonction accesible uniquement par l'admin pour supprimer des utilisateurs depuis le backoffice
    public function suppressionUsager($usagerId, UsagerRepository $usagerRepository) {

        if ($this->isGranted('ROLE_ADMIN')) {

            $usager = $usagerRepository->findOneById($usagerId);
            $usagerRepository->remove($usager);
            return $this->redirectToRoute('admin_creation');
        }

    }

    // fonction accessible uniquement par l'admin pour passer le role d'un  utilisateur en admin
    public function upgradeUsagerToAdmin($usagerId, UsagerRepository $usagerRepository) {

        if ($this->isGranted('ROLE_ADMIN')) {

            $usager = $usagerRepository->findOneById($usagerId);
            $usagerRepository->upgradeRoleAdmin($usager);
            return $this->redirectToRoute('admin_creation');
        }
    }




}
