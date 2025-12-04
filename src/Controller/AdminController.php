<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Job;
use App\Entity\JobApplication;
use App\Entity\Notification;
use App\Repository\CategoryRepository;
use App\Repository\JobRepository;
use App\Repository\JobApplicationRepository;
use App\Repository\UserRepository;
use App\Repository\UserActivityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'app_admin_dashboard')]
    public function dashboard(
        JobRepository $jobRepository,
        CategoryRepository $categoryRepository,
        UserRepository $userRepository,
        JobApplicationRepository $jobApplicationRepository
    ): Response {
        $stats = [
            'total_jobs' => count($jobRepository->findAll()),
            'active_jobs' => count($jobRepository->findActiveJobs()),
            'total_categories' => count($categoryRepository->findAll()),
            'total_users' => count($userRepository->findAll()),
            'active_users' => count($userRepository->findActiveUsers()),
            'admins' => count($userRepository->findAdmins()),
            'pending_applications' => $jobApplicationRepository->countByStatus('pending'),
            'accepted_applications' => $jobApplicationRepository->countByStatus('accepted'),
            'rejected_applications' => $jobApplicationRepository->countByStatus('rejected'),
        ];

        $recentJobs = $jobRepository->findBy([], ['createdAt' => 'DESC'], 5);
        $recentCategories = $categoryRepository->findBy([], ['createdAt' => 'DESC'], 5);
        $recentApplications = $jobApplicationRepository->findBy([], ['appliedAt' => 'DESC'], 10);

        return $this->render('admin/dashboard.html.twig', [
            'stats' => $stats,
            'recentJobs' => $recentJobs,
            'recentCategories' => $recentCategories,
            'recentApplications' => $recentApplications,
        ]);
    }

    // ===== JOBS =====
    #[Route('/jobs', name: 'app_admin_jobs')]
    public function listJobs(JobRepository $jobRepository): Response
    {
        $jobs = $jobRepository->findAll();

        return $this->render('admin/jobs/index.html.twig', [
            'jobs' => $jobs,
        ]);
    }

    #[Route('/jobs/create', name: 'app_admin_job_create')]
    public function createJob(
        Request $request,
        EntityManagerInterface $entityManager,
        CategoryRepository $categoryRepository
    ): Response {
        $job = new Job();
        $categories = $categoryRepository->findAll();

        $form = $this->createFormBuilder($job)
            ->add('title', TextType::class, [
                'label' => 'Job Title',
                'attr' => ['class' => 'form-control']
            ])
            ->add('company', TextType::class, [
                'label' => 'Company',
                'attr' => ['class' => 'form-control']
            ])
            ->add('location', TextType::class, [
                'label' => 'Location',
                'attr' => ['class' => 'form-control']
            ])
            ->add('salary', TextType::class, [
                'label' => 'Salary',
                'required' => false,
                'attr' => ['class' => 'form-control']
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => ['class' => 'form-control', 'rows' => 5]
            ])
            ->add('jobType', TextType::class, [
                'label' => 'Job Type (Full-time, Part-time, etc)',
                'attr' => ['class' => 'form-control']
            ])
            ->add('category', ChoiceType::class, [
                'label' => 'Category',
                'choices' => array_combine(
                    array_map(fn($c) => $c->getName(), $categories),
                    $categories
                ),
                'attr' => ['class' => 'form-control']
            ])
            ->add('isActive', ChoiceType::class, [
                'label' => 'Active',
                'choices' => ['Yes' => true, 'No' => false],
                'attr' => ['class' => 'form-control']
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Create Job',
                'attr' => ['class' => 'btn btn-success']
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $job->setCreatedAt(new \DateTimeImmutable());
            $job->setUpdatedAt(new \DateTimeImmutable());
            $entityManager->persist($job);
            $entityManager->flush();

            $this->addFlash('success', 'Job created successfully!');
            return $this->redirectToRoute('app_admin_jobs');
        }

        return $this->render('admin/jobs/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/jobs/{id}/edit', name: 'app_admin_job_edit')]
    public function editJob(
        Job $job,
        Request $request,
        EntityManagerInterface $entityManager,
        CategoryRepository $categoryRepository
    ): Response {
        $categories = $categoryRepository->findAll();

        $form = $this->createFormBuilder($job)
            ->add('title', TextType::class, [
                'label' => 'Job Title',
                'attr' => ['class' => 'form-control']
            ])
            ->add('company', TextType::class, [
                'label' => 'Company',
                'attr' => ['class' => 'form-control']
            ])
            ->add('location', TextType::class, [
                'label' => 'Location',
                'attr' => ['class' => 'form-control']
            ])
            ->add('salary', TextType::class, [
                'label' => 'Salary',
                'required' => false,
                'attr' => ['class' => 'form-control']
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => ['class' => 'form-control', 'rows' => 5]
            ])
            ->add('jobType', TextType::class, [
                'label' => 'Job Type',
                'attr' => ['class' => 'form-control']
            ])
            ->add('category', ChoiceType::class, [
                'label' => 'Category',
                'choices' => array_combine(
                    array_map(fn($c) => $c->getName(), $categories),
                    $categories
                ),
                'attr' => ['class' => 'form-control']
            ])
            ->add('isActive', ChoiceType::class, [
                'label' => 'Active',
                'choices' => ['Yes' => true, 'No' => false],
                'attr' => ['class' => 'form-control']
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Update Job',
                'attr' => ['class' => 'btn btn-warning']
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $job->setUpdatedAt(new \DateTimeImmutable());
            $entityManager->flush();

            $this->addFlash('success', 'Job updated successfully!');
            return $this->redirectToRoute('app_admin_jobs');
        }

        return $this->render('admin/jobs/edit.html.twig', [
            'form' => $form,
            'job' => $job,
        ]);
    }

    #[Route('/jobs/{id}/delete', name: 'app_admin_job_delete', methods: ['POST'])]
    public function deleteJob(
        Job $job,
        EntityManagerInterface $entityManager,
        Request $request
    ): Response {
        if ($this->isCsrfTokenValid('delete' . $job->getId(), $request->request->get('_token'))) {
            $entityManager->remove($job);
            $entityManager->flush();
            $this->addFlash('success', 'Job deleted successfully!');
        }

        return $this->redirectToRoute('app_admin_jobs');
    }

    // ===== CATEGORIES =====
    #[Route('/categories', name: 'app_admin_categories')]
    public function listCategories(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAll();

        return $this->render('admin/categories/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/categories/create', name: 'app_admin_category_create')]
    public function createCategory(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $category = new Category();

        $form = $this->createFormBuilder($category)
            ->add('name', TextType::class, [
                'label' => 'Category Name',
                'attr' => ['class' => 'form-control']
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => ['class' => 'form-control', 'rows' => 3]
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Create Category',
                'attr' => ['class' => 'btn btn-success']
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category->setCreatedAt(new \DateTimeImmutable());
            $entityManager->persist($category);
            $entityManager->flush();

            $this->addFlash('success', 'Category created successfully!');
            return $this->redirectToRoute('app_admin_categories');
        }

        return $this->render('admin/categories/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/categories/{id}/edit', name: 'app_admin_category_edit')]
    public function editCategory(
        Category $category,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $form = $this->createFormBuilder($category)
            ->add('name', TextType::class, [
                'label' => 'Category Name',
                'attr' => ['class' => 'form-control']
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => ['class' => 'form-control', 'rows' => 3]
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Update Category',
                'attr' => ['class' => 'btn btn-warning']
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Category updated successfully!');
            return $this->redirectToRoute('app_admin_categories');
        }

        return $this->render('admin/categories/edit.html.twig', [
            'form' => $form,
            'category' => $category,
        ]);
    }

    #[Route('/categories/{id}/delete', name: 'app_admin_category_delete', methods: ['POST'])]
    public function deleteCategory(
        Category $category,
        EntityManagerInterface $entityManager,
        Request $request
    ): Response {
        if ($this->isCsrfTokenValid('delete' . $category->getId(), $request->request->get('_token'))) {
            $entityManager->remove($category);
            $entityManager->flush();
            $this->addFlash('success', 'Category deleted successfully!');
        }

        return $this->redirectToRoute('app_admin_categories');
    }

    // ===== USERS =====
    #[Route('/users', name: 'app_admin_users')]
    public function listUsers(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        return $this->render('admin/users/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/users/{id}/edit', name: 'app_admin_user_edit')]
    public function editUser(
        \App\Entity\User $user,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        // Store current roles for form display (extract first role or default to ROLE_USER)
        $currentRole = !empty($user->getRoles()) ? $user->getRoles()[0] : 'ROLE_USER';

        $form = $this->createFormBuilder()
            ->add('email', TextType::class, [
                'label' => 'Email',
                'data' => $user->getEmail(),
                'attr' => ['class' => 'form-control', 'readonly' => true]
            ])
            ->add('role', ChoiceType::class, [
                'label' => 'Role',
                'choices' => ['User' => 'ROLE_USER', 'Admin' => 'ROLE_ADMIN'],
                'data' => $currentRole,
                'multiple' => false,
                'attr' => ['class' => 'form-control']
            ])
            ->add('isActive', ChoiceType::class, [
                'label' => 'Active',
                'choices' => ['Yes' => true, 'No' => false],
                'data' => $user->isIsActive(),
                'attr' => ['class' => 'form-control']
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Update User',
                'attr' => ['class' => 'btn btn-warning']
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $selectedRole = $form->get('role')->getData();
            $user->setRoles([$selectedRole]);
            $user->setUpdatedAt(new \DateTime());
            $entityManager->flush();

            $this->addFlash('success', 'User updated successfully!');
            return $this->redirectToRoute('app_admin_users');
        }

        return $this->render('admin/users/edit.html.twig', [
            'form' => $form,
            'user' => $user,
        ]);
    }

    #[Route('/users/{id}/delete', name: 'app_admin_user_delete', methods: ['POST'])]
    public function deleteUser(
        \App\Entity\User $user,
        EntityManagerInterface $entityManager,
        Request $request,
        \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface $tokenStorage
    ): Response {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            // If deleting the currently logged-in user, clear the security token first
            $currentUser = $this->getUser();
            if ($currentUser && ($currentUser instanceof \App\Entity\User) && $currentUser->getId() === $user->getId()) {
                $tokenStorage->setToken(null);
            }

            $entityManager->remove($user);
            $entityManager->flush();
            $this->addFlash('success', 'User deleted successfully!');
        }

        return $this->redirectToRoute('app_admin_users');
    }

    // ===== JOB APPLICATIONS =====
    #[Route('/applications', name: 'app_admin_applications')]
    public function listApplications(JobApplicationRepository $jobApplicationRepository): Response
    {
        $applications = $jobApplicationRepository->findBy([], ['appliedAt' => 'DESC']);

        return $this->render('admin/applications/index.html.twig', [
            'applications' => $applications,
        ]);
    }

    #[Route('/applications/{id}', name: 'app_admin_application_detail')]
    public function applicationDetail(JobApplication $application): Response
    {
        return $this->render('admin/applications/detail.html.twig', [
            'application' => $application,
        ]);
    }

    #[Route('/applications/{id}/review', name: 'app_admin_application_review', methods: ['POST'])]
    public function reviewApplication(
        JobApplication $application,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $status = $request->request->get('status');
        $notes = $request->request->get('notes', '');

        if (!in_array($status, ['accepted', 'rejected'])) {
            $this->addFlash('error', 'Invalid status!');
            return $this->redirectToRoute('app_admin_application_detail', ['id' => $application->getId()]);
        }

        $application->setStatus($status);
        $application->setAdminNotes($notes);
        $application->setReviewedAt(new \DateTime());
        $entityManager->persist($application);

        // Create notification for user
        $notification = new Notification();
        $notification->setUser($application->getUser());
        $notification->setType(Notification::TYPE_APPLICATION_STATUS);
        $notification->setTitle('Application ' . ucfirst($status));

        if ($status === 'accepted') {
            $notification->setMessage('Great news! Your application for ' . $application->getJob()->getTitle() . ' has been accepted!');
        } else {
            $notification->setMessage('Your application for ' . $application->getJob()->getTitle() . ' has been reviewed and rejected.');
        }

        $notification->setRelatedLink($this->generateUrl('app_application_detail', ['id' => $application->getId()]));
        $notification->setRelatedEntityId($application->getId());
        $entityManager->persist($notification);

        $entityManager->flush();

        $this->addFlash('success', 'Application status updated!');
        return $this->redirectToRoute('app_admin_applications');
    }

    // ===== USER ACTIVITIES =====
    #[Route('/activities', name: 'app_admin_activities')]
    public function listActivities(UserActivityRepository $userActivityRepository): Response
    {
        $activities = $userActivityRepository->findBy([], ['createdAt' => 'DESC'], 100);

        return $this->render('admin/activities/index.html.twig', [
            'activities' => $activities,
        ]);
    }

    #[Route('/user/{id}/activities', name: 'app_admin_user_activities')]
    public function userActivities(\App\Entity\User $user, UserActivityRepository $userActivityRepository): Response
    {
        $activities = $userActivityRepository->findByUser($user, 200);

        return $this->render('admin/activities/user_activities.html.twig', [
            'user' => $user,
            'activities' => $activities,
        ]);
    }
}
