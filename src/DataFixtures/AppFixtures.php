<?php

namespace App\DataFixtures;

use App\Entity\Categorie;
use App\Entity\Produit;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    // constantes permettant de lier un produit à une catégorie
    private const CATEGORIE_LEGUME = "legume";
    private const CATEGORIE_FRUIT = "fruit";
    private const CATEGORIE_JUNK_FOOD = "junk food";

    // liste des catégories insérés dans la BD
    private $categories = [
        [
            "id" => 1,
            "libelle" => "Fruits",
            "visuel" => "images/fruits.jpg",
            "texte" => "De la passion ou de ton imagination",
        ],
        [
            "id" => 3,
            "libelle" => "Junk Food",
            "visuel" => "images/junk_food.jpg",
            "texte" => "Chère et cancérogène, tu es prévenu(e)",
        ],
        [
            "id" => 2,
            "libelle" => "Légumes",
            "visuel" => "images/legumes.jpg",
            "texte" => "Plus tu en manges, moins tu en es un"],
    ];

    // liste des produits insérés dans la BD
    private $produits = [
        [
            "id" => 1,
            "idCategorie" => 1,
            "libelle" => "Pomme",
            "texte" => "Elle est bonne pour la tienne",
            "visuel" => "images/pommes.jpg",
            "prix" => 3.42
        ],
        [
            "id" => 2,
            "idCategorie" => 1,
            "libelle" => "Poire",
            "texte" => "Ici tu n'en es pas une",
            "visuel" => "images/poires.jpg",
            "prix" => 2.11
        ],
        [
            "id" => 3,
            "idCategorie" => 1,
            "libelle" => "Pêche",
            "texte" => "Elle va te la donner",
            "visuel" => "images/peche.jpg",
            "prix" => 2.84
        ],
        [
            "id" => 4,
            "idCategorie" => 2,
            "libelle" => "Carotte",
            "texte" => "C'est bon pour ta vue",
            "visuel" => "images/carottes.jpg",
            "prix" => 2.90
        ],
        [
            "id" => 5,
            "idCategorie" => 2,
            "libelle" => "Tomate",
            "texte" => "Fruit ou Légume ? Légume",
            "visuel" => "images/tomates.jpg",
            "prix" => 1.70
        ],
        [
            "id" => 6,
            "idCategorie" => 2,
            "libelle" => "Chou Romanesco",
            "texte" => "Mange des fractales",
            "visuel" => "images/romanesco.jpg",
            "prix" => 1.81
        ],
        [
            "id" => 7,
            "idCategorie" => 3,
            "libelle" => "Nutella",
            "texte" => "C'est bon, sauf pour ta santé",
            "visuel" => "images/nutella.jpg",
            "prix" => 4.50
        ],
        [
            "id" => 8,
            "idCategorie" => 3,
            "libelle" => "Pizza",
            "texte" => "Y'a pas pire que za",
            "visuel" => "images/pizza.jpg",
            "prix" => 8.25
        ],
        [
            "id" => 9,
            "idCategorie" => 3,
            "libelle" => "Oreo",
            "texte" => "Seulement si tu es un smartphone",
            "visuel" => "images/oreo.jpg",
            "prix" => 2.50
        ],
    ];

    // fonction d'ajout dans la BD
    // premiere boucle intègre les categories en base
    // récuperation du libelle pour ajouter une référence sur la catégorie
    // deuxième boucle pour insérer les produits
    // jointure avec la catégorie faite en fonction des produits à l'aide de la référence
    public function load(ObjectManager $manager): void
    {
        
        foreach($this->categories as $i => $metadata) {

            $categorie = new Categorie();
            $categorie->setLibelle($metadata["libelle"]);
            $categorie->setVisuel($metadata["visuel"]);
            $categorie->setTexte($metadata["texte"]);

            // gestion de la peristence de l'objet Categorie
            $manager->persist($categorie);

            switch ($metadata["libelle"]) {

                case "Fruits":
                  $this->addReference(self::CATEGORIE_FRUIT, $categorie);
                  break;
                
                case "Junk Food":
                    $this->addReference(self::CATEGORIE_JUNK_FOOD, $categorie);
                    break;
                
                case "Légumes":
                    $this->addReference(self::CATEGORIE_LEGUME, $categorie);
                    break;
            }
            
        }

        // mise à jour de la BD 
        $manager->flush();

        foreach($this->produits as $i => $metadata) {

            $produit = new Produit();
            $produit->setLibelle($metadata["libelle"]);
            $produit->setTexte($metadata["texte"]);
            $produit->setVisuel($metadata["visuel"]);
            $produit->setPrix($metadata["prix"]);

            switch ($metadata["idCategorie"]) {
                
                case 1:
                    $produit->setCategorie($this->getReference(self::CATEGORIE_FRUIT));
                    break;
                
                case 2:
                    $produit->setCategorie($this->getReference(self::CATEGORIE_LEGUME));
                    break;

                case 3:
                    $produit->setCategorie($this->getReference(self::CATEGORIE_JUNK_FOOD));
                    break;
            }
            
            // gestion de la peristence de l'objet Produit
            $manager->persist($produit);
        }

        // mise à jour de la BD 
        $manager->flush();


    }
}
