<nav class="bg-slate-900 text-white px-4 py-3 flex gap-4">
    <span class="font-semibold">Simple POS</span>

    <a href="{{ route('pos.create') }}"
       class="hover:underline px-2 py-1 rounded {{ request()->routeIs('pos.create') ? 'bg-slate-700' : '' }}">
        Kasir
    </a>

    <a href="{{ route('transactions.index') }}"
       class="hover:underline px-2 py-1 rounded {{ request()->routeIs('transactions.index') ? 'bg-slate-700' : '' }}">
        Transaksi
    </a>
</nav>
