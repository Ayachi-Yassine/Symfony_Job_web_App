<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserProfile;
use App\Entity\UserActivity;
use App\Repository\UserProfileRepository;
use App\Repository\JobApplicationRepository;
use App\Repository\UserActivityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Doctrine\ORM\EntityManagerInterface;

#[Route('/profile')]
class ProfileController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserProfileRepository $userProfileRepository,
        private JobApplicationRepository $jobApplicationRepository,
        private UserActivityRepository $userActivityRepository,
    ) {}

    #[Route('', name: 'app_profile_view', methods: ['GET'])]
    public function view(): Response
    {
        $this->ensureUserOrAdmin();
        /** @var User $user */
        $user = $this->getUser();
        $profile = $user->getProfile();

        // If profile doesn't exist, create one
        if (!$profile) {
            $profile = new UserProfile();
            $profile->setUser($user);
            $user->setProfile($profile);
            $this->entityManager->persist($profile);
            $this->entityManager->flush();
        }

        // Log profile view activity
        $activity = new UserActivity();
        $activity->setUser($user);
        $activity->setAction(UserActivity::ACTION_PROFILE_VIEW);
        $activity->setDescription('User viewed their profile');
        $activity->setIpAddress($this->getClientIp());
        $activity->setUserAgent($_SERVER['HTTP_USER_AGENT'] ?? null);
        $this->entityManager->persist($activity);
        $this->entityManager->flush();

        $applications = $this->jobApplicationRepository->findByUser($user);

        return $this->render('profile/view.html.twig', [
            'profile' => $profile,
            'applications' => $applications,
        ]);
    }

    #[Route('/edit', name: 'app_profile_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request): Response
    {
        $this->ensureUserOrAdmin();
        /** @var User $user */
        $user = $this->getUser();
        $profile = $user->getProfile();

        // If profile doesn't exist, create one
        if (!$profile) {
            $profile = new UserProfile();
            $profile->setUser($user);
            $user->setProfile($profile);
        }

        if ($request->isMethod('POST')) {
            $profile->setFirstName($request->request->get('firstName'));
            $profile->setLastName($request->request->get('lastName'));
            $profile->setPhone($request->request->get('phone'));
            $profile->setAddress($request->request->get('address'));
            $profile->setCity($request->request->get('city'));
            $profile->setPostalCode($request->request->get('postalCode'));
            $profile->setBio($request->request->get('bio'));
            $profile->setUpdatedAt(new \DateTime());

            $this->entityManager->persist($profile);
            $this->entityManager->flush();

            // Log profile update activity
            $activity = new UserActivity();
            $activity->setUser($user);
            $activity->setAction(UserActivity::ACTION_PROFILE_UPDATE);
            $activity->setDescription('User updated their profile information');
            $activity->setIpAddress($this->getClientIp());
            $activity->setUserAgent($_SERVER['HTTP_USER_AGENT'] ?? null);
            $this->entityManager->persist($activity);
            $this->entityManager->flush();

            $this->addFlash('success', 'Profile updated successfully!');
            return $this->redirectToRoute('app_profile_view');
        }

        return $this->render('profile/edit.html.twig', [
            'profile' => $profile,
        ]);
    }

    #[Route('/cv-upload', name: 'app_profile_cv_upload', methods: ['POST'])]
    public function uploadCV(Request $request): Response
    {
        $this->ensureUserOrAdmin();
        /** @var User $user */
        $user = $this->getUser();
        $profile = $user->getProfile();

        if (!$profile) {
            $profile = new UserProfile();
            $profile->setUser($user);
        }

        $cvFile = $request->files->get('cv');
        if ($cvFile) {
            // Validate file is PDF. getMimeType() may throw if symfony/mime is missing,
            // so try/catch and fallback to client mime type and extension.
            $mime = null;
            try {
                $mime = $cvFile->getMimeType();
            } catch (\LogicException $e) {
                $mime = $cvFile->getClientMimeType();
            }

            $originalName = $cvFile->getClientOriginalName();
            $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

            if ($mime !== 'application/pdf' && $ext !== 'pdf') {
                $this->addFlash('error', 'Only PDF files are allowed!');
                return $this->redirectToRoute('app_profile_edit');
            }

            // Generate unique filename
            $originalName = $cvFile->getClientOriginalName();
            $safeFilename = pathinfo($originalName, PATHINFO_FILENAME) . '_' . uniqid() . '.pdf';

            // Move file to uploads directory
            $uploadsDir = $this->getParameter('kernel.project_dir') . '/public/uploads/cv';
            if (!is_dir($uploadsDir)) {
                mkdir($uploadsDir, 0755, true);
            }

            $cvFile->move($uploadsDir, $safeFilename);

            // Delete old CV if exists
            if ($profile->getCvFileName()) {
                $oldCvPath = $uploadsDir . '/' . $profile->getCvFileName();
                if (file_exists($oldCvPath)) {
                    unlink($oldCvPath);
                }
            }

            $profile->setCvFileName($safeFilename);
            $profile->setUpdatedAt(new \DateTime());
            $this->entityManager->persist($profile);
            $this->entityManager->flush();

            // Log CV upload activity
            $activity = new UserActivity();
            $activity->setUser($user);
            $activity->setAction(UserActivity::ACTION_CV_UPLOAD);
            $activity->setDescription('User uploaded CV: ' . $originalName);
            $activity->setIpAddress($this->getClientIp());
            $activity->setUserAgent($_SERVER['HTTP_USER_AGENT'] ?? null);
            $this->entityManager->persist($activity);
            $this->entityManager->flush();

            $this->addFlash('success', 'CV uploaded successfully!');
        }

        return $this->redirectToRoute('app_profile_edit');
    }

    #[Route('/change-password', name: 'app_profile_change_password', methods: ['POST'])]
    public function changePassword(Request $request): Response
    {
        $this->ensureUserOrAdmin();
        /** @var User $user */
        $user = $this->getUser();

        $currentPassword = $request->request->get('currentPassword');
        $newPassword = $request->request->get('newPassword');
        $confirmPassword = $request->request->get('confirmPassword');

        // In production, you should use proper password hashing validation
        if ($newPassword !== $confirmPassword) {
            $this->addFlash('error', 'Passwords do not match!');
            return $this->redirectToRoute('app_profile_edit');
        }

        if (strlen($newPassword) < 6) {
            $this->addFlash('error', 'Password must be at least 6 characters long!');
            return $this->redirectToRoute('app_profile_edit');
        }

        // Hash and update password
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
        $user->setPassword($hashedPassword);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        // Log password change activity
        $activity = new UserActivity();
        $activity->setUser($user);
        $activity->setAction(UserActivity::ACTION_PASSWORD_CHANGE);
        $activity->setDescription('User changed their password');
        $activity->setIpAddress($this->getClientIp());
        $activity->setUserAgent($_SERVER['HTTP_USER_AGENT'] ?? null);
        $this->entityManager->persist($activity);
        $this->entityManager->flush();

        $this->addFlash('success', 'Password changed successfully!');
        return $this->redirectToRoute('app_profile_view');
    }

    #[Route('/activity', name: 'app_profile_activity', methods: ['GET'])]
    public function viewActivity(): Response
    {
        $this->ensureUserOrAdmin();
        /** @var User $user */
        $user = $this->getUser();
        $activities = $this->userActivityRepository->findByUser($user, 100);

        return $this->render('profile/activity.html.twig', [
            'activities' => $activities,
        ]);
    }

    private function getClientIp(): string
    {
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }

    private function ensureUserOrAdmin(): void
    {
        if (!($this->isGranted('ROLE_USER') || $this->isGranted('ROLE_ADMIN'))) {
            throw $this->createAccessDeniedException('You must be a user or admin to access this resource.');
        }
    }
}
