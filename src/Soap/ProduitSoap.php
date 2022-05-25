<?php

namespace App\Soap;


/**
     * Class ProduitSoap
     * @package    \App\Soap
     * @author     MMF
     */
class ProduitSoap
{
    /**
     * @var int $id id du produit
     */
    public $id;

    /**
     * @var string $libelle libelle du produit
     */
    public $libelle;


    /**
     * @var string $texte texte du produit
     */
    public $texte;

    /**
     * @var float $prix prix du produit
     */

    /**
     * ProduitSoap constructor.
     * @param int $id
     * @param string $libelle
     */
    public function __construct($id, string $libelle)
    {
        $this->id = $id;
        $this->libelle = $libelle;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getLibelle(): string
    {
        return $this->libelle;
    }

    /**
     * @param string $libelle
     */
    public function setLibelle(string $libelle): void
    {
        $this->libelle = $libelle;
    }

    /**
     * @return string
     */
    public function getTexte(): ?string
    {
        return $this->texte;
    }

    /**
     * @param string $texte
     */
    public function setTexte(?string $texte): self
    {
        $this->texte = $texte;

        return $this;
    }

    /**
     * @return float
     */
    public function getPrix(): ?string
    {
        return $this->prix;
    }

    /**
     * @param float $prix
     */
    public function setPrix(string $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

}
