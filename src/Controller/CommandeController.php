<?php

namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\UsagerRepository;
use \App\Service\PanierService;

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

    public function detail($commandeId, PanierService $panier) {

        $commande = $panier->getCommande($commandeId);
        $lignes = $panier->getLignes($commande);
        $prix = $panier->getPrixCommande($commande);

        return $this->render("detailCommande.html.twig",[
            "commande" => $commande,
            "prix" => $prix,
            "lignes" => $lignes
        ]);
    }
}