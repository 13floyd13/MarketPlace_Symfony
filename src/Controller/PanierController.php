<?php

namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use \App\Service\BoutiqueService;
use \App\Service\PanierService;

class PanierController extends AbstractController {

    public function index(PanierService $panier) {

        $lignes = $panier->getContenu();
        $nombreItems = $panier->getNbProduits();
        $total = $panier->getTotal();

        return $this->render("panier.html.twig",[
            "lignes" => $lignes,
            "nombreItems" => $nombreItems,
            "total" => $total
        ]);
    }

    public function ajoutProduit($idProduit, $quantite, PanierService $panier){

        $panier->ajouterProduit($idProduit, $quantite);
        return $this->redirectToRoute('panier_index');
    }

    public function enleverQuantite($idProduit, $quantite, PanierService $panier) {

        $panier->enleverProduit($idProduit, $quantite);
        return $this->redirectToRoute('panier_index');
    }

    public function ajouterQuantite($idProduit, $quantite, PanierService $panier) {

        $panier->ajouterProduit($idProduit, $quantite);
        return $this->redirectToRoute('panier_index');

    }

    public function supprimerLigne($idProduit, PanierService $panier) {

        $panier->supprimerProduit($idProduit);
        return $this->redirectToRoute('panier_index');

    }

    public function viderPanier(PanierService $panier) {

        $panier->vider();
        return $this->redirectToRoute('panier_index');
    }
}