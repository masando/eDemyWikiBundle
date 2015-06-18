<?php

namespace eDemy\WikiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;
use Doctrine\Common\Collections\ArrayCollection;
use eDemy\WikiBundle\Entity\WikiCategory;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use eDemy\MainBundle\Entity\BaseEntity;

/**
 * @ORM\Table("WikiArticle")
 * @ORM\Entity(repositoryClass="eDemy\WikiBundle\Entity\ArticleRepository")
 */
class Article extends BaseEntity implements Translatable
{
    public function __construct($em = null)
    {
        parent::__construct($em);
        $this->imagenes = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getName();
    }

    /**
     * @Gedmo\Translatable
     * @ORM\Column(name="description", type="text")
     */
    protected $description;

    public function setDescription($description)
    {
        $this->description = $description;
    
        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="articles")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     */
    protected $category;    

    public function setCategory(\eDemy\WikiBundle\Entity\Category $category = null)
    {
        $this->category = $category;
    
        return $this;
    }

    public function getCategory()
    {
        return $this->category;
    }

    public function showCategoryInForm()
    {
        return true;
    }

    public function showCategoryInPanel()
    {
        return true;
    }

    /**
     * @ORM\OneToMany(targetEntity="Imagen", mappedBy="article", cascade={"persist","remove"})
     */
    protected $imagenes;


    public function getImagenes()
    {
        return $this->imagenes;
    }

    public function addImagen(Imagen $imagen)
    {
        $imagen->setArticle($this);
        $this->imagenes->add($imagen);
    }

    public function removeImagen(Imagen $imagen)
    {
        $this->imagenes->removeElement($imagen);
        $this->getEntityManager()->remove($imagen);
    }

    public function showImagenesInPanel()
    {
        return true;
    }
    
    ////
    public function showMeta_DescriptionInForm()
    {
        return false;
    }

    ////
    public function showMeta_KeywordsInForm()
    {
        return false;
    }

    ////
    public function showOrdenInForm()
    {
        return false;
    }
}
