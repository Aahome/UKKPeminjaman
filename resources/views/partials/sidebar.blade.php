<aside class="w-80 p-3 bg-white/80 backdrop-blur border-r border-slate-200">
    @php
        // Mengambil role user yang sedang login
        $role = auth()->user()->role->role_name;
    @endphp

    {{-- Mengecek apakah route termasuk admin, staff, atau borrower --}}
    @if (request()->routeIs('admin.*', 'staff.*', 'borrower.*'))

        {{-- Menampilkan sidebar berdasarkan role --}}
        @if ($role === 'admin')
            {{-- Sidebar Admin --}}
            <div class="p-6">
                <h1 class="text-lg font-bold text-slate-800 tracking-wide">
                    <a href="/">UKK<span class="text-blue-600">Peminjaman</span></a>
                </h1>
                <p class="text-xs text-slate-500 mt-1">Admin Dashboard</p>
            </div>

            <nav class="px-4 space-y-1">

                {{-- Menu Dashboard Admin --}}
                <a href="{{ route('admin.dashboard') }}"
                    class="flex items-center gap-3 px-4 py-2 rounded-lg
                        {{ request()->routeIs('admin.dashboard') ? 'bg-blue-600 text-white' : 'hover:bg-slate-100 text-slate-700' }}">
                    Dashboard
                </a>

                {{-- Menu Manajemen Tools --}}
                <a href="{{ route('admin.tools.index') }}"
                    class="flex items-center gap-3 px-4 py-2 rounded-lg
                        {{ request()->routeIs('admin.tools.*') ? 'bg-blue-600 text-white' : 'hover:bg-slate-100 text-slate-700' }}">
                    Tool Management
                </a>

                {{-- Menu Manajemen Kategori --}}
                <a href="{{ route('admin.categories.index') }}"
                    class="flex items-center gap-3 px-4 py-2 rounded-lg
                        {{ request()->routeIs('admin.categories.*') ? 'bg-blue-600 text-white' : 'hover:bg-slate-100 text-slate-700' }}">
                    Category Management
                </a>

                {{-- Menu Manajemen Peminjaman --}}
                <a href="{{ route('admin.borrowings.index') }}"
                    class="flex items-center gap-3 px-4 py-2 rounded-lg
                        {{ request()->routeIs('admin.borrowings.*') ? 'bg-blue-600 text-white' : 'hover:bg-slate-100 text-slate-700' }}">
                    Borrowing Management
                </a>

                {{-- Menu Manajemen User --}}
                <a href="{{ route('admin.users.index') }}"
                    class="flex items-center gap-3 px-4 py-2 rounded-lg
                        {{ request()->routeIs('admin.users.*') ? 'bg-blue-600 text-white' : 'hover:bg-slate-100 text-slate-700' }}">
                    User Management
                </a>

                {{-- Menu Manajemen Role --}}
                <a href="{{ route('admin.roles.index') }}"
                    class="flex items-center gap-3 px-4 py-2 rounded-lg
                        {{ request()->routeIs('admin.roles.*') ? 'bg-blue-600 text-white' : 'hover:bg-slate-100 text-slate-700' }}">
                    Role Management
                </a>

                {{-- Menu Activity Logs --}}
                <a href="{{ route('admin.logs.index') }}"
                    class="flex items-center gap-3 px-4 py-2 rounded-lg
                            {{ request()->routeIs('admin.logs.*') ? 'bg-blue-600 text-white' : 'hover:bg-slate-100 text-slate-700' }}">
                    Activity Logs
                </a>

            </nav>
        @elseif ($role === 'staff')
            {{-- Sidebar Staff --}}
            <div class="p-6">
                <h1 class="text-lg font-bold text-slate-800 tracking-wide">
                    <a href="/">UKK<span class="text-blue-600">Peminjaman</span></a>
                </h1>
                <p class="text-xs text-slate-500 mt-1">Staff Dashboard</p>
            </div>

            <nav class="px-4 space-y-1">

                {{-- Menu Dashboard Staff --}}
                <a href="{{ route('staff.dashboard') }}"
                    class="flex items-center gap-3 px-4 py-2 rounded-lg 
                    {{ request()->routeIs('staff.dashboard') ? 'bg-blue-600 text-white' : 'hover:bg-slate-100 text-slate-700' }}">
                    Dashboard
                </a>

                {{-- Menu Data Peminjaman --}}
                <a href="{{ route('staff.borrowings.index') }}"
                    class="flex items-center gap-3 px-4 py-2 rounded-lg 
                    {{ request()->routeIs('staff.borrowings.*') ? 'bg-blue-600 text-white' : 'hover:bg-slate-100 text-slate-700' }}">
                    Borrowing Data
                </a>

                {{-- Menu Monitoring Pengembalian --}}
                <a href="{{ route('staff.returns.index') }}"
                    class="flex items-center gap-3 px-4 py-2 rounded-lg 
                    {{ request()->routeIs('staff.returns.*') ? 'bg-blue-600 text-white' : 'hover:bg-slate-100 text-slate-700' }}">
                    Return Monitoring
                </a>
            </nav>
        @elseif ($role === 'borrower')
            {{-- Sidebar Borrower --}}
            <div class="p-6">
                <h1 class="text-lg font-bold text-slate-800 tracking-wide">
                    <a href="/">UKK<span class="text-blue-600">Peminjaman</span></a>
                </h1>
                <p class="text-xs text-slate-500 mt-1">
                    Borrower Dashboard
                </p>
            </div>

            <nav class="px-4 space-y-1">

                {{-- Menu Dashboard Borrower --}}
                <a href="{{ route('borrower.dashboard') }}"
                    class="flex items-center gap-3 px-4 py-2 rounded-lg
        {{ request()->routeIs('borrower.dashboard') ? 'bg-blue-600 text-white' : 'hover:bg-slate-100 text-slate-700' }}">
                    Dashboard
                </a>

                {{-- Menu Tools yang Tersedia --}}
                <a href="{{ route('borrower.tools.index') }}"
                    class="flex items-center gap-3 px-4 py-2 rounded-lg
        {{ request()->routeIs('borrower.tools.*') ? 'bg-blue-600 text-white' : 'hover:bg-slate-100 text-slate-700' }}">
                    Available Tools
                </a>

                {{-- Menu Data Peminjaman User --}}
                <a href="{{ route('borrower.borrowings.index') }}"
                    class="flex items-center gap-3 px-4 py-2 rounded-lg
        {{ request()->routeIs('borrower.borrowings.*') ? 'bg-blue-600 text-white' : 'hover:bg-slate-100 text-slate-700' }}">
                    Borrowings
                </a>

            </nav>
        @endif
    @endauth
</aside>
