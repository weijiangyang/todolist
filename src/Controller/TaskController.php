<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Service\MailService;
use Symfony\Component\Mime\Email;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;

class TaskController extends AbstractController
{
    #[Route('/task', name: 'task_index', methods:['POST','GET'])]
    public function index(TaskRepository $taskRepository): Response
    {
        $tasks = $taskRepository->findAll();
        return $this->render('pages/task/index.html.twig', [
            'tasks' => $tasks,
        ]);
    }

    #[Route('/task/nouveau', name: 'task_new', methods: ['GET', 'POST'], priority: 1)]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, EntityManagerInterface $em, MailerInterface $mailer)
    {
        $task = new Task;

        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $task = $form->getData();
            $task->setCreater($this->getUser());
           
            $em->persist($task);
            $em->flush();

            $this->addFlash(
                'success',
                'Votre tâche a bien été crée!'
            );

            $email = (new Email())
            ->from('hello@example.com')
            ->to('you@example.com')
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Time for Symfony Mailer!')
            ->text('Sending emails is fun again!')
            ->html('<p>See Twig integration for better HTML integration!</p>');

            $mailer->send($email);
 
            return $this->redirectToRoute('task_show', [
               'id'=> $task->getId()
            ]);
        }

        return $this->render('pages/task/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/task/{id}', name: 'task_show', methods: ['GET', 'POST'])]
    
    public function show(String $id, TaskRepository $taskRepository)
    {
        $task = $taskRepository->findOneBy(['id'=> $id]);
      
        if (!$task) {
            return $this->redirectToRoute('task_index');
        }


        return $this->render('pages/task/show.html.twig', [
            'task' => $task
        ]);
    }

    #[Route('/task/edit/{id}', name: 'task_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, EntityManagerInterface $em, String $id, TaskRepository $taskRepository)
    {

        $task = $taskRepository->findOneBy(['id' => $id]);
        if (!$task) {
            $this->addFlash(
                'warning',
                'L\'ingrédient en question n\'existe pas!'
            );
            return $this->redirectToRoute('task_index');
        }
       
            $form = $this->createForm(TaskType::class, $task);
            $form->handleRequest($request);



            if ($form->isSubmitted() && $form->isValid()) {
                $task = $form->getData();
                
                    
                        
                        $task->setUpdatedAt(new \DateTime());
                        
                        $em->persist($task);
                        $em->flush();
                        $this->addFlash(
                            'success',
                            'Votre ingrédient a bien été modifié avec succès!'
                        );

                        return $this->redirectToRoute('task_index', [
                            'error' => null
                        ]);
                    } 
                      
            
            return $this->render('pages/task/edit.html.twig', [
                'form' => $form->createView(),
                'task' => $task
            ]);
        }
       
    }

