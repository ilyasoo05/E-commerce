<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\UserRepository;
use App\Repository\ProductRepository;
use App\Repository\CommandeRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin_dashboard')]
    public function admin(UserRepository $userRepository, ProductRepository $productRepository, CommandeRepository $commandeRepository, CommentRepository $commentRepository): Response
    {
        $totalClients = $userRepository->getTotalClients();
        $totalProducts = $productRepository->count([]);
        $totalCommandes = $commandeRepository->count([]);
        $recentComments = $commentRepository->findBy([], ['id' => 'DESC'], 3);
        $totalComments = $commentRepository->count([]);
        
        return $this->render('admin/dashboard.html.twig', [
            'totalClients' => $totalClients,
            'totalProducts' => $totalProducts,
            'totalCommandes' => $totalCommandes,
            'recentComments' => $recentComments,
            'totalComments' => $totalComments,
        ]);
    }



    #[Route('/admin/product', name: 'admin_addproduct')]
    public function addProduct(Request $request, EntityManagerInterface $entityManager): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();
            
            if ($imageFile) {
                $newFilename = uniqid().'.'.$imageFile->guessExtension();
                try {
                    $imageFile->move(
                        $this->getParameter('uploads_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', 'Failed to upload image.');
                }
                
                $product->setImage($newFilename);
            }
            
            $entityManager->persist($product);
            $entityManager->flush();
            
            $this->addFlash('success', 'Product added successfully!');
            return $this->redirectToRoute('admin_dashboard');
        }
        
        return $this->render('admin/addproduct.html.twig', [
            'form' => $form->createView(),
            'is_edit' => false,
        ]);
    }



    #[Route('/admin/products', name: 'admin_product')]
    public function viewProducts(Request $request, ProductRepository $productRepository): Response
    {
        $category = $request->query->get('category');
        
        if ($category) {
            $products = $productRepository->findBy(['category' => $category]);
        } else {
            $products = $productRepository->findAll();
        }
    
        return $this->render('admin/product.html.twig', [
            'products' => $products,
            'selectedCategory' => $category,
        ]);
    }



    #[Route('/admin/product/edit/{id}', name: 'admin_editproduct')]
    public function editProduct(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $product = $entityManager->getRepository(Product::class)->find($id);
        
        if (!$product) {
            throw $this->createNotFoundException('Product not found.');
        }
        
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();
            
            if ($imageFile) {
                $newFilename = uniqid().'.'.$imageFile->guessExtension();
                try {
                    $imageFile->move($this->getParameter('uploads_directory'), $newFilename);
                    $product->setImage($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Failed to upload image.');
                }
            }
            
            $entityManager->flush();
            $this->addFlash('success', 'Product updated successfully!');
            return $this->redirectToRoute('admin_product');
        }
        
        return $this->render('admin/addproduct.html.twig', [
            'form' => $form->createView(),
            'is_edit' => true,
        ]);
    }



    #[Route('/admin/product/delete/{id}', name: 'admin_deleteproduct', methods: ['POST', 'DELETE'])]
    public function deleteProduct(Request $request, int $id, EntityManagerInterface $em): Response
    {
        $product = $em->getRepository(Product::class)->find($id);
        
        if (!$product) {
            throw $this->createNotFoundException('Product not found.');
        }
        
        $submittedToken = $request->request->get('_token');
        
        if ($this->isCsrfTokenValid('delete' . $product->getId(), $submittedToken)) {
            $em->remove($product);
            $em->flush();
            $this->addFlash('success', 'Product deleted successfully.');
        } else {
            $this->addFlash('error', 'Invalid CSRF token.');
        }
        
        return $this->redirectToRoute('admin_product');
    }



    #[Route('/admin/clients', name: 'admin_clients')]
    public function clients(UserRepository $userRepository): Response
    {
        $clients = $userRepository->createQueryBuilder('u')
            ->leftJoin('u.profile', 'p')
            ->addSelect('p')
            ->where('u.roles LIKE :role')
            ->setParameter('role', '%"ROLE_USER"%')
            ->getQuery()
            ->getResult();
            
        return $this->render('admin/client.html.twig', [
            'clients' => $clients,
        ]);
    }



    #[Route('/admin/orders', name: 'admin_orders')]
    public function orders(CommandeRepository $commandeRepository): Response
    {
        $commandes = $commandeRepository->findAll();

        return $this->render('admin/orders.html.twig', [
            'commandes' => $commandes,
        ]);
    }



#[Route('/admin/comment', name: 'admin_comments')]
public function comment(CommentRepository $commentRepository): Response
{
    $comments = $commentRepository->findBy([], ['id' => 'DESC'], 3);

    return $this->render('admin/comment.html.twig', [
        'comments' => $comments,
    ]);
}
}
