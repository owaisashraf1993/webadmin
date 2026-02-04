<?php 
namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Slide
 *
 * @ORM\Table(name="socials_table")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SocialRepository")
 */
class Social
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
     *      min = 1,
     *      max = 30,
     * )
     * @ORM\Column(name="social", type="string", length=255))
     */
    private $social;

     /**
     * @ORM\ManyToOne(targetEntity="MediaBundle\Entity\Media")
     * @ORM\JoinColumn(name="media_id", referencedColumnName="id")
     * @ORM\JoinColumn(nullable=true)
     */
    private $media;


    /**
     * @Assert\File(mimeTypes={"image/jpeg","image/png" },maxSize="40M")
     */
    private $file;

    /**
     * @var int
     *
     * @ORM\Column(name="position", type="integer",nullable=true)
     */
    private $position;


    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Url()
     * @ORM\Column(name="value", type="string")
     */
    private $value;

    /**
     * @var string
     * @ORM\Column(name="username", type="string",nullable=true)
     */
    private $username;

    /**
     * @var string
     * @ORM\Column(name="color", type="string")
     */
    private $color;

    /**
     * @ORM\ManyToOne(targetEntity="Player", inversedBy="socials")
     * @ORM\JoinColumn(name="player_id", referencedColumnName="id")
     */
    private $player;


    public function __construct()
    {
        $this->color = "29ACFF" ;
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

    public function getSocial()
    {
        return $this->social;
    }

    public function setSocial($social)
    {
        $this->social = $social;
    }
    /**
    * Get value
    * @return  
    */
    public function getValue()
    {
        return $this->value;
    }
    
    /**
    * Set value
    * @return $this
    */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }
    public function addPlayer(Player $player)
    {
        $this->player=$player;
    }
    public function getPlayer()
    {
       return $this->player;
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
    * Get media
    * @return  
    */
    public function getMedia()
    {
        return $this->media;
    }
    /**
    * Get file
    * @return  
    */
    public function getFile()
    {
        return $this->file;
    }
    
    /**
    * Set file
    * @return $this
    */
    public function setFile($file)
    {
        $this->file = $file;
        return $this;
    }
    /**
    * Set media
    * @return $this
    */
    public function setMedia($media)
    {
        $this->media = $media;
        return $this;
    }

    /**
    * Get color
    * @return  
    */
    public function getColor()
    {
        return $this->color;
    }
    
    /**
    * Set color
    * @return $this
    */
    public function setColor($color)
    {
        $this->color = $color;
        return $this;
    }
    /**
    * Get username
    * @return  
    */
    public function getUsername()
    {
        return $this->username;
    }
    
    /**
    * Set username
    * @return $this
    */
    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }
}