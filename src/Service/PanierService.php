<?php
namespace App\Service;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Service\BoutiqueService;
use Symfony\Component\Security\Core\User\User;
use App\Entity\Commande;
use App\Entity\LigneCommande;
use App\Entity\Usager;
use App\Repository\CommandeRepository;
use App\Repository\LigneCommandeRepository;
use App\Repository\UsagerRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\Types\Float_;
use phpDocumentor\Reflection\Types\Integer;

// Service pour manipuler le panier et le stocker en session
class PanierService {
////////////////////////////////////////////////////////////////////////////

    const PANIER_SESSION = 'panier'; // Le nom de la variable de session du panier
    private $session; // Le service Session
    private $boutique; // Le service Boutique
    private $panier; // Tableau associatif idProduit => quantite
    private $commandeRepository;
    private $ligneCommandeRepository;
    // donc $this->panier[$i] = quantite du produit dont l'id = $i
    // constructeur du service


    public function __construct(SessionInterface $session, BoutiqueService $boutique, CommandeRepository $commandeRepository, LigneCommandeRepository $ligneCommandeRepository ) {
    // Récupération des services session et BoutiqueService
    $this->boutique = $boutique;
    $this->session = $session;
    // Récupération du panier en session s'il existe, init. à vide sinon
    //$this->session->clear(); // A utiliser si besoin de reset la session

    $this->panier = $session->get(self::PANIER_SESSION, array());// initialisation du Panier 
    $this->commandeRepository = $commandeRepository;
    $this->ligneCommandeRepository = $ligneCommandeRepository;


    }
    // renvoie le contenu du panier
    // tableau d'éléments [ "produit" => un produit, "quantite" => quantite ]
    public function getContenu() { 

        $produits = [];
        foreach($this->panier as $id=>$qte){
            $produit = $this->boutique->findProduitById($id);

            $produits[] = ["produit" => $produit, "quantite" => $qte];
        }

        return $produits;
    }    

    // renvoie le montant total du panier
    public function getTotal() { 

        $total = 0;
        

        foreach($this->panier as $id => $qte){
            
            $produit = $this->boutique->findProduitById($id);
            $prixUnitaire = $produit->getPrix();
            $total += $prixUnitaire * $qte;
        }

        return $total;

    }
    // renvoie le nombre de produits dans le panier
    public function getNbProduits() { 

        $quantiteTotal = 0;
        foreach($this->panier as $id => $qte){
            
            $quantiteTotal += $qte;
            
    }
        return $quantiteTotal;

    }

    //  ajoute au panier le produit $idProduit en quantite $quantite
    // si le produit est déjà présent dans le panier, on ajoute une quantité
    public function ajouterProduit(int $idProduit, int $quantite = 1) { 

        if(isset($this->panier[$idProduit])) {
            $this->panier[$idProduit] += $quantite;
            
        }else{
            $this->panier[$idProduit] = $quantite;
        }
        $this->session->set(self::PANIER_SESSION, $this->panier);

    }
    // enlève du panier le produit $idProduit en quantite $quantite
    // si la quqntité devait devenir < 0 on supprime le produit du panier
    public function enleverProduit(int $idProduit, int $quantite = 1) { 

        if($this->panier[$idProduit] > 0){
            $this->panier[$idProduit] -= $quantite;

        }else{
            $this->supprimerProduit($idProduit);
        }
        
        $this->session->set(self::PANIER_SESSION, $this->panier);

    }
    // supprime complètement le produit $idProduit du panier
    public function supprimerProduit(int $idProduit) { 

        unset($this->panier[$idProduit]);
        $this->session->set(self::PANIER_SESSION, $this->panier);

    }
    // vide complètement le panier
    public function vider() { 

        $this->panier = [];
        $this->session->set(self::PANIER_SESSION, $this->panier);
    }

    // Passe une commande en validant le panier
    // on vérifie que le panier n'est pas vide (bouton non accesssible normalement dans le template)
    // Creation d'une commande et des ligneCommandes
    // Ajout en BDD
    // On termine en vidant le panier
    public function panierToCommande(Usager $usager): Commande
        {
            $commande = new Commande();
            if($this->panier) {
                
                $commande->setUsager($usager)
                    ->setDateCommande(date_create())
                    ->setStatut("Confirmé");

                $this->commandeRepository->add($commande);

                foreach($this->panier as $idProduit => $quantite){
                    $produit = $this->boutique->findProduitById($idProduit);
                    $ligne = new LigneCommande();
                    $ligne->setCommande($commande)
                        ->setArticle($produit)
                        ->setPrix($produit->getPrix()*$quantite)
                        ->setQuantite($quantite);

                    $this->ligneCommandeRepository->add($ligne);
                }
                $this->session->set(self::PANIER_SESSION, $this->panier);

                $this->vider();
                
            }
            return $commande;
        }

    // permet de récupérer le prix total d'une commande
    public function getPrixCommande($commande): Float {


        $lignes = $this->ligneCommandeRepository->findByCommande($commande);
        $prix = 0.00;

        foreach( $lignes as $ligne) {
            $prix += $ligne->getPrix();
        }

        return $prix;
    }
}