<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CategoryRepository")
 */
class Category
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Artist", mappedBy="category")
     */
    private $artists;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Work", mappedBy="category")
     */
    private $works;

    public function __construct()
    {
        $this->artists = new ArrayCollection();
        $this->works = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getArtists()
    {
        return $this->artists;
    }

    /**
     * @return mixed
     */
    public function getWorks()
    {
        return $this->works;
    }


}
