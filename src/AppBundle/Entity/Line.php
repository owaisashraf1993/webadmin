<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Rowidator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Rowidator\Constraints\UniqueEntity;
use MediaBundle\Entity\Media;
/**
 * Line
 *
 * @ORM\Table(name="line_table")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\LineRepository")
 */
class Line
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
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;


    /**
     * @var string
     * @ORM\Column(name="color", type="string", length=255,nullable=true)
     */
    private $color;

    /**
     * @var string
     * @ORM\Column(name="prefix", type="string", length=255)
     */
    private $prefix;

    /**
     * @var string
     * @ORM\Column(name="label", type="string", length=255,nullable=true)
     */
    private $label;

    /**
     * @var int
     *
     * @ORM\Column(name="position", type="integer")
     */
    private $position;


    /**
     * @var int
     *
     * @ORM\Column(name="row1", type="string",length=255,nullable=true)
     */
    private $row1;


    /**
     * @var int
     *
     * @ORM\Column(name="row2", type="string",length=255,nullable=true)
     */
    private $row2;

    /**
     * @var int
     *
     * @ORM\Column(name="row3", type="string",length=255,nullable=true)
     */
    private $row3;

    /**
     * @var int
     *
     * @ORM\Column(name="row4", type="string",length=255,nullable=true)
     */
    private $row4;

    /**
     * @var int
     *
     * @ORM\Column(name="row5", type="string",length=255,nullable=true)
     */
    private $row5;

    /**
     * @var int
     *
     * @ORM\Column(name="row6", type="string",length=255,nullable=true)
     */
    private $row6;

    /**
     * @var int
     *
     * @ORM\Column(name="row7", type="string",length=255,nullable=true)
     */
    private $row7;

     /**
     * @var int
     *
     * @ORM\Column(name="row8", type="string",length=255,nullable=true)
     */
    private $row8;
    /**
     * @ORM\ManyToOne(targetEntity="Table", inversedBy="lines")
     * @ORM\JoinColumn(name="table_id", referencedColumnName="id")
     */
    private $table;

    /**
     * @ORM\ManyToOne(targetEntity="Club")
     * @ORM\JoinColumn(name="club_id", referencedColumnName="id", nullable=true)
     */
    private $club;

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
    * Get prefix
    * @return  
    */
    public function getPrefix()
    {
        return $this->prefix;
    }
    
    /**
    * Set prefix
    * @return $this
    */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
        return $this;
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
     * Set label
     *
     * @param string $label
     * @return Line
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get label
     *
     * @return string 
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set position
     *
     * @param integer $position
     * @return Line
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
    * Get table
    * @return  
    */
    public function getTable()
    {
        return $this->table;
    }
    
    /**
    * Set table
    * @return $this
    */
    public function setTable($table)
    {
        $this->table = $table;
        return $this;
    }
    public function getLine()
    {
        return $this;
    }

    /**
    * Get club
    * @return  
    */
    public function getClub()
    {
        return $this->club;
    }
    
    /**
    * Set club
    * @return $this
    */
    public function setClub($club)
    {
        $this->club = $club;
        return $this;
    }

    /**
    * Get row1
    * @return  
    */
    public function getRow1()
    {
        return $this->row1;
    }
    
    /**
    * Set row1
    * @return $this
    */
    public function setRow1($row1)
    {
        $this->row1 = $row1;
        return $this;
    }

    /**
    * Get row2
    * @return  
    */
    public function getRow2()
    {
        return $this->row2;
    }
    
    /**
    * Set row2
    * @return $this
    */
    public function setRow2($row2)
    {
        $this->row2 = $row2;
        return $this;
    }

    /**
    * Get row3
    * @return  
    */
    public function getRow3()
    {
        return $this->row3;
    }
    
    /**
    * Set row3
    * @return $this
    */
    public function setRow3($row3)
    {
        $this->row3 = $row3;
        return $this;
    }

    /**
    * Get row4
    * @return  
    */
    public function getRow4()
    {
        return $this->row4;
    }
    
    /**
    * Set row4
    * @return $this
    */
    public function setRow4($row4)
    {
        $this->row4 = $row4;
        return $this;
    }

    /**
    * Get row5
    * @return  
    */
    public function getRow5()
    {
        return $this->row5;
    }
    
    /**
    * Set row5
    * @return $this
    */
    public function setRow5($row5)
    {
        $this->row5 = $row5;
        return $this;
    }

    /**
    * Get row6
    * @return  
    */
    public function getRow6()
    {
        return $this->row6;
    }
    
    /**
    * Set row6
    * @return $this
    */
    public function setRow6($row6)
    {
        $this->row6 = $row6;
        return $this;
    }

    /**
    * Get Row7
    * @return  
    */
    public function getRow7()
    {
        return $this->row7;
    }
    
    /**
    * Set Row7
    * @return $this
    */
    public function setRow7($row7)
    {
        $this->row7 = $row7;
        return $this;
    }

    /**
    * Get row8
    * @return  
    */
    public function getRow8()
    {
        return $this->row8;
    }
    
    /**
    * Set row8
    * @return $this
    */
    public function setRow8($row8)
    {
        $this->row8 = $row8;
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
}
