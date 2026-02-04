<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use MediaBundle\Entity\Media;
use UserBundle\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * Trophy
 *
 * @ORM\Table(name="trophy_table")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TrophyRepository")
 */
class Trophy
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
     *      min = 3
     * )
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $title;



    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 15
     * )
     * @ORM\Column(name="content", type="text", nullable=true)
     */
    private $content;


    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 5
     * )
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var int
     *
     * @ORM\Column(name="number", type="string", length=10)
     */
    private $number;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

     /**
     * @ORM\ManyToOne(targetEntity="MediaBundle\Entity\Media")
     * @ORM\JoinColumn(name="media_id", referencedColumnName="id")
     * @ORM\JoinColumn(nullable=false)
     */
    private $media;

     /**
     * @ORM\ManyToOne(targetEntity="MediaBundle\Entity\Media")
     * @ORM\JoinColumn(name="icon_id", referencedColumnName="id")
     * @ORM\JoinColumn(nullable=false)
     */
    private $icon;

    /**
     * @var bool
     *
     * @ORM\Column(name="enabled", type="boolean")
     */
    private $enabled;

    /**
     * @var int
     *
     * @ORM\Column(name="position", type="integer")
     */
    private $position;


    /**
     * @Assert\File(mimeTypes={"image/jpeg","image/png" },maxSize="200M")
     */
    private $file;

    /**
     * @Assert\File(mimeTypes={"image/jpeg","image/png" },maxSize="200M")
     */
    private $fileicon;

     /**
     * @ORM\ManyToOne(targetEntity="Team", inversedBy="histories")
     * @ORM\JoinColumn(name="team_id", referencedColumnName="id", nullable=false)
     */
    private $team;

    public function __construct()
    {
        $this->created= new \DateTime();
    }

   /**
    * Get id
    * @return  
    */
    public function getId()
    {
        return $this->id;
    }
    
    /**
    * Set id
    * @return $this
    */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Wallpaper
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
     * Set created
     *
     * @param \DateTime $created
     * @return Wallpaper
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated()
    {
        return $this->created;
    }

   
    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return Album
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean 
     */
    public function getEnabled()
    {
        return $this->enabled;
    }
   
    /**
     * Set user
     *
     * @param string $user
     * @return Wallpaper
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return string 
     */
    public function getUser()
    {
        return $this->user;
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
    public function __toString()
    {
        return $this->getTitle();
    }


    /**
    * Get content
    * @return  
    */
    public function getContent()
    {
        return str_replace('\n', "</br>", $this->content);
    }
    
    /**
    * Set content
    * @return $this
    */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }
    
    /**
    * Get number
    * @return  
    */
    public function getNumber()
    {
        return $this->number;
    }
    
    /**
    * Set number
    * @return $this
    */
    public function setNumber($number)
    {
        $this->number = $number;
        return $this;
    }


    /**
    * Get team
    * @return  
    */
    public function getTeam()
    {
        return $this->team;
    }
    
    /**
    * Set team
    * @return $this
    */
    public function setTeam($team)
    {
        $this->team = $team;
        return $this;
    }

    /**
    * Get position
    * @return  
    */
    public function getPosition()
    {
        return $this->position;
    }
    
    /**
    * Set position
    * @return $this
    */
    public function setPosition($position)
    {
        $this->position = $position;
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
    * Get description
    * @return  
    */
    public function getDescription()
    {
        return $this->description;
    }
    
    /**
    * Set description
    * @return $this
    */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }
}
