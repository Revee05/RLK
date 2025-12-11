@extends('admin.partials._layout')
@section('title', 'Dashboard')
@section('dashboard', 'active')

@section('content')

    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    {{-- Custom CSS --}}
    <link rel="stylesheet" href="{{ asset('css/admin/dashboard.css') }}">

    <div class="container-fluid">
        <h3 class="mb-4 fw-bold text-dark">Dashboard</h3>

        {{-- TOP SMALL CARD --}}
        <div class="row g-3">
            @php
                $cards = [
                    [
                        'title' => 'Member',
                        'value' => $dashboard['member'],
                        'icon' => 'bi-people',
                        'gradient' => 'linear-gradient(135deg, #e3f2ff, #ffffff)',
                    ],
                    [
                        'title' => 'Seniman',
                        'value' => $dashboard['seniman'],
                        'icon' => 'bi-brush',
                        'gradient' => 'linear-gradient(135deg, #e8f6ff, #ffffff)',
                    ],
                    [
                        'title' => 'Produk Lelang',
                        'value' => $dashboard['products'],
                        'icon' => 'bi-box-seam',
                        'gradient' => 'linear-gradient(135deg, #e3f0ff, #ffffff)',
                    ],
                    [
                        'title' => 'Produk Merchandise',
                        'value' => $dashboard['merchandise'],
                        'icon' => 'bi-bag-check',
                        'gradient' => 'linear-gradient(135deg, #eef7ff, #ffffff)',
                    ],
                ];
            @endphp

            @foreach ($cards as $c)
                <div class="col-6 col-md-3">
                    <div class="card p-4 rounded-4 shadow-sm h-100" style="background: {{ $c['gradient'] }}">

                        <div class="d-flex justify-content-between align-items-start">

                            <div>
                                <div class="text-muted small fw-semibold">{{ $c['title'] }}</div>
                                <div class="h3 fw-bold text-dark">{{ $c['value'] }}</div>
                            </div>

                            <div class="icon-box">
                                <i class="{{ $c['icon'] }}"></i>
                            </div>

                        </div>

                    </div>
                </div>
            @endforeach

        </div>

        {{-- MIDDLE SECTION --}}
        <div class="row mt-4 g-4">

            {{-- BID Chart --}}
            <div class="col-md-6">
                <div class="card shadow-sm border-0 rounded-4 h-100">
                    <div class="card-body">

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold mb-0">Bid Tahun {{ $selectedYear }}</h5>

                            <form method="GET" action="{{ route('admin.dashboard') }}">
                                <div class="year-select-container">
                                    <i class="bi bi-calendar3 year-select-icon"></i>
                                    <select name="year" class="year-select" onchange="this.form.submit()">
                                        @foreach ($years as $y)
                                            <option value="{{ $y }}" {{ $y == $selectedYear ? 'selected' : '' }}>
                                                {{ $y }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </form>
                        </div>

                        <canvas id="bidChart" height="180"></canvas>

                        <div class="mt-3">
                            <span class="text-muted fw-semibold" style="font-size: 1rem;">
                                Total Bid Tahun {{ $selectedYear }} =
                            </span>
                            <span class="fw-bold text-primary" style="font-size: 1.8rem;">
                                {{ number_format($totalBidYear) }}
                            </span>
                        </div>

                    </div>
                </div>
            </div>

            {{-- Produk Lelang --}}
            <div class="col-md-6">
                <div class="card shadow-sm border-0 rounded-4 h-100">
                    <div class="card-body">

                        <div class="d-flex justify-content-between mb-3">
                            <h5 class="fw-bold">Produk Lelang Terbaru</h5>
                            <a href="{{ route('master.product.index') }}" class="text-primary small fw-semibold">
                                Lihat Semua →
                            </a>
                        </div>

                        <div class="table-responsive">
                            <table class="table modern-table mb-0">
                                <thead>
                                    <tr>
                                        <th>Produk</th>
                                        <th>Berakhir</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($produkAktif as $p)
                                        @php
                                            $img = $p->imageUtama->path ?? 'images/default-product.png';
                                        @endphp

                                        <tr>
                                            <td class="d-flex align-items-center">
                                                <img src="{{ asset($img) }}" class="produk-thumb me-3">
                                                <span class="fw-semibold">{{ $p->title }}</span>
                                            </td>

                                            <td>{{ \Carbon\Carbon::parse($p->end_date)->format('d M Y H:i') }}</td>

                                            <td>
                                                @if ($p->status == 1)
                                                    <span class="badge status-running">Lelang Berjalan</span>
                                                @else
                                                    <span class="badge status-ended">Lelang Berakhir</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>

                            </table>
                        </div>

                    </div>
                </div>
            </div>

        </div>

        {{-- BOTTOM SECTION --}}
        <div class="row mt-4 g-4">

            {{-- Transaksi --}}
            <div class="col-md-6">
                <div class="card shadow-sm border-0 rounded-4 h-100">
                    <div class="card-body pb-0">

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold mb-0">Transaksi Terbaru</h5>

                            <a href="{{ route('admin.daftar-pemenang.index') }}" class="text-primary small fw-semibold">
                                Lihat Semua →
                            </a>
                        </div>

                        <div class="table-responsive">
                            <table class="table modern-list-table mb-0">
                                <thead class="bg-primary text-white">
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Pemesan</th>
                                        <th>Total Tagihan</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @forelse ($daftar['transaksi'] as $t)
                                        @php
                                            $statusColor =
                                                [
                                                    1 => 'text-warning fw-bold',
                                                    2 => 'text-success fw-bold',
                                                    3 => 'text-danger fw-bold',
                                                ][$t->payment_status] ?? 'text-muted';

                                            $statusText =
                                                [
                                                    1 => 'Menunggu Pembayaran',
                                                    2 => 'Sudah Dibayar',
                                                    3 => 'Kadaluwarsa',
                                                ][$t->payment_status] ?? 'Tidak Diketahui';
                                        @endphp

                                        <tr class="list-row">
                                            <td class="list-cell">{{ optional($t->created_at)->format('d/m/Y') }}</td>
                                            <td class="list-cell">{{ $t->name ?? '-' }}</td>
                                            <td class="list-cell">Rp {{ number_format($t->total_tagihan) }}</td>

                                            <td class="list-cell">
                                                <span class="{{ $statusColor }}">●</span>
                                                <span class="fw-semibold">{{ $statusText }}</span>
                                            </td>
                                        </tr>

                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-4">
                                                Belum ada transaksi.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            <div class="mb-3 small mt-2">
                                <div><span class="text-danger fw-bold">●</span> Total Kadaluwarsa: <b>Rp
                                        {{ number_format($trxSummary['expired_total']) }}</b></div>
                                <div><span class="text-warning fw-bold">●</span> Total Menunggu: <b>Rp
                                        {{ number_format($trxSummary['pending_total']) }}</b></div>
                                <div><span class="text-success fw-bold">●</span> Total Dibayar: <b>Rp
                                        {{ number_format($trxSummary['paid_total']) }}</b></div>
                            </div>

                        </div>

                    </div>
                </div>
            </div>


            {{-- Blog --}}
            <div class="col-md-6">
                <div class="card shadow-sm border-0 rounded-4 h-100">
                    <div class="card-body pb-0">

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold mb-0">Blog Terbaru</h5>

                            <a href="{{ route('admin.blogs.index') }}" class="text-primary small fw-semibold">
                                Lihat Semua →
                            </a>
                        </div>

                        <div class="table-responsive">
                            <table class="table modern-list-table mb-0">

                                <thead>
                                    <tr class="text-primary">
                                        <th>Judul</th>
                                        <th>Penulis</th>
                                        <th>Dibuat</th>
                                        <th>Diperbarui</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($daftar['blog'] as $blog)
                                        <tr class="list-row">
                                            <td class="fw-semibold list-cell">{{ $blog->title }}</td>

                                            <td class="list-cell">
                                                {{ $blog->author->name ?? '-' }}
                                            </td>

                                            <td class="list-cell">{{ optional($blog->created_at)->format('d M Y') }}</td>
                                            <td class="list-cell">{{ optional($blog->updated_at)->format('d M Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>

                            </table>
                        </div>

                    </div>
                </div>
            </div>

        </div>

    </div>

    {{-- Scripts Chart JS --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    {{-- Scripts Diagram BID --}}
    <script>
        const bidCtx = document.getElementById('bidChart').getContext('2d');

        const gradientBg = bidCtx.createLinearGradient(0, 0, 0, 300);
        gradientBg.addColorStop(0, "rgba(78,115,223,0.25)");
        gradientBg.addColorStop(1, "rgba(78,115,223,0)");

        new Chart(bidCtx, {
            type: 'line',
            data: {
                labels: @json($chart['labels']),
                datasets: [{
                    data: @json($chart['data']),
                    fill: true,
                    backgroundColor: gradientBg,
                    borderColor: "#4e73df",
                    borderWidth: 3,
                    tension: 0.4,
                    pointRadius: 6,
                    pointBackgroundColor: "#ffffff",
                    pointBorderColor: "#4e73df",
                    pointBorderWidth: 2,
                    pointHoverRadius: 7,
                    pointHoverBorderWidth: 3
                }]
            },
            options: {
                responsive: true,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: "#9ca3af"
                        },
                        grid: {
                            color: "rgba(156, 163, 175, .15)"
                        }
                    },
                    x: {
                        ticks: {
                            color: "#9ca3af"
                        },
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    </script>

@endsection
