# Tech Belt - Martial Arts Academy Management System

![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=flat-square&logo=laravel)
![Livewire](https://img.shields.io/badge/Livewire-3.x-FB70A9?style=flat-square&logo=livewire)
![Filament](https://img.shields.io/badge/Filament-3.x-FF5C00?style=flat-square&logo=filamentphp)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-4.x-06B6D4?style=flat-square&logo=tailwindcss)
![Alpine.js](https://img.shields.io/badge/Alpine.js-3.x-8BC0D0?style=flat-square&logo=alpine.js)
![MySQL](https://img.shields.io/badge/MySQL-8.x-4479A1?style=flat-square&logo=mysql)

A comprehensive martial arts academy management system built with modern Laravel stack. The system provides separate interfaces for administrators, staff (instructors), and students with features for class management, student onboarding, billing, and progress tracking.

## 📋 Project Status

**✅ Production Ready** - All core features implemented and tested

### Current Implementation Status

| Feature | Status | Notes |
|---------|--------|-------|
| **Admin Panel (Filament v3)** | ✅ Complete | Full CRUD with visual distinctiveness and financial guardrails |
| **Student PWA (Livewire)** | ✅ Complete | Mobile-friendly student dashboard |
| **Invite System** | ✅ Complete | Token-based registration with WhatsApp integration |
| **Direct User Creation** | ✅ Complete | Admin can create users directly without invitation |
| **Onboarding Wizard** | ✅ Complete | 4-step wizard with automatic redirection and mock payment |
| **Staff Scopes & Policies** | ✅ Complete | Role-based data filtering with EnrollmentPolicy |
| **Notification System** | ✅ Complete | Database notifications via Filament |
| **Authentication** | ✅ Complete | Fortify with phone/email login |
| **Database Schema** | ✅ Complete | All migrations created with visual/commercial fields |
| **Visual Distinctiveness** | ✅ Complete | Color, icons, reorderable tables for modalities |
| **Commercial Definition** | ✅ Complete | Pricing tiers with billing periods, frequency types, modality scopes |
| **Financial Guardrails** | ✅ Complete | Strict "no-edit" strategy for enrollments with Infolists |
| **Financial Management Section** | ✅ Complete | Incoming payments dashboard, expenses tracking, and resources inventory |
| **Custom Dashboard Widgets** | ✅ Complete | Business-focused dashboard with statistics, cash flow chart, and recent payments |
| **Enrollment System Refactoring** | ✅ Complete | Multiple class enrollment, payment separation, price override functionality |
| **PWA Setup** | ⚠️ Partial | Basic setup, needs service worker optimization |
| **Payment Integration** | ✅ Mock Complete | Full payment flow with mock processing for testing |
| **Production Deployment** | ✅ Ready | Configuration optimized for production |

## ✨ Features

### 🎯 Core Philosophy: "Guardrails for Financials, Freedom for Operations"
The system implements a high-integrity admin experience with strict financial controls while allowing operational flexibility.

### 🏢 Admin & Staff Panel (`/admin`)
- **Visual Distinctiveness**: Color pickers, icon selectors, and reorderable tables for modalities
- **Commercial Definition**: Advanced pricing tiers with billing periods, frequency types, and modality scopes
- **Financial Guardrails**: Strict "no-edit" strategy for enrollments with read-only views using Filament Infolists
- **Role-Based Access Control** using Filament Shield with granular permissions
- **Staff Scoped Views** - Instructors only see their students and classes with proper policy enforcement
- **Invite Management** - Generate and track registration invites with WhatsApp integration
- **Direct User Creation** - Admins can create users (students, staff, admins) directly without requiring the invitation flow, with password set during creation
- **Notification System** - Send announcements to students/staff via database notifications
- **Financial Management** - Complete financial tracking with three dedicated sections:
  - **Incoming Payments Dashboard** - View all student payments, subscriptions, and billing status
  - **Outcomes/Expenses Tracking** - Manage staff payments, maintenance costs, marketing expenses, and other operational costs
  - **Resources Inventory** - Track academy resources like first aid kits, equipment, marketing materials with maintenance scheduling
- **Custom Dashboard Widgets** - Business-focused dashboard with real-time statistics:
  - **Statistics Overview**: 4-card widget showing total students, monthly revenue, profit, and active enrollments
  - **Monthly Cash Flow Chart**: Full-width chart tracking daily income vs expenses with accumulated balance
  - **Recent Payments Table**: Latest completed payments with student, plan, amount, and status information
  - **Portuguese Localization**: All dashboard content translated to Brazilian Portuguese ("Painel de Controle")
- **Enhanced Enrollment System**:
  - **Multiple Class Enrollment**: Students can enroll in multiple classes based on plan limits
  - **Payment Separation**: Payment information stored separately in dedicated payments table
  - **Price Override**: Admins can set custom prices while defaulting to plan pricing
  - **Class Limit Validation**: Automatic validation based on plan's class count

### 📱 Student PWA (`/app`)
- **Mobile-First Interface** with responsive design
- **Class Enrollment** - Browse and join available classes
- **Progress Tracking** - View graduation history and attendance
- **Subscription Management** - View current plan and billing
- **Notifications** - Real-time updates from academy
- **Profile Management** - Update personal information

### 🔐 Authentication & Security
- **Phone-Based Login** - Phone number is the primary identifier (required)
- **Optional Email** - Email is optional throughout the system
- **Two-Factor Authentication** (2FA) support
- **Dual Registration Methods**:
  - **Direct Creation** - Admins create users directly in the admin panel with password set during creation
  - **Invite-Based** - Token-based registration via WhatsApp for self-service onboarding
- **Role-Based Permissions** (Admin, Staff, Student) with multi-role support
- **Password Validation** with Laravel Fortify
- **Phone Mask** - Automatic formatting for Brazilian phone numbers (+55 (XX) XXXXX-XXXX)

#### Panel-Based Role Enforcement
The system implements strict role-based access control through separate Filament panels:

- **Admin Panel** (`/admin`) - Dark theme, accessible only to users with `admin` role
  - Uses `CheckPanelRole:admin` middleware
  - Contains all 9 resources (Students, Staff, Enrollments, Modalities, Gym Classes, Pricing Tiers, Invites, Expenses, Resources)
  - Full administrative privileges

- **Staff Panel** (`/staff`) - Light theme, accessible only to users with `staff` role
  - Uses `CheckPanelRole:staff` middleware
  - Limited to 4 resources (Students, Enrollments, Gym Classes, Modalities)
  - Staff can only view students enrolled in their classes (scoped access)

- **Student PWA** (`/app`) - Accessible to users with `student` role
  - Redirects students away from admin/staff panels
  - Provides onboarding wizard and class management

**Security Implementation:**
- Custom middleware `app/Http/Middleware/CheckPanelRole.php` validates user role against panel requirements
- Unauthorized access attempts redirect users to their appropriate panel
- Separate visual themes (dark/light) provide immediate visual feedback of current role context
- Database-level policies enforce data access restrictions

### 📊 Business Logic
- **Flexible Pricing** - Tier-based pricing with billing periods (monthly, quarterly, annual)
- **Frequency Types** - Unlimited vs fixed class frequency with class caps
- **Modality Scopes** - Pricing tiers can be scoped to specific modalities
- **Financial Guardrails** - Strict "no-edit" strategy for enrollments ensures contract immutability
- **Automatic Billing** - Subscription-based payment system with next billing date tracking
- **Attendance Tracking** - Record and monitor student presence
- **Graduation System** - Track belt/rank progression
- **Capacity Management** - Class enrollment limits
- **Visual Identity** - Modalities have colors and icons for visual distinctiveness
- **Enhanced Enrollment Logic**:
  - **Default Plan Pricing**: Enrollment uses pricing tier price as default
  - **Admin Price Override**: Custom prices can be set with `is_custom_price` flag
  - **Multiple Classes**: Students can enroll in multiple classes (many-to-many relationship)
  - **Payment Tracking**: Separate payment records with status tracking (pending, completed, failed, refunded)

## 🚀 Quick Start

### Prerequisites
- Docker and Docker Compose
- Git
- Node.js 18+ (optional, handled by Sail)

### Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd techbelt-backend
   ```

2. **Start the development environment**
   ```bash
   ./vendor/bin/sail up -d
   ```

3. **Install dependencies**
   ```bash
   ./vendor/bin/sail composer install
   ./vendor/bin/sail npm install
   ./vendor/bin/sail npm run build
   ```

4. **Run database migrations and seeders**
   ```bash
   ./vendor/bin/sail artisan migrate --seed
   ```

5. **Create admin user**
   After running migrations with seeders, create an admin user via Tinker:
   ```bash
   ./vendor/bin/sail artisan tinker
   ```
   Then run:
   ```php
   User::create([
       'name' => 'Admin User',
       'email' => 'admin@techbelt.io',
       'password' => Hash::make('password'),
       'email_verified_at' => now(),
   ])->assignRole('admin');
   ```
6. **Access the application**
  - Main Application: http://localhost:8080
  - Admin Panel: http://localhost:8080/admin
  - Student PWA: http://localhost:8080/app
  - MySQL: localhost:3306 (user: `sail`, password: `password`)

## 👥 Test Users & Credentials

After running the database seeder, the following test users are created for development and testing purposes:

### Primary Test Accounts

**Note:** Phone number is now the primary identifier for authentication. Email is optional and shown for reference only.

| User | Phone (Primary Login) | Password | Role | Email (Optional) | Purpose |
|------|----------------------|----------|------|------------------|---------|
| João Silva | `+55 (11) 98765-4321` | `password` | Student | `joao.silva@example.com` | **Test onboarding flow** - No subscription, will be redirected to onboarding |
| Maria Santos | `+55 (21) 99876-5432` | `password` | Student | `maria.santos@example.com` | **Test app access** - Has active subscription, can access app directly |
| Admin User | `+55 (31) 91234-5678` | `password` | Admin | `admin@techbelt.io` | **Create resources manually** - Full admin access |
| Carlos Oliveira | `+55 (41) 92345-6789` | `password` | Staff | `carlos.oliveira@example.com` | **Test staff role** - Limited access |

### Additional Users
- 3 random student users with Brazilian phone numbers
- All users have password: `password`
- Email verification is disabled (email is optional)

## 🔄 Onboarding Flow (Automatic Redirection)

The system now implements automatic onboarding redirection for students without subscriptions:

### Expected User Flow:
1. **New Student (No Subscription)**
  - Login or accept invite → Redirected to `/onboarding`
  - Step 1: Select modalities → Step 2: Select classes → Step 3: Choose pricing → Step 4: Mock payment
  - After completion: Subscription created → Redirected to main app

2. **Existing Student (With Subscription)**
  - Login → Direct access to `/app` (main student dashboard)
  - No onboarding required

3. **Admin/Staff Users**
  - Login → Access respective interfaces
  - Bypass subscription check (different roles)

### Key Components:
- **Middleware**: `CheckSubscription` - Automatically redirects students without active subscriptions
- **Onboarding Wizard**: 4-step process with validation and mock payment
- **Invite System**: New users created via invites are checked for subscription status

## 🏗️ Project Architecture

### Technology Stack
- **Backend**: Laravel 12.x with PHP 8.3
- **Frontend**: Livewire 3.x, Alpine.js 3.x, Tailwind CSS 4.x
- **Admin Panel**: Filament PHP 3.x
- **Database**: MySQL 8.x
- **Authentication**: Laravel Fortify
- **Development**: Laravel Sail (Docker)
- **Asset Bundling**: Vite
- **Localization**: Full Brazilian Portuguese (pt_BR) translation support

### Localization & Internationalization
The application is fully localized for Brazilian Portuguese (pt_BR) with the following features:

#### Translation Files
- **`lang/pt_BR.json`** - General application translations (navigation labels, buttons, common terms)
- **`lang/pt_BR/auth.php`** - Authentication messages
- **`lang/pt_BR/validation.php`** - Validation rules and messages
- **`lang/pt_BR/passwords.php`** - Password reset messages
- **`lang/pt_BR/pagination.php`** - Pagination translations

#### Key Translations
- **Role-based terminology**: "Students" → "Alunos", "Staff" → "Professores"
- **Financial terms**: "Incoming Payments" → "Pagamentos Recebidos", "Outcomes" → "Saídas"
- **Dashboard**: "Dashboard" → "Painel de Controle" (custom admin dashboard title)
- **Navigation labels**: All Filament resources use `getNavigationLabel()` method with `__()` helper
- **Business context**: Martial arts academy specific terms properly translated
- **Widget content**: All dashboard widgets (statistics, charts, tables) display Portuguese text

#### Implementation Details
- **Locale Configuration**: `config/app.php` set to `'locale' => 'pt_BR'`
- **Filament Integration**: Navigation labels automatically translated via Laravel's translation system
- **Best Practices**: All user-facing text uses translation keys, no hardcoded strings in resources
- **Extensibility**: Easy to add new languages by creating additional locale directories

### Directory Structure
```
app/
├── Filament/Resources/     # Admin panel resources
├── Livewire/              # Livewire components
│   ├── App/              # Student PWA components
│   ├── Settings/         # User settings
│   └── OnboardingWizard.php
├── Http/Controllers/     # Traditional controllers
├── Models/              # Eloquent models
└── Providers/           # Service providers
```

### Key Components

#### 1. Invite System
- **Controller**: `app/Http/Controllers/InviteController.php`
- **View**: `resources/views/invite/accept.blade.php`
- **Model**: `app/Models/Invite.php`
- **Routes**: `/invite/{token}`

#### 2. Direct User Creation
- **Resource**: `app/Filament/Resources/StudentResource.php` — Admin can create users directly via the Filament admin panel
- **Password Field**: Added to the form, required on create, optional on edit (same pattern as StaffResource)
- **Role Selection**: Admins can assign any role (student, staff, admin) during creation
- **Email Verification**: Auto-set to verified when admin creates the user directly
- **Password Hashing**: Automatic via Laravel's `'password' => 'hashed'` cast on the User model

#### 3. Onboarding Wizard
- **Component**: `app/Livewire/OnboardingWizard.php`
- **View**: `resources/views/livewire/onboarding-wizard.blade.php`
- **Route**: `/onboarding`

#### 4. Staff Scopes & Policies
- **Enrollment Policy**: `app/Policies/EnrollmentPolicy.php` - Strict "no-edit" strategy with role-based permissions
- **Student Scopes**: `app/Filament/Resources/StudentResource.php` - Role-based filtering for staff
- **Visual Resources**: `app/Filament/Resources/ModalityResource.php` - Color, icons, reorderable tables
- **Commercial Resources**: `app/Filament/Resources/PricingTierResource.php` - Billing periods, frequency types, modality scopes
- **Financial Guardrails**: `app/Filament/Resources/EnrollmentResource.php` - Read-only views with custom actions (cancel, renew, change payment)

#### 5. Enhanced Enrollment System
- **Payment Model**: `app/Models/Payment.php` - Dedicated payment tracking with status management
- **Many-to-Many Relationship**: `enrollment_class` pivot table for multiple class enrollment
- **Price Calculation**: Automatic default to plan pricing with admin override capability
- **Form Validation**: Class count validation based on plan limits
- **Payment History**: Complete payment tracking within enrollment infolist

#### 6. Custom Dashboard Widgets
- **Dashboard Page**: `app/Filament/Pages/Dashboard.php` - Custom dashboard class extending Filament's base Dashboard
- **Statistics Overview Widget**: `app/Filament/Widgets/StatsOverviewWidget.php` - 4-card widget showing key business metrics:
  - Total de Alunos (Total Students)
  - Receita Mensal (Monthly Revenue)
  - Lucro Mensal (Monthly Profit)
  - Matrículas Ativas (Active Enrollments)
- **Monthly Cash Flow Chart**: `app/Filament/Widgets/MonthlyCashFlowChart.php` - Full-width chart tracking daily income vs expenses with accumulated balance line
- **Recent Payments Widget**: `app/Filament/Widgets/RecentPaymentsWidget.php` - Table widget showing latest completed payments with Portuguese translations
- **Portuguese Localization**: Dashboard title translated to "Painel de Controle" in `lang/pt_BR.json`

#### 7. Database Schema Enhancements
- **Visual Fields Migration**: `database/migrations/2026_02_07_195638_add_visual_fields_to_modalities_table.php`
- **Commercial Fields Migration**: `database/migrations/2026_02_07_195820_add_commercial_fields_to_pricing_tiers_table.php`
- **Pivot Table Migration**: `database/migrations/2026_02_07_195902_create_pricing_tier_modality_table.php`
- **Financial Fields Migration**: `database/migrations/2026_02_07_200138_add_financial_fields_to_enrollments_table.php`
- **Payment System Migration**: `database/migrations/2026_02_12_145455_create_payments_table.php`
- **Multiple Class Enrollment**: `database/migrations/2026_02_12_145500_create_enrollment_class_table.php`
- **Enrollment Refactoring**: `database/migrations/2026_02_12_145506_modify_enrollments_table_for_new_requirements.php`
- **Foreign Key Fix**: `database/migrations/2026_02_12_170656_rename_class_id_to_gym_class_id_in_enrollment_class_table.php`

## 📖 Usage Guide

### For Administrators
1. Log in at `/admin` with admin credentials
2. **Create users directly** via **Alunos** (Students) resource — fill in name, phone, password, and select roles
3. Alternatively, navigate to **Convites** (Invites) to create user invitations for self-service registration
4. Use **Modalidades** (Modalities) with visual distinctiveness (colors, icons, reorderable tables)
5. Configure **Planos** (Pricing Tiers) with commercial definitions (billing periods, frequency types, modality scopes)
6. Manage **Matrículas** (Enrollments) with enhanced features:
   - Create enrollments with multiple class selection
   - Set custom prices when needed (defaults to plan pricing)
   - View payment history for each enrollment
   - Register new payments directly from enrollment actions
7. Monitor **Alunos** (Students) with role-based scopes and filtering
8. Use **Archive** actions for pricing tiers with active subscriptions
9. Enforce **Financial Contract Immutability** - enrollments cannot be edited once created

### For Staff/Instructors
1. Log in at `/admin` with staff credentials
2. View only your assigned classes and students (enforced by EnrollmentPolicy)
3. **Cannot edit enrollments** - Financial contracts are immutable
4. Can view enrollment details in read-only mode using Filament Infolists
5. Record attendance and update student progress
6. Send notifications to your students
7. Access is scoped based on role and class assignments

### For Students
1. Accept invitation via WhatsApp link
2. Complete onboarding wizard to select classes and payment
3. Access student dashboard at `/app`
4. View schedule, track progress, and manage subscription

## 🚢 Deployment

### Production Requirements
- PHP 8.2+ with extensions: mbstring, pdo_mysql, tokenizer, xml, ctype, json
- MySQL 8.0+ or MariaDB 10.3+
- Composer 2.0+
- Node.js 18+ (for asset compilation)
- Web server (Nginx/Apache)

### Deployment Steps

1. **Prepare the application**
   ```bash
   composer install --optimize-autoloader --no-dev
   npm run build
   php artisan key:generate
   php artisan storage:link
   ```

2. **Configure environment**
   ```bash
   cp .env.example .env
   # Edit .env with production values
   ```

3. **Set up database**
   ```bash
   php artisan migrate --force
   php artisan db:seed --force
   ```

4. **Optimize for production**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

5. **Configure web server**
   - Point document root to `public/`
   - Configure rewrite rules for Laravel
   - Set proper permissions for `storage/` and `bootstrap/cache/`

### Hostinger WordPress Server Deployment

Although designed for VPS deployment, the application can run on Hostinger's WordPress hosting with adjustments:

#### Prerequisites
- Hostinger account with WordPress hosting (PHP 8.2+ support)
- FTP/SFTP access or File Manager
- MySQL database created via Hostinger control panel

#### Steps
1. **Prepare locally**: Run `composer install --optimize-autoloader --no-dev` and `npm run build`
2. **Upload files**: Upload entire project (excluding `node_modules`, `.git`) to `public_html/laravel/`
3. **Configure database**: Update `.env` with Hostinger MySQL credentials
4. **Set permissions**: Make `storage` and `bootstrap/cache` writable (755)
5. **Adjust paths**: Move `public/*` to web root or configure document root
6. **Run migrations**: Via SSH or PHPMyAdmin: `php artisan migrate --force`

#### Troubleshooting Hostinger
- **White screen**: Check `storage/logs/laravel.log`, enable debug mode temporarily
- **404 errors**: Ensure `.htaccess` is present and mod_rewrite enabled
- **Database errors**: Verify credentials and MySQL remote access

**Recommendation**: For production use, upgrade to Hostinger VPS for better performance and control.

## 🧪 Testing

The project uses PestPHP for testing. Run tests with:

```bash
./vendor/bin/sail artisan test
```

Or for specific test suites:
```bash
./vendor/bin/sail artisan test --testsuite=Feature
./vendor/bin/sail artisan test --testsuite=Unit
```

### Enhanced Enrollment Tests
The test suite includes comprehensive tests for the enhanced enrollment system:
- Price calculation (default vs custom)
- Multiple class enrollment validation
- Payment creation and tracking
- Class limit enforcement based on plan

## 🔧 Development

### Common Sail Commands
```bash
# Start/stop containers
./vendor/bin/sail up -d
./vendor/bin/sail down

# Run artisan commands
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan make:model Product

# Run composer commands
./vendor/bin/sail composer require package-name

# Run npm commands
./vendor/bin/sail npm run dev
./vendor/bin/sail npm run build

# View logs
./vendor/bin/sail logs -f
```

### Environment Variables
Key environment variables for development:
```env
APP_PORT=8080
VITE_PORT=5173
DB_HOST=mysql
DB_DATABASE=techbelt
DB_USERNAME=sail
DB_PASSWORD=password
```

### Code Style
The project uses Laravel Pint for code formatting:
```bash
./vendor/bin/sail pint
```

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

### Development Guidelines
- Follow PSR-12 coding standards
- Write tests for new features
- Update documentation accordingly
- Use meaningful commit messages

## 📄 License

This project is proprietary software. All rights reserved.

## 📞 Support

For technical support or questions:
- Create an issue in the repository
- Contact the development team

## 🎯 Roadmap

### Planned Features
- [ ] Full payment gateway integration (MercadoPago/Asaas)
- [ ] Advanced PWA features (offline mode, push notifications)
- [ ] Mobile app (React Native/Ionic)
- [ ] Advanced reporting and analytics
- [ ] Integration with accounting software
- [ ] Bulk SMS/WhatsApp messaging

### Known Limitations
- Payment integration currently uses stub implementation
- PWA service workers need optimization for offline functionality
- Some advanced filtering features pending in admin panel

---

**Built with ❤️ using the TALL Stack (Tailwind, Alpine, Laravel, Livewire) + Filament**

*Last Updated: February 13, 2026*