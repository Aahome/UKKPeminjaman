@extends('layouts.app')

@section('title', 'UKKPemnijaman')

@section('main-content')

    <body class="bg-slate-100 text-slate-700 min-h-screen flex flex-col">
        <main class="flex-1">
            <!-- Hero -->
            <section class="max-w-7xl mx-auto px-6 py-20 grid md:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="text-4xl font-bold text-slate-800 leading-tight">
                        <span class="text-blue-600">Easy & Organized</span>
                        Tool Lending System
                    </h2>
                    <p class="mt-4 text-slate-500">
                        Web-based tool lending application to simplify loan recording, monitoring, and reporting.
                    </p>
                    <div class="mt-6 flex gap-4">
                        <a href="/dashboard" class="px-6 py-3 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700">
                            Start Now
                        </a>
                    </div>
                </div>
                <div class="bg-white rounded-2xl shadow-md p-8">
                    <h3 class="font-semibold text-slate-800 mb-4">System Summary</h3>
                    <ul class="space-y-3 text-sm">
                        <li class="flex gap-2">
                            <span class="text-blue-600">✔</span> Goods data management
                        </li>
                        <li class="flex gap-2">
                            <span class="text-blue-600">✔</span> Borrowing & returning
                        </li>
                        <li class="flex gap-2">
                            <span class="text-blue-600">✔</span> Status real-time
                        </li>
                        <li class="flex gap-2">
                            <span class="text-blue-600">✔</span> Neat & clear reports
                        </li>
                    </ul>
                </div>
            </section>

            <!-- Features -->
            <section class="bg-white py-20">
                <div class="max-w-7xl mx-auto px-6">
                    <h3 class="text-2xl font-semibold text-center text-slate-800">
                        Featured Features
                    </h3>
                    <div class="grid md:grid-cols-3 gap-8 mt-12">
                        <div class="p-6 rounded-xl shadow-sm border hover:shadow-md transition">
                            <h4 class="font-semibold text-slate-800 mb-2">Item Data</h4>
                            <p class="text-sm text-slate-500">
                                Manage all item data neatly and structured. </p>
                        </div>

                        <div class="p-6 rounded-xl shadow-sm border hover:shadow-md transition">
                            <h4 class="font-semibold text-slate-800 mb-2">Borrowing</h4>
                            <p class="text-sm text-slate-500">
                                Fast loan process with automatic status.
                            </p>
                        </div>

                        <div class="p-6 rounded-xl shadow-sm border hover:shadow-md transition">
                            <h4 class="font-semibold text-slate-800 mb-2">Report</h4>
                            <p class="text-sm text-slate-500">
                                Fast loan process with automatic status.
                            </p>
                        </div>
                    </div>
                </div>
            </section>

        </main>
    @endsection
