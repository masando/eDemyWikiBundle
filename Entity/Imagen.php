<?php

namespace eDemy\WikiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;
use eDemy\MainBundle\Entity\BaseImagen;

/**
 * @ORM\Table("WikiImagen")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity
 */
class Imagen extends BaseImagen
{
    public function __construct($em = null)
    {
        parent::__construct($em);
    }

    /**
     * @ORM\ManyToOne(targetEntity="eDemy\WikiBundle\Entity\Article", inversedBy="imagenes")
     */
    protected $article;

    public function setArticle($article)
    {
        $this->article = $article;

        return $this;
    }

    public function getArticle()
    {
        return $this->article;
    }

    ////
    public function showNameInForm()
    {
        return false;
    }

    public function showPublishedInForm()
    {
        return false;
    }

    public function showArticleInForm()
    {
        return false;
    }

    //// WebPath
    protected $webpath;
    
    public function showWebpathInForm()
    {
        return true;
    }

}
