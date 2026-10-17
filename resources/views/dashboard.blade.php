@extends('layouts.main')

@section('misc_top')
    @php
        $kpis = [
            ['key' => 'total_machine', 'label' => 'Total Machines', 'accent' => 'bg-indigo-500'],
            ['key' => 'running_order', 'label' => 'Running Work Orders', 'accent' => 'bg-sky-500'],
            ['key' => 'finished_order', 'label' => 'Finished Work Orders', 'accent' => 'bg-emerald-500'],
            ['key' => 'achievement', 'label' => 'Achievement', 'accent' => 'bg-violet-500'],
            ['key' => 'today_good', 'label' => 'Good Qty', 'accent' => 'bg-teal-500'],
            ['key' => 'today_reject', 'label' => 'Reject Qty', 'accent' => 'bg-rose-500'],
        ];
    @endphp
@endsection

@section('page_title')
    Production Dashboard | Avian Brands Technical Test
@endsection

@section('head_links')
    @vite(['resources/js/dashboard/index.js'])
@endsection

@section('content')
    <div id="dashboard" class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <header class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between" id="dashboard__header">
            {{-- data update info --}}
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-green-900">Production Dashboard</h1>
                <p class="mt-1 text-sm text-green-700">
                    Data as of <span id="as-of" class="font-medium text-green-700">—</span>
                    <span class="mx-3 text-green-700 font-semibold">|</span>
                    Updated <span id="updated-at" class="text-green-700">—</span>
                </p>
            </div>

            {{-- refresh button --}}
            <button id="dashboard__header__refresh-btn" type="button"
                class="inline-flex items-center justify-center gap-2 rounded-lg  px-4 py-2 text-sm font-medium text-green-800 transition hover:scale-105 cursor-pointer">
                <span class="underline decoration-dotted underline-offset-4">Refresh</span>
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none"
                    stroke="#0d542b" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4" />
                    <path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4" />
                </svg>
            </button>
        </header>

        <div id="dashboard__error" role="alert"
            class="mb-6 hidden items-center justify-between gap-4 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
            <span id="dashboard-error-text">Failed to load dashboard data.</span>
            <button id="retry-btn" type="button"
                class="font-semibold underline underline-offset-2 hover:text-rose-900">Retry</button>
        </div>

        <main id="dashboard__content" class="space-y-6 transition-opacity">
            {{-- kpi cards --}}
            <section aria-label="Key performance indicators" class="grid grid-cols-2 gap-4 md:grid-cols-3 xl:grid-cols-6">
                @foreach ($kpis as $kpi)
                    <article class="relative overflow-hidden rounded-xl bg-white p-4 shadow-2xs shadow-green-700">
                        <span class="absolute inset-y-0 left-0 w-1 bg-green-700"></span>
                        <p class="text-xs font-medium tracking-wide text-green-700">{{ $kpi['label'] }}</p>
                        <p class="mt-2 text-2xl font-semibold tabular-nums text-green-700" data-kpi="{{ $kpi['key'] }}">—
                        </p>
                    </article>
                @endforeach
            </section>

            {{-- trends & status --}}
            <section class="grid gap-6 lg:grid-cols-3">
                <article class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-green-700 lg:col-span-2">
                    <h2 class="text-base font-semibold text-green-800">Production Trend</h2>
                    <p class="mb-4 text-sm text-green-700">Last 7 days — good vs reject quantity</p>
                    <div class="relative h-72"><canvas id="trend-chart"></canvas></div>
                </article>

                <article class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-green-700">
                    <h2 class="text-base font-semibold text-green-800">Work Order Status</h2>
                    <p class="mb-4 text-sm text-green-700">All work orders</p>
                    <div class="relative h-72"><canvas id="status-chart"></canvas></div>
                </article>
            </section>

            {{-- top machines --}}
            <section class="grid gap-6 lg:grid-cols-2">
                <article class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-green-700">
                    <h2 class="text-base font-semibold text-green-800">Top 10 Machines by Good Qty</h2>
                    <p class="mb-4 text-sm text-green-700">All-time good quantity</p>
                    <div class="relative h-96"><canvas id="machine-chart"></canvas></div>
                </article>

                <article class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-green-700">
                    <h2 class="text-base font-semibold text-green-800">Top 10 Machine Performance</h2>
                    <p class="mb-4 text-sm text-green-700">Ranked by good quantity</p>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="border-b border-green-700 text-left text-xs tracking-wide text-green-800">
                                    <th class="py-2 pr-3 font-medium">#</th>
                                    <th class="py-2 pr-3 font-medium">Machine</th>
                                    <th class="py-2 pr-3 text-right font-medium">Good Qty</th>
                                    <th class="py-2 font-medium">Achievement</th>
                                </tr>
                            </thead>
                            <tbody id="machine-table-body" class="divide-y divide-green-600">
                                <tr>
                                    <td colspan="4" class="py-8 text-center text-green-800">Loading…</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </article>
            </section>
        </main>
    </div>
@endsection
