-- Script SQL untuk menambahkan permissions Pembayaran Pranota OB
-- Jalankan script ini di MySQL client atau phpMyAdmin

-- 1. Tambahkan permissions ke tabel permissions
INSERT INTO permissions (name, description, created_at, updated_at) VALUES
('pembayaran-pranota-ob-view', 'Izin untuk melihat pembayaran pranota OB', NOW(), NOW()),
('pembayaran-pranota-ob-create', 'Izin untuk membuat pembayaran pranota OB', NOW(), NOW()),
('pembayaran-pranota-ob-update', 'Izin untuk mengubah pembayaran pranota OB', NOW(), NOW()),
('pembayaran-pranota-ob-delete', 'Izin untuk menghapus pembayaran pranota OB', NOW(), NOW()),
('pembayaran-pranota-ob-approve', 'Izin untuk menyetujui pembayaran pranota OB', NOW(), NOW()),
('pembayaran-pranota-ob-print', 'Izin untuk mencetak pembayaran pranota OB', NOW(), NOW()),
('pembayaran-pranota-ob-export', 'Izin untuk mengekspor pembayaran pranota OB', NOW(), NOW());

-- 2. Cek permissions yang baru ditambahkan
SELECT * FROM permissions WHERE name LIKE 'pembayaran-pranota-ob-%' ORDER BY name;

-- 3. (OPSIONAL) Berikan semua permission pembayaran pranota OB ke user admin
-- Ganti @admin_user_id dengan ID user admin yang sebenarnya
SET @admin_user_id = (SELECT id FROM users WHERE username = 'admin' LIMIT 1);

INSERT INTO permission_user (user_id, permission_id, created_at, updated_at)
SELECT 
    @admin_user_id,
    id,
    NOW(),
    NOW()
FROM permissions 
WHERE name LIKE 'pembayaran-pranota-ob-%'
AND NOT EXISTS (
    SELECT 1 
    FROM permission_user 
    WHERE user_id = @admin_user_id 
    AND permission_id = permissions.id
);

-- 4. Verifikasi permissions untuk user admin
SELECT 
    u.username,
    mp.name,
    mp.description
FROM users u
JOIN permission_user mpu ON u.id = mpu.user_id
JOIN permissions mp ON mpu.permission_id = mp.id
WHERE u.username = 'admin' 
AND mp.name LIKE 'pembayaran-pranota-ob-%'
ORDER BY mp.name;

-- 5. (OPSIONAL) Hapus permissions jika ada kesalahan
-- HATI-HATI! Uncomment baris di bawah ini jika ingin menghapus
-- DELETE FROM permissions WHERE name LIKE 'pembayaran-pranota-ob-%';
