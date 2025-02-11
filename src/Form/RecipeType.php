<?php

namespace App\Form;

use App\Entity\Recipe;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use App\Form\PostSubmitEvent;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class RecipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::Class, [
                'empty_data' => ''
            ])
            ->add('content', TextareaType::class, [
                'empty_data' => '' 
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
            ])
            // ->add('save', SubmitType::class, [
            //     'label' => 'Envoyer'
            // ])
            ->addEventListener(FormEvents::POST_SUBMIT, function(FormEvent $event){
                $this->attachTimestamps($event);
            })
        ;
    }

    public function attachTimestamps(FormEvent $event) : void
    {
        $data = $event->getData();
        if(!($data instanceof Recipe)){
            return;
        }

        $data->setUpdatedAt(new \DateTimeImmutable());
        if(!$data->getId()){
            $data->setCreatedAt(new \DateTimeImmutable());
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
        ]);
    }
}
