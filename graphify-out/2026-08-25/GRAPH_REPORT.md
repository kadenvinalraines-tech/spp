# Graph Report - .  (2026-08-25)

## Corpus Check
- cluster-only mode — file stats not available

## Summary
- 821 nodes · 1222 edges · 160 communities (148 shown, 12 thin omitted)
- Extraction: 99% EXTRACTED · 1% INFERRED · 0% AMBIGUOUS · INFERRED: 17 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `05c3ccc4`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- User
- Controller
- Illuminate\Foundation\Http\FormRequest
- Student
- composer.json
- Illuminate\Http\Request
- SchoolClass
- Role
- devDependencies
- Expense
- scripts
- lsp-1d3a3f2dc6acff9e.php
- SettingController.php
- Illuminate\Contracts\View\View
- StudentsExport
- Illuminate\Database\Eloquent\Relations\BelongsTo
- SchoolSetting.php
- Auditable.php
- Bill.php
- Illuminate\Database\Eloquent\Model
- Payment.php
- AppServiceProvider
- ExampleTest
- profile/edit.blade.php
- deploy.sh
- layouts.navigation
- dashboard/admin.blade.php
- kepala_sekolah.blade.php
- orang_tua.blade.php
- superadmin.blade.php
- tata_usaha.blade.php
- yayasan.blade.php

## God Nodes (most connected - your core abstractions)
1. `User` - 51 edges
2. `Student` - 34 edges
3. `Controller` - 32 edges
4. `SchoolClass` - 22 edges
5. `AcademicYear` - 19 edges
6. `TestCase` - 18 edges
7. `Role` - 17 edges
8. `Expense` - 13 edges
9. `BillController` - 13 edges
10. `StudentController` - 12 edges

## Surprising Connections (you probably didn't know these)
- `up()` --calls--> `Permission`  [INFERRED]
  database/migrations/2026_07_14_094428_create_default_superadmin_user.php → app/Models/Permission.php
- `up()` --calls--> `User`  [EXTRACTED]
  database/migrations/2026_07_14_094428_create_default_superadmin_user.php → app/Models/User.php
- `up()` --calls--> `Role`  [EXTRACTED]
  database/migrations/2026_07_14_094428_create_default_superadmin_user.php → app/Models/Role.php
- `AcademicYearController` --inherits--> `Controller`  [EXTRACTED]
  app/Http/Controllers/AcademicYearController.php → app/Http/Controllers/Controller.php
- `AcademicYearSessionController` --inherits--> `Controller`  [EXTRACTED]
  app/Http/Controllers/AcademicYearSessionController.php → app/Http/Controllers/Controller.php

## Import Cycles
- None detected.

## Communities (160 total, 12 thin omitted)

### Community 0 - "User"
Cohesion: 0.05
Nodes (20): UserController, BelongsToMany, User, UserFactory, Illuminate\Database\Eloquent\Factories\Factory, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Foundation\Auth\User, Illuminate\Foundation\Testing\RefreshDatabase (+12 more)

### Community 1 - "Controller"
Cohesion: 0.07
Nodes (19): AuthenticatedSessionController, ConfirmablePasswordController, EmailVerificationNotificationController, EmailVerificationPromptController, NewPasswordController, PasswordController, PasswordResetLinkController, RegisteredUserController (+11 more)

### Community 2 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.05
Nodes (13): FinancePostController, LoginRequest, GenerateBillRequest, StoreFinancePostRequest, StorePaymentRequest, StoreSchoolClassRequest, StoreStudentRequest, UpdateFinancePostRequest (+5 more)

### Community 3 - "Student"
Cohesion: 0.07
Nodes (8): AcademicYearController, AlumniController, StudentController, AcademicYear, HasMany, BelongsToMany, HasMany, Student

### Community 4 - "composer.json"
Cohesion: 0.04
Nodes (44): pestphp/pest-plugin, php-http/discovery, autoload, autoload-dev, psr-4, psr-4, config, allow-plugins (+36 more)

### Community 5 - "Illuminate\Http\Request"
Cohesion: 0.08
Nodes (14): AcademicYearSessionController, BackupController, DashboardController, ReportController, SettingController, WaGatewayController, PermissionMiddleware, RoleMiddleware (+6 more)

### Community 6 - "SchoolClass"
Cohesion: 0.11
Nodes (9): PromotionController, SchoolClassController, SchoolClassesImport, StudentsImport, SchoolClass, Illuminate\Support\Collection, Maatwebsite\Excel\Concerns\ToCollection, Maatwebsite\Excel\Concerns\WithChunkReading (+1 more)

### Community 7 - "Role"
Cohesion: 0.11
Nodes (10): RoleController, Permission, Role, up(), DatabaseSeeder, PermissionSeeder, RoleSeeder, Illuminate\Database\Console\Seeds\WithoutModelEvents (+2 more)

### Community 8 - "devDependencies"
Cohesion: 0.07
Nodes (27): alpinejs, autoprefixer, axios, concurrently, laravel-vite-plugin, devDependencies, alpinejs, autoprefixer (+19 more)

### Community 9 - "Expense"
Cohesion: 0.11
Nodes (6): ExpenseCategoryController, ExpenseController, StoreExpenseCategoryRequest, StoreExpenseRequest, Expense, ExpenseCategory

### Community 10 - "scripts"
Cohesion: 0.08
Nodes (26): scripts, dev, post-autoload-dump, post-create-project-cmd, post-root-package-install, post-update-cmd, pre-package-uninstall, setup (+18 more)

### Community 11 - "lsp-1d3a3f2dc6acff9e.php"
Cohesion: 0.17
Nodes (17): ReflectionParameter, ReflectionProperty, all(), findFiles(), formatProps(), getAliases(), getAnonymous(), getAnonymousNamespaced() (+9 more)

### Community 12 - "SettingController.php"
Cohesion: 0.18
Nodes (4): BillController, Bill, FinancePost, Student

### Community 13 - "Illuminate\Contracts\View\View"
Cohesion: 0.21
Nodes (7): ArrearReportExport, CashReportExport, ExpenseReportExport, PaymentReportExport, Illuminate\Contracts\View\View, Maatwebsite\Excel\Concerns\FromView, Maatwebsite\Excel\Concerns\ShouldAutoSize

### Community 14 - "StudentsExport"
Cohesion: 0.19
Nodes (7): SchoolClassesExport, StudentsExport, Maatwebsite\Excel\Concerns\Exportable, Maatwebsite\Excel\Concerns\FromCollection, Maatwebsite\Excel\Concerns\FromQuery, Maatwebsite\Excel\Concerns\WithHeadings, Maatwebsite\Excel\Concerns\WithMapping

### Community 15 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.17
Nodes (4): Bill, Payment, PaymentDetail, Illuminate\Database\Eloquent\Relations\BelongsTo

### Community 16 - "SchoolSetting.php"
Cohesion: 0.22
Nodes (6): AutoBackupCommand, SendDueRemindersCommand, SyncTimeCommand, SchoolSetting, Command, Illuminate\Console\Command

### Community 17 - "Auditable.php"
Cohesion: 0.25
Nodes (4): AuditTrailController, AuditTrail, audit(), bootAuditable()

### Community 19 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.28
Nodes (3): BillDetail, StudentClass, Illuminate\Database\Eloquent\Model

### Community 23 - "profile/edit.blade.php"
Cohesion: 0.50
Nodes (3): profile.partials.delete-user-form, profile.partials.update-password-form, profile.partials.update-profile-information-form

## Knowledge Gaps
- **75 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+70 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **12 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `Controller`, `Auditable.php`, `Role`?**
  _High betweenness centrality (0.090) - this node is a cross-community bridge._
- **Why does `Student` connect `Student` to `Auditable.php`, `Illuminate\Database\Eloquent\Model`, `StudentsExport`, `SchoolClass`?**
  _High betweenness centrality (0.036) - this node is a cross-community bridge._
- **Why does `Controller` connect `Controller` to `User`, `Illuminate\Foundation\Http\FormRequest`, `Student`, `Illuminate\Http\Request`, `SchoolClass`, `Role`, `Expense`, `Auditable.php`?**
  _High betweenness centrality (0.028) - this node is a cross-community bridge._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _75 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `User` be split into smaller, more focused modules?**
  _Cohesion score 0.05328218243819267 - nodes in this community are weakly interconnected._
- **Should `Controller` be split into smaller, more focused modules?**
  _Cohesion score 0.06957047791893527 - nodes in this community are weakly interconnected._
- **Should `Illuminate\Foundation\Http\FormRequest` be split into smaller, more focused modules?**
  _Cohesion score 0.05279034690799397 - nodes in this community are weakly interconnected._