<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\CategoryRepository;
use App\Entity\Category;
use App\Form\CategoryType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route("/admin/category" , name: 'admin.category.')]
class CategoryController extends AbstractController
{
    #[Route(name: 'index')]
    public function index(CategoryRepository $repository): Response
    {
        return $this->render('admin/category/index.html.twig', [
            'categories' => $repository->findAllWithCount() 
        ]);
    }

    #[Route('/create', name: 'create')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class , $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em -> persist($category);
            $em ->flush();
            $this->addFlash('success', 'La catégorie a bien été ajoutée');
            return $this->redirectToRoute('admin.category.index');
        }

        return $this->render('/admin/category/create.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/{id}', name: 'edit', requirements: ['id' => '\d+'], methods: ['GET' , 'POST'])]
    public function edit(Category $category, Request $request, EntityManagerInterface $em): Response
    {

        $form = $this->createForm(CategoryType::class , $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em ->flush();
            $this->addFlash('success', 'La catégorie a bien été modifiée');
            return $this->redirectToRoute('admin.category.index');
        }

        return $this->render('/admin/category/edit.html.twig', [
            'category' => $category,
            'form' => $form
        ]);;
    }

    #[Route('/{id}', name: 'delete', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    public function remove(Category $category, EntityManagerInterface $em): Response
    {
        $em->remove($category);
        $em->flush();
        $this->addFlash('success', 'La catégorie a bien été supprimée');
        return $this->redirectToRoute('admin.category.index');
    }
}
