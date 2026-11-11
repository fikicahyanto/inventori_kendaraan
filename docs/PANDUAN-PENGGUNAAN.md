# Panduan Penggunaan Sistem Inventori Kendaraan

Sistem ini mengelola kendaraan sebagai aset, pegawai sebagai penanggung jawab, instansi sebagai pengguna, dan riwayat penggunaan kendaraan.

## 1. Login

1. Buka halaman login.
2. Masukkan email dan password.
3. Klik **Login**.
4. Sistem mengarahkan pengguna ke Dashboard Admin.

 `docs/screenshots/login.png`

## 2. Dashboard

Dashboard menampilkan total kendaraan, kendaraan berkondisi baik, instansi aktif, pegawai aktif, dan kendaraan yang sedang digunakan.

`docs/screenshots/dashboard.png` |

## 3. Kendaraan

Buka menu **Kendaraan**.

### Menambah kendaraan

1. Klik **Tambah Kendaraan**.
2. Isi kode/barcode, kategori, nama, merek, warna, dan spesifikasi.
3. Pilih status: **Baik**, **Rusak Ringan**, atau **Rusak Berat**.
4. Isi stok kendaraan.
5. Klik **Simpan**.

### Mengubah atau menghapus kendaraan

Gunakan tombol **Edit** untuk mengubah data. Gunakan tombol **Hapus** untuk menghapus data setelah konfirmasi.

> Kendaraan yang masih digunakan sebaiknya dikembalikan terlebih dahulu sebelum dihapus.

 `docs/screenshots/data-kendaraan.png` |
 `docs/screenshots/kendaraan-form.png` |

## 4. Pegawai

Buka menu **Pegawai**. Pegawai adalah penanggung jawab kendaraan, bukan akun login.

### Menambah pegawai

1. Klik **Tambah Pegawai**.
2. Isi NIP jika tersedia.
3. Isi nama, jabatan, dan nomor HP.
4. Klik **Simpan**.

### Mengubah atau menonaktifkan pegawai

Klik **Edit**, ubah data atau status pegawai menjadi **Nonaktif**, lalu klik **Simpan**.

 `docs/screenshots/pegawai.png` |

## 5. Instansi

Buka menu **Instansi**. Instansi adalah pihak atau unit yang menggunakan kendaraan.

### Menambah instansi

1. Klik **Tambah Instansi**.
2. Isi kode dan nama instansi.
3. Isi alamat dan nomor telepon jika tersedia.
4. Klik **Simpan**.

Instansi dapat diubah atau dinonaktifkan melalui tombol **Edit**.
 `docs/screenshots/instansi.png` |

## 6. Penggunaan Barang

Buka menu **Penggunaan Barang**, lalu klik **Input Penggunaan**.

1. Pilih kendaraan yang tersedia.
2. Pilih pegawai penanggung jawab.
3. Pilih instansi pengguna.
4. Isi tanggal penggunaan.
5. Isi catatan bila diperlukan.
6. Klik **Simpan Penggunaan**.

Setelah tersimpan, status menjadi **Digunakan** dan stok kendaraan berkurang satu.


## 7. Pengembalian Kendaraan

1. Buka menu **Penggunaan Barang**.
2. Cari data dengan status **Digunakan**.
3. Klik **Kembalikan**.
4. Konfirmasi tindakan.

Status berubah menjadi **Dikembalikan**, tanggal pengembalian dicatat, dan stok kendaraan bertambah satu.


## 8. Akun Login

Menu **Akun Login** digunakan untuk akun yang dapat masuk ke aplikasi. Menu ini berbeda dengan menu Pegawai.

- Akun login digunakan untuk autentikasi.
- Pegawai digunakan sebagai penanggung jawab kendaraan.
- Pegawai tidak wajib memiliki akun login.

## 9. Alur Kerja

```text
Tambah kendaraan → Tambah pegawai → Tambah instansi
        → Input penggunaan → Kendaraan digunakan
        → Kembalikan kendaraan
```

## 10. Catatan Sistem

- Kendaraan hanya dapat digunakan jika statusnya **Baik** dan stok tersedia.
- Pegawai nonaktif tidak muncul pada form penggunaan.
- Instansi nonaktif tidak muncul pada form penggunaan.
- Menghapus penggunaan aktif akan mengembalikan stok kendaraan.
- Hindari menghapus kendaraan yang masih memiliki riwayat penggunaan.

