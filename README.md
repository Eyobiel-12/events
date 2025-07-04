# Events - Multi-Tenant Event Management System

Een krachtige multi-tenant event management applicatie gebouwd met Laravel 11 en Filament, speciaal ontworpen voor het beheren van events, tickets, vendors en feedback.

## 🚀 Features

### Admin Panel
- **Organisatie Management**: Volledige controle over alle organisaties
- **Gebruiker Management**: Beheer van alle gebruikers en rollen
- **Event Overzicht**: Bekijk alle events van alle organisaties
- **Platform Statistieken**: Dashboard met totale statistieken
- **Permissie Management**: Volledige controle over rollen en permissies

### Organizer Panel
- **Event Management**: Beheer events voor eigen organisatie
- **Ticket Management**: Volledige ticket lifecycle management
- **Vendor & Booth Management**: Beheer vendors en booth reserveringen
- **Feedback Systeem**: Verzamel en analyseer attendee feedback
- **Ticket Scanning**: QR-code scanning voor event check-in
- **Export Functionaliteit**: Exporteer data naar verschillende formaten
- **Real-time Statistieken**: Dashboard met organisatie-specifieke metrics

### Multi-Tenant Architectuur
- **Organisatie Isolatie**: Elke organisatie heeft eigen data
- **Rol-gebaseerde Toegang**: Admin en Organizer rollen
- **Fijnmazige Permissies**: 111 permissies voor admin, 40 voor organizer
- **Secure Data Access**: Automatische data filtering per organisatie

## 🛠 Technische Stack

- **Backend**: Laravel 11 (PHP 8.3+)
- **Admin Panel**: Filament 3.x
- **Database**: SQLite (development) / MySQL/PostgreSQL (production)
- **Authentication**: Laravel Breeze
- **Authorization**: Spatie Laravel Permission
- **Frontend**: Blade templates met Tailwind CSS
- **Icons**: Heroicons

## 📋 Vereisten

- PHP 8.3 of hoger
- Composer
- Node.js & NPM (voor asset compilation)
- SQLite, MySQL of PostgreSQL

## 🚀 Installatie

### 1. Clone Repository
```bash
git clone <repository-url>
cd Events
```

### 2. Install Dependencies
```bash
composer install
npm install
```

### 3. Environment Setup
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Database Setup
```bash
php artisan migrate
php artisan db:seed
```

### 5. Build Assets
```bash
npm run build
```

### 6. Start Development Server
```bash
php artisan serve
```

## 👥 Gebruikers & Rollen

### Admin Account
- **Email**: `admin@habesha.events`
- **Password**: `admin1234`
- **Rollen**: `admin`
- **Permissies**: 111 permissies (volledige toegang)

### Organizer Account
- **Email**: `organizer@habesha.events`
- **Password**: `organizer1234`
- **Rollen**: `organizer`
- **Permissies**: 40 permissies (eigen organisatie)

## 🔐 Permissie Systeem

### Admin Permissies (111 total)
- **Organisatie Management**: Volledige CRUD toegang
- **Gebruiker Management**: Volledige CRUD toegang
- **Event Management**: Alle events van alle organisaties
- **Ticket Management**: Alle tickets van alle organisaties
- **Vendor/Booth Management**: Alle vendors en booths
- **Feedback/Scans**: Alle feedback en ticket scans
- **Systeem Permissies**: Rol en permissie management
- **Dashboard & Export**: Volledige export functionaliteit

### Organizer Permissies (40 total)
- **Event Management**: Alleen eigen organisatie events
- **Ticket Management**: Alleen eigen organisatie tickets
- **Vendor/Booth Management**: Alleen eigen organisatie vendors/booths
- **Feedback/Scans**: Alleen eigen organisatie feedback/scans
- **Dashboard & Export**: Alleen eigen organisatie data

## 📊 Database Schema

### Core Models
- **User**: Gebruikers met rollen en permissies
- **Organisation**: Multi-tenant organisaties
- **Event**: Events met locatie en timing informatie
- **TicketType**: Verschillende ticket categorieën
- **Ticket**: Individuele tickets met attendee informatie
- **Vendor**: Event vendors/exhibitors
- **Booth**: Vendor booth reserveringen
- **Feedback**: Attendee feedback en ratings
- **TicketScan**: QR-code scanning logs

### Relationships
- Users ↔ Organisations (Many-to-Many met pivot role)
- Organisation → Events (One-to-Many)
- Event → TicketTypes (One-to-Many)
- TicketType → Tickets (One-to-Many)
- Organisation → Vendors (One-to-Many)
- Event → Booths (One-to-Many)
- Vendor → Booths (One-to-Many)
- Event → Feedback (One-to-Many)
- Ticket → Feedback (One-to-Many)
- Event → TicketScans (One-to-Many)

## 🎯 API Endpoints

### Admin API
- `GET /admin/dashboard` - Admin dashboard
- `GET /admin/organisations` - Organisatie overzicht
- `GET /admin/users` - Gebruiker overzicht
- `GET /admin/events` - Event overzicht

### Organizer API
- `GET /organizer/dashboard` - Organizer dashboard
- `GET /organizer/events` - Eigen events
- `GET /organizer/tickets` - Eigen tickets
- `GET /organizer/vendors` - Eigen vendors
- `GET /organizer/feedback` - Eigen feedback

## 🔧 Artisan Commands

### Permissie Management
```bash
# Controleer permissies van alle gebruikers
php artisan users:check-permissions

# Controleer permissies van specifieke gebruiker
php artisan users:check-permissions admin@habesha.events
```

### Database Management
```bash
# Reset en seed database
php artisan migrate:fresh --seed

# Seed alleen permissies
php artisan db:seed --class=PermissionSeeder

# Seed test data
php artisan db:seed --class=TestDataSeeder
php artisan db:seed --class=OrganizerTestDataSeeder
```

## 🧪 Testing

```bash
# Run alle tests
php artisan test

# Run specifieke test suite
php artisan test --filter=EventTest
```

## 📁 Project Structuur

```
Events/
├── app/
│   ├── Console/Commands/
│   │   └── CheckUserPermissions.php
│   ├── Filament/
│   │   ├── Admin/           # Admin panel resources
│   │   └── Organizer/       # Organizer panel resources
│   ├── Http/
│   │   ├── Controllers/
│   │   └── Middleware/
│   │       ├── CheckPermission.php
│   │       └── RedirectBasedOnRole.php
│   ├── Models/
│   │   ├── Event.php
│   │   ├── Organisation.php
│   │   ├── Ticket.php
│   │   ├── User.php
│   │   └── Vendor.php
│   ├── Policies/
│   │   ├── EventPolicy.php
│   │   ├── OrganisationPolicy.php
│   │   ├── TicketPolicy.php
│   │   └── VendorPolicy.php
│   └── Providers/
│       └── AuthServiceProvider.php
├── database/
│   ├── migrations/
│   └── seeders/
│       ├── AdminUserSeeder.php
│       ├── DatabaseSeeder.php
│       ├── OrganizerTestDataSeeder.php
│       ├── PermissionSeeder.php
│       ├── RoleSeeder.php
│       └── TestDataSeeder.php
├── routes/
│   └── web.php
└── README.md
```

## 🔒 Security Features

- **Multi-Tenant Isolation**: Automatische data filtering per organisatie
- **Role-Based Access Control**: Fijnmazige permissie systeem
- **Policy-Based Authorization**: Model-level toegangscontrole
- **Middleware Protection**: Route-level permissie checks
- **CSRF Protection**: Automatische CSRF token validatie
- **Input Validation**: Uitgebreide form validatie
- **SQL Injection Protection**: Eloquent ORM met prepared statements

## 📈 Performance Optimizations

- **Database Indexing**: Geoptimaliseerde queries met indexes
- **Eager Loading**: Voorkom N+1 query problemen
- **Caching**: Permission caching voor 24 uur
- **Lazy Loading**: Componenten laden alleen wanneer nodig
- **Asset Optimization**: Minified CSS/JS voor productie

## 🚀 Deployment

### Production Checklist
- [ ] Set `APP_ENV=production` in `.env`
- [ ] Configure database connection
- [ ] Set `APP_DEBUG=false`
- [ ] Configure caching (Redis/Memcached)
- [ ] Set up queue workers
- [ ] Configure file storage
- [ ] Set up SSL certificate
- [ ] Configure backup strategy

### Environment Variables
```env
APP_NAME="Events Management"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=events_db
DB_USERNAME=events_user
DB_PASSWORD=secure_password

CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
```

## 🤝 Contributing

1. Fork het project
2. Maak een feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit je wijzigingen (`git commit -m 'Add some AmazingFeature'`)
4. Push naar de branch (`git push origin feature/AmazingFeature`)
5. Open een Pull Request

## 📝 License

Dit project is gelicenseerd onder de MIT License - zie het [LICENSE](LICENSE) bestand voor details.

## 🆘 Support

Voor vragen of problemen:
- Open een issue op GitHub
- Neem contact op via email
- Raadpleeg de documentatie

## 🔄 Changelog

### v1.0.0 (2024-07-04)
- Initial release
- Multi-tenant event management
- Admin en Organizer panels
- Uitgebreid permissie systeem
- QR-code ticket scanning
- Feedback systeem
- Export functionaliteit

---

**Gemaakt met ❤️ door het Events Team**
