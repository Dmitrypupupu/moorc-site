BEGIN;

-- Примерная стартовая таблица
CREATE TABLE IF NOT EXISTS public.sample (
  id BIGSERIAL PRIMARY KEY,
  message TEXT NOT NULL,
  created_at TIMESTAMPTZ NOT NULL DEFAULT now()
);

INSERT INTO public.sample (message) VALUES ('Добро пожаловать в МООРС!');

COMMIT;