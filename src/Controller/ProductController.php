<?php

namespace App\Controller;

use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    #[Route('/product', name: 'app_product')]
    public function index(): Response
    {
        return $this->render('product/index.html.twig', [
            'controller_name' => 'ProductController',
        ]);
    }

    #[Route('/add_product', name: 'add_product')]
    public function add(Request $request)
    {
        if ($request->isMethod('POST')) {

            $id = $request->get('id');
            $name = $request->get('name');
            $price = $request->get('price');
            $quantity = $request->get('quantity');
            $description = $request->get('description');

            $product = new Product($id, $name, $price, $quantity, $description);
            
            $session = $request->getSession();
            $products = $session->get('products', []);
            $products[] = $product;
            $session->set('products', $products);

            return $this->redirectToRoute('list');
        }
        return $this->render('add.html.twig');
    }

    #[Route('/products', name: 'list')]
    public function show(Request $request)
    {
        $session = $request->getSession();
        $products = $session->get('products', []);

        return $this->render('list.html.twig', ['products' => $products]);
    }

    #[Route('/delete_product/{id}', name: 'delete_product')]
    public function delete(Request $request, $id)
    {
        $session = $request->getSession();
        $products = $session->get('products', []);

        foreach ($products as $k => $v) {
            if ($v->getId() == $id) {
                unset($products[$k]);
                break;
            }
        }

        $session->set('products', $products);

        return $this->redirectToRoute('list');
    }

    #[Route('/product/{id}', name: 'details')]
    public function detail($id, Request $request)
    {
        $session = $request->getSession();
        $products = $session->get('products', []);

        foreach ($products as $product) {
            if ($product->getId() == $id) {
                return $this->render('detail.html.twig', ['product' => $product]);
            }
        }

    }

    #[Route('/products', name: 'list')]
    public function search(Request $request)
    {
        $session = $request->getSession();
        $products = $session->get('products', []);
        
        $item = $request->query->get('item');
        if ($item) {
            $products = array_filter($products, function ($product) use ($item){
                return strpos($product->getName(), $item) !== false;
            });
        }
        return $this->render('list.html.twig', ['products' => $products,'item' => $item]);
    }



}
