<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use MediaBundle\Entity\Media;
use UserBundle\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * Status
 *
 * @ORM\Table(name="status_table")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\StatusRepository")
 */
class Status
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
     * @ORM\Column(name="quote", type="text", nullable=true)
     */
    private $quote;

    /**
     * @var string
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;


    /**
     * @var string
     * @ORM\Column(name="color", type="string", length=255, nullable=true)
     */
    private $color;


    /**
     * @var string
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;


    /**
     * @var int
     *
     * @ORM\Column(name="likes", type="integer")
     */
    private $likes;

    /**
     * @var int
     *
     * @ORM\Column(name="shares", type="integer")
     */
    private $shares;

    /**
     * @var int
     *
     * @ORM\Column(name="downloads", type="integer")
     */
    private $downloads;

    /**
     * @var int
     *
     * @ORM\Column(name="views", type="integer")
     */
    private $views;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

     /**
     * @ORM\ManyToOne(targetEntity="MediaBundle\Entity\Media")
     * @ORM\JoinColumn(name="media_id", referencedColumnName="id")
     * @ORM\JoinColumn(nullable=false)
     */
    private $media;

     /**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;
    /**
     * @var bool
     *
     * @ORM\Column(name="enabled", type="boolean")
     */
    private $enabled;

        /**
     * @var bool
     *
     * @ORM\Column(name="review", type="boolean")
     */
    private $review;






    /**
     * @Assert\File(mimeTypes={"image/jpeg","image/png" },maxSize="200M")
     */
    private $file;



    /**
     * @Assert\File(mimeTypes={"video/mp4" },maxSize="200M")
     */
    private $filevideo;


    /**
    * @ORM\OneToMany(targetEntity="Comment", mappedBy="status",cascade={"persist", "remove"})
    * @ORM\OrderBy({"created" = "desc"})
    */
    private $comments;




    /**
     * @var bool
     *
     * @ORM\Column(name="comment", type="boolean")
     */
    private $comment;


     /**
     * @ORM\ManyToOne(targetEntity="MediaBundle\Entity\Media")
     * @ORM\JoinColumn(name="video_id", referencedColumnName="id")
     * @ORM\JoinColumn(nullable=false)
     */
    private $video;


    public function __construct()
    {
        $this->description = "" ;
        $this->likes = 0 ;
        $this->views = 0 ;
        $this->downloads = 0 ;
        $this->shares =0 ;
        $this->comments = new ArrayCollection();
        
        $this->created= new \DateTime();
        $this->review = false;
    }
    /**
    * Get font
    * @return  
    */
    public function getFont()
    {
        return $this->font;
    }
    
    /**
    * Set font
    * @return $this
    */
    public function setFont($font)
    {
        $this->font = $font;
        return $this;
    }
   
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
     * Set quote
     *
     * @param string $quote
     * @return Wallpaper
     */
    public function setQuote($quote)
    {
        $this->quote = $quote;

        return $this;
    }

    /**
     * Get quote
     *
     * @return string 
     */
    public function getQuote()
    {
        return $this->quote;
    }

    /**
     * Set downloads
     *
     * @param integer $downloads
     * @return Wallpaper
     */
    public function setDownloads($downloads)
    {
        $this->downloads = $downloads;

        return $this;
    }

    /**
     * Get downloads
     *
     * @return integer 
     */
    public function getDownloads()
    {
        return $this->downloads;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Wallpaper
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated()
    {
        return $this->created;
    }

   
    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return Album
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean 
     */
    public function getEnabled()
    {
        return $this->enabled;
    }
   
    /**
     * Set user
     *
     * @param string $user
     * @return Wallpaper
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return string 
     */
    public function getUser()
    {
        return $this->user;
    }
    /**
     * Set media
     *
     * @param string $media
     * @return Wallpaper
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
    * Get filevideo
    * @return  
    */
    public function getFilevideo()
    {
        return $this->filevideo;
    }
    
    /**
    * Set filevideo
    * @return $this
    */
    public function setFilevideo($filevideo)
    {
        $this->filevideo = $filevideo;
        return $this;
    }

    /**
    * Get review
    * @return  
    */
    public function getReview()
    {
        return $this->review;
    }
    
    /**
    * Set review
    * @return $this
    */
    public function setReview($review)
    {
        $this->review = $review;
        return $this;
    }
    public function __toString()
    {
        if ($this->getType()=="quote") {
            return $this->id." - ".$this->getClear();
        }else{
            return $this->id." - ".$this->quote;
        }
    }
        /**
    * Get comment
    * @return  
    */
    public function getComment()
    {
        return $this->comment;
    }
    
    /**
    * Set comment
    * @return $this
    */
    public function setComment($comment)
    {
        $this->comment = $comment;
        return $this;
    }

       /**
     * Add comments
     *
     * @param Wallpaper $comments
     * @return Categorie
     */
    public function addComment(Comment $comments)
    {
        $this->comments[] = $comments;

        return $this;
    }

    /**
     * Remove comments
     *
     * @param Comment $comments
     */
    public function removeComment(Comment $comments)
    {
        $this->comments->removeElement($comments);
    }

    /**
     * Get comments
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getComments()
    {
        return $this->comments;
    }

    
    /**
    * Get likes
    * @return  
    */
    public function getLikes()
    {
        return $this->likes;
    }
    
    /**
    * Set likes
    * @return $this
    */
    public function setLikes($likes)
    {
        $this->likes = $likes;
        return $this;
    }
  
    /**
    * Get video
    * @return  
    */
    public function getVideo()
    {
        return $this->video;
    }
    
    /**
    * Set video
    * @return $this
    */
    public function setVideo(Media $video)
    {
        $this->video = $video;
        return $this;
    }
  
    /**
    * Get description
    * @return  
    */
    public function getDescription()
    {
        return $this->description;
    }
    
    /**
    * Set description
    * @return $this
    */
    public function setDescription($description)
    {
        $this->description = $description;
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
    public function getClear()
    {
        return base64_decode($this->quote);
    }
    /**
    * Get views
    * @return  
    */
    public function getViews()
    {
        return $this->views;
    }

    /**
    * Get shares
    * @return  
    */
    public function getShares()
    {
        return $this->shares;
    }
    
    /**
    * Set shares
    * @return $this
    */
    public function setShares($shares)
    {
        $this->shares = $shares;
        return $this;
    }
     /**
    * Get views
    * @return  
    */
    public function getViewsNumber()
    {
        return $this->number_format_short($this->views);
    }  
     /**
    * Get views
    * @return  
    */
    public function getLikesNumber()
    {
        return $this->number_format_short($this->likes);
    } 
     /**
    * Get views
    * @return  
    */
    public function getSharesNumber()
    {
        return $this->number_format_short($this->shares);
    } 
     /**
    * Get views
    * @return  
    */
    public function getDownloadsNumber()
    {
        return $this->number_format_short($this->downloads);
    }   
     /**
    * Get views
    * @return  
    */
    public function getCommentsNumber()
    {
        return $this->number_format_short(sizeof($this->comments));
    }  
    /**
    * Set views
    * @return $this
    */
    public function setViews($views)
    {
        $this->views = $views;
        return $this;
    }
    /**
     * @param $n
     * @return string
     * Use to convert large positive numbers in to short form like 1K+, 100K+, 199K+, 1M+, 10M+, 1B+ etc
     */
    function number_format_short( $n ) {
        if ($n==0){
             return "0";
        }
        if ($n > 0 && $n < 1000) {
            // 1 - 999
            $n_format = floor($n);
            $suffix = '';
        } else if ($n >= 1000 && $n < 1000000) {
            // 1k-999k
            $n_format = floor($n / 1000);
            $suffix = 'K+';
        } else if ($n >= 1000000 && $n < 1000000000) {
            // 1m-999m
            $n_format = floor($n / 1000000);
            $suffix = 'M+';
        } else if ($n >= 1000000000 && $n < 1000000000000) {
            // 1b-999b
            $n_format = floor($n / 1000000000);
            $suffix = 'B+';
        } else if ($n >= 1000000000000) {
            // 1t+
            $n_format = floor($n / 1000000000000);
            $suffix = 'T+';
        }

        return !empty($n_format . $suffix) ? $n_format . $suffix : "0";
    }
}
