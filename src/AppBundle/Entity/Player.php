<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use MediaBundle\Entity\Media;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Player
 *
 * @ORM\Table(name="player_table")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PlayerRepository")
 */
class Player
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
     *      max = 25,
     * )
     * @ORM\Column(name="fname", type="string", length=255)
     */
    private $fname;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 3,
     *      max = 25,
     * )
     * @ORM\Column(name="lname", type="string", length=255)
     */
    private $lname;

    /**
     * @var string
     * @ORM\Column(name="born", type="date")
     */
    private $born;

    /**
     * @var string
     * @ORM\Column(name="number", type="string", length=255)
     */
    private $number;

    /**
     * @var string
     * @ORM\Column(name="height", type="string", length=255)
     */
    private $height;


    /**
     * @var string
     * @ORM\Column(name="weight", type="string", length=255)
     */
    private $weight;


    /**
     * @var string
     * @ORM\Column(name="position", type="integer", )
     */
    private $position;

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
     * @ORM\ManyToOne(targetEntity="Position", inversedBy="players")
     * @ORM\JoinColumn(name="position_id", referencedColumnName="id", nullable=false)
     */
    private $post;


     /**
     * @ORM\ManyToOne(targetEntity="Country", inversedBy="players")
     * @ORM\JoinColumn(name="country_id", referencedColumnName="id", nullable=false)
     */
    private $country;

    /**
     * @ORM\OneToMany(targetEntity="Statistic", mappedBy="player",cascade={"persist", "remove"})
     */
    protected $statistics;

    /**
     * @ORM\OneToMany(targetEntity="Social", mappedBy="player",cascade={"persist", "remove"})
     */
    protected $socials;

    public function __construct()
    {
        $this->statistics = new ArrayCollection();
        $this->socials = new ArrayCollection();

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
    * Get fname
    * @return  
    */
    public function getFname()
    {
        return $this->fname;
    }
    
    /**
    * Set fname
    * @return $this
    */
    public function setFname($fname)
    {
        $this->fname = $fname;
        return $this;
    }

    /**
    * Get lname
    * @return  
    */
    public function getLname()
    {
        return $this->lname;
    }
    
    /**
    * Set lname
    * @return $this
    */
    public function setLname($lname)
    {
        $this->lname = $lname;
        return $this;
    }

    public function __toString()
    {
        return $this->fname+" "+$this->lname;
    }

    public function getPlayer()
    {
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
     * @return image
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
    * Get height
    * @return  
    */
    public function getHeight()
    {
        return $this->height;
    }
    
    /**
    * Set height
    * @return $this
    */
    public function setHeight($height)
    {
        $this->height = $height;
        return $this;
    }

    /**
    * Get weight
    * @return  
    */
    public function getWeight()
    {
        return $this->weight;
    }
    
    /**
    * Set weight
    * @return $this
    */
    public function setWeight($weight)
    {
        $this->weight = $weight;
        return $this;
    }
    /**
    * Get born
    * @return  
    */
    public function getBorn()
    {
        return $this->born;
    }
    
    /**
    * Set born
    * @return $this
    */
    public function setBorn($born)
    {
        $this->born = $born;
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
    * Get post
    * @return  
    */
    public function getPost()
    {
        return $this->post;
    }
    
    /**
    * Set post
    * @return $this
    */
    public function setPost($post)
    {
        $this->post = $post;
        return $this;
    }

    /**
    * Get country
    * @return  
    */
    public function getCountry()
    {
        return $this->country;
    }
    
    /**
    * Set country
    * @return $this
    */
    public function setCountry($country)
    {
        $this->country = $country;
        return $this;
    }
    public function getStatistics()
    {
        return $this->statistics;
    }
    public function addStatistic(Statistic $statistic)
    {
        $statistic->addPlayer($this);

        $this->statistics->add($statistic);
    }
    public function removeStatistic(Statistic $statistic)
    {
        $this->statistics->removeElement($statistic);
    }

    public function getSocials()
    {
        return $this->socials;
    }
    public function addSocial(Social $social)
    {
        $social->addPlayer($this);

        $this->socials->add($social);
    }
    public function removeSocial(Social $social)
    {
        $this->socials->removeElement($social);
    }
}
