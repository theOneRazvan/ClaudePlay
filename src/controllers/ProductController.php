<?php

namespace PetFactory\Controllers;

use PetFactory\Core\Controller;
use PetFactory\Models\Product;
use PetFactory\Models\Collection;

class ProductController extends Controller
{
    public function index()
    {
        $page = $_GET['page'] ?? 1;
        $offset = ($page - 1) * ITEMS_PER_PAGE;

        $products = Product::all(true, ITEMS_PER_PAGE, $offset);
        $totalProducts = Product::count(true);
        $totalPages = ceil($totalProducts / ITEMS_PER_PAGE);

        $this->view('pages/products', [
            'products' => $products,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'collections' => Collection::all(true)
        ]);
    }

    public function show($slug)
    {
        $product = Product::findBySlug($slug);

        if (!$product) {
            http_response_code(404);
            $this->view('pages/404');
            return;
        }

        $this->view('pages/product-detail', [
            'product' => $product,
            'csrf_token' => $this->generateCSRF()
        ]);
    }

    public function byCollection($slug)
    {
        $collection = Collection::findBySlug($slug);

        if (!$collection) {
            http_response_code(404);
            $this->view('pages/404');
            return;
        }

        $page = $_GET['page'] ?? 1;
        $offset = ($page - 1) * ITEMS_PER_PAGE;

        $products = Product::getByCollection($collection['id'], ITEMS_PER_PAGE, $offset);
        $totalProducts = Collection::getProductCount($collection['id']);
        $totalPages = ceil($totalProducts / ITEMS_PER_PAGE);

        $this->view('pages/collection', [
            'collection' => $collection,
            'products' => $products,
            'currentPage' => $page,
            'totalPages' => $totalPages
        ]);
    }

    public function search()
    {
        $query = $_GET['q'] ?? '';

        if (empty($query)) {
            $this->redirect('/products');
        }

        $products = Product::search($query, 50);

        $this->view('pages/search', [
            'query' => $query,
            'products' => $products
        ]);
    }
}
