@forelse($history as $log)
    <tr class="bg-white">

        {{-- KOLOM 1: ITEM LELANG --}}
        <td data-label="Item Lelang" class="td-title" style="width: 20%; font-weight: 700; color: #000000ff;">
            {{ $log->product->title }}
        </td>

        {{-- KOLOM 2: PENUTUPAN --}}
        <td data-label="Penutupan" style="width: 20%;">
            <div class="penutupan-value">
                <div style="font-weight: 700; font-size: 0.9rem; color: #000; line-height: 1.2;">
                    {{ \Carbon\Carbon::parse($log->product->end_date)->format('d M Y') }}
                </div>
                <div style="color: #000000ff; font-size: 0.9rem; line-height: 1.2;" class="date-time">
                    {{ \Carbon\Carbon::parse($log->product->end_date)->format('H:i:s') }}
                </div>
            </div>
        </td>

        {{-- KOLOM 3: TAWARAN SAYA --}}
        <td data-label="Tawaran Saya" style="width: 20%;">
            <div class="value-my-price">
                @if ($log->status_label == 'Kalah')
                    <span class="price-lost">
                        Rp {{ number_format($log->price, 0, ',', '.') }}
                    </span>
                @else
                    <span class="price-normal">
                        Rp {{ number_format($log->price, 0, ',', '.') }}
                    </span>
                @endif
            </div>
        </td>

        {{-- KOLOM 4: TERTINGGI GLOBAL --}}
        <td data-label="Tawaran Tertinggi" style="width: 20%; font-style: bold;">
            <span class="price-high">
                Rp {{ number_format($log->highest_global, 0, ',', '.') }}
            </span>
        </td>

        {{-- KOLOM 5: STATUS (DIUBAH MENJADI LINK/TOMBOL) --}}
        <td data-label="Status" class="td-status" style="width: 15%; text-align: center;">
            {{-- 
                Menggunakan <a> agar bisa diklik.
                - href: Diambil dari Controller ($log->action_url)
                - class: Diambil dari Controller ($log->badge_class) -> agar warna sesuai CSS
                - style: Menjaga agar teks tidak ada garis bawah & warna teks putih
            --}}
            <a href="{{ $log->action_url }}" 
               class="{{ $log->badge_class }}" 
               style="text-decoration: none; color: #fff; cursor: pointer;">
               
                {{ $log->status_label }}
            </a>
        </td>

    </tr>
@empty
    <tr>
        <td colspan="5" class="text-center p-4 text-muted">
            Belum ada riwayat lelang.
        </td>
    </tr>
@endforelse