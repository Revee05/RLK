{{-- FILE: resources/views/account/auction/_table_rows.blade.php --}}

@forelse($history as $log)
<tr class="d-flex flex-column d-md-table-row mb-3 mb-md-0 bg-white rounded shadow-sm border border-light border-md-0" style="border-bottom: 1.5px solid #888;">
    
    {{-- KOLOM 1: JUDUL PRODUK --}}
    {{-- Mobile: order-0 (paling atas), background abu tipis, text-center --}}
    <td class="d-block d-md-table-cell align-middle px-3 py-2 fw-bold bg-light bg-md-white text-center text-md-start" style="width: 100%; font-size: 0.9rem;">
        {{ $log->product->title }}
    </td>

    {{-- KOLOM 2: PENUTUPAN --}}
    {{-- Mobile: d-flex justify-content-between (Kiri Label, Kanan Isi) --}}
    <td class="d-flex d-md-table-cell justify-content-between align-middle px-3 py-2" style="width: 100%;">
        {{-- Label Mobile --}}
        <span class="d-md-none text-muted fw-bold" style="font-size: 0.8rem;">Penutupan:</span>
        
        {{-- Isi --}}
        <div class="d-flex flex-column text-end text-md-start">
            <span class="date-bold" style="font-size: 0.85rem;">
                {{ \Carbon\Carbon::parse($log->product->end_date)->format('d M Y') }}
            </span>
            <span class="date-time" style="font-size: 0.75rem;">
                {{ \Carbon\Carbon::parse($log->product->end_date)->format('H:i:s') }}
            </span>
        </div>
    </td>

    {{-- KOLOM 3: TAWARAN SAYA --}}
    <td class="d-flex d-md-table-cell justify-content-between align-middle px-3 py-2" style="width: 100%;">
        <span class="d-md-none text-muted fw-bold" style="font-size: 0.8rem;">Tawaran Saya:</span>

        @if($log->status_label == 'Kalah')
            <span class="price-lost" style="font-size: 0.9rem;">
                Rp {{ number_format($log->price, 0, ',', '.') }}
            </span>
        @else
            <span class="price-normal" style="font-size: 0.9rem;">
                Rp {{ number_format($log->price, 0, ',', '.') }}
            </span>
        @endif
    </td>

    {{-- KOLOM 4: TERTINGGI GLOBAL --}}
    <td class="d-flex d-md-table-cell justify-content-between align-middle px-3 py-2" style="width: 100%;">
        <span class="d-md-none text-muted fw-bold" style="font-size: 0.8rem;">Tertinggi Global:</span>

        <span class="price-normal" style="font-size: 0.9rem;">
            Rp {{ number_format($log->highest_global, 0, ',', '.') }}
        </span>
    </td>

    {{-- KOLOM 5: STATUS --}}
    <td class="d-flex d-md-table-cell justify-content-between align-middle px-3 py-2 text-md-center" style="width: 100%;">
        <span class="d-md-none text-muted fw-bold" style="font-size: 0.8rem;">Status:</span>

        <div>
            @if($log->status_label == 'Dalam Proses')
                <span class="badge-status process">Dalam Proses</span>
            @elseif($log->status_label == 'Menang')
                <span class="badge-status win">Menang</span>
            @else
                <span class="badge-status lose">Kalah</span>
            @endif
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="5" class="text-center py-4 text-muted">Belum ada riwayat lelang.</td>
</tr>
@endforelse