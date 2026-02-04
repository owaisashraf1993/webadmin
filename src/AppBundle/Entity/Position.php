<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use MediaBundle\Entity\Media;
/**
 * Position
 *
 * @ORM\Table(name="position_table")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PositionRepository")
 */
class Position
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
     * @var int
     *
     * @ORM\Column(name="position", type="integer")
     */
    private $position;


     /**
     * @ORM\ManyToOne(targetEntity="Team", inversedBy="positions")
     * @ORM\JoinColumn(name="team_id", referencedColumnName="id", nullable=false)
     */
    private $team;


    /**
    * @ORM\OneToMany(targetEntity="Player", mappedBy="post",cascade={"persist", "remove"})
    * @ORM\OrderBy({"position" = "asc"})
    */
    private $players;

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
     * @return Position
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
    * Get Team
    * @return  
    */
    public function getTeam()
    {
        return $this->team;
    }
    
    /**
    * Set Team
    * @return $this
    */
    public function setTeam($team)
    {
        $this->team = $team;
        return $this;
    }

    public function __toString(){
        return $this->title;
    }

    /**
    * Get players
    * @return  
    */
    public function getPlayers()
    {
        return $this->players;
    }
    
    /**
    * Set players
    * @return $this
    */
    public function setPlayers($players)
    {
        $this->players = $players;
        return $this;
    }
}
