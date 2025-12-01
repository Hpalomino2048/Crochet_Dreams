<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Inertia\Inertia;
use Inertia\Response;

class WelcomeController extends Controller
{
    /**
     * Mostrar la página de bienvenida con productos destacados
     */
    public function index(): Response
    {
        // Obtener productos para mostrar en la portada
        $products = Product::query()
        ->orderBy('created_at', 'desc')
        ->take(8)
        ->get()
        ->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                // Genera la URL completa
                'thumbnail' => $product->thumbnail 
                    ? asset('storage/' . $product->thumbnail) 
                    : null,
                'slug' => $product->slug,
            ];
        });

        return Inertia::render('Welcome', [
            'products' => $products,
            'name' => 'Crochet Dreams',
            'quote' => [
                'message' => 'Cada puntada cuenta una historia',
                'author' => 'Artesanía con amor'
            ]
        ]);
    }
}