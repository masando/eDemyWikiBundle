<?php

namespace eDemy\WikiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;
use Doctrine\Common\Collections\ArrayCollection;
use eDemy\MainBundle\Entity\BaseEntity;

/**
 * ArticleCategory
 *
 * @ORM\Table("ArticleCategory")
 * @ORM\Entity(repositoryClass="eDemy\WikiBundle\Entity\CategoryRepository")
 */
class Category extends BaseEntity implements Translatable
{
    public function __construct($em = null)
    {
        parent::__construct($em);
        $this->articles = new ArrayCollection();
        $this->imagenes = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getName();
    }

    /**
     * @ORM\OneToOne(targetEntity="Category")
     */
    protected $category;

    public function setCategory(\eDemy\WikiBundle\Entity\Category $category)
    {
        $this->category = $category;
    
        return $this;
    }

    public function removeCategory(\eDemy\WikiBundle\Entity\Category $category)
    {
        $this->category->removeElement($category);
    }

    public function getCategory()
    {
        return $this->category;
    }

    public function showCategoryInForm()
    {
        return true;
    }
    /**
     * @ORM\OneToMany(targetEntity="Article", mappedBy="category")
     */
    protected $articles;

    public function addArticle(\eDemy\WikiBundle\Entity\Article $articles)
    {
        $this->articles[] = $articles;
    
        return $this;
    }

    public function removeArticle(\eDemy\WikiBundle\Entity\Article $articles)
    {
        $this->articles->removeElement($articles);
    }

    public function getArticles()
    {
        return $this->articles;
    }

    /**
     * @ORM\OneToMany(targetEntity="eDemy\WikiBundle\Entity\CategoryImagen", mappedBy="category", cascade={"persist","remove"})
     */
    protected $imagenes;


    public function getImagenes()
    {
        return $this->imagenes;
    }

    public function addImagen(CategoryImagen $imagen)
    {
        $imagen->setCategory($this);
        $this->imagenes->add($imagen);
    }

    public function removeImagen(CategoryImagen $imagen)
    {
        $this->imagenes->removeElement($imagen);
        $this->getEntityManager()->remove($imagen);
    }
    
    public function showImagenesInPanel()
    {
        return true;
    }

    public function showImagenesInForm()
    {
        return true;
    }
}
