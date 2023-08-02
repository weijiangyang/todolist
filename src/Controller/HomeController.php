<?php

namespace App\Controller;

use App\Repository\PriorityRepository;
use App\Repository\TaskRepository;
use App\Service\MailService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * This function allows to display all tasks which are distributed to the current user
     * and all the tasks which are distributed to nobody.
     * Also, it allows to discover the tasks which have exceed the time limite and send emails to all the concerning 
     * users for reminding. 
     *
     * @param TaskRepository $taskRepository
     * @param MailService $mail
     * @param EntityManagerInterface $em
     * @return Response
     */
    #[Route('/', name: 'app_home', methods:['GET'])]
    public function index(TaskRepository $taskRepository,MailService $mail,EntityManagerInterface $em): Response
    {
        /**
         * @var Task
         */
        $tasks = $taskRepository->findAll();
        /**
         * Send emails to the concerning users for reminding that the task has exceeded the limite date. 
        */
        foreach ($tasks as $task) {
            $finishTime = $task->getFinishAt();
        //change its property 'isLate' to 'true'  when a task has exceeded the limite date
            if($finishTime < new \DateTime()){
                $task->setIsLate(true);
                $em->persist($task);
                $em->flush();
            }
        // send emails to the concerning users when a task has exceeded the date limite for the first time
            if($task->isIsLate() === true && $task->isIsWarned() === false){
                    
                $operators = $task->getOperateurs();

                foreach ($operators as $operator) {
                    $emailUser = $operator->getEmail();
                    // send email 
                    $mail->sendEmail(
                        'weijiangyanglaval@gmail.com',
                        'La tache No°' . $task->getId().'is late',
                        'pages/emails/tacheLate.html.twig',
                        ['task' => $task],
                        $emailUser
                    );
                }
            //set the property 'isWarned' to 'true' for avoiding the repetition of sending email
                $task->setIsWarned(true);
                $em->persist($task);
                $em->flush(); 
            }
            
        }
            
        // display the homepage
        return $this->render('pages/home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
}
