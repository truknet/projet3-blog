<?php

namespace Blog\GeneralBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ArticleEditType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->remove('dateCreate');
        $builder->remove('comments');

    }

    public function getParent()
    {
        return ArticleType::class;
    }


}
