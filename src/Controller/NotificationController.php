<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\NotificationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Doctrine\ORM\EntityManagerInterface;

class NotificationController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private NotificationRepository $notificationRepository,
    ) {}

    #[Route('/notifications', name: 'app_notifications', methods: ['GET'])]
    public function index(): Response
    {
        if (!($this->isGranted('ROLE_USER') || $this->isGranted('ROLE_ADMIN'))) {
            throw $this->createAccessDeniedException('You must be a user or admin to view notifications.');
        }

        /** @var User $user */
        $user = $this->getUser();
        $notifications = $this->notificationRepository->findLatestByUser($user, 50);

        return $this->render('notifications/index.html.twig', [
            'notifications' => $notifications,
        ]);
    }

    #[Route('/notifications/unread', name: 'app_notifications_unread', methods: ['GET'])]
    public function unread(): Response
    {
        if (!($this->isGranted('ROLE_USER') || $this->isGranted('ROLE_ADMIN'))) {
            throw $this->createAccessDeniedException('You must be a user or admin to view notifications.');
        }

        /** @var User $user */
        $user = $this->getUser();
        $notifications = $this->notificationRepository->findUnreadByUser($user);

        return $this->render('notifications/unread.html.twig', [
            'notifications' => $notifications,
        ]);
    }

    #[Route('/notifications/{id}/mark-read', name: 'app_notification_mark_read', methods: ['POST'])]
    public function markRead($id, Request $request): Response
    {
        if (!($this->isGranted('ROLE_USER') || $this->isGranted('ROLE_ADMIN'))) {
            throw $this->createAccessDeniedException('You must be a user or admin to modify notifications.');
        }

        /** @var User $user */
        $user = $this->getUser();
        $notification = $this->entityManager->getRepository('App:Notification')->find($id);

        if (!$notification || $notification->getUser() !== $user) {
            $this->addFlash('error', 'Notification not found!');
            return $this->redirectToRoute('app_notifications');
        }

        $notification->setIsRead(true);
        $this->entityManager->persist($notification);
        $this->entityManager->flush();

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(['success' => true]);
        }

        return $this->redirectToRoute('app_notifications');
    }

    #[Route('/notifications/{id}/delete', name: 'app_notification_delete', methods: ['POST'])]
    public function delete($id, Request $request): Response
    {
        if (!($this->isGranted('ROLE_USER') || $this->isGranted('ROLE_ADMIN'))) {
            throw $this->createAccessDeniedException('You must be a user or admin to delete notifications.');
        }

        /** @var User $user */
        $user = $this->getUser();
        $notification = $this->entityManager->getRepository('App:Notification')->find($id);

        if (!$notification || $notification->getUser() !== $user) {
            $this->addFlash('error', 'Notification not found!');
            return $this->redirectToRoute('app_notifications');
        }

        $this->entityManager->remove($notification);
        $this->entityManager->flush();

        $this->addFlash('success', 'Notification deleted!');

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(['success' => true]);
        }

        return $this->redirectToRoute('app_notifications');
    }

    #[Route('/notifications/mark-all-read', name: 'app_notifications_mark_all_read', methods: ['POST'])]
    public function markAllRead(): Response
    {
        if (!($this->isGranted('ROLE_USER') || $this->isGranted('ROLE_ADMIN'))) {
            throw $this->createAccessDeniedException('You must be a user or admin to mark notifications.');
        }

        /** @var User $user */
        $user = $this->getUser();
        $notifications = $this->notificationRepository->findUnreadByUser($user);

        foreach ($notifications as $notification) {
            $notification->setIsRead(true);
            $this->entityManager->persist($notification);
        }

        $this->entityManager->flush();

        $this->addFlash('success', 'All notifications marked as read!');
        return $this->redirectToRoute('app_notifications');
    }

    #[Route('/notifications/count-unread', name: 'app_notifications_count_unread', methods: ['GET'])]
    public function countUnread(): JsonResponse
    {
        if (!($this->isGranted('ROLE_USER') || $this->isGranted('ROLE_ADMIN'))) {
            return new JsonResponse(['count' => 0]);
        }

        /** @var User $user */
        $user = $this->getUser();
        $count = $this->notificationRepository->countUnreadByUser($user);

        return new JsonResponse(['count' => $count]);
    }
}
