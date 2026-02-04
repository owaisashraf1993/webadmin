<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use MediaBundle\Entity\Media;
/**
 * Table
 *
 * @ORM\Table(name="table_table")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TableRepository")
 */
class Table
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
     * @var int
     *
     * @ORM\Column(name="columns", type="integer")
     */
    private $columns;

    /**
     * @ORM\ManyToOne(targetEntity="Competition", inversedBy="tables")
     * @ORM\JoinColumn(name="competition_id", referencedColumnName="id")
     */
    private $competition;

    /**
    * @ORM\OneToMany(targetEntity="Line", mappedBy="table",cascade={"persist", "remove"})
    * @ORM\OrderBy({"position" = "asc"})
    */
    private $lines;

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
     * @return Table
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
     * @return Table
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
    public function getTable()
    {
        return $this;
    }

    /**
    * Get columns
    * @return  
    */
    public function getColumns()
    {
        return $this->columns;
    }
    
    /**
    * Set columns
    * @return $this
    */
    public function setColumns($columns)
    {
        $this->columns = $columns;
        return $this;
    }

    /**
    * Get lines
    * @return  
    */
    public function getLines()
    {
        return $this->lines;
    }
    
    /**
    * Set lines
    * @return $this
    */
    public function setLines($lines)
    {
        $this->lines = $lines;
        return $this;
    }
}
