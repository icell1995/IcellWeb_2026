# Dokumentasi Setup dan Pengujian Kafka pada Aplikasi Penyidikan

## 1. Tujuan

Dokumentasi ini mencatat proses setup dan pengujian Kafka pada aplikasi Laravel sampai tahap pengujian koneksi menggunakan `kcat`.

Konteks integrasi:

```text
Aplikasi Penyidikan
        ↓
       Kafka
        ↓
   Aplikasi Pusat
        ↓
Kejaksaan / Pengadilan
```

Kafka digunakan sebagai media komunikasi/event antar aplikasi. Kafka bukan database utama dan bukan tempat penyimpanan dokumen utama.

---

## 2. Environment

Environment aplikasi yang digunakan:

| Komponen | Nilai |
|---|---|
| Laravel | 10.50.3 |
| PHP | 8.2.12 |
| Server | AlmaLinux 9.8 |
| php-rdkafka | 6.0.5 |
| Laravel Kafka package | mateusjunges/laravel-kafka v2.10.3 |
| Kafka DEV Broker | `IP_KAFKA_INTEGRATOR` |
| Security Protocol | `SASL_PLAINTEXT || SASL_SSL` |
| SASL Mechanism | `SCRAM-SHA-256 || PLAIN TEXT` |
| Topic DEV | `icell-dev` |

> Jangan menuliskan username dan password asli ke dalam dokumentasi.

---

## 3. Alur Setup

```text
PHP / Laravel
     ↓
librdkafka
     ↓
php-rdkafka
     ↓
mateusjunges/laravel-kafka
     ↓
config/kafka.php + .env
     ↓
Network Test
     ↓
Kafka Authentication
     ↓
Topic Test
     ↓
Publish / Consume Test
```

---

## 4. Cek PHP dan Laravel

```bash
php -v
```

Target:

```text
PHP 8.2.x
```

Cek Laravel:

```bash
php artisan --version
```

Target:

```text
Laravel Framework 10.50.3
```

---

## 5. Install / Cek `rdkafka`

Kafka dari PHP membutuhkan `librdkafka` dan extension PHP `rdkafka`.

Pada server berbasis AlmaLinux/RHEL, dependensi umumnya dapat dipasang dengan:

```bash
sudo dnf install -y librdkafka-devel php-devel php-pear gcc make
```

Kemudian:

```bash
sudo pecl install rdkafka
```

Aktifkan extension sesuai konfigurasi PHP server:

```ini
extension=rdkafka.so
```

Cek:

```bash
php -r 'echo extension_loaded("rdkafka") ? "RDKAFKA INSTALLED\n" : "RDKAFKA NOT INSTALLED\n";'
```

Target:

```text
RDKAFKA INSTALLED
```

Cek detail:

```bash
php --ri rdkafka
```

Hasil pada server:

```text
rdkafka support => enabled
version => 6.0.5
librdkafka version (runtime) => 2.15.1
librdkafka version (build) => 2.6.0.255
```

---

## 6. Install Package Kafka Laravel

Untuk Laravel 10 digunakan package seri 2.10:

```bash
composer require mateusjunges/laravel-kafka:^2.10
```

Cek:

```bash
composer show mateusjunges/laravel-kafka
```

Versi yang digunakan:

```text
v2.10.3
```

Publish konfigurasi:

```bash
php artisan vendor:publish --tag=laravel-kafka-config
```

File yang terbentuk:

```text
config/kafka.php
```

---

## 7. Konfigurasi `.env`

Contoh konfigurasi DEV:

```env
KAFKA_BROKERS=IP_KAFKA_INTEGRATOR

KAFKA_SECURITY_PROTOCOL=SASL_PLAINTEXT || SASL_SSL
KAFKA_MECHANISMS=SCRAM-SHA-256 || PLAIN TEXT

KAFKA_USERNAME=USERNAME_DEV || USERNAME_INTEGRATOR
KAFKA_PASSWORD=PASSWORD_DEV || PASSWORD_INTEGRATOR

KAFKA_TOPIC_DEV=icell-dev
KAFKA_TOPIC_PROD=TOPIC_PROD

KAFKA_CONSUMER_GROUP_ID=laravel-test-group
KAFKA_COMPRESSION_TYPE=none
```

Catatan:

- `KAFKA_SECURITY_PROTOCOL` = cara koneksi ke Kafka.
- `KAFKA_MECHANISMS` = mekanisme autentikasi.
- `KAFKA_TOPIC_DEV` = topic yang digunakan untuk pengujian DEV.
- Jangan menggunakan topic PROD untuk pengujian awal.
- Jangan mencatat credential asli dalam dokumentasi.

---

## 8. Konfigurasi `config/kafka.php`

Bagian penting:

```php
<?php declare(strict_types=1);

return [
    'brokers' => env('KAFKA_BROKERS', 'localhost:9092'),

    'securityProtocol' => env(
        'KAFKA_SECURITY_PROTOCOL',
        'PLAINTEXT'
    ),

    'sasl' => [
        'mechanisms' => env(
            'KAFKA_MECHANISMS',
            'SCRAM-SHA-256'
        ),
        'username' => env('KAFKA_USERNAME'),
        'password' => env('KAFKA_PASSWORD'),
    ],

    'topics' => [
        'dev' => env('KAFKA_TOPIC_DEV'),
        'prod' => env('KAFKA_TOPIC_PROD'),
    ],

    'consumer_group_id' => env(
        'KAFKA_CONSUMER_GROUP_ID',
        'laravel-test-group'
    ),

    'compression' => env(
        'KAFKA_COMPRESSION_TYPE',
        'none'
    ),
];
```

Setelah mengubah `.env` atau `config/kafka.php`:

```bash
php artisan optimize:clear
```

---

## 9. Verifikasi Konfigurasi Laravel

Cek broker:

```bash
php artisan tinker --execute="dump(config('kafka.brokers'));"
```

Target:

```text
"IP_KAFKA_INTEGRATOR"
```

Cek protocol:

```bash
php artisan tinker --execute="dump(config('kafka.securityProtocol'));"
```

Target:

```text
"SASL_PLAINTEXT || SASL_SSL"
```

Cek mechanism:

```bash
php artisan tinker --execute="dump(config('kafka.sasl.mechanisms'));"
```

Target:

```text
"SCRAM-SHA-256 || PLAIN TEXT"
```

Cek topic DEV:

```bash
php artisan tinker --execute="dump(config('kafka.topics.dev'));"
```

Target:

```text
"icell-dev"
```

Cek username/password tanpa menampilkan credential:

```bash
php artisan tinker --execute="dump(config('kafka.sasl.username') !== null);"
php artisan tinker --execute="dump(config('kafka.sasl.password') !== null);"
```

Target:

```text
true
true
```

---

## 10. Test Jaringan Server ke Kafka

Cek route:

```bash
ip route get IP_KAFKA_INTEGRATOR
```

Hasil menunjukkan route melalui interface server, misalnya:

```text
IP_KAFKA_INTEGRATOR via <gateway> dev eth1 src <source-ip>
```

Cek TCP port Kafka:

```bash
timeout 5 bash -c 'echo > /dev/tcp/IP_KAFKA_INTEGRATOR/PORT_KAFKA_INTEGRATOR'
```

Kemudian:

```bash
echo $?
```

Interpretasi:

| Hasil | Arti |
|---|---|
| `0` | Port berhasil diakses |
| `124` | Timeout |
| `1` | Gagal / connection refused / error lain |

Hasil akhir pengujian:

```text
0
```

Kesimpulan:

```text
Server DEV → IP_KAFKA_INTEGRATOR:PORT_KAFKA_INTEGRATOR = OPEN
```

---

## 11. Test dengan `RdKafka\Producer`

Masuk Tinker:

```bash
php artisan tinker
```

Buat konfigurasi:

```php
$conf = new \RdKafka\Conf(); null;

$conf->set('bootstrap.servers', config('kafka.brokers'));
$conf->set('security.protocol', config('kafka.securityProtocol'));
$conf->set('sasl.mechanisms', config('kafka.sasl.mechanisms'));
$conf->set('sasl.username', config('kafka.sasl.username'));
$conf->set('sasl.password', config('kafka.sasl.password'));
```

Pada server sempat muncul error:

```text
Invalid sasl.kerberos.kinit.cmd value:
Property not available: "sasl.kerberos.keytab"
```

Untuk pengujian ditambahkan:

```php
$conf->set('sasl.kerberos.kinit.cmd', '');
```

Kemudian:

```php
$producer = new \RdKafka\Producer($conf);
```

Hasil:

```text
RdKafka\Producer {
  out_q_len: 0,
}
```

Producer berhasil dibuat.

Saat meminta metadata:

```php
$metadata = $producer->getMetadata(true, null, 10000);
```

sempat menghasilkan:

```text
Local: Broker transport failure
```

Karena pesan dari PHP belum cukup spesifik, pengujian dilanjutkan menggunakan `kcat`.

---

## 12. Install `kcat`

Server menggunakan AlmaLinux 9.8.

Cek repository EPEL:

```bash
dnf repolist | grep -i epel
```

Hasil:

```text
epel Extra Packages for Enterprise Linux 9
```

Install:

```bash
sudo dnf install -y kcat
```

Cek:

```bash
kcat -V
```

---

## 13. Test Kafka dengan `kcat`

Command pengujian:

```bash
kcat -b 10.101.100.83:9093 \
  -X security.protocol=SASL_PLAINTEXT \
  -X sasl.mechanisms=PLAIN \
  -X sasl.username=icell_client \
  -X sasl.password=IcellDevSecret2026! \
  -L -t icell-dev
```

Arti parameter:

| Parameter | Fungsi |
|---|---|
| `-b` | Kafka bootstrap broker |
| `-X security.protocol=...` | Security protocol |
| `-X sasl.mechanisms=...` | SASL mechanism |
| `-X sasl.username=...` | Username |
| `-X sasl.password=...` | Password |
| `-L` | Meminta metadata Kafka |
| `-t icell-dev` | Membatasi metadata ke topic `icell-dev` |

Pada percobaan awal sempat muncul:

```text
Authentication failed during authentication due to invalid credentials
with SASL mechanism SCRAM-SHA-256
```

Artinya koneksi sudah mencapai Kafka dan proses SCRAM berjalan, tetapi credential saat itu ditolak.

Setelah credential dikoreksi/dikonfirmasi, authentication menggunakan `kcat` berhasil.

---

## 14. Status Pengujian Saat Ini

```text
PHP / Laravel                  ✓
librdkafka                     ✓
php-rdkafka                    ✓
mateusjunges/laravel-kafka     ✓
config/kafka.php               ✓
.env                           ✓
Routing ke Kafka DEV           ✓
TCP port 9093                  ✓
SASL_PLAINTEXT                 ✓
SCRAM-SHA-256                  ✓
Username / Password            ✓
Authentication via kcat        ✓
Topic DEV: icell-dev           ← tahap berikutnya
Publish test message           belum
Consumer test                  belum
Business flow                  belum
```

---

## 15. Tahap Berikutnya

Urutan lanjutan:

```text
Cek metadata topic icell-dev
        ↓
Test publish message
        ↓
Pihak integrator konfirmasi message diterima
        ↓
Test consumer / response balik (jika digunakan)
        ↓
Implementasi event bisnis
        ↓
Uji DEV end-to-end
        ↓
Persiapan PROD
```

Sebelum test publish, koordinasikan dengan pihak integrator bahwa akan ada message testing ke topic DEV.

---

## 16. Fungsi Kafka pada Aplikasi Penyidikan

Dalam arsitektur aplikasi:

```text
Penyidik
   ↓
Aplikasi Penyidikan
   ↓
Kafka
   ↓
Aplikasi Pusat
   ↓
Kejaksaan / Pengadilan
```

Kafka berfungsi sebagai jalur message/event antar aplikasi.

Contoh event:

```json
{
  "event": "DOCUMENT_READY",
  "document_id": "DOC-12345",
  "jenis_dokumen": "SPDP",
  "nomor_perkara": "LP/..."
}
```

Kafka dapat digunakan untuk:

- memberi tahu bahwa dokumen sudah tersedia;
- mengirim event perubahan status;
- mengirim perintah retry/pull data;
- menerima status dari aplikasi pusat;
- mendukung komunikasi asynchronous antar aplikasi.

Jika dokumen/data utama diambil melalui API, maka pola yang digunakan dapat berupa:

```text
Aplikasi Penyidikan
        │
        │ Kafka: "Dokumen tersedia"
        ▼
      Kafka
        │
        ▼
Aplikasi Pusat
        │
        │ API: ambil detail / file
        ▼
Aplikasi Penyidikan
```

Dengan demikian, Kafka berfungsi sebagai jalur event/notifikasi, sedangkan API dapat digunakan untuk mengambil data atau dokumen aktual sesuai desain integrasi yang disepakati.

---

## 17. Catatan Keamanan

- Jangan menulis username/password Kafka pada dokumentasi.
- Jangan commit credential ke Git.
- Simpan credential di `.env` atau secret manager.
- Gunakan topic DEV untuk pengujian.
- Topic PROD digunakan setelah uji DEV berhasil.
- Hapus mode debug Kafka setelah troubleshooting selesai.
