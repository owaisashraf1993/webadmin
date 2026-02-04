<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use MediaBundle\Entity\Media;
/**
 * Settings
 *
 * @ORM\Table(name="settings_table")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SettingsRepository")
 */
class Settings
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
     *
     * @ORM\Column(name="appname", type="string", length=255 , nullable = true)
     */
    private $appname;
    /**
     * @var string
     *
     * @ORM\Column(name="appsubname", type="string", length=255 , nullable = true)
     */
    private $appsubname;

    private $adminname;

     /**
     * @ORM\ManyToOne(targetEntity="MediaBundle\Entity\Media")
     * @ORM\JoinColumn(name="logo_id", referencedColumnName="id")
     * @ORM\JoinColumn(nullable=false)
     */
    private $logo;


     /**
     * @ORM\ManyToOne(targetEntity="MediaBundle\Entity\Media")
     * @ORM\JoinColumn(name="star_id", referencedColumnName="id")
     * @ORM\JoinColumn(nullable=false)
     */
    private $star;

     /**
     * @ORM\ManyToOne(targetEntity="MediaBundle\Entity\Media")
     * @ORM\JoinColumn(name="sponsors_id", referencedColumnName="id")
     * @ORM\JoinColumn(nullable=false)
     */
    private $sponsors;

    /**
     * @Assert\File(mimeTypes={"image/jpeg","image/png" },maxSize="40M")
     */
    private $fileadmin;
    /**
     * @Assert\File(mimeTypes={"image/jpeg","image/png" },maxSize="40M")
     */
    private $filelogo;

    /**
     * @Assert\File(mimeTypes={"image/jpeg","image/png" },maxSize="40M")
     */
    private $filestar;

    /**
     * @Assert\File(mimeTypes={"image/jpeg","image/png" },maxSize="40M")
     */
    private $filesponsors;


    /**
     * @var string
     *
     * @ORM\Column(name="firebasekey", type="string", length=255 , nullable = true)
     */
    private $firebasekey;


    /**
     * @var bool
     *
     * @ORM\Column(name="reviewnotification", type="boolean")
     */
    private $reviewnotification;


    /**
     * @var bool
     *
     * @ORM\Column(name="commentnotification", type="boolean")
     */
    private $commentnotification;



    /**
     * @var string
     *
     * @ORM\Column(name="appdescription", type="text", nullable = true)
     */
    private $appdescription;

    /**
     * @var string
     *
     * @ORM\Column(name="googleplay", type="text", nullable = true)
     */
    private $googleplay;



    /**
     * @var string
     *
     * @ORM\Column(name="appstore", type="text", nullable = true)
     */
    private $appstore;

    /**
     * @var string
     *
     * @ORM\Column(name="privacypolicy", type="text", nullable = true)
     */
    private $privacypolicy;

    /**
     * @var string
     *
     * @ORM\Column(name="publisherid", type="string", length=255 , nullable = true)
     */
    private $publisherid;

    /**
     * @var string
     *
     * @ORM\Column(name="publisheridios", type="string", length=255 , nullable = true)
     */
    private $publisheridios;

    /**
     * @var string
     *
     * @ORM\Column(name="appid", type="string", length=255 , nullable = true)
     */
    private $appid;

    /**
     * @var string
     *
     * @ORM\Column(name="appidios", type="string", length=255 , nullable = true)
     */
    private $appidios;

    /**
     * @var string
     *
     * @ORM\Column(name="rewardedadmobid", type="string", length=255 , nullable = true)
     */
    private $rewardedadmobid;

    /**
     * @var string
     *
     * @ORM\Column(name="banneradmobid", type="string", length=255 , nullable = true)
     */
    private $banneradmobid;


    /**
     * @var string
     *
     * @ORM\Column(name="bannerfacebookid", type="string", length=255 , nullable = true)
     */
    private $bannerfacebookid;


    /**
     * @var string
     *
     * @ORM\Column(name="bannertype", type="string", length=255 , nullable = true)
     */
    private $bannertype;

    /**
     * @var string
     *
     * @ORM\Column(name="nativeadmobid", type="string", length=255 , nullable = true)
     */
    private $nativeadmobid;

    /**
     * @var string
     *
     * @ORM\Column(name="nativefacebookid", type="string", length=255 , nullable = true)
     */
    private $nativefacebookid;

    /**
     * @var string
     *
     * @ORM\Column(name="nativeitem",  type="integer",  length=255 , nullable = true)
     */
    private $nativeitem;


    /**
     * @var string
     *
     * @ORM\Column(name="nativetype", type="string", length=255 , nullable = true)
     */
    private $nativetype;

    /**
     * @var string
     *
     * @ORM\Column(name="interstitialadmobid", type="string", length=255 , nullable = true)
     */
    private $interstitialadmobid;

    /**
     * @var string
     *
     * @ORM\Column(name="interstitialfacebookid", type="string", length=255 , nullable = true)
     */
    private $interstitialfacebookid;


     /**
     * @var string
     *
     * @ORM\Column(name="interstitialtype", type="string", length=255 , nullable = true)
     */
    private $interstitialtype;

     /**
     * @var string
     *
     * @ORM\Column(name="interstitialclick", type="integer", length=255 , nullable = true)
     */
    private $interstitialclick;





    /**
     * @var string
     *
     * @ORM\Column(name="rewardedadmobidios", type="string", length=255 , nullable = true)
     */
    private $rewardedadmobidios;

    /**
     * @var string
     *
     * @ORM\Column(name="banneradmobidios", type="string", length=255 , nullable = true)
     */
    private $banneradmobidios;


    /**
     * @var string
     *
     * @ORM\Column(name="bannerfacebookidios", type="string", length=255 , nullable = true)
     */
    private $bannerfacebookidios;


    /**
     * @var string
     *
     * @ORM\Column(name="bannertypeios", type="string", length=255 , nullable = true)
     */
    private $bannertypeios;

    /**
     * @var string
     *
     * @ORM\Column(name="nativeadmobidios", type="string", length=255 , nullable = true)
     */
    private $nativeadmobidios;

    /**
     * @var string
     *
     * @ORM\Column(name="nativefacebookidios", type="string", length=255 , nullable = true)
     */
    private $nativefacebookidios;

    /**
     * @var string
     *
     * @ORM\Column(name="nativeitemios",  type="integer",  length=255 , nullable = true)
     */
    private $nativeitemios;


    /**
     * @var string
     *
     * @ORM\Column(name="nativetypeios", type="string", length=255 , nullable = true)
     */
    private $nativetypeios;

    /**
     * @var string
     *
     * @ORM\Column(name="interstitialadmobidios", type="string", length=255 , nullable = true)
     */
    private $interstitialadmobidios;

    /**
     * @var string
     *
     * @ORM\Column(name="interstitialfacebookidios", type="string", length=255 , nullable = true)
     */
    private $interstitialfacebookidios;


     /**
     * @var string
     *
     * @ORM\Column(name="interstitialtypeios", type="string", length=255 , nullable = true)
     */
    private $interstitialtypeios;

     /**
     * @var string
     *
     * @ORM\Column(name="interstitialclickios", type="integer", length=255 , nullable = true)
     */
    private $interstitialclickios;








    



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
     * Set appname
     *
     * @param string $appname
     * @return Settings
     */
    public function setAppname($appname)
    {
        $this->appname = $appname;

        return $this;
    }

    /**
     * Get appname
     *
     * @return string 
     */
    public function getAppname()
    {
        return $this->appname;
    }

    /**
     * Set appdescription
     *
     * @param string $appdescription
     * @return Settings
     */
    public function setAppdescription($appdescription)
    {
        $this->appdescription = $appdescription;

        return $this;
    }

    /**
     * Get appdescription
     *
     * @return string 
     */
    public function getAppdescription()
    {
        return $this->appdescription;
    }

    /**
     * Set googleplay
     *
     * @param string $googleplay
     * @return Settings
     */
    public function setGoogleplay($googleplay)
    {
        $this->googleplay = $googleplay;

        return $this;
    }

    /**
     * Get googleplay
     *
     * @return string 
     */
    public function getGoogleplay()
    {
        return $this->googleplay;
    }

    /**
     * Set privacypolicy
     *
     * @param string $privacypolicy
     * @return Settings
     */
    public function setPrivacypolicy($privacypolicy)
    {
        $this->privacypolicy = $privacypolicy;

        return $this;
    }

    /**
     * Get privacypolicy
     *
     * @return string 
     */
    public function getPrivacypolicy()
    {
        return $this->privacypolicy;
    }

    /**
     * Set firebasekey
     *
     * @param string $firebasekey
     * @return Settings
     */
    public function setFirebasekey($firebasekey)
    {
        $this->firebasekey = $firebasekey;

        return $this;
    }

    /**
     * Get firebasekey
     *
     * @return string 
     */
    public function getFirebasekey()
    {
        return $this->firebasekey;
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
    * Get banneradmobid
    * @return  
    */
    public function getBanneradmobid()
    {
        return $this->banneradmobid;
    }
    
    /**
    * Set banneradmobid
    * @return $this
    */
    public function setBanneradmobid($banneradmobid)
    {
        $this->banneradmobid = $banneradmobid;
        return $this;
    }

    /**
    * Get bannerfacebookid
    * @return  
    */
    public function getBannerfacebookid()
    {
        return $this->bannerfacebookid;
    }
    
    /**
    * Set bannerfacebookid
    * @return $this
    */
    public function setBannerfacebookid($bannerfacebookid)
    {
        $this->bannerfacebookid = $bannerfacebookid;
        return $this;
    }

    /**
    * Get nativefacebookid
    * @return  
    */
    public function getNativefacebookid()
    {
        return $this->nativefacebookid;
    }
    
    /**
    * Set nativefacebookid
    * @return $this
    */
    public function setNativefacebookid($nativefacebookid)
    {
        $this->nativefacebookid = $nativefacebookid;
        return $this;
    }

    /**
    * Get nativeadmobid
    * @return  
    */
    public function getNativeadmobid()
    {
        return $this->nativeadmobid;
    }
    
    /**
    * Set nativeadmobid
    * @return $this
    */
    public function setNativeadmobid($nativeadmobid)
    {
        $this->nativeadmobid = $nativeadmobid;
        return $this;
    }

    /**
    * Get interstitialfacebookid
    * @return  
    */
    public function getInterstitialfacebookid()
    {
        return $this->interstitialfacebookid;
    }
    
    /**
    * Set interstitialfacebookid
    * @return $this
    */
    public function setInterstitialfacebookid($interstitialfacebookid)
    {
        $this->interstitialfacebookid = $interstitialfacebookid;
        return $this;
    }

    /**
    * Get interstitialadmobid
    * @return  
    */
    public function getInterstitialadmobid()
    {
        return $this->interstitialadmobid;
    }
    
    /**
    * Set interstitialadmobid
    * @return $this
    */
    public function setInterstitialadmobid($interstitialadmobid)
    {
        $this->interstitialadmobid = $interstitialadmobid;
        return $this;
    }

    /**
    * Get bannertype
    * @return  
    */
    public function getBannertype()
    {
        return $this->bannertype;
    }
    
    /**
    * Set bannertype
    * @return $this
    */
    public function setBannertype($bannertype)
    {
        $this->bannertype = $bannertype;
        return $this;
    }

    /**
    * Get interstitialtype
    * @return  
    */
    public function getInterstitialtype()
    {
        return $this->interstitialtype;
    }
    
    /**
    * Set interstitialtype
    * @return $this
    */
    public function setInterstitialtype($interstitialtype)
    {
        $this->interstitialtype = $interstitialtype;
        return $this;
    }

    /**
    * Get nativetype
    * @return  
    */
    public function getNativetype()
    {
        return $this->nativetype;
    }
    
    /**
    * Set nativetype
    * @return $this
    */
    public function setNativetype($nativetype)
    {
        $this->nativetype = $nativetype;
        return $this;
    }

    /**
    * Get interstitialclick
    * @return  
    */
    public function getInterstitialclick()
    {
        return $this->interstitialclick;
    }
    
    /**
    * Set interstitialclick
    * @return $this
    */
    public function setInterstitialclick($interstitialclick)
    {
        $this->interstitialclick = $interstitialclick;
        return $this;
    }

    /**
    * Get nativeitem
    * @return  
    */
    public function getNativeitem()
    {
        return $this->nativeitem;
    }
    
    /**
    * Set nativeitem
    * @return $this
    */
    public function setNativeitem($nativeitem)
    {
        $this->nativeitem = $nativeitem;
        return $this;
    }

    /**
    * Get rewardedadmobid
    * @return  
    */
    public function getRewardedadmobid()
    {
        return $this->rewardedadmobid;
    }
    
    /**
    * Set rewardedadmobid
    * @return $this
    */
    public function setRewardedadmobid($rewardedadmobid)
    {
        $this->rewardedadmobid = $rewardedadmobid;
        return $this;
    }






 /**
    * Get banneradmobid
    * @return  
    */
    public function getBanneradmobidios()
    {
        return $this->banneradmobidios;
    }
    
    /**
    * Set banneradmobid
    * @return $this
    */
    public function setBanneradmobidios($banneradmobidios)
    {
        $this->banneradmobidios = $banneradmobidios;
        return $this;
    }

    /**
    * Get bannerfacebookid
    * @return  
    */
    public function getBannerfacebookidios()
    {
        return $this->bannerfacebookidios;
    }
    
    /**
    * Set bannerfacebookid
    * @return $this
    */
    public function setBannerfacebookidios($bannerfacebookidios)
    {
        $this->bannerfacebookidios = $bannerfacebookidios;
        return $this;
    }

    /**
    * Get nativefacebookid
    * @return  
    */
    public function getNativefacebookidios()
    {
        return $this->nativefacebookidios;
    }
    
    /**
    * Set nativefacebookid
    * @return $this
    */
    public function setNativefacebookidios($nativefacebookidios)
    {
        $this->nativefacebookidios = $nativefacebookidios;
        return $this;
    }

    /**
    * Get nativeadmobid
    * @return  
    */
    public function getNativeadmobidios()
    {
        return $this->nativeadmobidios;
    }
    
    /**
    * Set nativeadmobid
    * @return $this
    */
    public function setNativeadmobidios($nativeadmobidios)
    {
        $this->nativeadmobidios = $nativeadmobidios;
        return $this;
    }

    /**
    * Get interstitialfacebookid
    * @return  
    */
    public function getInterstitialfacebookidios()
    {
        return $this->interstitialfacebookidios;
    }
    
    /**
    * Set interstitialfacebookid
    * @return $this
    */
    public function setInterstitialfacebookidios($interstitialfacebookidios)
    {
        $this->interstitialfacebookidios = $interstitialfacebookidios;
        return $this;
    }

    /**
    * Get interstitialadmobid
    * @return  
    */
    public function getInterstitialadmobidios()
    {
        return $this->interstitialadmobidios;
    }
    
    /**
    * Set interstitialadmobid
    * @return $this
    */
    public function setInterstitialadmobidios($interstitialadmobidios)
    {
        $this->interstitialadmobidios = $interstitialadmobidios;
        return $this;
    }

    /**
    * Get bannertype
    * @return  
    */
    public function getBannertypeios()
    {
        return $this->bannertypeios;
    }
    
    /**
    * Set bannertype
    * @return $this
    */
    public function setBannertypeios($bannertypeios)
    {
        $this->bannertypeios = $bannertypeios;
        return $this;
    }

    /**
    * Get interstitialtype
    * @return  
    */
    public function getInterstitialtypeios()
    {
        return $this->interstitialtypeios;
    }
    
    /**
    * Set interstitialtype
    * @return $this
    */
    public function setInterstitialtypeios($interstitialtypeios)
    {
        $this->interstitialtypeios = $interstitialtypeios;
        return $this;
    }

    /**
    * Get nativetype
    * @return  
    */
    public function getNativetypeios()
    {
        return $this->nativetypeios;
    }
    
    /**
    * Set nativetype
    * @return $this
    */
    public function setNativetypeios($nativetypeios)
    {
        $this->nativetypeios = $nativetypeios;
        return $this;
    }

    /**
    * Get interstitialclick
    * @return  
    */
    public function getInterstitialclickios()
    {
        return $this->interstitialclickios;
    }
    
    /**
    * Set interstitialclick
    * @return $this
    */
    public function setInterstitialclickios($interstitialclickios)
    {
        $this->interstitialclickios = $interstitialclickios;
        return $this;
    }

    /**
    * Get nativeitem
    * @return  
    */
    public function getNativeitemios()
    {
        return $this->nativeitemios;
    }
    
    /**
    * Set nativeitem
    * @return $this
    */
    public function setNativeitemios($nativeitemios)
    {
        $this->nativeitemios = $nativeitemios;
        return $this;
    }

    /**
    * Get rewardedadmobid
    * @return  
    */
    public function getRewardedadmobidios()
    {
        return $this->rewardedadmobidios;
    }
    
    /**
    * Set rewardedadmobid
    * @return $this
    */
    public function setRewardedadmobidios($rewardedadmobidios)
    {
        $this->rewardedadmobidios = $rewardedadmobidios;
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

    /**
    * Get sitekeywords
    * @return  
    */
    public function getSitekeywords()
    {
        return $this->sitekeywords;
    }
    
    /**
    * Set sitekeywords
    * @return $this
    */
    public function setSitekeywords($sitekeywords)
    {
        $this->sitekeywords = $sitekeywords;
        return $this;
    }

    /**
    * Get sitedescription
    * @return  
    */
    public function getSitedescription()
    {
        return $this->sitedescription;
    }
    
    /**
    * Set sitedescription
    * @return $this
    */
    public function setSitedescription($sitedescription)
    {
        $this->sitedescription = $sitedescription;
        return $this;
    }

    /**
    * Get favfile
    * @return  
    */
    public function getFavfile()
    {
        return $this->favfile;
    }
    
    /**
    * Set favfile
    * @return $this
    */
    public function setFavfile($favfile)
    {
        $this->favfile = $favfile;
        return $this;
    }

    /**
    * Get themoviedbkey
    * @return  
    */
    public function getThemoviedbkey()
    {
        return $this->themoviedbkey;
    }
    
    /**
    * Set themoviedbkey
    * @return $this
    */
    public function setThemoviedbkey($themoviedbkey)
    {
        $this->themoviedbkey = $themoviedbkey;
        return $this;
    }


    /**
    * Get themoviedblang
    * @return  
    */
    public function getThemoviedblang()
    {
        return $this->themoviedblang;
    }
    
    /**
    * Set themoviedblang
    * @return $this
    */
    public function setThemoviedblang($themoviedblang)
    {
        $this->themoviedblang = $themoviedblang;
        return $this;
    }

    /**
    * Get header
    * @return  
    */
    public function getHeader()
    {
        return $this->header;
    }
    
    /**
    * Set header
    * @return $this
    */
    public function setHeader($header)
    {
        $this->header = $header;
        return $this;
    }

    /**
    * Get paypalclientsecret
    * @return  
    */
    public function getPaypalclientsecret()
    {
        return $this->paypalclientsecret;
    }
    
    /**
    * Set paypalclientsecret
    * @return $this
    */
    public function setPaypalclientsecret($paypalclientsecret)
    {
        $this->paypalclientsecret = $paypalclientsecret;
        return $this;
    }

    /**
    * Get paypalclientid
    * @return  
    */
    public function getPaypalclientid()
    {
        return $this->paypalclientid;
    }
    
    /**
    * Set paypalclientid
    * @return $this
    */
    public function setPaypalclientid($paypalclientid)
    {
        $this->paypalclientid = $paypalclientid;
        return $this;
    }

    /**
    * Get gpay
    * @return  
    */
    public function getGpay()
    {
        return $this->gpay;
    }
    
    /**
    * Set gpay
    * @return $this
    */
    public function setGpay($gpay)
    {
        $this->gpay = $gpay;
        return $this;
    }

    /**
    * Get login
    * @return  
    */
    public function getLogin()
    {
        return $this->login;
    }
    
    /**
    * Set login
    * @return $this
    */
    public function setLogin($login)
    {
        $this->login = $login;
        return $this;
    }

    /**
    * Get homebannertype
    * @return  
    */
    public function getHomebannertype()
    {
        return $this->homebannertype;
    }
    
    /**
    * Set homebannertype
    * @return $this
    */
    public function setHomebannertype($homebannertype)
    {
        $this->homebannertype = $homebannertype;
        return $this;
    }

    /**
    * Get homebanner
    * @return  
    */
    public function getHomebanner()
    {
        return $this->homebanner;
    }
    
    /**
    * Set homebanner
    * @return $this
    */
    public function setHomebanner($homebanner)
    {
        $this->homebanner = $homebanner;
        return $this;
    }

    /**
    * Get moviebanner
    * @return  
    */
    public function getMoviebanner()
    {
        return $this->moviebanner;
    }
    
    /**
    * Set moviebanner
    * @return $this
    */
    public function setMoviebanner($moviebanner)
    {
        $this->moviebanner = $moviebanner;
        return $this;
    }
    /**
    * Get moviebannertype
    * @return  
    */
    public function getMoviebannertype()
    {
        return $this->moviebannertype;
    }
    
    /**
    * Set moviebannertype
    * @return $this
    */
    public function setMoviebannertype($moviebannertype)
    {
        $this->moviebannertype = $moviebannertype;
        return $this;
    }

    /**
    * Get seriebanner
    * @return  
    */
    public function getSeriebanner()
    {
        return $this->seriebanner;
    }
    
    /**
    * Set seriebanner
    * @return $this
    */
    public function setSeriebanner($seriebanner)
    {
        $this->seriebanner = $seriebanner;
        return $this;
    }

    /**
    * Get seriebannertype
    * @return  
    */
    public function getSeriebannertype()
    {
        return $this->seriebannertype;
    }
    
    /**
    * Set seriebannertype
    * @return $this
    */
    public function setSeriebannertype($seriebannertype)
    {
        $this->seriebannertype = $seriebannertype;
        return $this;
    }

    /**
    * Get channelbanner
    * @return  
    */
    public function getChannelbanner()
    {
        return $this->channelbanner;
    }
    
    /**
    * Set channelbanner
    * @return $this
    */
    public function setChannelbanner($channelbanner)
    {
        $this->channelbanner = $channelbanner;
        return $this;
    }

    /**
    * Get channelbannertype
    * @return  
    */
    public function getChannelbannertype()
    {
        return $this->channelbannertype;
    }
    
    /**
    * Set channelbannertype
    * @return $this
    */
    public function setChannelbannertype($channelbannertype)
    {
        $this->channelbannertype = $channelbannertype;
        return $this;
    }


    /**
    * Get appsubname
    * @return  
    */
    public function getAppsubname()
    {
        return $this->appsubname;
    }
    
    /**
    * Set appsubname
    * @return $this
    */
    public function setAppsubname($appsubname)
    {
        $this->appsubname = $appsubname;
        return $this;
    }

    /**
    * Get appstore
    * @return  
    */
    public function getAppstore()
    {
        return $this->appstore;
    }
    
    /**
    * Set appstore
    * @return $this
    */
    public function setAppstore($appstore)
    {
        $this->appstore = $appstore;
        return $this;
    }


    /**
    * Get logo
    * @return  
    */
    public function getLogo()
    {
        return $this->logo;
    }
    
    /**
    * Set logo
    * @return $this
    */
    public function setLogo($logo)
    {
        $this->logo = $logo;
        return $this;
    }

    /**
    * Get sponsors
    * @return  
    */
    public function getSponsors()
    {
        return $this->sponsors;
    }
    
    /**
    * Set sponsors
    * @return $this
    */
    public function setSponsors($sponsors)
    {
        $this->sponsors = $sponsors;
        return $this;
    }
    /**
    * Get filelogo
    * @return  
    */
    public function getFilelogo()
    {
        return $this->filelogo;
    }
    
    /**
    * Set filelogo
    * @return $this
    */
    public function setFilelogo($filelogo)
    {
        $this->filelogo = $filelogo;
        return $this;
    }

    /**
    * Get filesponsors
    * @return  
    */
    public function getFilesponsors()
    {
        return $this->filesponsors;
    }
    
    /**
    * Set filesponsors
    * @return $this
    */
    public function setFilesponsors($filesponsors)
    {
        $this->filesponsors = $filesponsors;
        return $this;
    }

    /**
    * Get filestar
    * @return  
    */
    public function getFilestar()
    {
        return $this->filestar;
    }
    
    /**
    * Set filestar
    * @return $this
    */
    public function setFilestar($filestar)
    {
        $this->filestar = $filestar;
        return $this;
    }

    /**
    * Get star
    * @return  
    */
    public function getStar()
    {
        return $this->star;
    }
    
    /**
    * Set star
    * @return $this
    */
    public function setStar($star)
    {
        $this->star = $star;
        return $this;
    }

    /**
    * Get commentnotification
    * @return  
    */
    public function getCommentnotification()
    {
        return $this->commentnotification;
    }
    
    /**
    * Set commentnotification
    * @return $this
    */
    public function setCommentnotification($commentnotification)
    {
        $this->commentnotification = $commentnotification;
        return $this;
    }

    /**
    * Get reviewnotification
    * @return  
    */
    public function getReviewnotification()
    {
        return $this->reviewnotification;
    }
    
    /**
    * Set reviewnotification
    * @return $this
    */
    public function setReviewnotification($reviewnotification)
    {
        $this->reviewnotification = $reviewnotification;
        return $this;
    }

    /**
    * Get fileadmin
    * @return  
    */
    public function getFileadmin()
    {
        return $this->fileadmin;
    }
    
    /**
    * Set fileadmin
    * @return $this
    */
    public function setFileadmin($fileadmin)
    {
        $this->fileadmin = $fileadmin;
        return $this;
    }

    /**
    * Get adminname
    * @return  
    */
    public function getAdminname()
    {
        return $this->adminname;
    }
    
    /**
    * Set adminname
    * @return $this
    */
    public function setAdminname($adminname)
    {
        $this->adminname = $adminname;
        return $this;
    }

    /**
    * Get publisheridios
    * @return  
    */
    public function getPublisheridios()
    {
        return $this->publisheridios;
    }
    
    /**
    * Set publisheridios
    * @return $this
    */
    public function setPublisheridios($publisheridios)
    {
        $this->publisheridios = $publisheridios;
        return $this;
    }

    /**
    * Get publisherid
    * @return  
    */
    public function getPublisherid()
    {
        return $this->publisherid;
    }
    
    /**
    * Set publisherid
    * @return $this
    */
    public function setPublisherid($publisherid)
    {
        $this->publisherid = $publisherid;
        return $this;
    }

    /**
    * Get appid
    * @return  
    */
    public function getAppid()
    {
        return $this->appid;
    }
    
    /**
    * Set appid
    * @return $this
    */
    public function setAppid($appid)
    {
        $this->appid = $appid;
        return $this;
    }

    /**
    * Get appidios
    * @return  
    */
    public function getAppidios()
    {
        return $this->appidios;
    }
    
    /**
    * Set appidios
    * @return $this
    */
    public function setAppidios($appidios)
    {
        $this->appidios = $appidios;
        return $this;
    }
}
