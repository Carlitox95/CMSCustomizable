<?php
namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\DateType;


class UsuarioCambiarPasswordType extends AbstractType
{
    public function buildForm (FormBuilderInterface $builder,array $options) {
     $builder                
        ->add('password',PasswordType::class,array('label'=>'Ingresar Nueva Contraseña','attr' => ['class' => 'input-field col s12']))        
        ->add('save', SubmitType::class,array('label'=>'Guardar','attr' => ['class' => 'waves-effect waves-light btn colorOficial white-text']));
    }
}

    