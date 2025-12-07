BEGIN;

-- Users/Participants table
CREATE TABLE IF NOT EXISTS public.users (
  id BIGSERIAL PRIMARY KEY,
  email VARCHAR(255) UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  first_name VARCHAR(100) NOT NULL,
  last_name VARCHAR(100) NOT NULL,
  birth_date DATE,
  city VARCHAR(100),
  region VARCHAR(100),
  wca_id VARCHAR(20), -- World Cube Association ID
  is_member BOOLEAN DEFAULT false,
  created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
  updated_at TIMESTAMPTZ NOT NULL DEFAULT now()
);

-- Competitions table
CREATE TABLE IF NOT EXISTS public.competitions (
  id BIGSERIAL PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  city VARCHAR(100) NOT NULL,
  region VARCHAR(100),
  venue VARCHAR(255),
  address TEXT,
  start_date DATE NOT NULL,
  end_date DATE NOT NULL,
  registration_open TIMESTAMPTZ,
  registration_close TIMESTAMPTZ,
  max_participants INTEGER,
  description TEXT,
  regulations_url TEXT,
  status VARCHAR(50) DEFAULT 'upcoming', -- upcoming, ongoing, completed, cancelled
  created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
  updated_at TIMESTAMPTZ NOT NULL DEFAULT now()
);

-- Competition events/disciplines
CREATE TABLE IF NOT EXISTS public.disciplines (
  id BIGSERIAL PRIMARY KEY,
  code VARCHAR(50) UNIQUE NOT NULL, -- e.g., '333', '222', '444', '333oh', etc.
  name VARCHAR(100) NOT NULL, -- e.g., 'Кубик 3x3x3', '2x2x2', etc.
  description TEXT,
  sort_order INTEGER DEFAULT 0,
  is_active BOOLEAN DEFAULT true,
  created_at TIMESTAMPTZ NOT NULL DEFAULT now()
);

-- Competition to discipline mapping (which events are at which competition)
CREATE TABLE IF NOT EXISTS public.competition_disciplines (
  id BIGSERIAL PRIMARY KEY,
  competition_id BIGINT NOT NULL REFERENCES public.competitions(id) ON DELETE CASCADE,
  discipline_id BIGINT NOT NULL REFERENCES public.disciplines(id) ON DELETE CASCADE,
  time_limit INTEGER, -- in seconds
  cutoff INTEGER, -- in seconds
  created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
  UNIQUE(competition_id, discipline_id)
);

-- Competition registrations
CREATE TABLE IF NOT EXISTS public.registrations (
  id BIGSERIAL PRIMARY KEY,
  competition_id BIGINT NOT NULL REFERENCES public.competitions(id) ON DELETE CASCADE,
  user_id BIGINT NOT NULL REFERENCES public.users(id) ON DELETE CASCADE,
  status VARCHAR(50) DEFAULT 'pending', -- pending, approved, rejected, cancelled
  registered_at TIMESTAMPTZ NOT NULL DEFAULT now(),
  UNIQUE(competition_id, user_id)
);

-- Results table
CREATE TABLE IF NOT EXISTS public.results (
  id BIGSERIAL PRIMARY KEY,
  competition_id BIGINT NOT NULL REFERENCES public.competitions(id) ON DELETE CASCADE,
  user_id BIGINT NOT NULL REFERENCES public.users(id) ON DELETE CASCADE,
  discipline_id BIGINT NOT NULL REFERENCES public.disciplines(id) ON DELETE CASCADE,
  round_type VARCHAR(50) DEFAULT 'final', -- qualification, first, second, semi-final, final
  attempt1 INTEGER, -- time in centiseconds (1/100 second)
  attempt2 INTEGER,
  attempt3 INTEGER,
  attempt4 INTEGER,
  attempt5 INTEGER,
  best INTEGER, -- best single result
  average INTEGER, -- average of 5 (or mean of 3)
  ranking INTEGER, -- position in this round
  created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
  updated_at TIMESTAMPTZ NOT NULL DEFAULT now()
);

-- Documents table
CREATE TABLE IF NOT EXISTS public.documents (
  id BIGSERIAL PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  description TEXT,
  file_url TEXT,
  category VARCHAR(100), -- regulations, reports, protocols, other
  is_public BOOLEAN DEFAULT true,
  created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
  updated_at TIMESTAMPTZ NOT NULL DEFAULT now()
);

-- News/Posts table
CREATE TABLE IF NOT EXISTS public.posts (
  id BIGSERIAL PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  slug VARCHAR(255) UNIQUE NOT NULL,
  content TEXT NOT NULL,
  excerpt TEXT,
  author_id BIGINT REFERENCES public.users(id) ON DELETE SET NULL,
  is_published BOOLEAN DEFAULT false,
  published_at TIMESTAMPTZ,
  created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
  updated_at TIMESTAMPTZ NOT NULL DEFAULT now()
);

-- Indexes for performance
CREATE INDEX IF NOT EXISTS idx_users_email ON public.users(email);
CREATE INDEX IF NOT EXISTS idx_users_wca_id ON public.users(wca_id);
CREATE INDEX IF NOT EXISTS idx_competitions_start_date ON public.competitions(start_date);
CREATE INDEX IF NOT EXISTS idx_competitions_status ON public.competitions(status);
CREATE INDEX IF NOT EXISTS idx_results_competition ON public.results(competition_id);
CREATE INDEX IF NOT EXISTS idx_results_user ON public.results(user_id);
CREATE INDEX IF NOT EXISTS idx_results_discipline ON public.results(discipline_id);
CREATE INDEX IF NOT EXISTS idx_results_best ON public.results(best);
CREATE INDEX IF NOT EXISTS idx_results_average ON public.results(average);
CREATE INDEX IF NOT EXISTS idx_posts_slug ON public.posts(slug);
CREATE INDEX IF NOT EXISTS idx_posts_published ON public.posts(is_published, published_at);

-- Insert default disciplines
INSERT INTO public.disciplines (code, name, description, sort_order) VALUES
  ('333', 'Кубик 3x3x3', 'Классический кубик Рубика', 1),
  ('222', 'Кубик 2x2x2', 'Карманный кубик', 2),
  ('444', 'Кубик 4x4x4', 'Кубик месть Рубика', 3),
  ('555', 'Кубик 5x5x5', 'Кубик профессора', 4),
  ('666', 'Кубик 6x6x6', 'Кубик 6x6x6', 5),
  ('777', 'Кубик 7x7x7', 'Кубик 7x7x7', 6),
  ('333oh', '3x3x3 одной рукой', 'Сборка кубика 3x3x3 одной рукой', 7),
  ('333bf', '3x3x3 вслепую', 'Сборка кубика 3x3x3 с завязанными глазами', 8),
  ('333ft', '3x3x3 ногами', 'Сборка кубика 3x3x3 ногами', 9),
  ('minx', 'Мегаминкс', 'Мегаминкс (додекаэдр)', 10),
  ('pyram', 'Пирамидка', 'Пирамидка Мефферта', 11),
  ('skewb', 'Скьюб', 'Скьюб', 12),
  ('sq1', 'Square-1', 'Square-1', 13),
  ('clock', 'Clock', 'Rubik''s Clock', 14)
ON CONFLICT (code) DO NOTHING;

COMMIT;
