@extends('layouts.app')

@section('title', 'Laporan Penjualan')

@section('content')
<h1 class="text-lg font-semibold mb-4">Laporan Penjualan</h1>

<form method="GET" action="{{ route('reports.index') }}" class="flex gap-3 items-end mb-6">
    <label class="block">
        <span class="text-sm font-medium">Dari</span>
        <input type="date" name="from" value="{{ $from }}" class="mt-1 block border rounded-md px-3 py-2 text-sm">
    </label>

    <label class="block">
        <span class="text-sm font-medium">Sampai</span>
        <input type="date" name="to" value="{{ $to }}" class="mt-1 block border rounded-md px-3 py-2 text-sm">
    </label>

    <label class="block">
        <span class="text-sm font-medium">Kategori</span>
        <select name="category_id" class="mt-1 block border rounded-md px-3 py-2 text-sm">
            <option value="">Semua Kategori</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" {{ $categoryId == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
    </label>

    <button type="submit" class="bg-slate-900 text-white px-4 py-2 rounded-md text-sm">Filter</button>
    
    <a href="{{ route('reports.export.xlsx', ['from' => $from, 'to' => $to]) }}" class="bg-green-600 text-white px-4 py-2 rounded-md text-sm">Export Excel</a>
    <a href="{{ route('reports.export.pdf', ['from' => $from, 'to' => $to]) }}" class="bg-red-600 text-white px-4 py-2 rounded-md text-sm">Export PDF</a>
    <a href="{{ route('reports.export.csv', ['from' => $from, 'to' => $to, 'category_id' => $categoryId]) }}" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm">Export CSV</a>
    
</form>

<div class="bg-white border rounded-md p-4 max-w-sm">
    <p class="text-sm text-slate-500">Total Penjualan</p>
    <p class="text-2xl font-semibold">Rp {{ number_format($totalPenjualan) }}</p>
    <p class="text-sm text-slate-500 mt-2">{{ $jumlahTransaksi }} transaksi</p>
</div>

<section class="mt-6">
    <h2 class="text-lg font-semibold mb-3">Produk Terlaris</h2>
    <div class="bg-white border rounded-md overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left">
                <tr>
                    <th class="px-4 py-3 font-medium">Produk</th>
                    <th class="px-4 py-3 font-medium text-right">Jumlah Terjual</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($produkTerlaris as $produk)
                    <tr class="border-t">
                        <td class="px-4 py-3">{{ $produk->name }}</td>
                        <td class="px-4 py-3 text-right">{{ number_format($produk->total_terjual) }}</td>
                    </tr>
                @empty
                    <tr class="border-t">
                        <td colspan="2" class="px-4 py-3 text-slate-500">Belum ada produk terjual pada rentang tanggal ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection