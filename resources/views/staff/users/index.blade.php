@extends('layouts.app')

@section('title', 'Staff | Borrower Management')

@section('dashboard-content')
    <div class="flex-1 p-8">

        <!-- Top bar -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h2 class="text-2xl font-semibold text-slate-800">
                    Borrower Management
                </h2>
                <p class="text-sm text-slate-500">
                    Manage and monitor borrower accounts
                </p>
            </div>

            <div class="relative">
                <button onclick="toggleProfileMenu()" class="flex items-center gap-3 focus:outline-none">
                    <span class="text-sm text-slate-600">
                        {{ auth()->user()->name }}
                    </span>
                    <div class="w-9 h-9 rounded-full bg-blue-600 text-white flex items-center justify-center font-semibold">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                </button>

                <div id="profileMenu"
                    class="hidden absolute right-0 mt-2 w-40 bg-white border border-slate-200 rounded-lg shadow-md">
                    <form method="POST" action="{{ route('logout') }}" class="p-1">
                        @csrf
                        <button type="submit"
                            class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 rounded-lg">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Search & Filter -->
        <form method="GET" action="{{ route('staff.users.index') }}" class="py-4 flex flex-wrap gap-3 items-center">

            <!-- Search Input -->
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search borrower name..."
                class="w-full sm:w-64 px-4 py-2 border rounded-lg text-sm
                      focus:ring focus:ring-blue-200 focus:border-blue-500 outline-none">

            <!-- Search Button -->
            <button type="submit" class="px-5 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700">
                Search
            </button>
        </form>

        <!-- Borrowers Table -->
        <section class="bg-white rounded-xl shadow-sm w-5xl">

            <!-- Header -->
            <div class="px-6 py-4 border-b border-slate-200 flex justify-between items-center">
                <h3 class="font-semibold text-slate-800">
                    Borrower List
                </h3>

                <button type="button" onclick="openCreateCard()" class="text-sm text-blue-600 hover:underline">
                    Add Borrower
                </button>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-slate-500">
                        <tr>
                            <th class="px-6 py-3 text-left w-12">No</th>
                            <th class="px-6 py-3 text-left">Name</th>
                            <th class="px-6 py-3 text-left">Username</th>
                            <th class="px-6 py-3 text-left">Email</th>
                            <th class="px-6 py-3 text-left">Phone</th>
                            <th class="px-6 py-3 text-left">Grade</th>
                            <th class="px-6 py-3 text-center w-40">Action</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-200">
                        @forelse ($users as $user)
                            <tr class="hover:bg-slate-50">
                                <td class="px-6 py-4">{{ $loop->iteration }}</td>

                                <td class="px-6 py-4 font-medium text-slate-800">
                                    {{ $user->name }}
                                </td>

                                <td class="px-6 py-4 font-medium text-slate-800">
                                    {{ $user->username }}
                                </td>

                                <td class="px-6 py-4 text-slate-600">
                                    {{ $user->email }}
                                </td>

                                <td class="px-6 py-4 text-slate-600">
                                    {{ $user->phone_number }}
                                </td>

                                <td class="px-6 py-4 text-slate-600">
                                    {{ optional($user->grade)->grade_name ?? '-' }}
                                </td>

                                <!-- Action -->
                                <td class="px-6 py-4 text-center">
                                    <div class="flex justify-center gap-2">
                                        <button type="button" data-id="{{ $user->id }}"
                                            data-name="{{ $user->name }}" data-username="{{ $user->username }}"
                                            data-email="{{ $user->email }}" data-phone="{{ $user->phone_number }}"
                                            data-grade="{{ $user->grade_id }}" onclick="openEditCard(this)"
                                            class="px-3 py-1 text-xs rounded-md bg-amber-100 text-amber-700 hover:bg-amber-200">
                                            Edit
                                        </button>
                                        <form action="{{ route('staff.users.destroy', $user->id) }}" method="POST"
                                            onsubmit="return confirm('Delete this borrower? (all data related to this student will be deleted too)')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="px-3 py-1 text-xs rounded-md bg-red-100 text-red-700 hover:bg-red-200">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-6 text-center text-slate-500">
                                    No borrowers found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </section>
    </div>

    <!-- Add and Edit Modal -->
    @include('staff.users.create')
    @include('staff.users.edit')

    <script>
        function openCreateCard() {
            const form = document.getElementById('createForm');
            form.reset();
            form.action = "{{ route('staff.users.store') }}";
            document.getElementById('createUserCard').hidden = false;
        }

        function closeCreateCard() {
            document.getElementById('createUserCard').hidden = true;
        }

        function openEditCard(button) {
            const id = button.dataset.id;
            const gradeId = button.dataset.grade;

            document.getElementById('editForm').action = `/staff/users/${id}`;
            document.getElementById('editUserId').value = id;
            document.getElementById('editUserName').value = button.dataset.name;
            document.getElementById('editUserUsername').value = button.dataset.username;
            document.getElementById('editUserEmail').value = button.dataset.email;
            document.getElementById('editUserPhone').value = button.dataset.phone;

            const gradeSelect = document.getElementById('editUserGrade');
            gradeSelect.value = gradeId || '';

            document.getElementById('editUserCard').hidden = false;
        }

        function closeEditCard() {
            document.getElementById('editUserCard').hidden = true;
        }
    </script>

    @if (session('open_create'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                document.getElementById('createUserCard').hidden = false;
            });
        </script>
    @endif

    @if (session('open_edit') && old('user_id'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const id = "{{ old('user_id') }}";
                const form = document.getElementById('editForm');

                form.action = `/staff/users/${id}`;
                document.getElementById('editUserCard').hidden = false;
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                alert("{{ session('error') }}");
            });
        </script>
    @endif
    @if (session('success'))
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                alert("{{ session('success') }}");
            });
        </script>
    @endif
@endsection
