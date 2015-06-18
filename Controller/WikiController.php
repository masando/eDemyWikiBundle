<?php

namespace eDemy\WikiBundle\Controller;

use eDemy\MainBundle\Controller\BaseController;
use eDemy\MainBundle\Event\ContentEvent;
use Symfony\Component\EventDispatcher\GenericEvent;
use eDemy\MainBundle\Entity\Param;

class WikiController extends BaseController
{
    public static function getSubscribedEvents()
    {
        return self::getSubscriptions('wiki', ['article', 'category'], array(
            'edemy_wiki_category_frontpage_lastmodified'    => array('onCategoryFrontpageLastModified', 0),
            'edemy_wiki_frontpage_lastmodified'             => array('onWikiFrontpageLastModified', 0),
            'edemy_wiki_article_details_lastmodified'       => array('onWikiArticleDetailsLastModified', 0),
            'edemy_wiki_article_details'                    => array('onWikiArticleDetails', 0),
            'edemy_wiki_category_details_lastmodified'      => array('onWikiCategoryDetailsLastModified', 0),
            'edemy_wiki_category_details'                   => array('onWikiCategoryDetails', 0),
            //'edemy_wiki_frontpage' => array('onFrontpage', 0),
            'edemy_mainmenu'                                => array('onWikiMainMenu', 0),
        ));
    }

    public function onWikiMainMenu(GenericEvent $menuEvent) {
        $items = array();
        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $item = new Param($this->get('doctrine.orm.entity_manager'));
            $item->setName('Admin_Wiki');
            if($namespace = $this->getNamespace()) {
                $namespace .= ".";
            }
            $item->setValue($namespace . 'edemy_wiki_article_index');
            $items[] = $item;
        }

        $menuEvent['items'] = array_merge($menuEvent['items'], $items);

        return true;
    }

    public function onFrontpage(ContentEvent $event)
    {
        $this->addEventModule($event, "templates/wiki/wiki_frontpage");
    }

    public function onWikiFrontpageLastModified(ContentEvent $event)
    {
        $wiki = $this->getRepository('edemy_wiki_category_index')->findLastModified($this->getNamespace());

        if($wiki->getUpdated()) {
            //die(var_dump($entity->getUpdated()));
            $event->setLastModified($wiki->getUpdated());
        }
    }

    public function onWikiFrontpage(ContentEvent $event)
    {
        $this->get('edemy.meta')->setTitlePrefix("Catálogo");
        $query = $this->getRepository($event->getRoute())->findAllOrderedByName($this->getNamespace(), true);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $this->get('request')->query->get('page', 1)/*page number*/,
            24/*limit per page*/
        );


        $this->addEventModule($event, "templates/wiki/wiki_frontpage", array(
            'pagination' => $pagination
        ));
    }

    public function onWikiArticleDetailsLastModified(ContentEvent $event)
    {
        $entity = $this->getRepository($event->getRoute())->findOneBy(array(
            'slug'        => $this->getRequestParam('slug'),
            'namespace' => $this->getNamespace(),
        ));
        $lastmodified = $entity->getUpdated();
        $lastmodified_files = null;
//        $lastmodified_files = $this->getLastModifiedFiles('/../../WikiBundle/Resources/views', '*.html.twig');
        if($lastmodified_files > $lastmodified) {
            $lastmodified = $lastmodified_files;
        }

        $event->setLastModified($lastmodified);
    }
    
    public function onWikiArticleDetails(ContentEvent $event) {
        $cart_url = null;
        $cart_button = null;
        if($this->getParam('add_to_cart_button') != 'add_to_cart_button') {
            $cart_button = $this->getParam('add_to_cart_button');
        }
        if($this->getParam('add_to_cart_url') != 'add_to_cart_url') {
            $cart_url = $this->getParam('add_to_cart_url');
        }
        $entity = $this->getRepository($event->getRoute())->findOneBy(array(
            'slug'        => $this->getRequestParam('slug'),
            'namespace' => $this->getNamespace(),
        ));
        $this->get('edemy.meta')->setTitlePrefix($entity->getName());
        $this->get('edemy.meta')->setDescription($entity->getMetaDescription());
        $this->get('edemy.meta')->setKeywords($entity->getMetaKeywords());

        $this->addEventModule($event, "templates/wiki/wiki_details", array(
            'entity' => $entity,
            'cart_button' => $cart_button,
            'cart_url' => $cart_url,
        ));
    }

    public function onWikiCategoryFrontpageLastModified(ContentEvent $event)
    {
        $category = $this->getRepository('edemy_wiki_category_frontpage')->findLastModified($this->getNamespace());
        //die(var_dump($category->getUpdated()));        
        if($category->getUpdated()) {
            $event->setLastModified($category->getUpdated());
        }

        return true;
    }
    
    public function onWikiCategoryFrontpage(ContentEvent $event)
    {
        $this->get('edemy.meta')->setTitlePrefix("Categorías de Wikis");

        $this->addEventModule($event, "templates/wiki/category_frontpage", array(
            'entities' => $this->getRepository('edemy_wiki_category_frontpage')->findBy(array(
                'namespace' => $this->getNamespace(),
            )),
        ));
        
        return true;
    }

    public function onCategoryDetailsLastModified(ContentEvent $event)
    {
        // get the category
        $request = $this->getRequest();
        $category_slug = $request->attributes->get('slug');
        $category = $this->getRepository($event->getRoute())->findOneBySlug($category_slug);
        
        // we get last modified wiki
        $article = $this->get('doctrine.orm.entity_manager')->getRepository('eDemyWikiBundle:Article')->findLastModified($category->getId(), $this->getNamespace());
        
        if($article) {
            if($article->getUpdated()) {
                $event->setLastModified($article->getUpdated());
            }
        }

        return true;
    }

    public function onCategoryDetails(ContentEvent $event)
    {
        $request = $this->getCurrentRequest();
        $category_slug = $request->attributes->get('slug');
        if(!$category_slug) {
            $category_slug = $event->getRouteMatch('slug');
            $event->setRoute($event->getRouteMatch('_route'));
        }
        //die(var_dump($this->getNamespace($event->getRoute())));
        $category = $this->getRepository($event->getRoute())->findOneBySlug($category_slug);
        $query = $this->get('doctrine.orm.entity_manager')->getRepository('eDemyWikiBundle:Article')->findAllByCategory($category->getId(), $this->getNamespace(), true);
        //die(var_dump($query));
        $num_categories = count($this->get('doctrine.orm.entity_manager')->getRepository('eDemyWikiBundle:Category')->findAll());
        
        $this->get('edemy.meta')->setTitlePrefix($category->getName());

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $this->get('request')->query->get('page', 1)/*page number*/,
            24/*limit per page*/
        );

        $this->addEventModule($event, "templates/wiki/wiki_frontpage", array(
            'pagination' => $pagination,
            'title' => 'Estás en la categoría ' . $category->getName(),
            'num_categories' => $num_categories,
        ));

        return true;
    }
}
