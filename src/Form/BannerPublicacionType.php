<?php

namespace App\Form;

use App\Entity\Banner;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Publicacion;

class BannerPublicacionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder            
            ->add('publicacion', EntityType::class, [    
                 'class' => Publicacion::class,                
                 'required' => true,
                 'multiple' => false,
                 'expanded' => true,
                 //'choice_label' => 'titulo',
                    'choice_label' => function($name){
                     return $name->getNombreDescriptivo();
                    },
                 'attr' => ['tipoInput' => 'multiple']
            ])
            ->add('save', SubmitType::class,array('label'=>'','attr' => ['class' => 'waves-effect waves-light btn colorOficial white-text','tipoInput' => 'button']));        
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Banner::class,
        ]);
    }
}
