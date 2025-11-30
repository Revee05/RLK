@forelse($history as $log)
<tr class="bg-white">
    
    {{-- KOLOM 1: ITEM LELANG --}}
    <td style="width: 20%; font-weight: 700; color: #000000ff;">
        {{ $log->product->title }}
    </td>

    {{-- KOLOM 2: PENUTUPAN (Dibuat Rapat) --}}
    <td style="width: 20%;">
        {{-- Tambahkan line-height: 1.2 agar tanggal dan jam nempel --}}
        <div style="font-weight: 700; font-size: 0.9rem; color: #000; line-height: 1.2;">
            {{ \Carbon\Carbon::parse($log->product->end_date)->format('d M Y') }}
        </div>
        <div style="color: #000000ff; font-size: 0.9rem; line-height: 1.2;">
            {{ \Carbon\Carbon::parse($log->product->end_date)->format('H:i:s') }}
        </div>
    </td>

    {{-- KOLOM 3: TAWARAN SAYA --}}
    <td style="width: 20%;">
        @if($log->status_label == 'Kalah')
            <span class="price-lost">
                Rp {{ number_format($log->price, 0, ',', '.') }}
            </span>
        @else
            <span class="price-normal">
                Rp {{ number_format($log->price, 0, ',', '.') }}
            </span>
        @endif
    </td>

    {{-- KOLOM 4: TERTINGGI GLOBAL --}}
    <td style="width: 20%;">
        <span class="price-normal">
            Rp {{ number_format($log->highest_global, 0, ',', '.') }}
        </span>
    </td>

    {{-- KOLOM 5: STATUS --}}
    <td style="width: 15%; text-align: center;">
        @php
            $badgeClass = 'badge-status'; 
            if ($log->status_label == 'Dalam Proses') {
                $badgeClass .= ' process';
            } elseif ($log->status_label == 'Menang') {
                $badgeClass .= ' win';
            } else {
                $badgeClass .= ' lose';
            }
        @endphp

        <span class="{{ $badgeClass }}">
            {{ $log->status_label }}
        </span>
    </td>
</tr>
@empty
<tr>
    <td colspan="5" class="text-center p-4 text-muted">
        Belum ada riwayat lelang.
    </td>
</tr>
@endforelse