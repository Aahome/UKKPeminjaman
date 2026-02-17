<aside class="w-80 p-3 bg-white/80 backdrop-blur border-r border-slate-200">
    <style>
        details.group summary svg {
            transition: transform .18s ease;
        }

        details.group[open] summary svg {
            transform: rotate(90deg);
        }

        details.group .dropdown-content {
            max-height: 0;
            overflow: hidden;
            opacity: 0;
            transform: translateY(-6px);
            transition: max-height .28s ease, opacity .18s ease, transform .18s ease;
        }

        details.group[open] .dropdown-content {
            max-height: 480px;
            opacity: 1;
            transform: translateY(0);
        }
    </style>
    @php
        // Mengambil role user yang sedang login
        $role = auth()->user()->role->role_name;
    @endphp

    {{-- Mengecek apakah route termasuk admin, staff, atau borrower --}}
    @if (request()->routeIs('admin.*', 'staff.*', 'borrower.*'))

        {{-- Menampilkan sidebar berdasarkan role --}}
        @if ($role === 'admin')
            {{-- Sidebar Admin --}}
            <a href="/" class="p-6 flex items-center">
                <img src="{{ asset('images/logo-image.png') }}" alt="UKK Peminjaman Logo" class="h-10 w-10 object-contain">
                <div class="p-2 gap-3">
                    <h1 class="text-lg font-bold text-slate-800">
                        UKK<span class="text-blue-600">Peminjaman</span>
                    </h1>
                    <p class="text-xs text-slate-500">Admin Dashboard</p>
                </div>
            </a>

            <nav class="px-4 space-y-1">

                {{-- Menu Dashboard Admin --}}
                <a href="{{ route('admin.dashboard') }}"
                    class="flex items-center gap-3 px-4 py-2 rounded-lg
                        {{ request()->routeIs('admin.dashboard') ? 'bg-blue-600 text-white' : 'hover:bg-slate-100 text-slate-700' }}">
                    Dashboard
                </a>

                {{-- Menu Manajemen Kategori & Tools (dropdown) --}}
                <details @if (request()->routeIs('admin.categories.*') || request()->routeIs('admin.tools.*')) open @endif class="group">
                    <summary
                        class="flex items-center justify-between gap-3 px-4 py-2 rounded-lg cursor-pointer
                        {{ request()->routeIs('admin.categories.*') || request()->routeIs('admin.tools.*') ? 'bg-blue-600 text-white' : 'hover:bg-slate-100 text-slate-700' }}">
                        <span class="flex items-center gap-3">Category & Tool Management</span>
                        <svg class="h-4 w-4 text-current" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </summary>

                    <div class="dropdown-content mt-1 space-y-1 pl-4">
                        {{-- Menu Manajemen Kategori --}}
                        <a href="{{ route('admin.categories.index') }}"
                            class="flex items-center gap-3 px-4 py-2 rounded-lg
                                {{ request()->routeIs('admin.categories.*') ? 'bg-blue-600 text-white' : 'hover:bg-slate-100 text-slate-700' }}">
                            Category
                        </a>

                        {{-- Menu Manajemen Tools --}}
                        <a href="{{ route('admin.tools.index') }}"
                            class="flex items-center gap-3 px-4 py-2 rounded-lg
                                {{ request()->routeIs('admin.tools.*') ? 'bg-blue-600 text-white' : 'hover:bg-slate-100 text-slate-700' }}">
                            Tool
                        </a>
                    </div>
                </details>

                {{-- Menu Manajemen User & Role (dropdown) --}}
                <details @if (request()->routeIs('admin.users.*') || request()->routeIs('admin.roles.*')) open @endif class="group">
                    <summary
                        class="flex items-center justify-between gap-3 px-4 py-2 rounded-lg cursor-pointer
                        {{ request()->routeIs('admin.users.*') || request()->routeIs('admin.roles.*') ? 'bg-blue-600 text-white' : 'hover:bg-slate-100 text-slate-700' }}">
                        <span class="flex items-center gap-3">User & Role Management</span>
                        <svg class="h-4 w-4 text-current" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </summary>

                    <div class="dropdown-content mt-1 space-y-1 pl-4">
                        {{-- Menu Manajemen User --}}
                        <a href="{{ route('admin.users.index') }}"
                            class="flex items-center gap-3 px-4 py-2 rounded-lg
                                {{ request()->routeIs('admin.users.*') ? 'bg-blue-600 text-white' : 'hover:bg-slate-100 text-slate-700' }}">
                            User
                        </a>

                        {{-- Menu Manajemen Role --}}
                        <a href="{{ route('admin.roles.index') }}"
                            class="flex items-center gap-3 px-4 py-2 rounded-lg
                                {{ request()->routeIs('admin.roles.*') ? 'bg-blue-600 text-white' : 'hover:bg-slate-100 text-slate-700' }}">
                            Role
                        </a>
                    </div>
                </details>

                {{-- Menu Manajemen Peminjaman --}}
                <a href="{{ route('admin.borrowings.index') }}"
                    class="flex items-center gap-3 px-4 py-2 rounded-lg
                        {{ request()->routeIs('admin.borrowings.*') ? 'bg-blue-600 text-white' : 'hover:bg-slate-100 text-slate-700' }}">
                    Borrowing Management
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

                {{-- Menu Borrower Management --}}
                <a href="{{ route('staff.users.index') }}"
                    class="flex items-center gap-3 px-4 py-2 rounded-lg 
                    {{ request()->routeIs('staff.users.*') ? 'bg-blue-600 text-white' : 'hover:bg-slate-100 text-slate-700' }}">
                    Borrower Management
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
