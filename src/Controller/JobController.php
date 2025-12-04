<?php

namespace App\Controller;

use App\Repository\JobRepository;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/jobs', name: 'app_jobs_')]
class JobController extends AbstractController
{
    #[Route('', name: 'index')]
    public function index(JobRepository $jobRepository, CategoryRepository $categoryRepository, Request $request): Response
    {
        $jobs = $jobRepository->findActiveJobs();
        $categories = $categoryRepository->findAll();

        // Handle search
        $searchTerm = $request->query->get('search');
        if ($searchTerm) {
            $jobs = $jobRepository->search($searchTerm);
        }

        // Handle category filter
        $categoryId = $request->query->get('category');
        if ($categoryId) {
            $jobs = $jobRepository->findByCategory($categoryId);
        }

        return $this->render('job/index.html.twig', [
            'jobs' => $jobs,
            'categories' => $categories,
            'searchTerm' => $searchTerm,
            'selectedCategory' => $categoryId,
        ]);
    }

    #[Route('/{id}', name: 'show')]
    public function show(int $id, JobRepository $jobRepository): Response
    {
        $job = $jobRepository->find($id);

        if (!$job) {
            throw $this->createNotFoundException('Job not found');
        }

        return $this->render('job/show.html.twig', [
            'job' => $job,
        ]);
    }
}
