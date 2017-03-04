<?php

namespace Blog\GeneralBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ConfigurationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('themeBlog',              ChoiceType::class, array(
                'choices'  => array(
                    'Standard' => 'css/bootstrap.min.css',
                    'Cerulean' => 'css/bootstrap-cerulean/bootstrap.min.css',
                    'Cosmo' => 'css/bootstrap-cosmo/bootstrap.min.css',
                    'Cyborg' => 'css/bootstrap-cyborg/bootstrap.min.css',
                    'Darkly' => 'css/bootstrap-darkly/bootstrap.min.css',
                    'Flatly' => 'css/bootstrap-flatly/bootstrap.min.css',
                    'Journal' => 'css/bootstrap-journal/bootstrap.min.css',
                    'Lumen' => 'css/bootstrap-lumen/bootstrap.min.css',
                    'Paper' => 'css/bootstrap-paper/bootstrap.min.css',
                    'Readable' => 'css/bootstrap-readable/bootstrap.min.css',
                    'Sandstone' => 'css/bootstrap-sandstone/bootstrap.min.css',
                    'Simplex' => 'css/bootstrap-simplex/bootstrap.min.css',
                    'Slate' => 'css/bootstrap-slate/bootstrap.min.css',
                    'Spacelab' => 'css/bootstrap-spacelab/bootstrap.min.css',
                    'Superhero' => 'css/bootstrap-superhero/bootstrap.min.css',
                    'United' => 'css/bootstrap-united/bootstrap.min.css',
                    'Yeti' => 'css/bootstrap-yeti/bootstrap.min.css'
                )
            ))
            ->add('themeAdmin',              ChoiceType::class,
                array
                (
                    'choices'  => array(
                        'Standard' => 'css/bootstrap.min.css',
                        'Cerulean' => 'css/bootstrap-cerulean/bootstrap.min.css',
                        'Cosmo' => 'css/bootstrap-cosmo/bootstrap.min.css',
                        'Cyborg' => 'css/bootstrap-cyborg/bootstrap.min.css',
                        'Darkly' => 'css/bootstrap-darkly/bootstrap.min.css',
                        'Flatly' => 'css/bootstrap-flatly/bootstrap.min.css',
                        'Journal' => 'css/bootstrap-journal/bootstrap.min.css',
                        'Lumen' => 'css/bootstrap-lumen/bootstrap.min.css',
                        'Paper' => 'css/bootstrap-paper/bootstrap.min.css',
                        'Readable' => 'css/bootstrap-readable/bootstrap.min.css',
                        'Sandstone' => 'css/bootstrap-sandstone/bootstrap.min.css',
                        'Simplex' => 'css/bootstrap-simplex/bootstrap.min.css',
                        'Slate' => 'css/bootstrap-slate/bootstrap.min.css',
                        'Spacelab' => 'css/bootstrap-spacelab/bootstrap.min.css',
                        'Superhero' => 'css/bootstrap-superhero/bootstrap.min.css',
                        'United' => 'css/bootstrap-united/bootstrap.min.css',
                        'Yeti' => 'css/bootstrap-yeti/bootstrap.min.css'
                    )
                ))
            ->add('nbArticlePerPageBlog',                   IntegerType::class)
            ->add('nbArticlePerPageAdmin',                  IntegerType::class)
            ->add('nbArticlePerPageInListBlog',             IntegerType::class)
            ->add('nbArticlePerPageInListAdmin',            IntegerType::class)
            ->add('nbArticleInSidebarArticleRecentBlog',    IntegerType::class)
            ->add('nbLevelCommentBlog',                     IntegerType::class)
            ->add('commentAutoPublished',                   ChoiceType::class,
                array
                (
                    'choices'  => array(
                        'Oui' => 1,
                        'Non' => 0,
                    )
                ))
            ->add('save',                                   SubmitType::class, array('label' => 'Valider'))
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Blog\GeneralBundle\Entity\Configuration'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'blog_generalbundle_configuration';
    }


}
