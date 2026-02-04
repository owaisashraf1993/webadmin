<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use MediaBundle\Entity\Media;
/**
 * Match
 *
 * @ORM\Table(name="match_table")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MatchRepository")
 */
class MatchEntity
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
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @Assert\NotBlank()
     * @ORM\ManyToOne(targetEntity="Club")
     * @ORM\JoinColumn(name="home_club_id", referencedColumnName="id", nullable=false)
     */
    private $homeclub;

    /**
     * @Assert\NotBlank()
     * @ORM\ManyToOne(targetEntity="Club")
     * @ORM\JoinColumn(name="away_club_id", referencedColumnName="id", nullable=false)
     */
    private $awayclub;

    /**
     * @ORM\ManyToOne(targetEntity="Competition")
     * @ORM\JoinColumn(name="competition_id", referencedColumnName="id", nullable=false)
     */
    private $competition;


    /**
     * @var string
     * @ORM\Column(name="awayresult", type="string", length=255 ,nullable=true)
     */
    private $awayresult;


    /**
     * @var string
     * @ORM\Column(name="homeresult", type="string", length=255 ,nullable=true)
     */
    private $homeresult;


    /**
     * @var string
     * @ORM\Column(name="awaysubresult", type="string", length=255 ,nullable=true)
     */
    private $awaysubresult;


    /**
     * @var string
     * @ORM\Column(name="homesubresult", type="string", length=255 ,nullable=true)
     */
    private $homesubresult;

    /**
     * @var string
     * @ORM\Column(name="stadium", type="string", length=255 ,nullable=true)
     */
    private $stadium;

    /**
     * @var string
     * @Assert\Url()
     * @ORM\Column(name="highlights", type="string", length=255 ,nullable=true)
     */
    private $highlights;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="datetime", type="datetime")
     */
    private $datetime;


    /**
     * @var string
     * @ORM\Column(name="state", type="string", length=255)
     */
    private $state;


    /**
     * @ORM\OneToMany(targetEntity="Info", mappedBy="match",cascade={"persist", "remove"})
     */
    protected $infos;

    /**
     * @ORM\OneToMany(targetEntity="Event", mappedBy="match",cascade={"persist", "remove"})
        * @ORM\OrderBy({"position" = "asc"})
     */
    protected $events;

    /**
     * @var bool
     *
     * @ORM\Column(name="enabled", type="boolean")
     */
    private $enabled;

    /**
     * @var bool
     *
     * @ORM\Column(name="featured", type="boolean")
     */
    private $featured;

    public function __construct()
    {
        $this->infos = new ArrayCollection();
        $this->events = new ArrayCollection();
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
    * Get awayclub
    * @return  
    */
    public function getAwayclub()
    {
        return $this->awayclub;
    }
    
    /**
    * Set awayclub
    * @return $this
    */
    public function setAwayclub($awayclub)
    {
        $this->awayclub = $awayclub;
        return $this;
    }

    /**
    * Get homeclub
    * @return  
    */
    public function getHomeclub()
    {
        return $this->homeclub;
    }
    
    /**
    * Set homeclub
    * @return $this
    */
    public function setHomeclub($homeclub)
    {
        $this->homeclub = $homeclub;
        return $this;
    }
    /**
    * Get datetime
    * @return  
    */
    public function getDatetime()
    {
        return $this->datetime;
    }
    
    /**
    * Set datetime
    * @return $this
    */
    public function setDatetime($datetime)
    {
        $this->datetime = $datetime;
        return $this;
    }
    /**
    * Get competition
    * @return  
    */
    public function getCompetition()
    {
        return $this->competition;
    }
    
    /**
    * Set competition
    * @return $this
    */
    public function setCompetition($competition)
    {
        $this->competition = $competition;
        return $this;
    }

    /**
    * Get state
    * @return  
    */
    public function getState()
    {
        return $this->state;
    }
    
    /**
    * Set state
    * @return $this
    */
    public function setState($state)
    {
        $this->state = $state;
        return $this;
    }

    /**
    * Get homesubresult
    * @return  
    */
    public function getHomesubresult()
    {
        return $this->homesubresult;
    }
    
    /**
    * Set homesubresult
    * @return $this
    */
    public function setHomesubresult($homesubresult)
    {
        $this->homesubresult = $homesubresult;
        return $this;
    }

    /**
    * Get homeresult
    * @return  
    */
    public function getHomeresult()
    {
        return $this->homeresult;
    }
    
    /**
    * Set homeresult
    * @return $this
    */
    public function setHomeresult($homeresult)
    {
        $this->homeresult = $homeresult;
        return $this;
    }
    /**
    * Get awaysubresult
    * @return  
    */
    public function getAwaysubresult()
    {
        return $this->awaysubresult;
    }
    
    /**
    * Set awaysubresult
    * @return $this
    */
    public function setAwaysubresult($awaysubresult)
    {
        $this->awaysubresult = $awaysubresult;
        return $this;
    }

    /**
    * Get awayresult
    * @return  
    */
    public function getAwayresult()
    {
        return $this->awayresult;
    }
    
    /**
    * Set awayresult
    * @return $this
    */
    public function setAwayresult($awayresult)
    {
        $this->awayresult = $awayresult;
        return $this;
    }

    /**
    * Get title
    * @return  
    */
    public function getTitle()
    {
        return $this->title;
    }
    
    /**
    * Set title
    * @return $this
    */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }
    /**
    * Get stadium
    * @return  
    */
    public function getStadium()
    {
        return $this->stadium;
    }
    
    /**
    * Set stadium
    * @return $this
    */
    public function setStadium($stadium)
    {
        $this->stadium = $stadium;
        return $this;
    }

    /**
    * Get highlights
    * @return  
    */
    public function getHighlights()
    {
        return $this->highlights;
    }
    
    /**
    * Set highlights
    * @return $this
    */
    public function setHighlights($highlights)
    {
        $this->highlights = $highlights;
        return $this;
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
    /**
    * Get infos
    * @return  
    */
    public function getInfos()
    {
        return $this->infos;
    }
    
    /**
    * Set infos
    * @return $this
    */
    public function setInfos($infos)
    {
        $this->infos = $infos;
        return $this;
    }
    public function addInfo(Info $info)
    {
        $info->setMatch($this);

        $this->infos->add($info);
    }
    public function removeInfo(Info $info)
    {
        $this->infos->removeElement($info);
    }

    /**
    * Get featured
    * @return  
    */
    public function getFeatured()
    {
        return $this->featured;
    }
    
    /**
    * Set featured
    * @return $this
    */
    public function setFeatured($featured)
    {
        $this->featured = $featured;
        return $this;
    }
    /**
    * Get events
    * @return  
    */
    public function getEvents()
    {
        return $this->events;
    }
    
    /**
    * Set events
    * @return $this
    */
    public function setEvents($events)
    {
        $this->events = $events;
        return $this;
    }
    public function __toString()
    {
       return $this->homeclub->getName()." VS ".$this->awayclub->getName() . " (".$this->title.")";
    }
    public function getFullname()
    {
       return $this->competition->getName() ." : ".$this->homeclub->getName()." Vs ".$this->awayclub->getName() . " (".$this->title.")";
    }
    public function getName()
    {
       return $this->competition->getAbbreviation() ." : ".$this->homeclub->getName()." Vs ".$this->awayclub->getName() ;
    }
}
