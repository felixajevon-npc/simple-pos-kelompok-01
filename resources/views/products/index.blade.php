@extends('layouts.app')
@section('title', 'Produk')

@section('content')
<h1 class="text-lg font-semibold mb-4">Daftar Produk</h1>
@if (session('success'))
    <div class="bg-green-50 text-green-700 p-3 rounded-md mb-4">
        {{ session('success') }}
    </div>
@endif

<div class="flex items-center gap-4 mb-4">
    <a href="{{ route('products.create') }}"
       class="bg-blue-600 text-white px-4 py-2 rounded-md">
        Tambah Produk
    </a>

    <form action="{{ route('products.import') }}" method="POST" enctype="multipart/form-data" class="flex items-center gap-2">
        @csrf
        <input type="file" name="file" accept=".xlsx, .xls, .csv" class="block text-sm border rounded-md file:mr-4 file:py-2 file:px-4 file:border-0 file:text-sm file:font-semibold file:bg-slate-900 file:text-white hover:file:bg-slate-700" required>
        <button type="submit" class="bg-emerald-600 text-white px-4 py-2 rounded-md text-sm font-medium">Import Excel</button>
    </form>
</div>

<table class="w-full text-left border-collapse">
    <thead>
        <tr class="border-b">
            <th class="py-2 pr-4">Nama</th>
            <th class="py-2 pr-4">Kategori</th>
            <th class="py-2 pr-4">Harga</th>
            <th class="py-2 pr-4">Stok</th>
            <th class="py-2 pr-4">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($products as $product)
        <tr class="border-b">
            <td class="py-2 pr-4">{{ $product->name }}</td>
            <td class="py-2 pr-4">{{ $product->category->name }}</td>
            <td class="py-2 pr-4">Rp {{ number_format($product->price) }}</td>
            <td class="py-2 pr-4">{{ $product->stock }}</td>
            <td class="py-2 pr-4">
        <a href="{{ route('products.edit', $product) }}" class="text-blue-600 hover:underline">Edit</a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="mt-4">
    {{ $products->links() }}
</div>
@endsection