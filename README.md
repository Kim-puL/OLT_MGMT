# olt-mgmt
Sekedar untuk memonitoring perangkat olt dan ont/onu

Berikut panduan **install web server (Apache + PHP + MySQL)** di **Ubuntu 24.04**, cocok untuk keperluan aplikasi web standar:

---

### ✅ **Langkah 1: Update Sistem**

```bash
sudo apt update && sudo apt upgrade -y
```

---

### ✅ **Langkah 2: Install Apache**

```bash
sudo apt install apache2 -y
```

🔍 Cek status:

```bash
sudo systemctl status apache2
```

💡 Tes di browser:

```
http://localhost
```

Jika muncul halaman “Apache2 Ubuntu Default Page” berarti berhasil.

---

### ✅ **Langkah 3: Install PHP**

```bash
sudo apt install php libapache2-mod-php php-mysql php-snmp php-cli -y
```

🧪 Tes PHP:

```bash
echo "<?php phpinfo(); ?>" | sudo tee /var/www/html/info.php
```

Akses:

```
http://localhost/info.php
```

---

### ✅ **Langkah 4: Install MySQL**

```bash
sudo apt install mysql-server -y
```

🔐 Jalankan konfigurasi keamanan:

```bash
sudo mysql_secure_installation
```

🛠️ Login MySQL:

```bash
sudo mysql -u root -p
```

---

### ✅ **Langkah 5: Uji Koneksi PHP ↔ MySQL**

1. Buat file `/var/www/html/dbtest.php`

```bash
sudo nano /var/www/html/dbtest.php
```

2. Masukkan kode berikut:

```php
<?php
$mysqli = new mysqli("localhost", "root", "PASSWORD_KAMU");

if ($mysqli->connect_error) {
    die("Koneksi gagal: " . $mysqli->connect_error);
}
echo "Koneksi sukses!";
?>
```

3. Akses:

```
http://localhost/dbtest.php
```

---

### ✅ **Langkah 6 (Opsional): Install phpMyAdmin**

```bash
sudo apt install phpmyadmin -y
```

Jika diminta, pilih `apache2`, dan buat user database jika perlu.

---

### ✅ **Langkah 7: Aktifkan Rewrite & Restart Apache**

```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

---

### 🧼 **Langkah Akhir: Hapus info.php jika tidak diperlukan**

```bash
sudo rm /var/www/html/info.php
```
