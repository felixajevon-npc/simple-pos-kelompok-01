<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Product;

class TransactionController extends Controller
{
    public function create()
    {
        $products = Product::where('stock', '>', 0)->paginate(12);

        return view('pos.create', ['products' => $products]);
    }

    public function store()
    {
        return 'Transaksi disimpan (belum ada logika penyimpanan)';
    }

    public function index()
    {
        $transactions = Transaction::with(['details.product', 'user'])
            ->latest()
            ->paginate(15);

        return view('transactions.index', compact('transactions'));
    }

    public function show(string $id)
    {
        return "Detail transaksi #{$id}";
    }
}
