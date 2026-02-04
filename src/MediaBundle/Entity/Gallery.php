<?php

namespace MediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use MediaBundle\Entity\Media;

/**
 * @ORM\Entity(repositoryClass="MediaBundle\Repository\GalleryRepository")
 * @ORM\Table(name="gallery_table")
 */
class Gallery
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="titre", type="string", length=255, nullable=true)
     */
    private $titre;

    /**
     * @ORM\ManyToMany(targetEntity="Media")
     * @ORM\JoinTable(
     *     name="medias_gallerys_table",
     *     joinColumns={
     *         @ORM\JoinColumn(name="gallery_id", referencedColumnName="id", onDelete="CASCADE")
     *     },
     *     inverseJoinColumns={
     *         @ORM\JoinColumn(name="media_id", referencedColumnName="id", onDelete="CASCADE")
     *     }
     * )
     * @ORM\OrderBy({"date" = "DESC"})
     */
    private $medias;

    public function __construct()
    {
        $this->medias = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTitre()
    {
        return $this->titre;
    }

    public function setTitre($titre)
    {
        $this->titre = $titre;
        return $this;
    }

    public function addMedia(Media $media)
    {
        if (!$this->medias->contains($media)) {
            $this->medias[] = $media;
        }

        return $this;
    }

    public function removeMedia(Media $media)
    {
        $this->medias->removeElement($media);
        return $this;
    }

    public function getMedias()
    {
        return $this->medias;
    }
}
