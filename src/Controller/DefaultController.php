<?php
// Controller/DefaultController.php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\PanierService;


class DefaultController extends AbstractController {

    public function accueil() {
        return $this->render("accueil.html.twig");
    }

    public function contact() {
              
        return $this->render("contact.html.twig");
    }

    public function navBar(PanierService $panier) {

        $nbArticles = $panier->getNbProduits();
        return $this->render("nbArticlesNavBar.html.twig",[
            'nbArticles' => $nbArticles
        ]);
    }
}