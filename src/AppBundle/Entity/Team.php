<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use MediaBundle\Entity\Media;
/**
 * Team
 *
 * @ORM\Table(name="team_table")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TeamRepository")
 */
class Team
{
   
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 3,
     *      max = 50,
     * )
     * @ORM\Column(name="title", type="string", length=255))
     */
    private $title;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 3,
     *      max = 50,
     * )
     * @ORM\Column(name="subtitle", type="string", length=255))
     */
    private $subtitle;

    /**
     * @var string
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @var int
     *
     * @ORM\Column(name="position", type="integer")
     */
    private $position;

    /**
     * @var bool
     *
     * @ORM\Column(name="enabled", type="boolean")
     */
    private $enabled;

    /**
     * @Assert\File(mimeTypes={"image/jpeg","image/png" },maxSize="40M")
     */
    private $file;
     /**
     * @ORM\ManyToOne(targetEntity="MediaBundle\Entity\Media")
     * @ORM\JoinColumn(name="media_id", referencedColumnName="id")
     * @ORM\JoinColumn(nullable=false)
     */
    private $media;

    /**
     * @Assert\File(mimeTypes={"image/jpeg","image/png" },maxSize="40M")
     */
    private $fileicon;
     /**
     * @ORM\ManyToOne(targetEntity="MediaBundle\Entity\Media")
     * @ORM\JoinColumn(name="icon_id", referencedColumnName="id")
     * @ORM\JoinColumn(nullable=false)
     */
    private $icon;

    /**
    * @ORM\OneToMany(targetEntity="Position", mappedBy="team",cascade={"persist", "remove"})
    * @ORM\OrderBy({"position" = "asc"})
    */
    private $positions;

    /**
    * @ORM\OneToMany(targetEntity="Article", mappedBy="team",cascade={"persist", "remove"})
    * @ORM\OrderBy({"position" = "asc"})
    */
    private $articles;

    /**
    * @ORM\OneToMany(targetEntity="Staff", mappedBy="team",cascade={"persist", "remove"})
    * @ORM\OrderBy({"position" = "asc"})
    */
    private $staffs;


    /**
    * @ORM\OneToMany(targetEntity="Trophy", mappedBy="team",cascade={"persist", "remove"})
    * @ORM\OrderBy({"position" = "asc"})
    */
    private $trophies;

    public function __construct()
    {
            $this->articles = new ArrayCollection();
            $this->staffs = new ArrayCollection();
            $this->positions = new ArrayCollection();
            $this->players = new ArrayCollection();
    }
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Team
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set position
     *
     * @param integer $position
     * @return Position
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return integer 
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
    * Get enabled
    * @return  
    */
    public function getEnabled()
    {
        return $this->enabled;
    }
    
    /**
    * Set enabled
    * @return $this
    */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
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
    /**
     * Set media
     *
     * @param string $media
     * @return Wallpaper
     */
    public function setMedia(Media $media)
    {
        $this->media = $media;

        return $this;
    }

    /**
     * Get media
     *
     * @return string 
     */
    public function getMedia()
    {
        return $this->media;
    }

    /**
    * Get positions
    * @return  
    */
    public function getPositions()
    {
        return $this->positions;
    }
    
    /**
    * Set positions
    * @return $this
    */
    public function setPositions($positions)
    {
        $this->positions = $positions;
        return $this;
    }
    public function __toString(){
        return $this->title;
    }
    /**
    * Get subtitle
    * @return  
    */
    public function getSubtitle()
    {
        return $this->subtitle;
    }
    
    /**
    * Set subtitle
    * @return $this
    */
    public function setSubtitle($subtitle)
    {
        $this->subtitle = $subtitle;
        return $this;
    }
    /**
    * Get type
    * @return  
    */
    public function getType()
    {
        return $this->type;
    }
    
    /**
    * Set type
    * @return $this
    */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
    * Get articles
    * @return  
    */
    public function getArticles()
    {
        return $this->articles;
    }
    
    /**
    * Set articles
    * @return $this
    */
    public function setArticles($articles)
    {
        $this->articles = $articles;
        return $this;
    }
    /**
    * Get trophies
    * @return  
    */
    public function getTrophies()
    {
        return $this->trophies;
    }
    
    /**
    * Set trophies
    * @return $this
    */
    public function setTrophies($trophies)
    {
        $this->trophies = $trophies;
        return $this;
    }
    /**
    * Get icon
    * @return  
    */
    public function getIcon()
    {
        return $this->icon;
    }
    
    /**
    * Set icon
    * @return $this
    */
    public function setIcon($icon)
    {
        $this->icon = $icon;
        return $this;
    }

    /**
    * Get fileicon
    * @return  
    */
    public function getFileicon()
    {
        return $this->fileicon;
    }
    
    /**
    * Set fileicon
    * @return $this
    */
    public function setFileicon($fileicon)
    {
        $this->fileicon = $fileicon;
        return $this;
    }

    /**
    * Get staffs
    * @return  
    */
    public function getStaffs()
    {
        return $this->staffs;
    }
    
    /**
    * Set staffs
    * @return $this
    */
    public function setStaffs($staffs)
    {
        $this->staffs = $staffs;
        return $this;
    }
}
