<?php

namespace App\Controller;

use App\Repository\JobRepository;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(JobRepository $jobRepository, CategoryRepository $categoryRepository): Response
    {
        $jobs = $jobRepository->findActiveJobs();
        $categories = $categoryRepository->findAll();
        $featuredJobs = array_slice($jobs, 0, 6);

        return $this->render('home/index.html.twig', [
            'jobs' => $featuredJobs,
            'categories' => $categories,
            'totalJobs' => count($jobs),
        ]);
    }

    #[Route('/about', name: 'app_about')]
    public function about(): Response
    {
        return $this->render('home/about.html.twig');
    }

    #[Route('/contact', name: 'app_contact')]
    public function contact(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            // Handle contact form submission here
            $this->addFlash('success', 'Your message has been sent successfully!');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('home/contact.html.twig');
    }

    #[Route('/testimonials', name: 'app_testimonials')]
    public function testimonials(): Response
    {
        return $this->render('home/testimonials.html.twig');
    }
}
