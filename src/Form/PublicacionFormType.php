<?php

namespace App\Form;

use App\Entity\Publicacion;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Objeto;

class PublicacionFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('descripcion', TextareaType::class, [
                'required' => false,
                'label' => 'Descripción',
            ])
            ->add('buscaObjeto', null, [
                'required' => false,
                'label' => 'Objeto buscado',
            ])
            ->add('cantidad', IntegerType::class, [
                'required' => true,
                'label' => 'Cantidad',
                'data' => 1,
            ])
            ->add('estado', ChoiceType::class, [
                'choices'  => [
                    'Activa' => 'activa',
                    'Completada' => 'completada',
                    'Cancelada' => 'cancelada',
                ],
                'label' => 'Estado',
                'data' => 'activa',
            ])
            ->add('objeto', EntityType::class, [
                'class' => Objeto::class,
                'choice_label' => 'nombre',
                'label' => 'Objeto ofrecido',
                'attr' => ['class' => 'select2'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Publicacion::class,
        ]);
    }
}
