# Module: equipments

## Recent Work

### init_relation_options() Permission Fix (2026-05-24)

**File:** `helpers/equipment_relation_helpers.php`

**Change:** Updated `equipments_init_relation_options()` to check both 'view' AND 'view_own' permissions.

**Pattern:** 
```php
if (!staff_can('view', 'equipments') && !staff_can('view_own', 'equipments')) {
    return [];
}
```

**Logic:** Deny access only when user has **neither** view nor view_own capability. Allow if user has either one (or both).

**Reason:** Previously only checked `has_permission(..., 'view')`, blocking staff with only `view_own` permission. This prevented them from accessing ajax-search relation selects.

**Commit:** acb3773

**Related:** /public_html/module_helper_audit.md (documentation for all 12 modules)

---

## Notes
- This fix was applied consistently across 12 modules
- Two modules were NOT changed: rfqs (already correct) and billing_payments (intentionally no guard)
- See architecture_permission_system.md in parent project memory for complete permission system documentation
