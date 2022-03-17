<?php

namespace App\Form;

use App\Entity\Menu;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Publicacion;
use Doctrine\ORM\EntityRepository;

class MenuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre',TextType::class,array('label'=>'Nombre del Menu','attr' => ['class' => 'input-field col s12','tipoInput' => 'input']))
            ->add('orden',TextType::class,array('label'=>'Nro Posicionamiento','required' => false,'attr' => ['class' => 'input-field col s12','tipoInput' => 'input'])) 
            ->add('publicaciones', EntityType::class, [    
                 'class' => Publicacion::class,
                 'query_builder' => function (EntityRepository $er) {
                 return $er->createQueryBuilder('p')
                 ->orderBy('p.flagNoticia', 'DESC');
                },
                
                'required' => true,
                'multiple' => true,
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
            'data_class' => Menu::class,
        ]);
    }
}
