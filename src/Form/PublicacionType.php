<?php

namespace App\Form;

use App\Entity\Publicacion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class PublicacionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder            
            ->add('fecha',DateType::class,array('label'=>'Fecha Publicacion','widget' => 'single_text','data' => new \DateTime(),'attr' => ['class' => 'materialize-textarea','readonly' => 'true','tipoInput' => 'textarea']))
            ->add('titulo',TextType::class,array('label'=>'Titulo de la Publicacion','attr' => ['class' => 'input-field col s12','tipoInput' => 'input']))  
            ->add('descripcion',TextType::class,array('label'=>'Sintesis de la Publicacion','attr' => ['class' => 'input-field col s12','tipoInput' => 'input']))  
            ->add('cuerpo',TextareaType::class,array('label'=>'Cuerpo de la Publicacion','required' => false,'attr' => ['class' => 'materialize-textarea','tipoInput' => 'textarea'])) 
            ->add('flagNoticia',CheckboxType::class, array(
                'label'=>'¿Es una Noticia?',
                'required' => false,
                'attr' => array('class'=>'browser-default','tipoInput' => 'checkbox'),
            ))
            ->add('imagenes', FileType::class, [
                'label' => 'Adjuntar Imagenes Jpg/Png ',
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
                        'maxSize' => '8192k',
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
            ->add('archivos', FileType::class, [
                'label' => 'Adjuntar Archivos',
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
                        'maxSize' => '8192k',
                        'mimeTypes' => [
                            'application/pdf',
                            'application/msword',
                            'application/ms-excel',
                            'application/csv', 
                        ],
                        'mimeTypesMessage' => 'Por favor suba Archivos Word,Excel,PDF,CSV',
                    ])
                ],
            ])
            ->add('save', SubmitType::class,array('label'=>'Guardar','attr' => ['class' => 'waves-effect waves-light btn colorOficial white-text','tipoInput' => 'button'])); 
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Publicacion::class,
        ]);
    }
}
