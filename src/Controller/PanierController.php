<?php

namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use \App\Service\BoutiqueService;
use \App\Service\PanierService;

class PanierController extends AbstractController {

    //retourne la vue du panier avec les produits ajoutés
    // ainsi que les le nombre et le prix total
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

    // fonction d'ajout de produit dans le panier
    // utilisé lors d'un click sur un produit
    // l'ajout se fait dans le service 
    // et la fonction redirige sur la route d'affichage du panier
    public function ajoutProduit($idProduit, $quantite, PanierService $panier){

        $panier->ajouterProduit($idProduit, $quantite);
        return $this->redirectToRoute('panier_index');
    }

    // permet de retirer des produits du panier
    // est appelé lors du click sur le '-' d'une ligne de produit
    // la fonction redirige sur la route d'affichage du panier
    public function enleverQuantite($idProduit, $quantite, PanierService $panier) {

        $panier->enleverProduit($idProduit, $quantite);
        return $this->redirectToRoute('panier_index');
    }

    // permet de retirer des produits du panier
    // est appelé lors du click sur le '+' d'une ligne de produit
    // la fonction redirige sur la route d'affichage du panier
    public function ajouterQuantite($idProduit, $quantite, PanierService $panier) {

        $panier->ajouterProduit($idProduit, $quantite);
        return $this->redirectToRoute('panier_index');

    }

    // permet de retirer un produit du panier en supprimant toute la ligne, peu importe la quantité
    // est appelé lors du click sur la poubelle d'une ligne
    // la fonction redirige sur la route d'affichage du panier
    public function supprimerLigne($idProduit, PanierService $panier) {

        $panier->supprimerProduit($idProduit);
        return $this->redirectToRoute('panier_index');

    }

    // permet de retirer tous les produits du panier
    // est appelé lors du click sur la poubelle en regard du Total du panier
    // la fonction redirige sur la route d'affichage du panier
    public function viderPanier(PanierService $panier) {

        $panier->vider();
        return $this->redirectToRoute('panier_index');
    }
}