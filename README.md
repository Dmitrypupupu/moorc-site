# МООРС — стартовый каркас (PHP + PostgreSQL + JS)

Минимальный проект для сайта МООРС. Включает:
- PHP 8.2+, Composer, автолоадинг PSR-4
- Подключение к PostgreSQL через PDO
- Простенький роутер и контроллер
- Маршруты `/`, `/health`, `/db-test`
- Миграция `migrations/001_init.sql`
- Опционально: Docker Compose для PostgreSQL + Adminer

## Быстрый старт

### 1) macOS: что установить

Вариант A — нативно (через Homebrew):
```bash
# Установи Homebrew, если нет — https://brew.sh
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"

# Инструменты
brew install php composer node

# PostgreSQL (вариант 1 — через brew)
brew install postgresql@16
brew services start postgresql@16

# (Опционально) pgcli — удобная консоль
brew install pgcli
```

Вариант B — PostgreSQL через Postgres.app:
- Скачай и установи Postgres.app: https://postgresapp.com
- Запусти сервер из приложения

Вариант C — Docker (для БД):
- Установи Docker Desktop: https://www.docker.com/products/docker-desktop
- В проекте: `docker compose up -d` (поднимет PostgreSQL + Adminer)

Рекомендуемый набор: PHP, Composer (нативно), PostgreSQL через Docker — быстро и изолированно.

### 2) Клонируй/создай проект и установи зависимости

```bash
composer install
cp .env.example .env
```

Открой `.env` и задай параметры БД. Если БД в Docker (из `docker-compose.yml`), то:
```
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=moorc_dev
DB_USERNAME=moorc
DB_PASSWORD=moorc
```

### 3) Поднять PostgreSQL (варианты)

- Docker:
```bash
docker compose up -d
```
Adminer будет доступен на http://localhost:8080  
В Adminer для подключения:
- Система: PostgreSQL
- Сервер: db
- Пользователь: moorc
- Пароль: moorc
- База: moorc_dev

- Нативно (brew):
```bash
# Создать пользователя и базу (пример)
createuser -s moorc
createdb moorc_dev -O moorc
# или через psql:
# psql -U postgres -c "CREATE USER moorc WITH PASSWORD 'moorc';"
# psql -U postgres -c "CREATE DATABASE moorc_dev OWNER moorc;"
```

### 4) Применить миграцию
```bash
psql -h 127.0.0.1 -p 5432 -U moorc -d moorc_dev -f migrations/001_init.sql
```

### 5) Запустить локальный сервер
```bash
composer start
# или
php -S localhost:8000 -t public
```

Проверь:
- http://localhost:8000/ — главная
- http://localhost:8000/health — статус
- http://localhost:8000/db-test — проверка соединения с БД

## Структура
```
.
├─ .env.example
├─ .gitignore
├─ composer.json
├─ docker-compose.yml
├─ migrations/
│  └─ 001_init.sql
├─ public/
│  ├─ assets/
│  │  └─ js/
│  │     └─ app.js
│  └─ index.php
└─ src/
   ├─ Controllers/
   │  └─ HomeController.php
   ├─ Database.php
   └─ Router.php
```

## Полезные команды

- Установка зависимостей PHP:
```bash
composer install
```

- Запуск dev-сервера:
```bash
composer start
```

- Поднять БД (Docker):
```bash
docker compose up -d
```

- Остановить БД:
```bash
docker compose down
```

## Идеи на будущее

- Подключить полноценный фреймворк (Slim, Laravel, Symfony) при росте функционала
- Оформить миграции через инструмент (phinx/laravel-migrations)
- Шаблонизатор (Twig) и layout’ы
- Аутентификация и роли
- CI (GitHub Actions): проверка кода, тесты, деплой
- Nginx + PHP-FPM в Docker для продакшена

## Initial commit

Рекомендуемое сообщение коммита:
```
chore: initial commit — PHP starter (router, DB, health, migrations, Docker)
```