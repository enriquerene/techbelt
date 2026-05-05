# Feature: Admin Direct User Creation

## Overview

Currently, the system only allows new users to be created via the **invitation flow**: an admin creates an invite, and the invited person accepts it by setting a password. This feature enables admins to **create users directly** in the Filament admin panel without requiring the invitation process.

## Motivation

- Admins need to onboard users (students, staff, or other admins) who are physically present or already known to the organization.
- The invitation flow is useful for self-service onboarding, but direct creation is more efficient for admin-managed enrollment.
- The existing [`StaffResource`](app/Filament/Resources/StaffResource.php:68) already has a password field for direct creation — this pattern should be extended to all user types.

## Current Architecture

### User Model (`App\Models\User`)

- Uses a **single `users` table** with a JSON `role` column supporting multiple roles: `student`, `staff`, `admin`.
- Sub-types: [`Student`](app/Models/Student.php) and [`Instructor`](app/Models/Instructor.php) extend `User` with global scopes for filtering.
- Roles are stored as JSON arrays (e.g., `["student"]`, `["staff"]`, `["admin"]`).

### Existing Resources

| Resource | Model | Create Page | Password Field | Notes |
|----------|-------|-------------|----------------|-------|
| [`StudentResource`](app/Filament/Resources/StudentResource.php) | `Student` (extends `User`) | **Disabled** (commented out in `getPages()`) | ❌ No | Comment says "Students should be created via invites, not directly" |
| [`StaffResource`](app/Filament/Resources/StaffResource.php) | `Instructor` (extends `User`) | ✅ Enabled | ✅ Yes | Has password field on create |
| [`InviteResource`](app/Filament/Resources/InviteResource.php) | `Invite` | ✅ Enabled | N/A | Creates invites, not users |

### Invitation Flow

1. Admin creates an [`Invite`](app/Models/Invite.php) with `name`, `phone`, `role`, optional `expires_at`.
2. Invite generates a unique `token`.
3. Invited person visits `/invite/{token}` and sets their password.
4. [`InviteController::accept()`](app/Http/Controllers/InviteController.php:26) creates the `User` and marks invite as used.

## Proposed Changes

### 1. Enable Direct User Creation in `StudentResource`

**File:** [`app/Filament/Resources/StudentResource.php`](app/Filament/Resources/StudentResource.php)

- **Uncomment/re-enable the `create` page** in `getPages()`.
- **Add a password field** to the form, required on create, optional on edit (same pattern as [`StaffResource`](app/Filament/Resources/StaffResource.php:68)).
- **Add a `CreateStudent` page** (already exists at [`app/Filament/Resources/StudentResource/Pages/CreateStudent.php`](app/Filament/Resources/StudentResource/Pages/CreateStudent.php)) — ensure it properly handles password hashing.

### 2. Add Password Field to `StudentResource` Form

The form should include a password field with the same behavior as `StaffResource`:

```php
Forms\Components\TextInput::make('password')
    ->password()
    ->required(fn (string $context): bool => $context === 'create')
    ->minLength(8)
    ->dehydrated(fn ($state) => filled($state))
    ->revealable()
    ->label('Senha'),
```

### 3. Ensure Proper Password Hashing

The `User` model has `'password' => 'hashed'` in its `$casts` array, so Laravel will automatically hash the password when saving. No additional hashing logic is needed.

### 4. Update `StudentResource::getPages()`

```php
public static function getPages(): array
{
    return [
        'index' => Pages\ListStudents::route('/'),
        'create' => Pages\CreateStudent::route('/create'),
        'edit' => Pages\EditStudent::route('/{record}/edit'),
    ];
}
```

### 5. Update Tests

**File:** [`tests/Feature/Filament/AdminCapabilitiesTest.php`](tests/Feature/Filament/AdminCapabilitiesTest.php)

- Update the test `student resource creation follows business rules` to reflect that admin CAN now create students directly.
- Add a new test: `admin can access student create page`.
- Add a new test: `admin can create student with password via filament`.

**File:** [`tests/Feature/Filament/StudentResourceTest.php`](tests/Feature/Filament/StudentResourceTest.php)

- Add test: `admin can create a student via the create page`.
- Add test: `student create page requires password`.
- Add test: `student create page shows password field`.

### 6. Update README

Document the new capability in the admin panel section.

## Business Rules

1. **Only admins** can create users directly (enforced by [`CheckPanelRole`](app/Http/Middleware/CheckPanelRole.php) middleware on the admin panel).
2. **Password is required** when creating a new user, optional when editing.
3. **Email is optional** — the system uses phone as the primary identifier (see migration [`make_email_nullable_and_phone_required_in_users_table`](database/migrations/2026_02_09_215107_make_email_nullable_and_phone_required_in_users_table.php)).
4. **Role selection** is available only to admins (already implemented via `visible(fn (): bool => auth()->user()->isAdmin())`).
5. **Email verification** is auto-set to verified when admin creates the user directly (already implemented via toggle).

## Implementation Order

1. ✅ Documentation (this file)
2. Write/update tests
3. Modify `StudentResource` form to add password field
4. Enable `create` page in `StudentResource::getPages()`
5. Update README

## Files to Modify

| File | Change |
|------|--------|
| [`app/Filament/Resources/StudentResource.php`](app/Filament/Resources/StudentResource.php) | Add password field to form; enable create page |
| [`tests/Feature/Filament/AdminCapabilitiesTest.php`](tests/Feature/Filament/AdminCapabilitiesTest.php) | Update business rules test; add new tests |
| [`tests/Feature/Filament/StudentResourceTest.php`](tests/Feature/Filament/StudentResourceTest.php) | Add tests for direct student creation |
| [`README.md`](README.md) | Document new feature |

## Files NOT to Modify

- [`InviteResource`](app/Filament/Resources/InviteResource.php) — invitation flow remains unchanged
- [`InviteController`](app/Http/Controllers/InviteController.php) — no changes needed
- [`StaffResource`](app/Filament/Resources/StaffResource.php) — already supports direct creation
- [`User`](app/Models/User.php) — no changes needed (password casting already handles hashing)
- [`CreateStudent`](app/Filament/Resources/StudentResource/Pages/CreateStudent.php) — already exists, no changes needed
- Database migrations — no schema changes needed
