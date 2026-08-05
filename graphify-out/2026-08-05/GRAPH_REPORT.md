# Graph Report - .  (2026-08-05)

## Corpus Check
- cluster-only mode — file stats not available

## Summary
- 772 nodes · 1190 edges · 155 communities (139 shown, 16 thin omitted)
- Extraction: 98% EXTRACTED · 2% INFERRED · 0% AMBIGUOUS · INFERRED: 29 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `ea96a19e`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- Illuminate\Database\Eloquent\Model
- User
- Controller
- Illuminate\Http\Request
- composer.json
- Student
- Role
- devDependencies
- scripts
- SchoolSetting
- ReportController.php
- StudentsExport
- ExpenseCategory
- FinancePost
- StudentsImport.php
- StoreStudentRequest
- Illuminate\Foundation\Http\FormRequest
- LoginRequest
- AppServiceProvider
- BillController
- StoreSchoolClassRequest
- UpdateSchoolClassRequest
- UpdateFinancePostRequest
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
2. `Student` - 41 edges
3. `Controller` - 40 edges
4. `SchoolClass` - 34 edges
5. `AcademicYear` - 28 edges
6. `Expense` - 20 edges
7. `Payment` - 19 edges
8. `TestCase` - 18 edges
9. `Role` - 17 edges
10. `Bill` - 16 edges

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

## Communities (155 total, 16 thin omitted)

### Community 0 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.06
Nodes (16): AuditTrailController, ExpenseController, PaymentController, StoreExpenseRequest, AuditTrail, Bill, BillDetail, Expense (+8 more)

### Community 1 - "User"
Cohesion: 0.05
Nodes (20): UserController, BelongsToMany, User, UserFactory, Illuminate\Database\Eloquent\Factories\Factory, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Foundation\Auth\User, Illuminate\Foundation\Testing\RefreshDatabase (+12 more)

### Community 2 - "Controller"
Cohesion: 0.07
Nodes (19): AuthenticatedSessionController, ConfirmablePasswordController, EmailVerificationNotificationController, EmailVerificationPromptController, NewPasswordController, PasswordController, PasswordResetLinkController, RegisteredUserController (+11 more)

### Community 3 - "Illuminate\Http\Request"
Cohesion: 0.08
Nodes (12): AcademicYearSessionController, PromotionController, ReportController, SchoolClassController, StudentController, PermissionMiddleware, RoleMiddleware, SecurityHeaders (+4 more)

### Community 4 - "composer.json"
Cohesion: 0.04
Nodes (44): pestphp/pest-plugin, php-http/discovery, autoload, autoload-dev, psr-4, psr-4, config, allow-plugins (+36 more)

### Community 5 - "Student"
Cohesion: 0.07
Nodes (8): AcademicYearController, AlumniController, DashboardController, WaGatewayController, AcademicYear, HasMany, HasMany, Student

### Community 6 - "Role"
Cohesion: 0.11
Nodes (10): RoleController, Permission, Role, up(), DatabaseSeeder, PermissionSeeder, RoleSeeder, Illuminate\Database\Console\Seeds\WithoutModelEvents (+2 more)

### Community 7 - "devDependencies"
Cohesion: 0.07
Nodes (27): alpinejs, autoprefixer, axios, concurrently, laravel-vite-plugin, devDependencies, alpinejs, autoprefixer (+19 more)

### Community 8 - "scripts"
Cohesion: 0.08
Nodes (26): scripts, dev, post-autoload-dump, post-create-project-cmd, post-root-package-install, post-update-cmd, pre-package-uninstall, setup (+18 more)

### Community 9 - "SchoolSetting"
Cohesion: 0.13
Nodes (8): AutoBackupCommand, SendDueRemindersCommand, SyncTimeCommand, BackupController, SettingController, SchoolSetting, Command, Illuminate\Console\Command

### Community 10 - "ReportController.php"
Cohesion: 0.21
Nodes (7): ArrearReportExport, CashReportExport, ExpenseReportExport, PaymentReportExport, Illuminate\Contracts\View\View, Maatwebsite\Excel\Concerns\FromView, Maatwebsite\Excel\Concerns\ShouldAutoSize

### Community 11 - "StudentsExport"
Cohesion: 0.19
Nodes (7): SchoolClassesExport, StudentsExport, Maatwebsite\Excel\Concerns\Exportable, Maatwebsite\Excel\Concerns\FromCollection, Maatwebsite\Excel\Concerns\FromQuery, Maatwebsite\Excel\Concerns\WithHeadings, Maatwebsite\Excel\Concerns\WithMapping

### Community 12 - "ExpenseCategory"
Cohesion: 0.22
Nodes (3): ExpenseCategoryController, StoreExpenseCategoryRequest, ExpenseCategory

### Community 13 - "FinancePost"
Cohesion: 0.23
Nodes (3): FinancePostController, StoreFinancePostRequest, FinancePost

### Community 14 - "StudentsImport.php"
Cohesion: 0.30
Nodes (6): SchoolClassesImport, StudentsImport, Illuminate\Support\Collection, Maatwebsite\Excel\Concerns\ToCollection, Maatwebsite\Excel\Concerns\WithChunkReading, Maatwebsite\Excel\Concerns\WithHeadingRow

### Community 16 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.28
Nodes (3): GenerateBillRequest, StorePaymentRequest, Illuminate\Foundation\Http\FormRequest

### Community 24 - "profile/edit.blade.php"
Cohesion: 0.50
Nodes (3): profile.partials.delete-user-form, profile.partials.update-password-form, profile.partials.update-profile-information-form

## Knowledge Gaps
- **75 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+70 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **16 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `Illuminate\Database\Eloquent\Model`, `Controller`, `Role`?**
  _High betweenness centrality (0.096) - this node is a cross-community bridge._
- **Why does `Controller` connect `Controller` to `Illuminate\Database\Eloquent\Model`, `User`, `Illuminate\Http\Request`, `Student`, `Role`, `SchoolSetting`, `ExpenseCategory`, `FinancePost`, `BillController`?**
  _High betweenness centrality (0.047) - this node is a cross-community bridge._
- **Why does `SchoolClass` connect `Illuminate\Http\Request` to `Illuminate\Database\Eloquent\Model`, `Student`, `ReportController.php`, `StudentsExport`, `StudentsImport.php`, `BillController`?**
  _High betweenness centrality (0.038) - this node is a cross-community bridge._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _75 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `Illuminate\Database\Eloquent\Model` be split into smaller, more focused modules?**
  _Cohesion score 0.060041407867494824 - nodes in this community are weakly interconnected._
- **Should `User` be split into smaller, more focused modules?**
  _Cohesion score 0.05443371378402107 - nodes in this community are weakly interconnected._
- **Should `Controller` be split into smaller, more focused modules?**
  _Cohesion score 0.07080200501253132 - nodes in this community are weakly interconnected._