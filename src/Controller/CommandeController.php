<?php

namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\UsagerRepository;

class CommandeController extends AbstractController {

    //Affiche la liste des commandes de l'utilisateur connecté
    public function index(UsagerRepository $usagerRepository){

        $user = $this->getUser();
        $usager = $usagerRepository->findOneByEmail($user->getUserIdentifier());
        $commandes = $usager->getCommande();

        return $this->render("commandes.html.twig",[
            'usager' => $user,
            'commandes' => $commandes
        ]);
    }
}