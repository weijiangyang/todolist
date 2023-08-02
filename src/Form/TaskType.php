<?php

namespace App\Form;

use App\Entity\Task;
use App\Entity\User;
use App\Entity\Status;
use App\Entity\Category;
use App\Entity\Priority;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [

                'label' => 'Nom',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['min' => 2, 'max' => 255])
                ]
            ])
            ->add('description',TextareaType::class,[
            'label' => 'Description',
            'constraints' => [
                new Assert\NotBlank(),
               
            ]
            ])
            
            ->add('finishAt',DateType::class,[
                'label'=> 'Date limite',
                'constraints' => [
                    new Assert\NotBlank(),

            ]
            ])
            ->add('status', EntityType::class,[
                'class' => Status::class,
                'choice_label' => 'name',
                'multiple' => false,
                'expanded' => true,
                'label' => 'Status',
                'constraints' => [
                    new Assert\NotBlank(),
                ]
            ])
            ->add('priority',EntityType::class,[
            'class' => Priority::class,
            'choice_label' => 'name',
            'multiple' => false,
            'expanded' => true,
            'label' => 'Priority',
            'constraints' => [
                new Assert\NotBlank(),
            ]
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'multiple' => false,
                'expanded' => true,
                'label' => 'Catégorie',
                'constraints' => [
                    new Assert\NotBlank(),
                ]

            ])  
           
            ->add('operateurs',EntityType::class,[
                'class' => User::class,
                'choice_label' => 'nickname',
                'multiple' => true,
                'expanded' => true,
                'label' => 'Utilisateurs',
                
            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary my-3'
                ],
                'label' => 'Créer une tâche'
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
        ]);
    }
}
