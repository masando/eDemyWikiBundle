<?php

namespace eDemy\WikiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;
use eDemy\MainBundle\Entity\BaseImagen;

/**
 * @ORM\Table("WikiCategoryImagen")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity
 */
class CategoryImagen extends BaseImagen
{
    public function __construct($em = null)
    {
        parent::__construct($em);
    }

    /**
     * @ORM\ManyToOne(targetEntity="eDemy\WikiBundle\Entity\Category", inversedBy="imagenes")
     */
    protected $category;

    public function setCategory($category)
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
        return false;
    }
}
