<?php

namespace App\Form;

use App\Entity\Location;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;

class LocationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Miasto – zwykły input tekstowy z placeholderem
            ->add('city', TextType::class, [
                'attr' => ['placeholder' => 'Enter city name'],
                'constraints' => [
                    new NotBlank(groups: ['create','edit']),
                ],
            ])

            // Kraj – select z listą (skrót ISO w bazie)
            ->add('country', ChoiceType::class, [
                'choices' => [
                    'Poland'          => 'PL',
                    'Germany'         => 'DE',
                    'France'          => 'FR',
                    'Spain'           => 'ES',
                    'Italy'           => 'IT',
                    'United Kingdom'  => 'GB',
                    'United States'   => 'US',
                ],
                'placeholder' => 'Choose country',
                'constraints' => [
                    new NotBlank(groups: ['create','edit']),
                ],
            ])

            // Szerokość geogr. (−90..90)
            ->add('latitude', NumberType::class, [
                'html5' => true,
                'scale' => 6,              // ile miejsc po przecinku pokazujemy
                'constraints' => [
                    new NotBlank(groups: ['create']),
                    new Range(min: -90, max: 90, groups: ['create','edit']),
                ],
            ])

            // Długość geogr. (−180..180)
            ->add('longitude', NumberType::class, [
                'html5' => true,
                'scale' => 7,
                'constraints' => [
                    new NotBlank(groups: ['create']),
                    new Range(min: -180, max: 180, groups: ['create','edit']),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Location::class,
            // domyślnie nie ustawiamy grup – podamy je w kontrolerze
            'validation_groups' => null,
        ]);
    }
}
