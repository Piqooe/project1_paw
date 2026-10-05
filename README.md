```bash
composer install
npm install
```

```bash
cp .env.example .env
php artisan key:generate
```

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_app
DB_USERNAME=root
DB_PASSWORD=
```

```sql
CREATE DATABASE laravel_app;
```

```bash
php artisan migrate
```

```bash
php artisan serve
```

`http://localhost:8000`.

<!-- buat ui nanti -->

```bash
npm run dev
```

<!-- buat production -->

```bash
npm run build
```

<!-- buat verif email di development -->

```bash
php artisan tinker
App\Models\User::query()->update(['email_verified_at' => now()]);
```
