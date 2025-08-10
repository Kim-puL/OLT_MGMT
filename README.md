# 📘 OLT_MGMT - Sistem Manajemen OLT Berbasis Web (PHP + SNMP)

**OLT_MGMT** adalah aplikasi monitoring dan manajemen OLT berbasis web, menggunakan SNMP untuk mengambil data ONU dari vendor HSGQ & Hioso.

---

## 🖥️ Persyaratan Sistem

- Ubuntu Server/Desktop 24.04 LTS
- Apache 2.4+
- PHP 8.3+
- MySQL/MariaDB 10.5+
- Modul PHP: SNMP, MySQLi
- SNMP support (`snmpwalk`, `snmpget`)

---

## 📦 Struktur Proyek

```
/var/www/html/OLT_MGMT/
├── api/
│   ├── check_olt_ping.php
│   └── check_olt_status.php
├── includes/
│   ├── db.php
│   ├── footer.php
│   ├── header.php
│   └── onu_functions.php
├── add_olt.php
├── edit_olt.php
├── index.php
├── olt_list.php
├── onu_list.php
├── onu_snmp_update.php
├── test.php
└── update_all_onu.php
```

---

## 🛠️ Fitur Utama

- Monitoring ONU /5 Menit (TX/RX power, status)
- Multi-OLT & Multi-vendor
- Auto-refresh & pencarian
- Update data via SNMP
- Logging hasil update
- Bootstrap UI sederhana & clean

---

## 🔐 Keamanan

- Ganti `community` SNMP dari "public" menjadi private jika perlu
- Amankan akses ke `onu_snmp_update.php`
- Gunakan HTTPS (SSL) untuk akses aman
- Buat fitur login/user management (bisa dikembangkan)

---

## ⚙️ Instalasi Lengkap di Ubuntu 24.04

### 🔹 1. Update sistem
```bash
sudo apt update && sudo apt upgrade -y
```

### 🔹 2. Install Apache + PHP + MySQL
```bash
sudo apt install apache2 php php-mysqli php-snmp php-cli libapache2-mod-php mysql-server unzip git -y
```

### 🔹 3. Clone Repositori OLT_MGMT
```bash
cd /var/www/html
sudo git clone https://github.com/Kim-puL/OLT_MGMT.git
sudo chown -R www-data:www-data OLT_MGMT
sudo chmod -R 755 OLT_MGMT
```

### 🔹 4. Konfigurasi Apache (Opsional)
```bash
sudo nano /etc/apache2/sites-available/oltmgmt.conf
```
Isi:
```apacheconf
<VirtualHost *:80>
    DocumentRoot /var/www/html/OLT_MGMT
    <Directory /var/www/html/OLT_MGMT>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```
Aktifkan:
```bash
sudo a2ensite oltmgmt
sudo a2enmod rewrite
sudo systemctl restart apache2
```

### 🔹 5. Buat Database MySQL
```bash
sudo mysql -u root -p
```
Di dalam prompt MySQL:
```sql
CREATE DATABASE olt_db;
CREATE USER 'olt_user'@'localhost' IDENTIFIED BY 'passwordku';
GRANT ALL PRIVILEGES ON olt_db.* TO 'olt_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 🔹 6. Import Struktur dan Data Awal
```bash
mysql -u olt_user -p olt_db < /var/www/html/OLT_MGMT/olt.sql
```

### 🔹 7. Atur Koneksi Database
```bash
sudo nano /var/www/html/OLT_MGMT/includes/db.php
```
Isi:
```php
<?php
$host = "localhost";
$user = "olt_user";
$pass = "passwordku";
$db   = "olt_db";
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
```

### 🔹 8. Uji Aplikasi di Browser
```
http://localhost/OLT_MGMT
```

### 🔹 9. (Opsional) Tambahkan Modul SNMP CLI
```bash
sudo apt install snmp -y
snmpwalk -v2c -c public 192.168.x.x
```

---

### 🔹 10. Crontab

🔹 via Crontab
```bash
crontab -e
```

🔹 Update /5 Menit
```bash
*/5 * * * * /usr/bin/php /path/to/update_all_onu.php >> /var/log/onu_update.log 2>&1
```

🔹 Cek Permission dan sesuaikan dengan crontab
```bash
ls -l /var/log/onu_update.log
```
---

## 🙋 Dukungan & Kontribusi

Bantu kembangkan proyek ini:
- ⭐ Star repo ini
- 📂 Fork dan buat Pull Request
- 🐛 Laporkan bug atau issue
- 💡 Usulkan fitur baru

---

## 📄 Lisensi

MIT License — Silakan gunakan & modifikasi sesuai kebutuhan.
