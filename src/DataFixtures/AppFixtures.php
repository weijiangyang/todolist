<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Task;
use App\Entity\User;
use Faker\Generator;
use App\Entity\Status;
use App\Entity\Category;
use App\Entity\Priority;
use App\Repository\StatusRepository;
use App\Repository\CategoryRepository;
use App\Repository\PriorityRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class AppFixtures extends Fixture
{
    /**
     * Undocumented variable
     *
     * @var Generator
     */
    private Generator $faker;
    private PriorityRepository $priorityRepository;
    private StatusRepository $statusRepository;
    private CategoryRepository $categoryRepository;
    

    public function __construct()
    {
        $this->faker = Factory::create('fr-FR');
        
       
       
    }
    public function load(ObjectManager $manager): void
    {
        $users = [];
        for ($k = 0; $k < 10; $k++) {
            $user = new User();
            $user->setEmail($this->faker->email())
                ->setRoles(['ROLE_USER'])
                ->setPlainPassword('password')
                ->setnickName($this->faker->name());
            $manager->persist($user);
            $users[] = $user;
        }

        $categories = [];
        $categoriesString = ['C1','C2','C3'];
        foreach($categoriesString as $value){
            $category = new Category();
            $category->setName($value);
            $manager->persist($category);
            $categories[] = $category;
        }

        $priorites = [];
        $prioritesString = ['basse','moyenne','haute'];
        foreach($prioritesString as $value){
            $priority = new Priority();
            $priority->setName($value);
            $manager->persist($priority);
            $priorites[] = $priority;
            
        }

        $statuss =[];
        $statussString = ['en attente', 'en cours', 'fini'];
        foreach($statussString as $value){
            $status = new Status();
            $status->setName(($value));
            $manager->persist($status);
            $statuss[] = $status;
        }
        
       
   
        
        
       
       for($i = 0; $i < 20; $i++){
            $task = new Task();
            
            $task->setName($this->faker->word())
                ->setDescription($this->faker->text())
                ->setCategory($categories[array_rand($categories)])
                ->setPriority($priorites[array_rand($priorites)])
                ->setStatus($statuss[array_rand($statuss)])
                ->setFinishAt($this->faker->dateTimeBetween('-1 day', '+1 week'));

            $manager->persist(($task));
       }

       

        $manager->flush();
    }
}
