<?php

namespace PetFactory\Controllers;

use PetFactory\Core\Controller;
use PetFactory\Models\Product;
use PetFactory\Models\Collection;

class HomeController extends Controller
{
    public function index()
    {
        $featuredProducts = Product::getFeatured(8);
        $collections = Collection::all(true);

        $this->view('pages/home', [
            'featuredProducts' => $featuredProducts,
            'collections' => $collections
        ]);
    }

    public function about()
    {
        $this->view('pages/about');
    }

    public function contact()
    {
        $this->view('pages/contact', [
            'csrf_token' => $this->generateCSRF()
        ]);
    }
}
