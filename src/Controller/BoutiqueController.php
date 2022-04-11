<?php

namespace App\Controller;

use App\Entity\Produit;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use \App\Service\BoutiqueService;


class BoutiqueController extends AbstractController {

    // retourne toutes les categories dans une vue
    public function index(BoutiqueService $boutique){

        $categories = $boutique->findAllCategories();

        return $this->render("boutique.html.twig",[
            "categories" => $categories, "nb_rayons" => count($categories)
        ]);
    }

    // retourne tous les produits d'une catégorie
    public function rayon($idCategorie, BoutiqueService $boutique) {
        
        $produits = $boutique->findProduitsByCategorie($idCategorie);

        return $this->render("rayon.html.twig", [
            "produits" => $produits
        ]);
    }

    // retourne les produits recherchés dans un formulaire texte
    public function recherche($recherche, BoutiqueService $boutique){

        $produits = $boutique->findProduitsByLibelleOrTexte($recherche);


        // $produits = $this->getDoctrine()
        // ->getRepository(Produit::class)
        // ->findByLibelle($recherche);

        return $this->render('recherche.html.twig',[
            "produits" => $produits, "recherche"=> $recherche
        ]);

    }
}