@extends('layouts.app')

@section('title', 'Kategori')

@section('content')
<div class="p-6 max-w-4xl">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <h1 class="text-xl font-bold text-slate-800">Daftar Kategori</h1>

        {{-- Form Pencarian --}}
        <form method="GET" action="{{ route('categories.index') }}" class="flex gap-2">
            <input type="text" name="q" value="{{ $q }}" placeholder="Cari kategori..."
                   class="border border-slate-300 rounded-md px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-slate-900">
            @if ($q)
                <a href="{{ route('categories.index') }}" class="bg-slate-200 text-slate-700 px-3 py-1.5 rounded-md text-sm hover:bg-slate-300 flex items-center">
                    Reset
                </a>
            @endif
            <button type="submit" class="bg-slate-900 text-white px-3 py-1.5 rounded-md text-sm hover:bg-slate-800">
                Cari
            </button>
        </form>
    </div>

    {{-- Pesan Status / Sukses --}}
    @if (session('status'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-md mb-4 text-sm">
            {{ session('status') }}
        </div>
    @endif

    {{-- Pesan Error --}}
    @if (session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md mb-4 text-sm">
            {{ session('error') }}
        </div>
    @endif

    @if (isset($errors) && $errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md mb-4 text-sm">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form Tambah Kategori --}}
    <div class="bg-white border border-slate-200 rounded-lg p-4 mb-6 shadow-sm">
        <h2 class="text-sm font-semibold text-slate-700 mb-2">Tambah Kategori Baru</h2>
        <form method="POST" action="{{ route('categories.store') }}" class="flex flex-col sm:flex-row gap-2">
            @csrf
            <input type="text" name="name" placeholder="Nama kategori..." required
                   class="border border-slate-300 rounded-md px-3 py-2 text-sm flex-1 focus:outline-none focus:ring-2 focus:ring-slate-900">
            <button type="submit" class="bg-slate-900 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-slate-800 whitespace-nowrap">
                + Tambah Kategori
            </button>
        </form>
    </div>

    {{-- Tabel Kategori --}}
    <div class="bg-white border border-slate-200 rounded-lg overflow-hidden shadow-sm">
        <table class="w-full text-left border-collapse text-sm">
            <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-medium">
                <tr>
                    <th class="py-3 px-4">
                        <a href="{{ route('categories.index', ['sort' => 'name', 'direction' => ($sort === 'name' && $direction === 'asc') ? 'desc' : 'asc', 'q' => $q]) }}" class="hover:underline flex items-center gap-1">
                            Nama
                            @if ($sort === 'name')
                                <span>{{ $direction === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </a>
                    </th>
                    <th class="py-3 px-4 w-40">
                        <a href="{{ route('categories.index', ['sort' => 'products_count', 'direction' => ($sort === 'products_count' && $direction === 'asc') ? 'desc' : 'asc', 'q' => $q]) }}" class="hover:underline flex items-center gap-1">
                            Jumlah Produk
                            @if ($sort === 'products_count')
                                <span>{{ $direction === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </a>
                    </th>
                    <th class="py-3 px-4 w-48 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse ($categories as $category)
                <tr x-data="{ isEditing: false, name: '{{ addslashes($category->name) }}' }" class="hover:bg-slate-50">
                    <td class="py-3 px-4">
                        <template x-if="!isEditing">
                            <span class="font-medium text-slate-900" x-text="name"></span>
                        </template>
                        <template x-if="isEditing">
                            <form method="POST" action="{{ route('categories.update', $category) }}" class="flex gap-2 items-center">
                                @csrf
                                @method('PUT')
                                <input type="text" name="name" x-model="name" required
                                       class="border border-slate-300 rounded px-2 py-1 text-sm focus:outline-none focus:ring-1 focus:ring-slate-900 w-full max-w-xs">
                                <button type="submit" class="bg-emerald-600 text-white px-2 py-1 rounded text-xs hover:bg-emerald-700">Simpan</button>
                                <button type="button" @click="isEditing = false; name = '{{ addslashes($category->name) }}'" class="bg-slate-200 text-slate-700 px-2 py-1 rounded text-xs hover:bg-slate-300">Batal</button>
                            </form>
                        </template>
                    </td>
                    <td class="py-3 px-4 text-slate-600">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800">
                            {{ $category->products_count }} produk
                        </span>
                    </td>
                    <td class="py-3 px-4 text-right">
                        <div class="flex items-center justify-end gap-2" x-show="!isEditing">
                            <button type="button" @click="isEditing = true" class="text-blue-600 hover:text-blue-800 hover:underline text-xs font-medium">
                                Edit
                            </button>
                            <span class="text-slate-300">|</span>
                            <form method="POST" action="{{ route('categories.destroy', $category) }}"
                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini?');" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 hover:underline text-xs font-medium">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="py-6 text-center text-slate-500">
                        Tidak ada kategori yang ditemukan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $categories->links() }}
    </div>
</div>
@endsection
