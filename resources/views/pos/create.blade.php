@extends('layouts.app')
@section('title', 'Kasir')
@section('content')
    <div class="container mx-auto px-4">
        <h1 class="text-lg font-semibold mb-4">Transaksi Kasir</h1>
        <div x-data="{
            cart: [],
            addToCart(id, name, price) {
                this.cart.push({ id, name, price, uniqueId: Date.now() });
            },
            removeFromCart(targetId) {
                this.cart = this.cart.filter(item => item.uniqueId !== targetId);
            },
            subtotal() {
                return this.cart.reduce((sum, item) => sum + item.price, 0);
            }
        }">
            <div class="grid grid-cols-3 gap-4">
                @foreach ($products as $product)
                    <div class="border rounded-md p-3 cursor-pointer"
                        @click="addToCart({{ $product->id }}, '{{ $product->name }}', {{ $product->price }})">
                        <p class="font-medium">{{ $product->name }}</p>
                        <p class="text-sm text-slate-500">Rp {{ number_format($product->price) }}</p>
                    </div>
                @endforeach
            </div>

            <div class="mt-4 border-t pt-3">
                <template x-for="item in cart" :key="item.uniqueId">
                    <div class="flex justify-between items-center mb-1">
                        <p x-text="item.name + ' - Rp ' + item.price"></p>
                        <button type="button" @click="removeFromCart(item.uniqueId)"
                            class="text-xs text-red-600 hover:underline font-semibold">Hapus</button>
                    </div>
                </template>
                <p class="font-semibold mt-2">Subtotal: Rp <span x-text="subtotal()"></span></p>
            </div>
        </div>
    </div>
@endsection
