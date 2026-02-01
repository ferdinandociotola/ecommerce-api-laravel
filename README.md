# E-commerce REST API

[![Tests](https://img.shields.io/badge/tests-10%20passed-brightgreen)]()
[![Laravel](https://img.shields.io/badge/Laravel-12-red)]()
[![PHP](https://img.shields.io/badge/PHP-8.3-blue)]()

API REST completa per e-commerce sviluppata con Laravel 12, Sanctum authentication, sistema carrello dual-mode (guest/logged), integrazione Stripe e test suite PHPUnit.

## 🚀 Tecnologie

- **Laravel 12** - Framework PHP
- **PHP 8.3** - Linguaggio backend
- **MySQL 8.0** - Database relazionale
- **Laravel Sanctum** - Token authentication
- **Stripe** - Payment processing
- **PHPUnit** - Testing automatico
- **Nginx** - Web server (produzione)

## 📋 Features Complete

### Authentication & Security
- ✅ Registrazione utenti con email/password
- ✅ Login con token Sanctum (API token-based)
- ✅ Logout con invalidazione token
- ✅ Sistema ruoli (admin/user)
- ✅ Middleware protezione route admin
- ✅ Middleware custom OptionalAuth (guest + logged)

### Products & Categories
- ✅ CRUD prodotti completo (admin only)
- ✅ API pubbliche visualizzazione prodotti/categorie
- ✅ Validazione stock disponibile
- ✅ Relazioni Eloquent (Product → Category)

### Shopping Cart System
- ✅ **Carrello guest** (sessione PHP)
- ✅ **Carrello logged users** (database persistente)
- ✅ Middleware custom `OptionalAuth` per gestione dual-mode
- ✅ Cart merge automatico al login/registrazione
- ✅ Operazioni complete: add, view, update, delete, clear
- ✅ Calcolo totale automatico con validazione stock

### Orders & Checkout
- ✅ Checkout flow completo (cart → order + order_items)
- ✅ Transazione database atomica (all or nothing)
- ✅ Price snapshot (storico prezzi al momento ordine)
- ✅ Svuotamento automatico carrello post-checkout
- ✅ API visualizzazione ordini utente
- ✅ Status tracking (pending, confirmed, shipped)

### Payment Integration
- ✅ Integrazione Stripe payment gateway
- ✅ Payment Intent creation
- ✅ Secure payment flow (carta non passa mai per backend)
- ✅ Test mode con carte test Stripe
- ✅ Metadata tracking (order_id, user_id)
- ✅ Payment confirmation e aggiornamento status

### Email Notifications
- ✅ Email conferma ordine HTML professionale
- ✅ SMTP Gmail configurato
- ✅ Template con tabella prodotti, prezzi, totale
- ✅ Invio automatico post-checkout

### Testing
- ✅ PHPUnit test suite completa (10 test, 47 assertions)
- ✅ Authentication tests
- ✅ Cart operations tests
- ✅ Checkout flow tests
- ✅ Payment integration tests
- ✅ SQLite :memory: per velocità
- ✅ RefreshDatabase per isolamento

## 📡 API Endpoints

### Public Routes
```
GET    /api/products           - Lista prodotti con paginazione
GET    /api/products/{id}      - Dettaglio singolo prodotto
GET    /api/categories         - Lista categorie
```

### Authentication
```
POST   /api/register           - Registrazione nuovo utente
POST   /api/login              - Login (ritorna token Sanctum)
POST   /api/logout             - Logout (invalida token)
```

### Admin Routes (require admin role + auth)
```
POST   /api/admin/products     - Crea nuovo prodotto
PUT    /api/admin/products/{id} - Modifica prodotto esistente
DELETE /api/admin/products/{id} - Elimina prodotto
```

### Cart (guest + logged users)
```
GET    /api/cart               - Visualizza carrello corrente
POST   /api/cart               - Aggiungi prodotto al carrello
PUT    /api/cart/{id}          - Modifica quantità prodotto
DELETE /api/cart/{id}          - Rimuovi prodotto dal carrello
DELETE /api/cart               - Svuota completamente il carrello
```

### Orders (require auth)
```
POST   /api/checkout           - Crea ordine da carrello
GET    /api/orders             - Lista ordini utente
GET    /api/orders/{id}        - Dettaglio singolo ordine
```

### Payments (require auth)
```
POST   /api/payment/create-intent  - Crea Stripe Payment Intent
POST   /api/payment/confirm        - Conferma pagamento completato
```

## 🗄️ Database Schema

### Tables
- **users** - Utenti con ruoli (admin/user)
- **categories** - Categorie prodotti
- **products** - Prodotti con stock e prezzi
- **cart_items** - Carrello utenti loggati
- **orders** - Ordini con status e totali
- **order_items** - Dettaglio prodotti ordinati con price snapshot
- **payments** - Pagamenti Stripe con tracking
- **personal_access_tokens** - Token Sanctum (auth)

### Relazioni
```
User → hasMany Orders
User → hasMany CartItems
Order → hasMany OrderItems
Order → hasOne Payment
Product → hasMany OrderItems
Product → hasMany CartItems
Category → hasMany Products
```

## 🛠️ Setup Locale

### Requisiti
- PHP >= 8.3
- Composer
- MySQL >= 8.0
- Git
- Account Stripe (test mode)
- Account Gmail (per SMTP)

### Installazione

**1. Clone repository:**
```bash
git clone https://github.com/ferdinandociotola/ecommerce-api-laravel.git
cd ecommerce-api-laravel
```

**2. Installa dipendenze:**
```bash
composer install
```

**3. Configura environment:**
```bash
cp .env.example .env
php artisan key:generate
```

**4. Configura database in .env:**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ecommerce_api
DB_USERNAME=root
DB_PASSWORD=tua_password
```

**5. Configura Stripe in .env:**
```env
STRIPE_KEY=pk_test_xxxxx
STRIPE_SECRET=sk_test_xxxxx
```

**6. Configura email SMTP in .env:**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_ENCRYPTION=tls
MAIL_USERNAME=tua_email@gmail.com
MAIL_PASSWORD=password_app_gmail
MAIL_FROM_ADDRESS="tua_email@gmail.com"
MAIL_FROM_NAME="E-commerce API"
```

**7. Crea database:**
```bash
mysql -u root -p -e "CREATE DATABASE ecommerce_api;"
```

**8. Esegui migrations e seeders:**
```bash
php artisan migrate --seed
```

**9. Avvia server:**
```bash
php artisan serve
```

API disponibile su: `http://127.0.0.1:8000`

## 🧪 Testing

**Esegui tutti i test:**
```bash
php artisan test
```

**Test specifici:**
```bash
php artisan test --filter AuthTest
php artisan test --filter CartTest
php artisan test --filter OrderTest
php artisan test --filter PaymentTest
```

**Test con coverage:**
```bash
php artisan test --coverage
```

**Test suite include:**
- ✅ Authentication (register, login, validation)
- ✅ Cart operations (add, view, stock validation)
- ✅ Checkout flow (empty cart, order creation)
- ✅ Payment integration (Stripe intent, validation)

## 🧪 Test API con cURL

### Registrazione
```bash
curl -X POST http://127.0.0.1:8000/api/register \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "password": "password",
    "password_confirmation": "password"
  }'
```

### Login
```bash
curl -X POST http://127.0.0.1:8000/api/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "password"
  }'
```

### Aggiungi al carrello (con token)
```bash
curl -X POST http://127.0.0.1:8000/api/cart \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "product_id": 1,
    "quantity": 2
  }'
```

### Checkout
```bash
curl -X POST http://127.0.0.1:8000/api/checkout \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

## 🏗️ Architettura

### Design Patterns
- **Repository Pattern** via Eloquent ORM
- **MVC Architecture** (Model-View-Controller)
- **Token-based Authentication** (Sanctum)
- **RESTful API Design**
- **Middleware Chain Pattern** (OptionalAuth)
- **Factory Pattern** (Database seeders)

### Middleware Custom

**OptionalAuth** - Middleware per route che accettano sia guest che logged users:
- Processa token Sanctum se presente (logged)
- Continua senza bloccare se token assente (guest)
- Utilizzato per Cart API (dual-mode functionality)

### Security Features
- SQL injection protection (Eloquent ORM)
- XSS protection (Laravel sanitization)
- CSRF protection (API tokens)
- Password hashing (bcrypt)
- Role-based access control (RBAC)
- Rate limiting (configurable)

## 📝 Roadmap Future Features

- [ ] Product images upload (Storage/S3)
- [ ] Admin dashboard UI
- [ ] Order tracking & shipping
- [ ] Product reviews & ratings
- [ ] Wishlist functionality
- [ ] Coupon/discount system
- [ ] Inventory management
- [ ] Multi-currency support
- [ ] Advanced search & filters
- [ ] API rate limiting refinement

## 👨‍💻 Autore

**Ferdinando Ciotola**

Junior PHP Developer in formazione - Portfolio Project 2026

- LinkedIn: [linkedin.com/in/ferdinando-ciotola](https://www.linkedin.com/in/ferdinando-ciotola-b189a9163/)
- GitHub: [@ferdinandociotola](https://github.com/ferdinandociotola)
- Email: ferdinandociotola@gmail.com

## 📄 Licenza

Progetto sviluppato a scopo didattico e portfolio professionale.

## 🙏 Acknowledgments

- Laravel Framework Team
- Stripe Documentation
- PHPUnit Testing Framework
- Laravel Sanctum