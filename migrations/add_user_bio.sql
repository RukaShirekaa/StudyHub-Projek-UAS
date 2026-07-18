-- Migration: add bio column to users table for existing DBs
ALTER TABLE users ADD COLUMN bio TEXT NULL AFTER semester;
-- If you want to set a default for existing users, uncomment and modify the next line:
-- UPDATE users SET bio = 'Hi, saya baru di StudyHub' WHERE bio IS NULL;
