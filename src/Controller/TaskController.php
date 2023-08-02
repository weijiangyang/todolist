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
    /**
     * This function allows to display the table which lists all the unfinished tasks  which are distributed to the curren user
     * or to nobody. 
     *
     * @param TaskRepository $taskRepository
     * @return Response
     */
    #[Route('/task', name: 'task_index', methods:['POST','GET'])]
    public function index(TaskRepository $taskRepository): Response
    {
        $tasks = $taskRepository->findAll();

        return $this->render('pages/task/index.html.twig', [
            'tasks' => $tasks,
        ]);
    }

    /**
     * This function allows the current user to create a new task ane send emais to all the concerning users for reminding once the task is created.  
     *
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param MailerInterface $mailer
     * @param MailService $mail
     * @return void
     */
    #[Route('/task/nouveau', name: 'task_new', methods: ['GET', 'POST'], priority: 1)]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request,EntityManagerInterface $em, MailerInterface $mailer,MailService $mail)
    {
        /*
         @var Task
        */ 
        $task = new Task;

        //create the form
        $form = $this->createForm(TaskType::class, $task);

        // handling the request when the form is submit
        $form->handleRequest($request);

        //save the new task in the BDD 
        if ($form->isSubmitted() && $form->isValid()) {
            $task = $form->getData();
            $task->setCreater($this->getUser());
           
            $em->persist($task);
            $em->flush();

            $this->addFlash(
                'success',
                'Votre tâche a bien été crée!'
            );

            // once the task is created, send emaisl to the concerning users for reminding
            $operators = $task->getOperateurs();
            foreach($operators as $operator){
                $emailUser = $operator->getEmail();
                $mail->sendEmail(
                    'weijiangyanglaval@gmail.com',
                    'Une new tache No°'.$task->getId(),
                    'pages/emails/newTache.html.twig',
                    ['task' => $task],
                    $emailUser
                );
            }

            //when the task is created, turn to the page pour showing the details of this task
            return $this->redirectToRoute('task_show', [
               'id'=> $task->getId()
            ]);
        }

        //display the form of creation of task
        return $this->render('pages/task/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * The funtion allow to display the task specified
     *
     * @param String $id
     * @param TaskRepository $taskRepository
     * @return void
     */
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

    /**
     * This function allow to update a task specified
     *
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param String $id
     * @param TaskRepository $taskRepository
     * @return void
     */
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
       
        //submit the form and save the result of changement in BDD
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $task = $form->getData();
                    $task->setUpdatedAt(new \DateTime());
                    $em->persist($task);
                    $em->flush();
                    $this->addFlash(
                        'success',
                        'Votre tâche a bien été modifié avec succès!'
                    );

                    return $this->redirectToRoute('task_index', [
                        'error' => null
                    ]);
        } 
                      
        //display the page of modification of task  
        return $this->render('pages/task/edit.html.twig', [
            'form' => $form->createView(),
            'task' => $task
        ]);
    }
       
}

