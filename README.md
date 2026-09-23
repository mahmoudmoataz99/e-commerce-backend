# MyShop API (Laravel backend)

This is the backend for the React Native shop app. It's built with Laravel 11
and uses **Sanctum** for login tokens (the standard way to do auth for a
mobile app talking to a Laravel API).

I wrote this to match exactly what the app already needs — products,
categories, cart, wishlist, addresses, payment methods, promo codes,
orders/checkout, notifications, and a basic profile.

> Note: comments in the code are written casually/simply on purpose, like a
> beginner working through it step by step — not "enterprise" Laravel style.
> It still follows normal Laravel conventions (migrations, Eloquent models,
> Sanctum auth, form validation) so it's easy to build on later.

---

## 1. Requirements

- PHP 8.2 or newer
- Composer (https://getcomposer.org)
- That's it — this uses SQLite by default so you don't need to install MySQL
  or anything else to get it running.

Check your PHP version:
```bash
php -v
```

---

## 2. Setup (copy/paste these one at a time)

```bash
# 1. install all the php packages (laravel, sanctum, etc)
composer install

# 2. copy the example env file to a real one
cp .env.example .env

# 3. generate the app encryption key (laravel needs this)
php artisan key:generate

# 4. make sure the sqlite database file exists
touch database/database.sqlite

# 5. create all the database tables
php artisan migrate

# 6. fill the database with sample products/categories/promo codes/demo user
php artisan db:seed

# 7. start the server
php artisan serve
```

Your API is now running at **http://localhost:8000**

Test it worked by opening `http://localhost:8000/api/products` in your
browser — you should see a JSON list of 10 products.

---

## 3. Connecting the React Native app

In your Expo app, point your API calls at:

- **iOS simulator / web**: `http://localhost:8000/api`
- **Android emulator**: `http://10.0.2.2:8000/api` (Android can't see
  "localhost" the same way, it needs this special IP instead)
- **Real phone on same wifi**: `http://YOUR_COMPUTER_IP:8000/api`
  (find your IP with `ipconfig` on Windows or `ifconfig`/`ip a` on Mac/Linux,
  then run `php artisan serve --host=0.0.0.0` so it's reachable)

Every protected route needs this header once you have a token:
```
Authorization: Bearer <the token you got from /login or /register>
Accept: application/json
```

---

## 4. Demo login

The seeder creates a test account so you don't have to register every time:

```
email:    alex@example.com
password: password
```

It also comes with 2 saved addresses, 2 saved cards, and a couple sample
notifications — matches the fake data that used to be hardcoded in the app.

---

## 5. Promo codes

`SAVE10` (10% off) and `WELCOME20` (20% off) are seeded and ready to use on
checkout, matching the `SAVE10` code that was hardcoded in `checkout.tsx`
before.

---

## 6. All the API routes

Base URL: `http://localhost:8000/api`

### Auth (public)
| Method | URL | What it does |
|---|---|---|
| POST | `/register` | name, email, password, password_confirmation → returns user + token |
| POST | `/login` | email, password → returns user + token |
| POST | `/logout` 🔒 | deletes the current token |
| GET | `/me` 🔒 | returns the logged in user |

### Products & Categories (public)
| Method | URL | What it does |
|---|---|---|
| GET | `/categories` | list of all categories |
| GET | `/products` | list of products. Supports `?category=tops`, `?search=hoodie`, `?tag=sale` |
| GET | `/products/{id}` | one product |

### Cart 🔒
| Method | URL | What it does |
|---|---|---|
| GET | `/cart` | current user's cart items |
| POST | `/cart` | add item: `product_id`, `selected_size?`, `selected_color?`, `quantity?` |
| PUT | `/cart/{id}` | update quantity: `quantity` |
| DELETE | `/cart/{id}` | remove one item |
| DELETE | `/cart` | clear the whole cart |

### Wishlist 🔒
| Method | URL | What it does |
|---|---|---|
| GET | `/wishlist` | current user's wishlist |
| POST | `/wishlist` | toggle a product on/off the wishlist: `product_id` |
| DELETE | `/wishlist/{id}` | remove one item |

### Addresses 🔒
| Method | URL | What it does |
|---|---|---|
| GET | `/addresses` | list |
| POST | `/addresses` | create: `label`, `address`, `is_default?` |
| PUT | `/addresses/{id}` | update |
| DELETE | `/addresses/{id}` | delete |

### Payment Methods 🔒
| Method | URL | What it does |
|---|---|---|
| GET | `/payment-methods` | list |
| POST | `/payment-methods` | create: `label`, `last4?`, `type?`, `is_default?` |
| PUT | `/payment-methods/{id}/default` | set as default card |
| DELETE | `/payment-methods/{id}` | delete |

### Promo Codes 🔒
| Method | URL | What it does |
|---|---|---|
| POST | `/promo-codes/check` | `code` → tells you if it's valid + the discount % |

### Orders / Checkout 🔒
| Method | URL | What it does |
|---|---|---|
| GET | `/orders` | order history, newest first |
| GET | `/orders/{id}` | one order with its items |
| POST | `/orders` | places an order from whatever is in the cart. Body: `address`, `payment_method`, `promo_code?`, `shipping_method?` (`std` or `exp`). Totals are calculated on the server (not trusted from the app) and the cart is cleared automatically. |

### Profile 🔒
| Method | URL | What it does |
|---|---|---|
| GET | `/profile` | current user info |
| PUT | `/profile` | update: `name?`, `email?`, `phone?` |

### Notifications 🔒
| Method | URL | What it does |
|---|---|---|
| GET | `/notifications` | list, newest first |
| PUT | `/notifications/{id}/read` | mark one as read |
| PUT | `/notifications/read-all` | mark everything as read |

🔒 = needs `Authorization: Bearer <token>` header

---

## 7. Important honesty note about payments

The `payment_methods` table only stores a label and the **last 4 digits**
for display purposes (like "Visa •••• 4242"). It does **not** store real
card numbers, and this project does not actually charge any money — it's a
demo/portfolio checkout flow. If you want real payments later, hook up
Stripe (or similar) and only ever store the token Stripe gives you, never
raw card details.

---

## 8. Folder structure quick tour

```
app/Models/                     - Eloquent models (User, Product, Order, etc)
app/Http/Controllers/Api/       - one controller per feature (Cart, Orders, etc)
routes/api.php                  - every API route lives here
database/migrations/            - defines all the database tables
database/seeders/               - fills the database with demo data
```
