<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class TransactionController extends Controller
{
    public function create()
    {
        $products = Product::take(12)->get();

        return view('pos.create', ['products' => $products]);
    }

    public function store()
    {
        return 'Transaksi disimpan (belum ada logika penyimpanan)';
    }

    public function index()
    {
        return view('transaction.index');
    }

    public function show(string $id)
    {
        return "Detail transaksi #{$id}";
    }
}
