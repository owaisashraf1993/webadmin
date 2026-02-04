<?php 
namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Slide
 *
 * @ORM\Table(name="statistics_table")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\StatisticRepository")
 */
class Statistic
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
     * @ORM\Column(name="statistic", type="string", length=255))
     */
    private $statistic;



    /**
     * @var int
     * @Assert\NotBlank()
    * @Assert\Range(
     *      min = 0
     * )
     * @ORM\Column(name="value", type="integer")
     */
    private $value;

    /**
     * @ORM\ManyToOne(targetEntity="Player", inversedBy="statistics")
     * @ORM\JoinColumn(name="player_id", referencedColumnName="id")
     */
    private $player;
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    public function getStatistic()
    {
        return $this->statistic;
    }

    public function setStatistic($statistic)
    {
        $this->statistic = $statistic;
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
}