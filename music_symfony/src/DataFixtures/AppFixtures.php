<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Playlist;
use App\Entity\Song;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker\Factory;


class AppFixtures extends Fixture
{

    private UserPasswordHasherInterface $passwordHasher;

    // Injection du service d'encodeur de mot de passe
    public function __construct(UserPasswordHasherInterface $passwordEncoder)
    {
        $this->passwordHasher = $passwordEncoder;
    }
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // Création de l'utilisateur avec le rôle 'ROLE_ADMIN'
        $admin = new User();
        $admin->setEmail('admin@example.com')
            ->setFirstName('John')
            ->setLastName('Doe')
            ->setRoles(['ROLE_ADMIN'])
            ->setPassword($this->passwordHasher->hashPassword($admin, 'password'));
        $manager->persist($admin);

        $this->addReference('user_admin', $admin);

        // Création de l'utilisateur avec le rôle 'ROLE_USER'
        $user = new User();
        $user->setEmail('user@example.com')
            ->setFirstName('Jane')
            ->setLastName('Doe')
            ->setRoles(['ROLE_USER'])
            ->setPassword($this->passwordHasher->hashPassword($user, 'userpassword'));
        $manager->persist($user);

        $this->addReference('user', $user);

        // Création de l'utilisateur avec le rôle 'ROLE_BANNED'
        $bannedUser = new User();
        $bannedUser->setEmail('banned@example.com')
            ->setFirstName('Mike')
            ->setLastName('Smith')
            ->setRoles(['ROLE_BANNED'])
            ->setPassword($this->passwordHasher->hashPassword($bannedUser, 'bannedpassword'));
        $manager->persist($bannedUser);

        $this->addReference('user_banned', $bannedUser);

        $song = array();

        // Générer 10 chansons fictives
        for ($i = 0; $i < 10; $i++) {
            $song[$i] = new Song();
            $song[$i]->setTitle($faker->sentence)  // Titre de la chanson (une phrase de 3 mots)
                ->setArtist($faker->name)  // Artiste
                ->setAlbum($faker->word)  // Album (génère un mot)
                ->setReleaseDate($faker->dateTimeThisCentury)  // Date de sortie (cette année)
                ->setGenre($faker->word);  // Genre (un mot)
            $this->addReference('song_' . $i, $song[$i]);
            // Persister l'entité pour l'ajouter à la base de données
            $manager->persist($song[$i]);
        }

        $playlist = array();

        // Créer 10 playlists
        for ($i = 0; $i < 10; $i++) {
            $playlist[$i] = new Playlist();
            $playlist[$i]->setName($faker->word())
                ->setCreatedAt($faker->dateTimeBetween('-1 year', 'now'))
                ->setUpdatedAt($faker->dateTimeBetween($playlist[$i]->getCreatedAt(), 'now'));
            $this->addReference('playlist_' . $i, $playlist[$i]); // Ajouter une référence pour chaque playlist


            // Ajouter 5 chansons à chaque playlist
            for ($j = 0; $j < 5; $j++) {
                $song = new Song();
                $song->setTitle($faker->word())
                    ->setArtist($faker->name())
                    ->setAlbum($faker->word())
                    ->setReleaseDate($faker->dateTimeBetween('-10 years', 'now'))
                    ->setGenre($faker->word());

                // Ajouter la chanson à la playlist
                $playlist[$i]->addSong($song);


                // Enregistrer la chanson
                $manager->persist($song);
            }

            // Enregistrer la playlist
            $manager->persist($playlist[$i]);
        }

        $comment = array();

        // Crée 10 commentaires fictifs
        for ($i = 0; $i < 10; $i++) {
            $comment = new Comment();
            $comment->setContent($faker->word); // Contenu du commentaire
            $comment->setCreatedAt($faker->dateTimeThisYear()); // Date de création aléatoire
            $comment->setCommentor($this->getReference('user_admin', User::class)); // Utilisateur aléatoire (assure-toi d'avoir des utilisateurs déjà créés dans une autre fixture)

            // Associe un morceau de musique aléatoire

            $manager->persist($comment);
        }












        $manager->flush();
    }
}
