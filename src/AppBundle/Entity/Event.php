<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Rowidator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Rowidator\Constraints\UniqueEntity;
use MediaBundle\Entity\Media;
/**
 * Event
 *
 * @ORM\Table(name="event_table")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EventRepository")
 */
class Event
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
     * @ORM\Column(name="time", type="string", length=255)
     */
    private $time;

    /**
     * @var string
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;


    /**
     * @var string
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    private $title;


    /**
     * @var string
     * @ORM\Column(name="subtitle", type="string", length=255, nullable=true)
     */
    private $subtitle;

    /**
     * @var int
     *
     * @ORM\Column(name="position", type="integer")
     */
    private $position;

    /**
     * @ORM\ManyToOne(targetEntity="MatchEntity" ,inversedBy="infos")
     * @ORM\JoinColumn(name="match_id", referencedColumnName="id", nullable=false)
     */
    private $match;

    /**
     * @ORM\ManyToOne(targetEntity="Action" )
     * @ORM\JoinColumn(name="action_id", referencedColumnName="id", nullable=false)
     */
    private $action;

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
    * Get time
    * @return  
    */
    public function getTime()
    {
        return $this->time;
    }
    
    /**
    * Set time
    * @return $this
    */
    public function setTime($time)
    {
        $this->time = $time;
        return $this;
    }

    /**
    * Get Position
    * @return  
    */
    public function getPosition()
    {
        return $this->position;
    }
    
    /**
    * Set Position
    * @return $this
    */
    public function setPosition($position)
    {
        $this->position = $position;
        return $this;
    }

    /**
    * Get action
    * @return  
    */
    public function getAction()
    {
        return $this->action;
    }
    
    /**
    * Set action
    * @return $this
    */
    public function setAction($action)
    {
        $this->action = $action;
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
}
   
