-- Insert Pranota Surat Jalan Permissions
-- Run this SQL script to add permissions and assign them to admin user

-- Insert permissions if they don't exist
INSERT IGNORE INTO permissions (name, description, created_at, updated_at) VALUES
('pranota-surat-jalan-view', 'View Pranota Surat Jalan', NOW(), NOW()),
('pranota-surat-jalan-create', 'Create Pranota Surat Jalan', NOW(), NOW()),
('pranota-surat-jalan-update', 'Update Pranota Surat Jalan', NOW(), NOW()),
('pranota-surat-jalan-delete', 'Delete Pranota Surat Jalan', NOW(), NOW());

-- Get the permission IDs (adjust these IDs based on your actual permission table)
SET @perm_view_id = (SELECT id FROM permissions WHERE name = 'pranota-surat-jalan-view');
SET @perm_create_id = (SELECT id FROM permissions WHERE name = 'pranota-surat-jalan-create');
SET @perm_update_id = (SELECT id FROM permissions WHERE name = 'pranota-surat-jalan-update');
SET @perm_delete_id = (SELECT id FROM permissions WHERE name = 'pranota-surat-jalan-delete');

-- Assign permissions to admin user (user_id = 1, adjust if needed)
INSERT IGNORE INTO user_permissions (user_id, permission_id, created_at, updated_at) VALUES
(1, @perm_view_id, NOW(), NOW()),
(1, @perm_create_id, NOW(), NOW()),
(1, @perm_update_id, NOW(), NOW()),
(1, @perm_delete_id, NOW(), NOW());

-- Verify the permissions were added
SELECT p.name, p.description
FROM permissions p
WHERE p.name LIKE 'pranota-surat-jalan%';
