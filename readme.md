# Kinetika
Veb aplikacija za praćenje toka fizikalne terapije. PHP, MySQL/MariaDB, višeslojna arhitektura.

```bash
sudo service mariadb start
cd /workspaces/SI2_Pracenje-toka-fizikalne-terapije
sudo mysql -e "CREATE DATABASE IF NOT EXISTS kinetika CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
sudo mysql -e "CREATE USER IF NOT EXISTS 'kinetika'@'127.0.0.1' IDENTIFIED BY 'kinetika'; GRANT ALL ON kinetika.* TO 'kinetika'@'127.0.0.1'; FLUSH PRIVILEGES;"
sudo mysql kinetika < sql/shema.sql
/usr/bin/php8.4 sql/pokreni_seed.php
pkill -f "php -S" || true
/usr/bin/php8.4 -S 0.0.0.0:8080 -t javno javno/ruter-dev.php >/tmp/php-server.log 2>&1 &
sleep 1
ss -tlnp | grep 8080
curl -I http://127.0.0.1:8080/prijava
curl -I http://127.0.0.1:8080/css/stil.css