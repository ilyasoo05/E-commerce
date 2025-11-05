<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Profile;
use App\Entity\Comment;
use App\Form\CommandeType;
use App\Form\ProfileType;
use App\Form\CommentType;
use App\Repository\ProductRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ClientController extends AbstractController
{
    #[Route('/client', name: 'client_dashboard')]
    public function client(ProductRepository $productRepository): Response
    {
        $allProducts = $productRepository->findAll();
        shuffle($allProducts);
        $recommendedProducts = array_slice($allProducts, 0, 10);

        return $this->render('client/dashboard.html.twig', [
            'recommendedProducts' => $recommendedProducts
        ]);
    }

    #[Route('/profile', name: 'profile_page')]
    public function profile(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        $profile = $user->getProfile();
        if (!$profile) {
            $profile = new Profile();
            $profile->setUser($user);
        }

        $form = $this->createForm(ProfileType::class, $profile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($profile);
            $entityManager->flush();

            $this->addFlash('success', 'Profile updated successfully.');
            return $this->redirectToRoute('profile_page');
        }

        return $this->render('client/profile.html.twig', [
            'profileForm' => $form->createView(),
            'profile' => $profile,
        ]);
    }

    #[Route('/category/{name}', name: 'category_products')]
    public function categoryProducts(string $name, ProductRepository $productRepository): Response
    {
        $products = $productRepository->findBy(['category' => $name]);

        return $this->render('client/categoryproducts.html.twig', [
            'products' => $products,
            'categoryName' => $name
        ]);
    }

    #[Route('/cart', name: 'cart_page')]
    public function cart(SessionInterface $session): Response
    {
        $cart = $session->get('cart', []);
        $total = 0;

        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        return $this->render('client/cart.html.twig', [
            'cart' => $cart,
            'total' => $total,
        ]);
    }

    #[Route('/cart/add/{id}', name: 'cart_add')]
    public function addToCart(int $id, ProductRepository $productRepository, SessionInterface $session): Response
    {
        $product = $productRepository->find($id);

        if (!$product) {
            throw $this->createNotFoundException('Product not found');
        }

        $cart = $session->get('cart', []);

        if (isset($cart[$product->getId()])) {
            $cart[$product->getId()]['quantity']++;
        } else {
            $cart[$product->getId()] = [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'price' => $product->getPrice(),
                'quantity' => 1,
                'image' => $product->getImage(),
            ];
        }

        $session->set('cart', $cart);

        return $this->redirectToRoute('cart_page');
    }

    #[Route('/cart/remove/{id}', name: 'cart_remove')]
    public function removeFromCart(int $id, SessionInterface $session): Response
    {
        $cart = $session->get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
        }

        $session->set('cart', $cart);

        return $this->redirectToRoute('cart_page');
    }

    #[Route('/paiement', name: 'client_paiement')]
    public function paiement(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        $commande = new Commande();
        $commande->setUser($user);

        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($commande);
            $entityManager->flush();

            $this->addFlash('success', 'Order has been placed successfully!');
            return $this->redirectToRoute('client_dashboard');
        }

        return $this->render('client/paiement.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/client/comment', name: 'client_comments')]
    public function comments(Request $request, EntityManagerInterface $em, CommentRepository $commentRepository): Response
    {
        $user = $this->getUser();

        $comment = new Comment();
        $comment->setUser($user);

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($comment);
            $em->flush();

            $this->addFlash('success', 'Your comment has been submitted!');
            return $this->redirectToRoute('client_comments');
        }

        $comments = $commentRepository->findAll();

        return $this->render('client/comment.html.twig', [
            'form' => $form->createView(),
            'comments' => $comments,
        ]);
    }
}
