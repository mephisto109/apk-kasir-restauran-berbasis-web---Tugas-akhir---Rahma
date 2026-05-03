<!--================================
    Komponen popup notifikasi reusable.
    Cara pakai:
    1. include file ini di halaman yang butuh popup
    2. Panggil lewat JS: tampilkanPopup_rahma('sukses', 'Judul', 'Pesan', 'url_redirect') 
    atau tanpa redirect: tampilkanPopup_rahma('gagal', 'Judul', 'Pesan')
-->

<!-- ===== OVERLAY POPUP NOTIFIKASI ===== -->
<div id="popup-notif-rahma" style="
    display: none;
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(0,0,0,0.45);
    backdrop-filter: blur(3px);
    z-index: 99999;
    justify-content: center;
    align-items: center;
    padding: 20px;
">
    <!-- Kartu popup -->
    <div id="popup-card-rahma" style="
        background: #fff;
        border-radius: 24px;
        padding: 36px 32px;
        max-width: 380px;
        width: 100%;
        text-align: center;
        box-shadow: 0 20px 60px rgba(0,0,0,0.2);
        animation: popupMuncul_rahma 0.35s cubic-bezier(0.34,1.56,0.64,1) both;
        position: relative;
    ">
        <!-- Icon status (sukses/gagal/warning) -->
        <div id="popup-icon-rahma" style="font-size: 3.8rem; margin-bottom: 16px;"></div>

        <!-- Judul -->
        <div id="popup-judul-rahma" style="
            font-size: 1.15rem;
            font-weight: 700;
            color: #222;
            margin-bottom: 8px;
        "></div>

        <!-- Pesan detail -->
        <div id="popup-pesan-rahma" style="
            font-size: 0.88rem;
            color: #777;
            margin-bottom: 24px;
            line-height: 1.5;
        "></div>

        <!-- Tombol tutup / lanjut -->
        <button id="popup-btn-rahma"
                onclick="tutupPopup_rahma()"
                style="
                    border: none;
                    border-radius: 12px;
                    padding: 10px 32px;
                    font-weight: 600;
                    font-size: 0.9rem;
                    cursor: pointer;
                    transition: opacity 0.15s, transform 0.15s;
                    min-width: 120px;
                ">
            OK
        </button>

    </div>
</div>

<style>
    /* Animasi kartu popup muncul — kayak balon yang melambung keluar */
    @keyframes popupMuncul_rahma {
        from { opacity: 0; transform: scale(0.7) translateY(20px); }
        to   { opacity: 1; transform: scale(1) translateY(0); }
    }

    #popup-btn-rahma:hover {
        opacity: 0.88;
        transform: translateY(-1px);
    }
</style>

<script>
    // URL redirect setelah popup ditutup — null kalau tidak perlu redirect
    let popupRedirectUrl_rahma = null;

    // =====================================================
    // Fungsi utama: tampilkan popup
    // tipe: 'sukses' | 'gagal' | 'warning'
    // judul: teks judul popup
    // pesan: teks detail popup
    // redirectUrl: (opsional) URL yang dituju setelah klik OK
    // =====================================================
    function tampilkanPopup_rahma(tipe_rahma, judul_rahma, pesan_rahma, redirectUrl_rahma = null) {
        popupRedirectUrl_rahma = redirectUrl_rahma;

        const overlay_rahma = document.getElementById('popup-notif-rahma');
        const icon_rahma    = document.getElementById('popup-icon-rahma');
        const judul_el      = document.getElementById('popup-judul-rahma');
        const pesan_el      = document.getElementById('popup-pesan-rahma');
        const btn_rahma     = document.getElementById('popup-btn-rahma');
        const card_rahma    = document.getElementById('popup-card-rahma');

        // Set konten sesuai tipe
        if (tipe_rahma === 'sukses') {
            icon_rahma.textContent  = '✅';
            btn_rahma.style.background   = 'linear-gradient(90deg, #e0650a, #c2185b)';
            btn_rahma.style.color        = '#fff';
            card_rahma.style.borderTop   = '5px solid #e0650a';
        } else if (tipe_rahma === 'gagal') {
            icon_rahma.textContent  = '❌';
            btn_rahma.style.background   = '#f44336';
            btn_rahma.style.color        = '#fff';
            card_rahma.style.borderTop   = '5px solid #f44336';
        } else if (tipe_rahma === 'warning') {
            icon_rahma.textContent  = '⚠️';
            btn_rahma.style.background   = '#ff9800';
            btn_rahma.style.color        = '#fff';
            card_rahma.style.borderTop   = '5px solid #ff9800';
        }

        judul_el.textContent = judul_rahma;
        pesan_el.textContent = pesan_rahma;

        // Label tombol: kalau ada redirect, tulis "Lanjut", kalau tidak "OK"
        btn_rahma.textContent = redirectUrl_rahma ? 'Lanjut →' : 'OK';

        // Tampilkan overlay
        overlay_rahma.style.display = 'flex';

        // Reset animasi kartu
        card_rahma.style.animation = 'none';
        card_rahma.offsetHeight; // trigger reflow
        card_rahma.style.animation = 'popupMuncul_rahma 0.35s cubic-bezier(0.34,1.56,0.64,1) both';
    }

    // Tutup popup — kalau ada redirect, jalankan redirect
    function tutupPopup_rahma() {
        document.getElementById('popup-notif-rahma').style.display = 'none';
        if (popupRedirectUrl_rahma) {
            window.location.href = popupRedirectUrl_rahma;
        }
    }

    // Tutup popup kalau user klik di luar kartu (di area gelap)
    document.getElementById('popup-notif-rahma').addEventListener('click', function(e_rahma) {
        if (e_rahma.target === this) {
            // Kalau ada redirect, tetap jalankan redirect walau klik di luar
            tutupPopup_rahma();
        }
    });
</script>