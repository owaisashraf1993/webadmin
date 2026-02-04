<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use MediaBundle\Entity\Media;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Staff
 *
 * @ORM\Table(name="staff_table")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\StaffRepository")
 */
class Staff
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 3,
     *      max = 50,
     * )
     * @ORM\Column(name="status", type="string", length=255)
     */
    private $status;


    /**
     * @var int
     *
     * @ORM\Column(name="position", type="integer")
     */
    private $position;


     /**
     * @var string
     * @ORM\Column(name="bio", type="text")
     */
    private $bio;

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
     * @ORM\ManyToOne(targetEntity="Team", inversedBy="histories")
     * @ORM\JoinColumn(name="team_id", referencedColumnName="id", nullable=false)
     */
    private $team;

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
     * @return Staff
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

    public function getStaff()
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
    * Get bio
    * @return  
    */
    public function getBio()
    {
        return $this->bio;
    }
    
    /**
    * Set bio
    * @return $this
    */
    public function setBio($bio)
    {
        $this->bio = $bio;
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
    * Set teamPartner and CEO of the companies ADELTE and EFS, Bartomeu served on the board of FC Barcelona during Joan Laporta's presidency (as head of the basketball section) along with Sandro Rosell, who resigned due to differences with the then president. He then served as Rosell's vice-president of Barcelona from July 2010 to January 2014 after they won the election with 61.35% of the vote of the members of the club. Following the resignation of Sandro Rosell on 23 January 2014, due to the so-called "Neymar case," Bartomeu was, following the club's constitution, elected, as the fortieth President of Barcelona, to complete Rosell's term. Bartomeu is being investigated in a case of alleged tax fraud over the signing of striker Neymar along with former president Sandro Rosell. He is set to stand trial after his appeal was rejected.[4][5]


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
    * Get status
    * @return  
    */
    public function getStatus()
    {
        return $this->status;
    }
    
    /**
    * Set status
    * @return $this
    */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }
}
