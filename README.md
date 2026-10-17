# Simple POS — Sistem Informasi Kasir

Repositori ini merupakan proyek praktikum kelompok untuk mata kuliah **Pemrograman Web Lanjut (SIB245007)** pada Program Studi D-IV Sistem Informasi Bisnis, Jurusan Teknologi Informasi, Politeknik Negeri Malang.

## Tim Pengembang (Kelompok 1 - SIB 3C)

| Nama Mahasiswa | NIM |
| :--- | :--- |
| Desy Dwi Puspita | 244107060145 |
| Felix Alberta Jevon | 244107060111 |
| Meutia Arafah Hidayat | 244107060078 |
| Najwa Kus Syafira | 244107060034 |
| Sultan Syarif Ubaidillah A. N. | 244107060151 |

---

## Ringkasan Pengembangan: Increment 6 (Validasi dan Keamanan Input)

Pada *Increment 6*, pengembangan berfokus pada pengamanan *endpoint* aplikasi dari manipulasi data serta penerapan validasi input di sisi peladen (*server-side validation*) dengan cakupan sebagai berikut:

1. **Perlindungan *Mass Assignment*:** Penerapan properti `$fillable` pada model `Product`, `Transaction`, dan `TransactionDetail` guna membatasi atribut basis data yang dapat diisi secara massal.
2. **Validasi Berbasis *Form Request*:** Penggunaan kelas `StoreProductRequest` dan `StoreTransactionRequest` untuk memisahkan logika validasi dari *controller* serta memastikan integritas struktur data sebelum diproses lebih lanjut.
3. **Validasi Ketersediaan Stok dan Penggabungan Kuantitas:** Penanganan duplikasi item pada keranjang kasir di sisi antarmuka serta validasi kecukupan stok produk terhadap jumlah pembelian yang diminta.
4. **Kalkulasi Harga di Sisi Peladen (*Server-Side Calculation*):** Perhitungan nilai `subtotal` dan `total` transaksi secara langsung di sisi peladen menggunakan harga produk yang tersimpan di dalam basis data di dalam blok transaksi `DB::transaction()`.

---

## Analisis Keamanan Input: Evaluasi Validasi `numeric` pada Atribut `total` dari Sisi Klien

### Rumusan Masalah
> *Apabila atribut `total` tetap dikirimkan melalui formulir dari sisi klien dan divalidasi menggunakan aturan `numeric`, apakah mekanisme tersebut cukup untuk mencegah manipulasi nilai total transaksi? Mengapa demikian?*

### Analisis dan Pembahasan

Pengiriman atribut `total` dari formulir di sisi klien (*client-side*) yang hanya disertai dengan aturan validasi `numeric` di sisi peladen **tidak cukup dan tidak aman** untuk mencegah manipulasi nilai transaksi.

Terdapat tiga alasan teknis utama yang mendasari hal tersebut:

1. **Keterbatasan Cakupan Validasi Tipe Data (`numeric`)**
   Aturan validasi `numeric` pada kerangka kerja Laravel hanya berfungsi untuk memverifikasi bahwa nilai yang diterima merupakan representasi angka yang valid secara sintaksis (contoh: `150000`, `0`, `1`, atau `-500`). Validasi ini hanya memeriksa tipe data, tetapi tidak memiliki kemampuan untuk memverifikasi kebenaran logika bisnis, yaitu apakah nilai angka tersebut benar-benar merupakan hasil akumulasi yang sah dari harga satuan produk dikalikan dengan kuantitas (`qty`) item yang dibeli.

2. **Kerentanan Manipulasi Parameter di Sisi Klien (*Client-Side Parameter Tampering*)**
   Dalam prinsip keamanan aplikasi web (*Zero Trust Client Input*), seluruh data yang berasal dari peramban pengguna tidak dapat dianggap tepercaya. Meskipun nilai `total` dihitung secara otomatis menggunakan JavaScript (Alpine.js) dan disimpan di dalam elemen `<input type="hidden" name="total">`, pengguna tetap memiliki kendali penuh atas permintaan HTTP yang dikirimkan. Pihak yang tidak bertanggung jawab dapat memodifikasi nilai tersebut sebelum *request* diproses oleh peladen melalui:
   - **Perkakas Pengembang Peramban (*Browser Developer Tools*):** Mengubah nilai atribut `value` pada elemen HTML secara langsung sebelum menekan tombol kirim.
   - **Aplikasi *Proxy* dan Klien HTTP (cURL, Postman, atau Burp Suite):** Mencegat (*intercept*) permintaan `POST /pos` dan memanipulasi muatan data (*payload*) menjadi nilai yang tidak semestinya (misalnya mengubah `total=72955` menjadi `total=1`).
   
   Apabila peladen hanya mengandalkan aturan `numeric`, nilai `1` tersebut akan tetap dinyatakan lolos validasi sehingga sistem akan mencatat transaksi yang merugikan secara finansial.

3. **Ketidaksinkronan Data antara Klien dan Basis Data**
   Harga yang tampil pada antarmuka pengguna dapat mengalami perbedaan dengan data terkini di basis data apabila terjadi pembaruan harga produk pada waktu yang bersamaan. Mengandalkan nilai perhitungan dari sisi klien berisiko menghasilkan pencatatan transaksi yang tidak akurat.

### Kesimpulan dan Implementasi Solusi
Untuk menjamin integritas data finansial, aplikasi **tidak boleh** menerima input harga satuan maupun nilai `total` dari formulir sisi klien. Pada implementasi `TransactionController::store()`, formulir kasir hanya diizinkan mengirimkan identitas produk (`product_id`) dan jumlah pembelian (`qty`). 

Selanjutnya, peladen secara mandiri mengambil harga resmi produk dari basis data (`Product::findOrFail($item['product_id'])`), menghitung ulang nilai `$subtotal = $product->price * $item['qty']`, serta mengakumulasikan nilai `$total` di dalam transaksi basis data (`DB::transaction()`). Pendekatan ini memastikan bahwa seluruh perhitungan harga sepenuhnya dikendalikan oleh peladen dan bebas dari intervensi sisi klien.

## Skenario Uji Manual (Otorisasi & RBAC)

Berikut adalah skenario pengujian manual untuk memastikan pembatasan hak akses berjalan dengan benar untuk setiap peran:

### 1. Peran: Tamu (Belum Login)
* **Langkah:** Buka browser dan langsung akses URL `/products` atau `/pos` tanpa melakukan login.
* **Hasil yang Diharapkan:** Sistem menolak akses dan otomatis mengarahkan (redirect) pengguna ke halaman `/login`.

### 2. Peran: Kasir
* **Langkah:** 
  1. Login menggunakan akun `kasir@pos.test`.
  2. Buka URL `/pos` (Halaman Kasir).
  3. Buka URL `/products` (Halaman Produk).
* **Hasil yang Diharapkan:** 
  * Halaman `/pos` berhasil diakses dan tampil normal.
  * Halaman `/products` menampilkan halaman error **403 Forbidden** (Akses ditolak).

### 3. Peran: Manager
* **Langkah:**
  1. Login menggunakan akun dengan role manager (misal: `manager@pos.test`).
  2. Buka URL `/transactions` (Daftar Transaksi).
  3. Buka URL `/products` (Halaman Produk).
* **Hasil yang Diharapkan:**
  * Halaman `/transactions` berhasil diakses dan tampil normal.
  * Halaman `/products` menampilkan halaman error **403 Forbidden** (Akses ditolak).

### 4. Peran: Admin
* **Langkah:**
  1. Login menggunakan akun `admin@pos.test`.
  2. Buka URL `/products` dan `/categories`.
* **Hasil yang Diharapkan:** Semua halaman tersebut berhasil diakses dan dirender dengan normal tanpa ada error 403.