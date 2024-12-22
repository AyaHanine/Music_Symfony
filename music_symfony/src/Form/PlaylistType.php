<?php

namespace App\Form;

use App\Entity\Playlist;
use App\Entity\song;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlaylistType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if (isset($options['playlists'])) {
            $builder->add('existing_playlist', EntityType::class, [
                'class' => Playlist::class,
                'choices' => $options['playlists'],
                'choice_label' => 'name', // Afficher le nom de la playlist
                'placeholder' => 'Choisir une playlist existante',
                'required' => false, // Ne pas obliger la sélection
            ]);
        }

        // Champ pour le nom de la nouvelle playlist
        $builder->add('name');

        // Champ pour ajouter des chansons à la playlist
        $builder->add('songs', EntityType::class, [
            'class' => Song::class,
            'choice_label' => 'title',
            'multiple' => true,
        ]);


    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Playlist::class,
            'playlists' => null
        ]);
    }
}
