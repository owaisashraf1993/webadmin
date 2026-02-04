<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Rowidator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Rowidator\Constraints\UniqueEntity;
use MediaBundle\Entity\Media;
/**
 * Info
 *
 * @ORM\Table(name="infos_table")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\InfoRepository")
 */
class Info
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;


    /**
     * @var string
     * @ORM\Column(name="away", type="string", length=255)
     */
    private $away;

    /**
     * @var string
     * @ORM\Column(name="home", type="string", length=255)
     */
    private $home;

    /**
     * @ORM\ManyToOne(targetEntity="MatchEntity" ,inversedBy="infos")
     * @ORM\JoinColumn(name="club_id", referencedColumnName="id", nullable=true)
     */
    private $match;

    /**
    * Get id
    * @return  
    */
    public function getId()
    {
        return $this->id;
    }
    
    /**
    * Get name
    * @return  
    */
    public function getName()
    {
        return $this->name;
    }
    
    /**
    * Set name
    * @return $this
    */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
    * Get away
    * @return  
    */
    public function getAway()
    {
        return $this->away;
    }
    
    /**
    * Set away
    * @return $this
    */
    public function setAway($away)
    {
        $this->away = $away;
        return $this;
    }

    /**
    * Get home
    * @return  
    */
    public function getHome()
    {
        return $this->home;
    }
    
    /**
    * Set home
    * @return $this
    */
    public function setHome($home)
    {
        $this->home = $home;
        return $this;
    }

    /**
    * Get match
    * @return  
    */
    public function getMatch()
    {
        return $this->match;
    }
    
    /**
    * Set match
    * @return $this
    */
    public function setMatch($match)
    {
        $this->match = $match;
        return $this;
    }
}
   
