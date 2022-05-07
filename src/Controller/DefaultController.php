<?php
// Controller/DefaultController.php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\PanierService;


class DefaultController extends AbstractController {

    // affichage de la page d'accueil du site
    public function accueil() {
        return $this->render("accueil.html.twig");
    }

    // affichage de la page de contact
    public function contact() {
              
        return $this->render("contact.html.twig");
    }

    // envoie dans la barre de navigation le nombre de produits mis dans le panier
    public function navBar(PanierService $panier) {

        $nbArticles = $panier->getNbProduits();

        return $this->render("nbArticlesNavBar.html.twig",[
            'nbArticles' => $nbArticles
        ]);
    }

    public function aside(PanierService $panier) {

        $articles = $panier->getTopSales();

        return $this->render("topSales.html.twig",[
            'articles' => $articles
        ]);
    }
}