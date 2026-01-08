<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Job;
use App\Entity\JobApplication;
use App\Entity\Notification;
use App\Entity\UserActivity;
use App\Repository\JobApplicationRepository;
use App\Repository\NotificationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Doctrine\ORM\EntityManagerInterface;

#[Route('/jobs')]
class JobApplicationController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private JobApplicationRepository $jobApplicationRepository,
        private NotificationRepository $notificationRepository,
    ) {}

    #[Route('/{id}/apply', name: 'app_job_apply', methods: ['POST'])]
    public function apply(Job $job, Request $request): Response
    {
        if (!($this->isGranted('ROLE_USER') || $this->isGranted('ROLE_ADMIN'))) {
            throw $this->createAccessDeniedException('You must be a user or admin to apply.');
        }
        /** @var User $user */
        $user = $this->getUser();

        // CSRF protection for quick-apply button
        $token = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('apply_job' . $job->getId(), $token)) {
            $this->addFlash('error', 'Invalid form submission.');
            return $this->redirectToRoute('app_jobs_show', ['id' => $job->getId()]);
        }

        // Check if already applied
        $existing = $this->jobApplicationRepository->findUserApplicationForJob($user, $job);
        if ($existing && !$existing->isWithdrawn()) {
            $this->addFlash('warning', 'You have already applied for this job!');
            return $this->redirectToRoute('app_jobs_show', ['id' => $job->getId()]);
        }

        $application = new JobApplication();
        $application->setUser($user);
        $application->setJob($job);
        $application->setApplicationLetter($request->request->get('letter', ''));
        $application->setStatus(JobApplication::STATUS_PENDING);

        // Upload CV if provided
        $cvFile = $request->files->get('cv');
        if ($cvFile) {
            if ($cvFile->getMimeType() !== 'application/pdf') {
                $this->addFlash('error', 'Only PDF files are allowed for CV!');
                return $this->redirectToRoute('app_jobs_show', ['id' => $job->getId()]);
            }

            $safeFilename = 'app_' . $user->getId() . '_' . $job->getId() . '_' . uniqid() . '.pdf';
            $uploadsDir = $this->getParameter('kernel.project_dir') . '/public/uploads/applications';

            if (!is_dir($uploadsDir)) {
                mkdir($uploadsDir, 0755, true);
            }
// ........
            $cvFile->move($uploadsDir, $safeFilename);
            $application->setCvFileName($safeFilename);
        } else {
            // If user didn't upload a CV, but has one in their profile, copy it into applications
            try {
                $profile = $user->getProfile();
                if ($profile && $profile->getCvFileName()) {
                    $profilePath = $this->getParameter('kernel.project_dir') . '/public/uploads/cv/' . $profile->getCvFileName();
                    if (file_exists($profilePath)) {
                        $safeFilename = 'app_' . $user->getId() . '_' . $job->getId() . '_' . uniqid() . '.pdf';
                        $uploadsDir = $this->getParameter('kernel.project_dir') . '/public/uploads/applications';
                        if (!is_dir($uploadsDir)) {
                            mkdir($uploadsDir, 0755, true);
                        }
                        copy($profilePath, $uploadsDir . '/' . $safeFilename);
                        $application->setCvFileName($safeFilename);
                    }
                }
            } catch (\Throwable $e) {
                // Non-fatal: continue without CV
            }
        }

        $this->entityManager->persist($application);
        $this->entityManager->flush();

        // Log application activity
        $activity = new UserActivity();
        $activity->setUser($user);
        $activity->setAction(UserActivity::ACTION_JOB_APPLICATION);
        $activity->setDescription('Applied for job: ' . $job->getTitle());
        $activity->setRelatedEntityId($job->getId());
        $activity->setRelatedEntityType('Job');
        $activity->setIpAddress($this->getClientIp());
        $activity->setUserAgent($_SERVER['HTTP_USER_AGENT'] ?? null);
        $this->entityManager->persist($activity);

        // Create notification
        $notification = new Notification();
        $notification->setUser($user);
        $notification->setType(Notification::TYPE_APPLICATION);
        $notification->setTitle('Application Submitted');
        $notification->setMessage('Your application for ' . $job->getTitle() . ' at ' . $job->getCompany() . ' has been submitted');
        $notification->setRelatedLink($this->generateUrl('app_jobs_show', ['id' => $job->getId()]));
        $notification->setRelatedEntityId($application->getId());
        $this->entityManager->persist($notification);

        $this->entityManager->flush();

        $this->addFlash('success', 'Application submitted successfully!');
        return $this->redirectToRoute('app_profile_view');
    }

    #[Route('/applications', name: 'app_user_applications', methods: ['GET'])]
    public function myApplications(): Response
    {
        if (!($this->isGranted('ROLE_USER') || $this->isGranted('ROLE_ADMIN'))) {
            throw $this->createAccessDeniedException('You must be a user or admin to view applications.');
        }
        /** @var User $user */
        $user = $this->getUser();
        $applications = $this->jobApplicationRepository->findByUser($user);

        return $this->render('jobs/applications.html.twig', [
            'applications' => $applications,
        ]);
    }

    #[Route('/application/{id}/withdraw', name: 'app_application_withdraw', methods: ['POST'])]
    public function withdrawApplication(JobApplication $application, Request $request): Response
    {
        if (!($this->isGranted('ROLE_USER') || $this->isGranted('ROLE_ADMIN'))) {
            throw $this->createAccessDeniedException('You must be a user or admin to withdraw applications.');
        }
        /** @var User $user */
        $user = $this->getUser();

        if ($application->getUser() !== $user) {
            $this->addFlash('error', 'You cannot withdraw this application!');
            return $this->redirectToRoute('app_user_applications');
        }

        if ($application->isWithdrawn()) {
            $this->addFlash('warning', 'This application has already been withdrawn!');
            return $this->redirectToRoute('app_user_applications');
        }

        $application->setStatus(JobApplication::STATUS_WITHDRAWN);
        $this->entityManager->persist($application);

        // Log activity
        $activity = new UserActivity();
        $activity->setUser($user);
        $activity->setAction(UserActivity::ACTION_WITHDRAW_APPLICATION);
        $activity->setDescription('Withdrew application for: ' . $application->getJob()->getTitle());
        $activity->setRelatedEntityId($application->getId());
        $activity->setRelatedEntityType('JobApplication');
        $activity->setIpAddress($this->getClientIp());
        $activity->setUserAgent($_SERVER['HTTP_USER_AGENT'] ?? null);
        $this->entityManager->persist($activity);

        // Create notification
        $notification = new Notification();
        $notification->setUser($user);
        $notification->setType(Notification::TYPE_APPLICATION);
        $notification->setTitle('Application Withdrawn');
        $notification->setMessage('Your application for ' . $application->getJob()->getTitle() . ' has been withdrawn');
        $this->entityManager->persist($notification);

        $this->entityManager->flush();

        $this->addFlash('success', 'Application withdrawn successfully!');
        return $this->redirectToRoute('app_user_applications');
    }

    #[Route('/application/{id}', name: 'app_application_detail', methods: ['GET'])]
    public function applicationDetail(JobApplication $application): Response
    {
        if (!($this->isGranted('ROLE_USER') || $this->isGranted('ROLE_ADMIN'))) {
            throw $this->createAccessDeniedException('You must be a user or admin to view application details.');
        }
        /** @var User $user */
        $user = $this->getUser();

        if ($application->getUser() !== $user) {
            $this->addFlash('error', 'You cannot view this application!');
            return $this->redirectToRoute('app_user_applications');
        }

        return $this->render('jobs/application_detail.html.twig', [
            'application' => $application,
        ]);
    }

    private function getClientIp(): string
    {
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
}
