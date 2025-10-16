<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Author;
use App\Form\BookType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{
    #[Route('/book/add', name: 'app_book_add')]
    public function addBook(Request $request, EntityManagerInterface $em): Response
    {
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Initialisation de published à true
            $book->setPublished(true);

            // Récupérer l’auteur et incrémenter nb_books
            $author = $book->getAuthor();
            if ($author) {
                $author->setNbBooks($author->getNbBooks() + 1);
                $em->persist($author);
            }

            $em->persist($book);
            $em->flush();

            $this->addFlash('success', 'Book added successfully!');
            return $this->redirectToRoute('app_book_list');
        }

        return $this->render('book/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route('/books', name: 'app_book_list')]
public function listBooks(EntityManagerInterface $em): Response
{
    $bookRepository = $em->getRepository(Book::class);
    $publishedBooks = $bookRepository->findBy(['published' => true]);
    $unpublishedBooks = $bookRepository->findBy(['published' => false]);

    return $this->render('book/list.html.twig', [
        'publishedBooks' => $publishedBooks,
        'publishedCount' => count($publishedBooks),
        'unpublishedCount' => count($unpublishedBooks),
    ]);
}

 #[Route('/book/edit/{id}', name: 'app_book_edit')]
    public function editBook(int $id, Request $request, EntityManagerInterface $em): Response
    {
        $book = $em->getRepository(Book::class)->find($id);

        if (!$book) {
            $this->addFlash('error', 'Book not found!');
            return $this->redirectToRoute('app_book_list');
        }

        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Book updated successfully!');
            return $this->redirectToRoute('app_book_list');
        }

        return $this->render('book/edit.html.twig', [
            'form' => $form->createView(),
            'book' => $book,
        ]);
    }

       #[Route('/book/delete/{id}', name: 'app_book_delete')]
    public function deleteBook(int $id, EntityManagerInterface $em): Response
    {
        $book = $em->getRepository(Book::class)->find($id);

        if (!$book) {
            $this->addFlash('error', 'Book not found!');
            return $this->redirectToRoute('app_book_list');
        }

        // Get the author and decrement nb_books
        $author = $book->getAuthor();
        if ($author) {
            $author->setNbBooks($author->getNbBooks() - 1);
            $em->persist($author);
        }

        // Delete the book
        $em->remove($book);
        $em->flush();

        // Delete authors with nb_books = 0
        $this->deleteAuthorsWithZeroBooks($em);

        $this->addFlash('success', 'Book deleted successfully!');
        return $this->redirectToRoute('app_book_list');
    }

    /**
     * Delete all authors with nb_books = 0
     */
    private function deleteAuthorsWithZeroBooks(EntityManagerInterface $em): void
    {
        $authorRepository = $em->getRepository(Author::class);
        $authorsToDelete = $authorRepository->findBy(['nb_books' => 0]);

        foreach ($authorsToDelete as $author) {
            $em->remove($author);
        }

        $em->flush();
    }
    #[Route('/book/{id}', name: 'app_book_detail')]
    public function showdetailbook(EntityManagerInterface $em, int $id): Response
    {
        $book = $em->getRepository(Book::class)->find($id);

        if (!$book) {
            $this->addFlash('error', 'Book not found!');
            return $this->redirectToRoute('app_book_list');
        }

        return $this->render('book/detail.html.twig', [
            'book' => $book,
        ]);
    }
    
}
