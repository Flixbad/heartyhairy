<?php

namespace App\Controller;

use App\Entity\Dish;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Category;
use App\Form\PaymentType;
use App\Repository\DishRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class HeartyController extends AbstractController
{
    #[Route('/menu', name: 'menu')]
    public function menu(DishRepository $dishRepository, SessionInterface $session): Response
    {
        $dishes = $dishRepository->findAll();
        $panier = $session->get('panier', []);
        $quantitePanier = array_sum($panier);

        return $this->render('hearty/menu.html.twig', [
            'dishes' => $dishes,
            'quantitePanier' => $quantitePanier
        ]);
    }

    #[Route('/', name: 'home')]
    public function index(SessionInterface $session): Response
    {
        $panier = $session->get('panier', []);
        $quantitePanier = array_sum($panier);

        return $this->render('home/home.html.twig', [
            'quantitePanier' => $quantitePanier
        ]);
    }

    #[Route('/order', name: 'create_order', methods: ['POST'])]
    public function order(Request $request, EntityManagerInterface $em, DishRepository $dishRepo): Response
    {
        $data = $request->request->all();
        $order = new Order();
        $total = 0;

        foreach ($data['items'] as $item) {
            $dish = $dishRepo->find($item['dish_id']);
            if ($dish) {
                $orderItem = new OrderItem();
                $orderItem->setDish($dish);
                $orderItem->setQuantity($item['quantity']);
                $order->addOrderItem($orderItem);
                $total += $dish->getPrice() * $item['quantity'];
            }
        }

        $order->setTotalprice($total);
        $em->persist($order);
        $em->flush();

        return $this->redirectToRoute('order_summary', ['id' => $order->getId()]);
    }

    #[Route('/order/{id}', name: 'order_summary')]
    public function summary(Order $order, SessionInterface $session): Response
    {
        $panier = $session->get('panier', []);
        $quantitePanier = array_sum($panier);

        return $this->render('order/summary.html.twig', [
            'order' => $order,
            'quantitePanier' => $quantitePanier
        ]);
    }

    #[Route('/conf', name: 'admin')]
    public function adminPanel(CategoryRepository $catRepo, DishRepository $dishRepo, SessionInterface $session): Response
    {
        $panier = $session->get('panier', []);
        $quantitePanier = array_sum($panier);

        return $this->render('admin/index.html.twig', [
            'categories' => $catRepo->findAll(),
            'dishes' => $dishRepo->findAll(),
            'quantitePanier' => $quantitePanier
        ]);
    }

    #[Route('/panier/ajout', name: 'panier_ajout', methods: ['POST'])]
    public function addToCart(Request $request, SessionInterface $session): Response
    {
        $quantities = $request->request->all('quantities');
        $panier = $session->get('panier', []);

        foreach ($quantities as $dishId => $qty) {
            if ((int)$qty > 0) {
                $panier[$dishId] = isset($panier[$dishId]) ? $panier[$dishId] + (int)$qty : (int)$qty;
            }
        }

        $session->set('panier', $panier);

        $this->addFlash('success', '✅ Plat ajouté au panier !');
        return $this->redirectToRoute('menu');
    }

    #[Route('/panier', name: 'panier_affichage')]
    public function showCart(SessionInterface $session, DishRepository $dishRepo): Response
    {
        $panier = $session->get('panier', []);
        $items = [];
        $total = 0;

        foreach ($panier as $dishId => $qty) {
            $dish = $dishRepo->find($dishId);
            if ($dish) {
                $items[] = ['dish' => $dish, 'quantity' => $qty];
                $total += $dish->getPrice() * $qty;
            }
        }

        $quantitePanier = array_sum($panier);

        return $this->render('order/cart.html.twig', [
            'items' => $items,
            'total' => $total,
            'quantitePanier' => $quantitePanier
        ]);
    }

    #[Route('/panier/supprimer/{id}', name: 'supprimer_article')]
public function supprimerArticle(int $id, SessionInterface $session): Response
{
    $panier = $session->get('panier', []);

    if (isset($panier[$id])) {
        // Retire une seule unité
        if ($panier[$id] > 1) {
            $panier[$id]--;
        } else {
            unset($panier[$id]);
        }

        $session->set('panier', $panier);
        $this->addFlash('success', '🗑️ 1 unité retirée du panier');
    }

    return $this->redirectToRoute('panier_affichage');
}

    #[Route('/panier/valider', name: 'valider_commande', methods: ['POST'])]
    public function validerCommande(SessionInterface $session, EntityManagerInterface $em, DishRepository $dishRepo): Response
    {
        $panier = $session->get('panier', []);
        $order = new Order();
        $total = 0;

        foreach ($panier as $dishId => $qty) {
            $dish = $dishRepo->find($dishId);
            if ($dish) {
                $item = new OrderItem();
                $item->setDish($dish);
                $item->setQuantity($qty);
                $order->addOrderItem($item);
                $total += $dish->getPrice() * $qty;
            }
        }

        $order->setTotalprice($total);
        $em->persist($order);
        $em->flush();

        $session->remove('panier');

        return $this->redirectToRoute('order_summary', ['id' => $order->getId()]);
    }

    #[Route('/panier/vider', name: 'vider_panier')]
    public function viderPanier(SessionInterface $session): Response
    {
        $session->remove('panier');
        return $this->redirectToRoute('panier_affichage');
    }

    #[Route('/paiement', name: 'afficher_paiement')]
    public function afficherPaiement(Request $request, SessionInterface $session, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(PaymentType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $mode = $data['payment'];
            $session->set('mode_paiement', $mode);

            return $this->redirectToRoute('valider_commande');
        }

        $panier = $session->get('panier', []);
        $quantitePanier = array_sum($panier);

        return $this->render('order/paiement.html.twig', [
            'form' => $form->createView(),
            'quantitePanier' => $quantitePanier
        ]);
    }
}
