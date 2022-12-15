<?php

namespace App\Form;

use App\Entity\Human;
use App\Entity\User;
use App\Repository\HumanRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;



class HumanType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $currentHuman = $options['currentHuman'];
        $builder
            ->add('firstname', TextType::class, ['label' => 'Имя', 'required' => false])
            ->add('lastname', TextType::class, ['label' => 'Фамилия', 'required' => false])

            ->add('middlename', TextType::class, ['label' => 'Отчество', 'required' => false])

            ->add('maidenname', TextType::class, ['label' => 'Девичья фамилия (для женщин)', 'required' => false])

            ->add('gender', ChoiceType::class, [
                'label' => 'Пол',
                'required' => true,
                'choices' => [
                    'Мужской' => 'male',
                    'Женский' => 'female',
                ],
            ])

            ->add('year_birth', IntegerType::class, ['label' => 'Год рождения', 'required' => false])


            ->add('month_birth', IntegerType::class, ['label' => 'Месяц рождения', 'required' => false])

            ->add('day_birth', IntegerType::class, ['label' => 'День рождения', 'required' => false])
            ->add('year_death', IntegerType::class, ['label' => 'Год смерти', 'required' => false])


            ->add('month_death', IntegerType::class, ['label' => 'Месяц смерти', 'required' => false])


            ->add('day_death', IntegerType::class, ['label' => 'День смерти', 'required' => false])


            ->add('picture', FileType::class, [
                'label' => 'Фото',
                // unmapped means that this field is not associated to any entity property
                'mapped' => false,
                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,
                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '8000k',
                        'mimeTypes' => [
                            'image/*'
                        ],
                        'mimeTypesMessage' => 'Пожалуйста, загрузите изображение',
                    ])
                ],
            ])
            ->add('description', TextareaType::class, ['required' => false, 'label' => 'Описание'])
            ->add('mother', EntityType::class, [
                'label' => 'Мать',
                'class' => Human::class,
                'placeholder' => 'Не выбрано',
                'required' => false,
                'query_builder' => function (HumanRepository $er) use ($currentHuman)
                {
                    return $er->createQueryBuilder('u')
                        ->where('u.gender = \'female\'')
                        ->andWhere('u.id != :humanid', )
                        ->andWhere('( COALESCE(u.year_birth,0) < :human_year)')
                        ->setParameter('humanid', ($currentHuman ? $currentHuman->getId() : 0))
                        ->setParameter('human_year', ($currentHuman ? $currentHuman->getYearBirth() : 9999))
                        ->orderBy('u.firstname', 'ASC');
                },
                'choice_label' => function (? Human $human)
                {
                    return $human ? $human->getFullname() : 'не выбрано';


                },
                'choice_value' => function (? Human $entity)
                {
                    return $entity ? $entity->getId() : '';
                },
            ])
            ->add('father', EntityType::class, [
                'label' => 'Отец',
                'required' => false,
                'class' => Human::class,
                'placeholder' => 'Не выбрано',
                'query_builder' => function (HumanRepository $er) use ($currentHuman)
                {

                    return $er->createQueryBuilder('u')
                        ->where('u.gender = \'male\'')
                        ->andWhere('u.id != :humanid', )
                    ->andWhere('COALESCE(u.year_birth,0) < :human_year')

                        ->setParameter('humanid', ($currentHuman ? $currentHuman->getId() : 0))
                        ->setParameter('human_year', ($currentHuman ? $currentHuman->getYearBirth() : 9999))

                        ->orderBy('u.lastname', 'ASC');
                },
                'choice_label' => function ($human)
                {
                    return $human ? $human->getFullname() : 'не выбрано';

                },
                'choice_value' => function (? Human $entity)
                {
                    return $entity ? $entity->getId() : '';
                },
            ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Human::class,
            'currentHuman' => null
        ]);
        $resolver->setAllowedTypes('currentHuman', ['null', Human::class]);

    }
































}