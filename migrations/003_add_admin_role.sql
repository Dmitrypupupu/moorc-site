BEGIN;

-- Add admin role to users table
ALTER TABLE public.users 
  ADD COLUMN IF NOT EXISTS is_admin BOOLEAN DEFAULT false;

-- Create an admin user for testing (password: admin123)
-- You should change this password in production!
INSERT INTO public.users (email, password_hash, first_name, last_name, is_admin)
VALUES (
  'admin@moorc.ru',
  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: admin123
  'Админ',
  'МООРС',
  true
) ON CONFLICT (email) DO UPDATE SET is_admin = true;

COMMIT;
