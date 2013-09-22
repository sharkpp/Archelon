@echo off
del fuel\app\config\development\migrations.php 2>&1 >nul
del fuel\app\config\db.php 2>&1 >nul
mysql -u root -ptest aaas_dev < reset.sql
php oil r migrate --all
