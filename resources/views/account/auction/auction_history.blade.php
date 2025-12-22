@extends('account.partials.layout') 

@section('title', 'Riwayat Lelang')

@section('css')
    <link href="{{ asset('css/account/auction_history_style.css') }}" rel="stylesheet" />
@endsection

@section('content')
<div class="container" style="max-width:1200px; margin-top:40px; margin-bottom:5px;">
    <div class="row">
        @include('account.partials.nav_new')

        <div class="col-md-9">
            <div class="card content-border shadow-sm">
                <div
                    class="card-head border-bottom border-darkblue ps-4 d-flex align-items-center justify-content-between">
                    <h3 class="mb-0 fw-bolder">Riwayat Lelang</h3>
                </div>

                <div class="card-body p-4">
                    {{-- A. STATISTIK (Tambahkan ID pada angka agar bisa diupdate JS) --}}
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            {{-- UPDATE: Tambahkan display: flex dan flex-direction column serta align-items flex-start --}}
                            <div class="card-stat" style="text-align: left !important; display: flex !important; flex-direction: column !important; align-items: flex-start !important; justify-content: center !important;">
                                <span class="stat-title">Total Penawaran</span>
                                <h3 class="stat-value">
                                    <span id="stat-total-bids">{{ $totalBids }}</span> 
                                    <span class="fs-4" style="color: #15bcc5;">Bids</span>
                                </h3>
                            </div>
                        </div>

                        <div class="col-md-4">
                            {{-- UPDATE: Sama seperti di atas --}}
                            <div class="card-stat" style="text-align: left !important; display: flex !important; flex-direction: column !important; align-items: flex-start !important; justify-content: center !important;">
                                <span class="stat-title">Item Dimenangkan</span>
                                <h3 class="stat-value">
                                    <span id="stat-items-won">{{ $itemsWon }}</span> 
                                    <span class="fs-4" style="color: #15bcc5;">Item</span>
                                </h3>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card-stat" style="text-align: left !important; display: flex !important; flex-direction: column !important; align-items: flex-start !important; justify-content: center !important;">
                                <span class="stat-title">Penawaran Tertinggi</span>
                                <h3 class="stat-value">
                                    Rp <span id="stat-highest-bid">{{ number_format($highestBid, 0, ',', '.') }}</span>
                                </h3>
                            </div>
                        </div>
                    </div>

                    <h5 class="fw-bold mb-3" style="color: #051a36;">Log Aktivitas Terbaru</h4>

                    <table class="ea_table">
                        <thead>
                        <tr>
                            <th style="width: 20%; background-color: #051a36; color: white; padding: 0.8rem; border: none;">Item Lelang</th>
                            <th style="width: 18.5%; background-color: #051a36; color: white; padding: 0.8rem; border: none;">Penutupan</th>
                            <th style="width: 18%; background-color: #051a36; color: white; padding: 0.8rem; border: none;">Tawaran Saya</th>
                            <th style="width: 24%; background-color: #051a36; color: white; padding: 0.8rem; border: none;">Tawaran Tertinggi</th>
                            <th class="text-center" style="width: 15%; background-color: #051a36; color: white; padding: 0.8rem; border: none;">Status</th>
                        </tr>
                        </thead>

                        <tbody id="auction-table-body">
                            @include('account.auction._table_rows')
                        </tbody>
                        
                    </table>
            
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js') 
{{-- Pastikan kamu punya section 'js' atau 'script' di layout utama, atau taruh script ini paling bawah sebelum endsection --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> {{-- Pastikan jQuery sudah ada --}}

<script>
    $(document).ready(function() {
        
        // Console Log awal saat halaman dimuat
        console.log("Halaman Riwayat Lelang Siap!"); 

        function refreshAuctionData() {
            // Log penanda bahwa request sedang dikirim
            console.log("⏳ Sedang meminta data terbaru ke server..."); 

            $.ajax({
                url: "{{ route('account.auction_history') }}", 
                type: "GET",
                dataType: "json",
                success: function(response) {
                    // --- TAMBAHKAN LOG DI SINI (Saat Sukses) ---
                    
                    // 1. Log pesan sukses
                    console.log("✅ Sukses menerima data!");

                    // 2. Log Data Statistik (Lihat angkanya benar/tidak)
                    console.log("📊 Data Statistik:", response.stats);

                    // 3. Log Data Debug dari Controller (Yang kita tambah tadi)
                    // Ini akan menampilkan array data lelang asli di console
                    console.group("🔍 Debug Data Server"); // Biar rapi pakai group
                        console.log("User ID:", response.debug_data.user_id);
                        console.log("Waktu Server:", response.debug_data.waktu_server);
                        console.log("Raw History Data:", response.debug_data.raw_history);
                        
                        // Contoh: Cek status item pertama (jika ada)
                        if(response.debug_data.raw_history.length > 0){
                            console.log("Status Item Terakhir:", response.debug_data.raw_history[0].status_label);
                        }
                    console.groupEnd();

                    // -------------------------------------------

                    // Update Isi Tabel
                    $('#auction-table-body').html(response.html);

                    // Update Statistik
                    $('#stat-total-bids').text(response.stats.totalBids);
                    $('#stat-items-won').text(response.stats.itemsWon);
                    $('#stat-highest-bid').text(response.stats.highestBid);
                },
                error: function(xhr, status, error) {
                    // --- LOG JIKA ERROR ---
                    console.error("❌ Terjadi Kesalahan!");
                    console.error("Status:", status);
                    console.error("Error Detail:", error);
                    console.warn("Respon Server:", xhr.responseText); // Lihat apa pesan error dari Laravel
                }
            });
        }

        // Jalankan fungsi refresh setiap 5 detik
        setInterval(refreshAuctionData, 5000);
    });
</script>
@endsection