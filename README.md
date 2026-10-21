Query untuk bagian A dapat dilihat di **Query Bagian A.sql** di project root

# Production Dashboard

REST API Laravel 13 dan dashboard untuk data manufaktur (mesin, produk, work order, hasil produksi, downtime).

---

## 1. Menjalankan aplikasi dengan Docker

**Kebutuhan:** Docker dengan Docker Compose (Docker Desktop untuk Windows/macOS). PHP, Node.js, dan database tidak perlu diinstal.

Jalankan di folder proyek:

```bash
docker compose up -d --build
```

Perintah tersebut membangun image, menjalankan MariaDB (dataset di-import otomatis), lalu menjalankan aplikasi.
Build pertama membutuhkan koneksi internet dan memakan waktu lebih lama dari build-build selanjutnya.

---

## 2. Konfigurasi environment (`.env`)

Docker memakai file [`.env.docker`](.env.docker) yang sudah disediakan, jadi tidak perlu membuat atau mengubah `.env`.

| Variabel | Nilai | Keterangan |
|---|---|---|
| `APP_ENV` | `production` | Mode aplikasi |
| `APP_DEBUG` | `false` | Detail error tidak ditampilkan di browser |
| `APP_KEY` | sudah di-generate | Kunci enkripsi Laravel (kunci uji coba) |
| `APP_URL` | `http://localhost:8080` | Alamat aplikasi |
| `APP_TIMEZONE` | `Asia/Jakarta` | Zona waktu |
| `LOG_CHANNEL` | `stderr` | Log dibaca dengan `docker compose logs app` |
| `DB_CONNECTION` | `mysql` | Driver Laravel (berlaku untuk MariaDB dan MySQL) |
| `DB_HOST` | `db` | Nama service database di jaringan Docker |
| `DB_PORT` | `3306` | Port database di jaringan Docker |
| `DB_DATABASE` | `manufacturing_test` | Nama database |
| `DB_USERNAME` / `DB_PASSWORD` | `app` / `secret` | Akun database untuk aplikasi |
| `SESSION_DRIVER` / `CACHE_STORE` / `QUEUE_CONNECTION` | `file` / `file` / `sync` | Tidak memakai tabel database, agar skema yang disediakan tidak berubah |
| `MYSQL_ROOT_PASSWORD` | `root` | Password root database |
| `MYSQL_DATABASE`, `MYSQL_USER`, `MYSQL_PASSWORD` | sama dengan `DB_*` | Dipakai container database untuk membuat akun |

Catatan: `MYSQL_*` harus sama dengan `DB_*`. Jangan menjalankan `php artisan migrate`, karena skema database yang disediakan tidak boleh diubah.

---

## 3. Import database

**Otomatis.** File [`docker/db/dataset.sql`](docker/db/dataset.sql) di-mount ke `/docker-entrypoint-initdb.d/` pada container database.
Image MariaDB menjalankan file tersebut **satu kali, saat volume data masih kosong** (yaitu `docker compose up` pertama).
Tidak ada langkah manual.

Cek hasilnya:

```bash
docker compose exec db mysql -uroot -proot -e "USE manufacturing_test; SHOW TABLES;"
```

**Import ulang dari awal:**

```bash
docker compose down -v
docker compose up -d --build
```

**Import manual** (ke container yang sedang berjalan, atau ke server MySQL/MariaDB lain):

```bash
docker compose exec -T db mysql -uroot -proot < docker/db/dataset.sql
mysql -u root -p < docker/db/dataset.sql
```

Koneksi dari klien database (DBeaver, HeidiSQL, dll.): `127.0.0.1:3307`, database `manufacturing_test`, user `app`, password `secret`.

---

## 4. URL aplikasi dan endpoint API

| Apa | URL |
|---|---|
| Dashboard | http://localhost:8080 |
| Base URL API | http://localhost:8080/api |

| Method | Endpoint | Deskripsi |
|---|---|---|
| GET | `/api/dashboard` | Ringkasan, tren 7 hari, breakdown status, 10 mesin teratas |
| GET | `/api/dashboard/machine/{id}` | Performa satu mesin (`{id}` = kode mesin, contoh `DSP02`) |
| GET | `/api/production-orders` | Daftar work order. Parameter: `search`, `product`, `machine`, `status`, `date`, `sort_by`, `sort_dir`, `per_page`, `page` |
| POST | `/api/production-results` | Menyimpan hasil produksi |

Body `POST /api/production-results` (JSON): `wo_number`, `production_date` (`Y-m-d`, tidak boleh lebih dari hari ini),
`qty_good` >= 0, `qty_reject` >= 0, dan opsional `runtime_minutes`. Work order harus berstatus `RUNNING`, jika tidak API menjawab `422`.
