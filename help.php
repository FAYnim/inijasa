<?php
/**
 * Help Page
 * IniJasa - Platform Manajemen Bisnis Jasa
 */

require_once 'includes/db.php';
require_once 'includes/functions.php';

requireLogin();

$page_title = 'Bantuan';
$business_id = getCurrentBusinessId();

if (!$business_id) {
    redirect('setup-business');
}

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="main-content">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <h2 class="page-title">Bantuan</h2>
        <p class="text-muted">Panduan penggunaan platform IniJasa untuk membantu Anda memulai dan mengelola bisnis jasa Anda.</p>
    </div>

    <!-- Help Content -->
    <div class="row">
        <div class="col-12">
            <div class="help-container">
                <div class="accordion" id="helpAccordion">
                    
                    <!-- Getting Started -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingGettingStarted">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseGettingStarted" aria-expanded="true" aria-controls="collapseGettingStarted">
                                Memulai
                            </button>
                        </h2>
                        <div id="collapseGettingStarted" class="accordion-collapse collapse show" aria-labelledby="headingGettingStarted" data-bs-parent="#helpAccordion">
                            <div class="accordion-body">
                                <p>Berikut langkah-langkah dasar untuk memulai menggunakan platform IniJasa:</p>
                                <ol>
                                    <li><strong>Setup Bisnis:</strong> Setelah login pertama kali, Anda akan diminta untuk mengatur profil bisnis Anda melalui menu <em>Pengaturan → Profil Bisnis</em>.</li>
                                    <li><strong>Tambah Layanan:</strong> Tambahkan paket jasa yang Anda tawarkan melalui menu <em>Layanan & Keuangan → Paket Jasa</em>.</li>
                                    <li><strong>Tambah Klien:</strong> Input data klien Anda melalui menu <em>Menu Utama → Klien</em>.</li>
                                    <li><strong>Buat Deal:</strong> Mulai negosiasi dengan membuat deal baru melalui menu <em>Menu Utama → Deals → Buat Deal Baru</em>.</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Managing Deals -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingDeals">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDeals" aria-expanded="false" aria-controls="collapseDeals">
                                Mengelola Deals
                            </button>
                        </h2>
                        <div id="collapseDeals" class="accordion-collapse collapse" aria-labelledby="headingDeals" data-bs-parent="#helpAccordion">
                            <div class="accordion-body">
                                <p>Deal adalah kesempatan bisnis atau proyek yang Anda negosiasikan dengan klien. Berikut cara mengelola deals:</p>
                                <ul>
                                    <li><strong>Membuat Deal Baru:</strong> Klik tombol <em>Buat Deal Baru</em> di halaman Deals atau gunakan quick action di dashboard.</li>
                                    <li><strong>Stages Deal:</strong> Deal memiliki beberapa tahap: Lead → Qualified → Proposal → Negotiation → Won/Lost.</li>
                                    <li><strong>Melacak Progress:</strong> Update stage deal sesuai dengan perkembangan negosiasi Anda.</li>
                                    <li><strong>Menambahkan Nilai:</strong> Set nilai akhir deal ketika Anda yakin akan menang.</li>
                                    <li><strong>Menutup Deal:</strong> Ubah stage menjadi 'Won' ketika deal berhasil ditutup atau 'Lost' jika tidak berhasil.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Managing Clients -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingClients">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseClients" aria-expanded="false" aria-controls="collapseClients">
                                Mengelola Klien
                            </button>
                        </h2>
                        <div id="collapseClients" class="accordion-collapse collapse" aria-labelledby="headingClients" data-bs-parent="#helpAccordion">
                            <div class="accordion-body">
                                <p>Berikut cara mengelola data klien Anda:</p>
                                <ul>
                                    <li><strong>Menambahkan Klien:</strong> Klik <em>Tambah Klien</em> di halaman Klien atau gunakan quick action di dashboard.</li>
                                    <li><strong>Mengedit Klien:</strong> Klik nama klien untuk melihat detail dan mengedit informasi.</li>
                                    <li><strong>Menghapus Klien:</strong> Anda dapat menghapus klien yang tidak lagi aktif (pastikan tidak ada deal terkait).</li>
                                    <li><strong>Melihat Riwayat:</strong> Dari profil klien Anda dapat melihat semua deal yang terkait dengan klien tersebut.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Managing Services -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingServices">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseServices" aria-expanded="false" aria-controls="collapseServices">
                                Mengelola Paket Jasa
                            </button>
                        </h2>
                        <div id="collapseServices" class="accordion-collapse collapse" aria-labelledby="headingServices" data-bs-parent="#helpAccordion">
                            <div class="accordion-body">
                                <p>Paket jasa adalah layanan yang Anda tawarkan kepada klien. Berikut cara mengelolanya:</p>
                                <ul>
                                    <li><strong>Menambahkan Paket:</strong> Klik <em>Tambah Paket Jasa</em> di halaman Paket Jasa atau gunakan quick action di dashboard.</li>
                                    <li><strong>Mengedit Paket:</strong> Klik nama paket untuk mengubah detail seperti nama, deskripsi, dan harga.</li>
                                    <li><strong>Menghapus Paket:</strong> Hapus paket yang tidak lagi ditawarkan.</li>
                                    <li><strong>Menggunakan Paket:</strong> Saat membuat deal, Anda dapat memilih paket jasa yang relevan.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Managing Finances -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingFinance">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFinance" aria-expanded="false" aria-controls="collapseFinance">
                                Mengelola Keuangan
                            </button>
                        </h2>
                        <div id="collapseFinance" class="accordion-collapse collapse" aria-labelledby="headingFinance" data-bs-parent="#helpAccordion">
                            <div class="accordion-body">
                                <p>Modul keuangan membantu Anda melacak pendapatan dan pengeluaran bisnis:</p>
                                <ul>
                                    <li><strong>Mencatat Transaksi:</strong> Gunakan menu <em>Keuangan → Catat Transaksi</em> untuk mencatat pemasukan dan pengeluaran.</li>
                                    <li><strong>Melihat Laporan:</strong> Lihat laporan keuangan melalui menu <em>Laporan</em> untuk menganalisis kinerja bisnis.</li>
                                    <li><strong>Manajemen Invoice:</strong> Buat dan kirim invoice kepada klien melalui menu <em>Invoice</em>.</li>
                                    <li><strong>Pembayaran:</strong> Catat pembayaran yang diterima dari klien untuk mengurangi outstanding payments.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Reports & Analytics -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingReports">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseReports" aria-expanded="false" aria-controls="collapseReports">
                                Laporan dan Analitik
                            </button>
                        </h2>
                        <div id="collapseReports" class="accordion-collapse collapse" aria-labelledby="headingReports" data-bs-parent="#helpAccordion">
                            <div class="accordion-body">
                                <p>Platform IniJasa menyediakan berbagai laporan untuk membantu Anda membuat keputusan bisnis:</p>
                                <ul>
                                    <li><strong>Dashboard:</strong> Lihat ringkasan kinerja bisnis termasuk revenue, active deals, dan conversion rate.</li>
                                    <li><strong>Laporan Keuangan:</strong> Lihat tren pendapatan dan pengeluaran bulanan.</li>
                                    <li><strong>Laporan Sales:</strong> Analisis performa deals dan tingkat konversi.</li>
                                    <li><strong>Laporan Klien:</strong> Lihat statistik klien dan nilai hidup pelanggan (LTV).</li>
                                    <li><strong>Export Data:</strong> Export laporan ke format CSV untuk analisis lebih lanjut.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Settings & Profile -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingSettings">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSettings" aria-expanded="false" aria-controls="collapseSettings">
                                Pengaturan dan Profil
                            </button>
                        </h2>
                        <div id="collapseSettings" class="accordion-collapse collapse" aria-labelledby="headingSettings" data-bs-parent="#helpAccordion">
                            <div class="accordion-body">
                                <p>Atur profil bisnis dan preferensi akun Anda:</p>
                                <ul>
                                    <li><strong>Profil Bisnis:</strong> Update nama bisnis, deskripsi, alamat, dan informasi kontak melalui menu <em>Pengaturan → Profil Bisnis</em>.</li>
                                    <li><strong>Pengguna:</strong> Kelola akses tim jika Anda memiliki anggota tim (fitur futura).</li>
                                    <li><strong>Notifikasi:</strong> Atur preferensi notifikasi untuk email dan dalam aplikasi.</li>
                                    <li><strong>Keamanan:</strong> Ganti password dan atur autentikasi dua faktor.</li>
                                    <li><strong>Logout:</strong> Keluar dari akun Anda melalui menu profil di kanan atas.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Troubleshooting -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingTroubleshooting">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTroubleshooting" aria-expanded="false" aria-controls="collapseTroubleshooting">
                                Pemecahan Masalah
                            </button>
                        </h2>
                        <div id="collapseTroubleshooting" class="accordion-collapse collapse" aria-labelledby="headingTroubleshooting" data-bs-parent="#helpAccordion">
                            <div class="accordion-body">
                                <p>Berikut solusi untuk masalah umum yang mungkin Anda temui:</p>
                                <ul>
                                    <li><strong>Tidak Bisa Login:</strong> Pastikan username dan password benar. Gunakan fitur "Lupa Password" jika diperlukan.</li>
                                    <li><strong>Data Tidak Tersimpan:</strong> Periksa koneksi internet Anda dan pastikan tidak ada kolom wajib yang kosong.</li>
                                    <li><strong>Halaman Lambat:</strong> Bersihkan cache browser atau coba gunakan browser berbeda.</li>
                                    <li><strong>Notifikasi Tidak Muncul:</strong> Periksa pengaturan notifikasi di profil Anda dan pastikan izin browser untuk notifikasi diberikan.</li>
                                    <li><strong>Butuh Bantuan Lebih Lanjut:</strong> Hubungi tim support melalui halaman kontak atau email support@inijasa.com.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap Accordion JS (if not already included) -->
<script>
    // Ensure accordion works properly
    document.addEventListener('DOMContentLoaded', function() {
        var accordionElementList = [].slice.call(document.querySelectorAll('.accordion'));
        var accordionList = accordionElementList.map(function (accordionEl) {
            return new bootstrap.Accordion(accordionEl, {
                alwaysOpen: false
            });
        });
    });
</script>

<?php
include 'includes/footer.php';
?>