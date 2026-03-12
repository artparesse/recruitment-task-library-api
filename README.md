# Laravel Library API

REST API for managing a library — books and authors. Built with Laravel 12, PHP 8.5, MySQL, Redis, and Docker.

---

## Requirements

- Docker & Docker Compose (recommended)
- **or** PHP 8.5+, Composer, MySQL 8, Redis (local setup)

---

## Setup (Docker)

```bash
# 1. Clone and enter the project
git clone <repo-url> library-api
cd library-api

# 2. Copy environment file
cp .env.example .env

# 3. Build and start containers
docker compose up -d --build

# 4. Install dependencies
docker compose exec app composer install

# 5. Generate application key
docker compose exec app php artisan key:generate

# 6. Run migrations and seed test data
docker compose exec app php artisan migrate --seed
```

The API is available at **http://localhost:8080**.

---

## Queue Worker

The `worker` service starts automatically with Docker Compose and processes Redis queues:

```bash
# Already running via docker-compose worker service
# To run manually inside the container:
docker compose exec app php artisan queue:work
```

---

## Running Tests

```bash
docker compose exec app php artisan test
```

---

## Authentication (Sanctum Token)

Write endpoints (POST / PUT / DELETE on `/api/books`) require a Bearer token.

**Obtain a token:**

```bash
curl -s -X POST http://localhost:8080/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"password"}' | jq .
```

Response:
```json
{ "token": "1|plainTextToken..." }
```

Use the token in subsequent requests:
```
Authorization: Bearer 1|plainTextToken...
```

**Test credentials (seeded):**
- Email: `test@example.com`
- Password: `password`

---

## Implemented Features

### Core
- **Books CRUD** — list, show, create, update, delete
- **Authors** — list and show with related books
- **Many-to-many** relationship between books and authors (`author_book` pivot)
- **Pagination** — `page` and `per_page` (default 15, max 100) query parameters
- **Search** — `GET /api/authors?search=war` filters authors by book title
- **Validation** — Form Requests (`StoreBookRequest`, `UpdateBookRequest`)
- **API Resources** — consistent JSON structure without circular nesting
- **Sanctum auth** — token-based auth for write endpoints
- **Queue job** — `UpdateAuthorLatestBookTitle` dispatched via `BookObserver` on book creation, updates `latest_book_title` on all related authors
- **Artisan command** — `php artisan author:create` (interactive prompt)
- **Docker Compose** — PHP-FPM, Nginx, MySQL 8, Redis, dedicated queue worker
- **Database seeder** — test user + 15 authors + 100 books with random author assignments

### Additional
- **Postman collection** — `Library_API.postman_collection.json` ready to import

---

## API Endpoints

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| POST | /api/login | No | Obtain Sanctum token |
| GET | /api/books | No | List all books (paginated) |
| GET | /api/books/{id} | No | Book details with authors |
| POST | /api/books | Yes | Create a new book |
| PUT | /api/books/{id} | Yes | Update a book |
| DELETE | /api/books/{id} | Yes | Delete a book |
| GET | /api/authors | No | List all authors (paginated, searchable) |
| GET | /api/authors/{id} | No | Author details with books |

---

## Example curl Requests

### Login
```bash
curl -s -X POST http://localhost:8080/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"password"}'
```

### List books (paginated)
```bash
curl http://localhost:8080/api/books?page=1&per_page=5
```

### Get book details
```bash
curl http://localhost:8080/api/books/1
```

### Create a book (auth required)
```bash
curl -s -X POST http://localhost:8080/api/books \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{"title":"The Pragmatic Programmer","author_ids":[1,2]}'
```

### Update a book (auth required)
```bash
curl -s -X PUT http://localhost:8080/api/books/1 \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{"title":"Updated Title"}'
```

### Delete a book (auth required)
```bash
curl -s -X DELETE http://localhost:8080/api/books/1 \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### List authors
```bash
curl http://localhost:8080/api/authors
```

### Search authors by book title
```bash
curl "http://localhost:8080/api/authors?search=pragmatic"
```

### Get author details
```bash
curl http://localhost:8080/api/authors/1
```

---

## Postman Collection

Import `Library_API.postman_collection.json` from the project root into Postman.
Set the `base_url` variable to `http://localhost:8080` and `token` after logging in.
