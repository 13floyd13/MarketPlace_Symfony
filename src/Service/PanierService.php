<?php
namespace App\Service;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Service\BoutiqueService;
// Service pour manipuler le panier et le stocker en session
class PanierService {
////////////////////////////////////////////////////////////////////////////
const PANIER_SESSION = 'panier'; // Le nom de la variable de session du panier
private $session; // Le service Session
private $boutique; // Le service Boutique
private $panier; // Tableau associatif idProduit => quantite
// donc $this->panier[$i] = quantite du produit dont l'id = $i
// constructeur du service
public function __construct(SessionInterface $session, BoutiqueService $boutique) {
// Récupération des services session et BoutiqueService
$this->boutique = $boutique;
$this->session = $session;
// Récupération du panier en session s'il existe, init. à vide sinon
//$this->session->clear();
$this->panier = $session->get(self::PANIER_SESSION, array());// initialisation du Panier : à compléter

}
// getContenu renvoie le contenu du panier
// tableau d'éléments [ "produit" => un produit, "quantite" => quantite ]
public function getContenu() { // à compléter }

    $produits = [];
    foreach($this->panier as $id=>$qte){
        $produit = $this->boutique->findProduitById($id);

        $produits[] = ["produit" => $produit, "quantite" => $qte];
    }
    dump($produits);
    return $produits;
}    
// getTotal renvoie le montant total du panier
public function getTotal() { // à compléter }

    $total = 0;
    
    dump($this->panier);
    foreach($this->panier as $id => $qte){
        
        $produit = $this->boutique->findProduitById($id);
        $prixUnitaire = $produit['prix'];
        $total += $prixUnitaire * $qte;
    }

    return $total;

}
// getNbProduits renvoie le nombre de produits dans le panier
public function getNbProduits() { // à compléter }

    $quantiteTotal = 0;
    foreach($this->panier as $id => $qte){
        
        $quantiteTotal += $qte;
        
}
    return $quantiteTotal;

}

// ajouterProduit ajoute au panier le produit $idProduit en quantite $quantite
public function ajouterProduit(int $idProduit, int $quantite = 1) { // à compléter }

    //$this->panier = ["idProduit" => $idProduit, "quantite" => $quantite];

    if(isset($this->panier[$idProduit])) {
        $this->panier[$idProduit] += $quantite;
        
    }else{
        $this->panier[$idProduit] = $quantite;
    }
    $this->session->set(self::PANIER_SESSION, $this->panier);

}
// enleverProduit enlève du panier le produit $idProduit en quantite $quantite
public function enleverProduit(int $idProduit, int $quantite = 1) { // à compléter }

    if($this->panier[$idProduit] > 0){
        $this->panier[$idProduit] -= $quantite;

    }else{
        $this->supprimerProduit($idProduit);
    }
    
    $this->session->set(self::PANIER_SESSION, $this->panier);

}
// supprimerProduit supprime complètement le produit $idProduit du panier
public function supprimerProduit(int $idProduit) { // à compléter }

    unset($this->panier[$idProduit]);
    $this->session->set(self::PANIER_SESSION, $this->panier);

}
// vider vide complètement le panier
public function vider() { // à compléter }

    $this->panier = [];
    $this->session->set(self::PANIER_SESSION, $this->panier);
}
}