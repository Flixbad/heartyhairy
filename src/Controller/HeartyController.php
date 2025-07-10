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
    public function menu(DishRepository $dishRepository): Response
    {
        $dishes = $dishRepository->findAll();
        return $this->render('hearty/menu.html.twig', ['dishes' => $dishes]);
    }

        #[Route('/', name: 'home')]
        public function index(): Response
        {
            return $this->render('home/home.html.twig');
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
    public function summary(Order $order): Response
    {
        return $this->render('order/summary.html.twig', ['order' => $order]);
    }

    #[Route('/conf', name: 'admin')]
    public function adminPanel(CategoryRepository $catRepo, DishRepository $dishRepo): Response
    {
        return $this->render('admin/index.html.twig', [
            'categories' => $catRepo->findAll(),
            'dishes' => $dishRepo->findAll()
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

    return $this->redirectToRoute('panier_affichage');
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

    return $this->render('order/cart.html.twig', ['items' => $items, 'total' => $total]);
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

        // Tu peux l'utiliser ou le stocker dans l'entité Order par exemple
        $session->set('mode_paiement', $mode);

        return $this->redirectToRoute('valider_commande');
    }

    return $this->render('order/paiement.html.twig', [
        'form' => $form->createView(),
    ]);
}





}

