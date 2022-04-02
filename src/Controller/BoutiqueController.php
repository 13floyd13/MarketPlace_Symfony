<?php

namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use \App\Service\BoutiqueService;

class BoutiqueController extends AbstractController {

    public function index(BoutiqueService $boutique){

        $categories = $boutique->findAllCategories();

        return $this->render("boutique.html.twig",[
            "categories" => $categories, "nb_rayons" => count($categories)
        ]);
    }

    public function rayon($idCategorie, BoutiqueService $boutique) {
        
        $produits = $boutique->findProduitsByCategorie($idCategorie);

        return $this->render("rayon.html.twig", [
            "produits" => $produits
        ]);
    }

    public function recherche($recherche, BoutiqueService $boutique){

        $produits = $boutique->findProduitsByLibelleOrTexte($recherche);


        return $this->render('recherche.html.twig',[
            "produits" => $produits, "recherche"=> $recherche
        ]);

    }
}