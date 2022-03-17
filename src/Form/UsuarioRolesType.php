<?php
namespace App\Form;

use App\Entity\Usuario;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\CallbackTransformer;


class UsuarioRolesType extends AbstractType
{
    public function buildForm (FormBuilderInterface $builder,array $options) {
     $builder        
        ->add('username',TextType::class,array('label'=>'Nombre de Usuario','attr' => ['class' => 'input-field col s12']))
        //->add('password',PasswordType::class,array('label'=>'Contraseña','attr' => ['class' => 'input-field col s12']))
        ->add('Roles', ChoiceType::class, [
                    'attr' => ['class' => 'browser-default'],
                    'required' => true,
                    'multiple' => false,
                    'expanded' => false,
                    'label' => 'Rango',                    
                    'choice_label' => function ($choice, $key, $value) {
                        if (true === $choice) { return 'Sin Rangos'; }
                          return strtoupper($key);
                        },
                    //'choice_attr' => function($choice, $key, $value) {
                          //return ['class' => 'with-gap'];
                    //},
                    'choices'  => [
                      'Usuario' => 'ROLE_USER',                    
                      'Administrador' => 'ROLE_ADMIN',
                    ],
                ])
        ->add('mail',TextType::class,array('label'=>'Mail','attr' => ['class' => 'input-field col s12']))    
        ->add('save', SubmitType::class,array('label'=>'Guardar','attr' => ['class' => 'waves-effect waves-light btn colorOficial white-text']));

        //Utilizamos un Data transformer para obtener los roles
        $builder->get('Roles')
            ->addModelTransformer(new CallbackTransformer(
                function ($rolesArray) {
                     // transform the array to a string
                     return count($rolesArray)? $rolesArray[0]: null;
                },
                function ($rolesString) {
                     // transform the string back to an array
                     return [$rolesString];
                }
        ));
        
    }

    public function configureOptions(OptionsResolver $resolver) {

        $resolver->setDefaults([
            'data_class' => Usuario::class,
        ]);
    }

}
