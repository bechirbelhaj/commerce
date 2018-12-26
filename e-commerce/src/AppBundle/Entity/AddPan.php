<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AddPan
 *
 * @ORM\Table(name="add_pan")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AddPanRepository")
 */
class AddPan
{   
     /**
     * @var string
     *
     * @ORM\Column(name="client",type="string")
     */

    private $client;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="datec", type="datetime")
     */
    private $datec;

    /**
     * @var float
     *
     * @ORM\Column(name="PrixTotal", type="float")
     */
    private $prixTotal;

    /**
     * @var int
     *
     * @ORM\Column(name="qt", type="integer")
     */
    private $qt;
    /**
     *@ORM\ManyToOne(targetEntity="Produit",inversedBy="pans")
     *@ORM\JoinColumn(name="produit_id",referencedColumnName="id")
     */
    private $produit;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set datec
     *
     * @param \DateTime $datec
     *
     * @return AddPan
     */
    public function setDatec($datec)
    {
        $this->datec = $datec;

        return $this;
    }

    /**
     * Get datec
     *
     * @return \DateTime
     */
    public function getDatec()
    {
        return $this->datec;
    }

    /**
     * Set prixTotal
     *
     * @param float $prixTotal
     *
     * @return AddPan
     */
    public function setPrixTotal($prixTotal)
    {
        $this->prixTotal = $prixTotal;

        return $this;
    }

    /**
     * Get prixTotal
     *
     * @return float
     */
    public function getPrixTotal()
    {
        return $this->prixTotal;
    }

    /**
     * Set qt
     *
     * @param integer $qt
     *
     * @return AddPan
     */
    public function setQt($qt)
    {
        $this->qt = $qt;

        return $this;
    }

    /**
     * Get qt
     *
     * @return int
     */
    public function getQt()
    {
        return $this->qt;
    }

    /**
     * Set produit
     *
     * @param \AppBundle\Entity\Produit $produit
     *
     * @return AddPan
     */
    public function setProduit(\AppBundle\Entity\Produit $produit = null)
    {
        $this->produit = $produit;

        return $this;
    }

    /**
     * Get produit
     *
     * @return \AppBundle\Entity\Produit
     */
    public function getProduit()
    {
        return $this->produit;
    }

    /**
     * Set client
     *
     * @param string $client
     *
     * @return AddPan
     */
    public function setClient($client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client
     *
     * @return string
     */
    public function getClient()
    {
        return $this->client;
    }
}
