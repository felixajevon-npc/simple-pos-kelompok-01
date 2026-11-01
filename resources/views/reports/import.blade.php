@extends('layouts.app')

@section('title', 'Import Produk')

@section('content')
    <h1 class="text-lg font-semibold mb-4">Import Produk dari CSV</h1>

    @if (session('success'))
        <div class="bg-green-50 text-green-700 p-3 rounded-md mb-4">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('import.store') }}" enctype="multipart/form-data" class="max-w-md">
        @csrf
        <label class="block mb-3">
            <span class="text-sm font-medium">File CSV</span>
            <input type="file" name="csv" accept=".csv" class="mt-1 block w-full text-sm">
            @error('csv')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </label>
        <p class="text-xs text-slate-500 mb-3">Column yang dibutuhkan: <code>name</code>, <code>category_id</code>,
            <code>price</code>.</p>
        <button type="submit" class="bg-slate-900 text-white px-4 py-2 rounded-md text-sm">Import</button>
    </form>

    @if (isset($importErrors) && $importErrors->isNotEmpty())
        <div class="max-w-2xl mt-6">
            <h2 class="text-md font-semibold mb-3 text-red-600">Daftar Baris Gagal Import</h2>
            <div class="bg-white border rounded-md overflow-hidden border-slate-200">
                <table class="w-full text-left border-collapse text-sm">
                    <thead class="bg-red-50 text-red-700 border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-2 w-28">Baris ke-</th>
                            <th class="px-4 py-2">Pesan Error</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($importErrors as $error)
                            <tr>
                                <td class="px-4 py-2 text-slate-600 font-mono">{{ $error->row_number }}</td>
                                <td class="px-4 py-2 text-slate-600">{{ $error->error_message }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
@endsection
