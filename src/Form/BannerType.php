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

class BannerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder            
            ->add('nombre',TextType::class,array('label'=>'Imagen para el Banner de Aviso','attr' => ['class' => 'input-field col s12','tipoInput' => 'input']))  
            ->add('activo',CheckboxType::class, array(
                'label'=>'Activo',
                'required' => false,
                'attr' => array('class'=>'browser-default','tipoInput' => 'checkbox'),
            ))
            ->add('imagen', FileType::class, [
                'label' => 'Adjuntar Imagen Jpg/Png ',
                // unmapped means that this field is not associated to any entity property
                //sin asignar significa que este campo no está asociado a ninguna propiedad de entidad
                'mapped' => false,
                // make it optional so you don't have to re-upload the PDF file
                //hazlo opcional para que no tengas que volver a cargar el archivo PDF
                // every time you edit the Product details
                //cada vez que editas los detalles del producto
                'required' => false,
                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'attr' => array('class'=>'browser-default','tipoInput' => 'file'),
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpe',
                            'image/jpg',
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Por favor suba una Imagen JPG o PNG correcta',
                    ])
                ],
            ])
            ->add('save', SubmitType::class,array('label'=>'Guardar','attr' => ['class' => 'waves-effect waves-light btn colorOficial white-text','tipoInput' => 'button'])); 
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Banner::class,
        ]);
    }
}
