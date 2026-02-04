<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use MediaBundle\Entity\Media;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ActionRepository")
 * @ORM\Table(name="action_table")
 */
class Action
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(min=3, max=50)
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(name="position", type="integer")
     */
    private $position;

    /**
     * @Assert\File(mimeTypes={"image/jpeg","image/png"}, maxSize="40M")
     */
    private $file;

    /**
     * @ORM\ManyToOne(targetEntity="MediaBundle\Entity\Media")
     * @ORM\JoinColumn(name="media_id", referencedColumnName="id", nullable=false)
     */
    private $media;

    public function __construct()
    {
        // Remove $channels initialization unless you define it
        // $this->channels = new ArrayCollection();
    }

    // -------- Getters & Setters --------

    public function getId()
    {
        return $this->id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    public function getPosition()
    {
        return $this->position;
    }

    public function setPosition($position)
    {
        $this->position = $position;
        return $this;
    }

    public function getMedia()
    {
        return $this->media;
    }

    public function setMedia(Media $media)
    {
        $this->media = $media;
        return $this;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function setFile($file)
    {
        $this->file = $file;
        return $this;
    }

    public function __toString()
    {
        return $this->title ?: '';
    }
}
