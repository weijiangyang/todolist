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
    #[Route('/', name: 'app_home', methods:['GET'])]
    public function index(TaskRepository $taskRepository,MailService $mail,EntityManagerInterface $em): Response
    {
        $tasks = $taskRepository->findAll();
        foreach ($tasks as $task) {
            $finishTime = $task->getFinishAt();
            if($finishTime < new \DateTime()){
                $task->setIsLate(true);
                $em->persist($task);
                $em->flush();
            }
            if($task->isIsLate() === true && $task->isIsWarned() === false){
                    
                    $operators = $task->getOperateurs();
                    foreach ($operators as $operator) {
                        $emailUser = $operator->getEmail();
                        $mail->sendEmail(
                            'weijiangyanglaval@gmail.com',
                            'La tache No°' . $task->getId().'is late',
                            'pages/emails/tacheLate.html.twig',
                            ['task' => $task],
                            $emailUser
                        );
                    }
                        
                    }
                    $task->setIsWarned(true);
                    $em->persist($task);
                    $em->flush();
                }
            
        
        return $this->render('pages/home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
}
