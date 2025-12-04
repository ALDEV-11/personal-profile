-- Fix Timestamps untuk Messages
-- Script ini akan memperbarui created_at yang NULL atau invalid

-- Update NULL atau 0000-00-00 00:00:00 timestamps
UPDATE contact_messages 
SET created_at = NOW() 
WHERE created_at IS NULL 
   OR created_at = '0000-00-00 00:00:00'
   OR created_at = '0000-00-00';

-- Untuk testing, set beberapa message dengan waktu berbeda
-- Uncomment baris di bawah jika ingin test dengan data dummy

-- UPDATE contact_messages SET created_at = DATE_SUB(NOW(), INTERVAL 30 SECOND) WHERE id = 1;
-- UPDATE contact_messages SET created_at = DATE_SUB(NOW(), INTERVAL 5 MINUTE) WHERE id = 2;
-- UPDATE contact_messages SET created_at = DATE_SUB(NOW(), INTERVAL 2 HOUR) WHERE id = 3;
-- UPDATE contact_messages SET created_at = DATE_SUB(NOW(), INTERVAL 1 DAY) WHERE id = 4;
-- UPDATE contact_messages SET created_at = DATE_SUB(NOW(), INTERVAL 7 DAY) WHERE id = 5;

-- Verifikasi hasil
SELECT id, name, email, created_at, 
       TIMESTAMPDIFF(SECOND, created_at, NOW()) as seconds_ago
FROM contact_messages 
ORDER BY created_at DESC 
LIMIT 10;
