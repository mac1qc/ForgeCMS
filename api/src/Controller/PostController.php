<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/post')]
final class PostController extends AbstractController
{
    #[Route('/api/posts', name: 'api_post_index', methods: ['GET'])]
    public function index(PostRepository $postRepository): Response
    {
        $posts = $postRepository->findAll();
        return $this->json($posts);
    }

    #[Route('/api/posts', name: 'api_post_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);

        $post = new Post();
        $post->setTitle($data['title']);
        $post->setContent($data['content']);

        $entityManager->persist($post);
        $entityManager->flush();

        return $this->json($post, Response::HTTP_CREATED);
    }

    #[Route('/api/posts/{id}', name: 'api_post_show', methods: ['GET'])]
    public function show(Post $post): Response
    {
        return $this->json($post);
    }

    #[Route('/api/posts/{id}', name: 'api_post_edit', methods: ['PUT'])]
    public function edit(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);

        $post->setTitle($data['title']);
        $post->setContent($data['content']);

        $entityManager->flush();

        return $this->json($post);
    }

    #[Route('/api/posts/{id}', name: 'api_post_delete', methods: ['DELETE'])]
    public function delete(Post $post, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($post);
        $entityManager->flush();

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
