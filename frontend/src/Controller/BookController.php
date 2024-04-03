<?php

namespace App\Controller;

use App\Form\BookType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[Route('/book', name: 'app_book_')]
class BookController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(HttpClientInterface $httpClientInterface): Response
    {
        $request = $httpClientInterface->request('GET', 'http://localhost:3000/books');

        return $this->render('book/index.html.twig', [
            'books' => $request->toArray(),
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(HttpClientInterface $httpClientInterface, Request $request): Response
    {
        $form = $this->createForm(BookType::class);
        //data
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $httpClientInterface->request('POST', 'http://localhost:3000/books', [
                'json' => [
                    'title' => $data['title'],
                    'auteur' => $data['auteur'],
                    'description' => $data['description'],
                ],
            ]);

            return $this->redirectToRoute('app_book_index');
        }

        return $this->render('book/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, HttpClientInterface $httpClientInterface, $id): Response
    {
        $book = $httpClientInterface->request('GET', 'http://localhost:3000/books/' . $id)->toArray()[0];
        $form = $this->createForm(BookType::class);

        $form->setData([
            'title' => $book['title'],
            'auteur' => $book['auteur'],
            'description' => $book['description'],
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $httpClientInterface->request('PUT', 'http://localhost:3000/books/' . $id, [
                'json' => [
                    'title' => $data['title'],
                    'auteur' => $data['auteur'],
                    'description' => $data['description'],
                ],
            ]);

            return $this->redirectToRoute('app_book_index');
        }

        return $this->render('book/new.html.twig', [
            'book' => $book,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['GET'])]
    public function delete(HttpClientInterface $httpClientInterface, $id): Response
    {
        $httpClientInterface->request('DELETE', 'http://localhost:3000/books/' . $id);

        return $this->redirectToRoute('app_book_index');
    }
}
