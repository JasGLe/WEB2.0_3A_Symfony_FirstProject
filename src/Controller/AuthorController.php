<?php

namespace App\Controller;

use App\Entity\Author;
use App\Form\AuthorType;
use App\Repository\AuthorRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AuthorController extends AbstractController
{
    #[Route('/author', name: 'app_author')]
    public function index(): Response
    {
        return $this->render('author/index.html.twig', [
            'controller_name' => 'AuthorController',
        ]);
    }
    #[Route("/add",name:"add")]
    public function add(ManagerRegistry $doctrine,Request $request):Response{
        $author = new  Author();
        $em = $doctrine->getManager();
        $form =  $this->createForm(AuthorType::class, $author);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em->persist($author);
            $em->flush();
            return $this->redirectToRoute("findAll");
        }
        return $this->render("author/add.html.twig",[
             'form' => $form->createView(),
             ]);
    }

    #[Route("/findAll",name : "findAll")]
    public function findAll(AuthorRepository $repository):Response{
        $authors = $repository->findAll();
        return $this->render("author/authors.html.twig",[
            "authors" => $authors
        ]);
    }


    #[Route("/update/{id}" , name :"update")]
    public function update(ManagerRegistry $doctrine , $id , AuthorRepository $repository , Request $request):Response{
        $author = $repository->find($id);
        $em = $doctrine->getManager();
        $form = $this->createForm(AuthorType::class, $author);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em->flush();
            return new Response("Author updated successfully");
        }

        return $this->render("author/update.html.twig",[
            'form' => $form->createView(),
        ]);
    }

    #[Route("/delete/{id}" ,name : "delete")]
    public function delete(ManagerRegistry $doctrine , AuthorRepository $repository , $id):Response{
        $author = $repository->find($id);
        $em = $doctrine->getManager();
        $em->remove($author);
        $em->flush();
        return new Response("Author deleted successfully");
    }
    #[Route("/showAllAuthorDQ" ,name : "showAllAuthorDQ")]
    public function showAllAuthorQB(AuthorRepository $repo){
        $author = $repo->showAllQB();
        return $this->render("author/authors.html.twig",["authors" => $author
        ]);
    }
}