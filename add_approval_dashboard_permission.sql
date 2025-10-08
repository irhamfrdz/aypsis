INSERT INTO permissions (name, guard_name, created_at, updated_at)
VALUES ('approval-dashboard', 'web', NOW(), NOW())
ON DUPLICATE KEY UPDATE updated_at = NOW();

-- Assign to admin role (assuming role ID 1 is admin)
INSERT INTO role_has_permissions (permission_id, role_id)
SELECT p.id, 1
FROM permissions p
WHERE p.name = 'approval-dashboard'
AND NOT EXISTS (
    SELECT 1 FROM role_has_permissions rhp
    WHERE rhp.permission_id = p.id AND rhp.role_id = 1
);

-- Assign to user ID 1
INSERT INTO model_has_permissions (permission_id, model_type, model_id)
SELECT p.id, 'App\\Models\\User', 1
FROM permissions p
WHERE p.name = 'approval-dashboard'
AND NOT EXISTS (
    SELECT 1 FROM model_has_permissions mhp
    WHERE mhp.permission_id = p.id AND mhp.model_type = 'App\\Models\\User' AND mhp.model_id = 1
);
