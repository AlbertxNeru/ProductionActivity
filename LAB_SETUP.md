# Laboratory Exercise No. 6 - Backend Setup

This LavaLust project is prepared for the Product Management System laboratory.

## 1. Configure Aiven MySQL

Edit `.env` and fill in:

```env
DB_DRIVER=mysql
DB_HOST=your-aiven-host
DB_PORT=your-aiven-port
DB_USER=your-aiven-user
DB_PASSWORD=your-aiven-password
DB_NAME=your-aiven-database
DB_CHARSET=utf8mb4
```

Do not commit `.env`. It is already ignored by Git.

## 2. Run migrations

From the `ProductionActivity` folder:

```bash
php lava migration status
php lava migration run
```

This creates/updates these tables:

- `migrations`
- `users`
- `refresh_tokens`
- `products`

## 3. Start the API

```bash
php lava serve
```

Default local URL: `http://127.0.0.1:3000`

## 4. API endpoints

Authentication:

- `POST /api/register`
- `POST /api/login`
- `POST /api/refresh`
- `POST /api/logout`
- `GET /api/me`

Products (Bearer token required):

- `GET /api/products`
- `GET /api/products/{id}`
- `POST /api/products`
- `PUT /api/products/{id}`
- `PATCH /api/products/{id}`
- `DELETE /api/products/{id}`

## 5. React frontend

The included React project expects the API at `http://127.0.0.1:3000` by default.
If your backend uses another URL, edit `product-frontend/.env`.

## 6. Render deployment

Set the database/JWT values as Render environment variables. Also set:

```env
FRONTEND_URL=https://your-frontend-domain.example
```

Never put Aiven credentials in the frontend project.
