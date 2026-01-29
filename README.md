# E-commerce REST API

API REST completa per e-commerce sviluppata con Laravel 12, Sanctum authentication e sistema carrello guest/logged.

## 🚀 Tecnologie

- **Laravel 12** - Framework PHP
- **PHP 8.3** - Linguaggio backend
- **MySQL 8.0** - Database relazionale
- **Laravel Sanctum** - Token authentication
- **Nginx** - Web server (produzione)

## 📋 Features

### Authentication
- Registrazione utenti con email/password
- Login con token Sanctum
- Sistema ruoli (admin/user)
- Middleware protezione route admin

### Products & Categories
- CRUD prodotti (admin)
- API pubbliche visualizzazione prodotti/categorie
- Validazione stock disponibile

### Cart System
- **Carrello guest** (sessione PHP)
- **Carrello logged users** (database)
- Middleware custom `OptionalAuth` per gestione dual-mode
- Operazioni: aggiungi, visualizza, modifica, rimuovi, svuota
- Calcolo totale automatico

## 📡 API Endpoints

### Public Routes