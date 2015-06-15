<?php

namespace eDemy\WikiBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            //->add('name', null, ['label' => 'edit_article.name'])
            ->add('name')
            ->add('model')
            ->add('description', 'ckeditor')
            ->add('price')
            ->add('category','entity', array(
                'class' => 'eDemyWikiBundle:ArticleCategory',
                'property' => 'name',
                'empty_value' => '',
                'required' => false,
            ))
            ->add('published')
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'eDemy\WikiBundle\Entity\Article'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'edemy_wikibundle_article';
    }
}
