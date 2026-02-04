<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use MediaBundle\Entity\Media;

/**
 * Competition
 *
 * @ORM\Table(name="competition_table")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CompetitionRepository")
 */
class Competition
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="abbreviation", type="string", length=255)
     */

    private $abbreviation;

    /**
     * @var int
     *
     * @ORM\Column(name="position", type="integer")
     */
    private $position;


     /**
     * @var string
     * @ORM\Column(name="season", type="string", length=255)
     */
    private $season;

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
     * @var bool
     *
     * @ORM\Column(name="enabled", type="boolean")
     */
    private $enabled;

    /**
    * @ORM\OneToMany(targetEntity="Table", mappedBy="competition",cascade={"persist", "remove"})
    * @ORM\OrderBy({"position" = "asc"})
    */
    private $tables;

    /**
    * @ORM\OneToMany(targetEntity="MatchEntity", mappedBy="competition",cascade={"persist", "remove"})
    * @ORM\OrderBy({"datetime" = "asc"})
    */
    private $matches;
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
     * Set name
     *
     * @param string $name
     * @return Competition
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    public function __toString()
    {
        return $this->name;
    }

    public function getCompetition()
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
    * Get season
    * @return  
    */
    public function getSeason()
    {
        return $this->season;
    }
    
    /**
    * Set season
    * @return $this
    */
    public function setSeason($season)
    {
        $this->season = $season;
        return $this;
    }
    /**
     * Set abbreviation
     *
     * @param string $abbreviation
     * @return Article
     */
    public function setAbbreviation($abbreviation)
    {
        $this->abbreviation = $abbreviation;

        return $this;
    }

    /**
     * Get abbreviation
     *
     * @return string 
     */
    public function getAbbreviation()
    {
        return $this->abbreviation;
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
    * Get tables
    * @return  
    */
    public function getTables()
    {
        return $this->tables;
    }
    
    /**
    * Set tables
    * @return $this
    */
    public function setTables($tables)
    {
        $this->tables = $tables;
        return $this;
    }

    /**
    * Get matches
    * @return  
    */
    public function getMatches()
    {
        return $this->matches;
    }
    
    /**
    * Set matches
    * @return $this
    */
    public function setMatches($matches)
    {
        $this->matches = $matches;
        return $this;
    }
}
