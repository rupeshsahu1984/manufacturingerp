# Playwright E2E test plan — PRODX / Manufacturing ERP (CodeIgniter 4)

Use this document to author `*.spec.ts` files. Adjust `baseURL` and credentials for your environment.

---

## 1. Environment

| Item | Typical value |
|------|----------------|
| **baseURL** | `http://localhost/manufacturingerp` (root `.htaccess` forwards to `public/index.php`) |
| **Auth** | Session-based; unauthenticated users are redirected to `/login` |
| **Login fields** | `name="username"`, `name="password"` (POST to same `/login`) |
| **CSRF** | Globally **off** in `app/Config/Filters.php` today — if you enable `csrf`, every POST must include the hidden `csrf_test_name` field from the form |
| **Test user** | Use a real DB user (e.g. from your seed); do **not** commit passwords |

### Suggested `playwright.config.ts`

```ts
import { defineConfig } from '@playwright/test';

export default defineConfig({
  use: {
    baseURL: 'http://localhost/manufacturingerp',
    trace: 'on-first-retry',
  },
});
```

---

## 2. Global setup — authenticated session

**Goal:** One login, reuse storage state for all module specs.

**Steps:**

1. `page.goto('/login')`
2. `page.locator('input[name="username"]').fill(process.env.E2E_USER!)`
3. `page.locator('input[name="password"]').fill(process.env.E2E_PASS!)`
4. Submit: `page.locator('form').getByRole('button', { name: /sign in|login/i }).click()`  
   — or `page.locator('button[type="submit"]').click()` if no accessible name
5. Expect redirect: `await expect(page).toHaveURL(/dashboard/)` (or URL containing `dashboard`)

**Playwright pattern:**

```ts
// global-setup.ts (pseudo)
await page.goto('/login');
await page.fill('input[name="username"]', user);
await page.fill('input[name="password"]', pass);
await page.click('button[type="submit"]');
await page.context().storageState({ path: 'storageState.json' });
```

Point tests at `storageState: 'storageState.json'` or use a `test.beforeEach` that performs login.

**Logout (optional check):**

- `page.goto('/logout')` → should land on login or home with session cleared.

---

## 3. Shared assertions (smoke per page)

For each **GET** screen under test:

- `await expect(page).not.toHaveURL(/login/)` (unless testing guest flow)
- `await expect(response).toBeOK()` if using `page.goto` with `waitUntil: 'networkidle'` and capturing response
- Prefer: `const r = await page.goto(path); expect(r?.status()).toBeLessThan(400)` — treat **302 to login** as failure for “logged-in” tests
- No JSON body with `PageNotFoundException` (CI4 debug) — if present, assert failure
- Optional: `await expect(page.locator('body')).not.toContainText('Whoops')`

**Selector strategy:** The app uses Bootstrap + `layouts/main`. Prefer:

- `getByRole('heading', { level: 1 })` or visible page title text where stable
- `locator('.header h1')` if consistent
- Form fields: `name="..."` from each view

Long term, add `data-testid` on critical buttons for stability.

---

## 4. Modules and URLs to cover

Below, paths are relative to **baseURL** (no trailing slash on base).

### 4.0 Sidebar navigation — authoritative checklist

The live menu is defined in [`app/Views/partials/sidebar.php`](app/Views/partials/sidebar.php). **Playwright UI tests should iterate these paths** (after expanding each accordion) so coverage matches what users see. Earlier sections group routes by *controller*; this table matches the *sidebar*.

| Sidebar section | Menu label | Path (as in sidebar) | Route / note |
|-----------------|------------|----------------------|--------------|
| Dashboard | Dashboard | `/dashboard` | OK |
| Master settings | Supplier Master | `/supplier` | OK |
| | Customer Master | `/customer` | OK |
| | Material Master | `/product` | OK |
| | Category Master | `/category` | OK |
| | Production Settings | `/production-settings` | OK |
| | BOM Management | `/bom` | `SimpleModuleController` |
| | Warehouse Master | `/warehouse` | OK |
| | Department Master | `/department` | OK |
| | Employee Master | `/employee` | OK |
| Purchase | Purchase Requisition | `/purchase-requisition` | OK |
| | Purchase Order | `/purchase-order` | OK |
| | Purchase Bills | `/purchase-bill` | OK |
| | Purchase Returns | `/purchase-return` | OK |
| | Vendor Payments | `/vendor-payment` | OK |
| Sales | Sales Orders | `/sales-orders` | OK (also `/sales-order` legacy in §4.7) |
| | Sales Invoices | `/sales-invoice` | OK |
| | Sales Returns | `/sales-return` | OK |
| | Customer Payments | `/customer-payment` | OK |
| | Quotations | `/quotation` | OK |
| Production | Work Orders | `/work-orders` | **Mismatch:** only registered under `/production/work-orders` today — sidebar link may **404** until a root alias is added |
| | Production Planning | `/production-planning` | OK |
| | Production Tracking | `/production-tracking` | OK |
| | Quality Control | `/quality-control` | OK |
| | Maintenance | `/maintenance` | OK |
| Inventory | Stock Management | `/stock` | OK |
| | Stock Transfer | `/stock-transfer` | OK |
| | Stock Adjustment | `/stock-adjustment` | OK |
| | Stock Count | `/stock-count` | OK |
| | Low Stock Alerts | `/low-stock-alerts` | OK |
| Human resources | Attendance | `/attendance` | OK |
| | Leave Management | `/leave-management` | OK |
| | Salary Management | `/salary` | **Mismatch:** routes use `/salary-management` or `/hr/salary` — `/salary` may **404** |
| | Payroll | `/payroll` | OK |
| | Documents | `/documents` | OK |
| | Training | `/training` | OK |
| Accounting | General Ledger | `/general-ledger` | **No root route** — use `/accounting/ledger` |
| | Accounts Payable | `/accounts-payable` | **No root route** — use `/accounting/payables` or `/accounting/ap` |
| | Accounts Receivable | `/accounts-receivable` | **No root route** — use `/accounting/receivables` or `/accounting/ar` |
| | Bank Reconciliation | `/bank-reconciliation` | **No root route** — use `/accounting/bank-reconciliation` |
| | Journal Entries | `/journal-entries` | **No root route** — use `/accounting/journal` |
| | Financial Reports | `/financial-reports` | **No root route** — use `/accounting/reports` |
| Gate | Gate Entry | `/gate-entry` | OK |
| | Gate Exit | `/gate-exit` | OK |
| | Visitor Management | `/visitor-management` | OK |
| Reports | Sales Reports | `/sales-reports` | Sidebar path not registered — working target: `/sales/reports` (or `/sales-report` placeholder in §4.12) |
| | Purchase Reports | `/purchase-reports` | Sidebar path not registered — working target: `/purchase/reports` (or `/purchase-report`) |
| | Production Reports | `/production-reports` | Sidebar path not registered — working target: `/production/reports` |
| | Inventory Reports | `/inventory-reports` | Sidebar path not registered — working target: `/inventory/reports` |
| | HR Reports | `/hr-reports` | Sidebar path not registered — working target: `/hr/reports` |
| | Financial Reports | `/financial-reports` | Duplicate menu item; same as Accounting row |
| | Custom Reports | `/custom-reports` | **No matching route** |
| Company settings | *(visible if `user_role === superadmin`)* | `/company-profile`, `/logo-settings`, `/department-assignment`, `/user-management`, `/role-permissions`, `/system-settings` | See §4.13; may 500 if DB/role issues |
| Help | Help Center | `/help` | OK |
| | Support | `/support` | OK |
| Account | Logout | `/logout` | OK |

**Master settings — full coverage:** §4.8 lists every sidebar master with index/create, parameterized routes, exports, toggles, and alternate hubs (`/purchase/suppliers`, `/production/boms`, `/inventory/warehouses`, HR paths).

**Production / purchase / sales / reports — full coverage:** §4.3–§4.7 (operations + standalone controllers); **§4.15** consolidates every **report** URL.

**HR deep links** (`/hr/employees`, `/hr/departments`, analytics, etc.) are **not** on the sidebar today; they remain in §4.9 for API-style or bookmark tests.

**What’s New** block uses `href="#"` — no URL to test.

---

### 4.1 Core & diagnostics

| Test ID | Path | Notes |
|---------|------|--------|
| `core-home` | `/` | Redirects to dashboard when logged in |
| `core-test` | `/test` | Closure route; plain text “Routing is working!” |
| `core-dashboard` | `/dashboard` | Main dashboard |

**Playwright smoke:** `goto` each; for `/test` assert body text.

---

### 4.2 Inventory

| Test ID | Path |
|---------|------|
| `inv-root` | `/inventory` |
| `inv-warehouses` | `/inventory/warehouses` |
| `inv-warehouses-create` | `/inventory/warehouses/create` |
| `inv-items` | `/inventory/items` |
| `inv-stock` | `/inventory/stock` |
| `inv-stock-in` | `/inventory/stock/stock-in` |
| `inv-stock-out` | `/inventory/stock/stock-out` |
| `inv-transfers` | `/inventory/transfers` |
| `inv-adjustments` | `/inventory/adjustments` |
| `inv-reports` | `/inventory/reports` |
| `stock-alias` | `/stock` |
| `stock-transfer-alias` | `/stock-transfer` |
| `stock-adjustment-alias` | `/stock-adjustment` |
| `stock-count` | `/stock-count` |
| `low-stock` | `/low-stock-alerts` |

**Parameterized IDs:** `/inventory/warehouses/edit/:id`, `/inventory/items/view/:id`, etc. — seed a known ID from DB or skip until data exists.

**Known issues (last smoke):** some nested inventory URLs returned **500** — mark as `test.fixme` or fix app first.

---

### 4.3 Production — full route map (`ProductionController` + sidebar)

**Group prefix:** paths below are under **`/production/...`**.

| Area | Method | Path |
|------|--------|------|
| Dashboard | GET | `/production` |
| BOMs | GET | `/production/boms`, `/production/boms/create`, `/production/boms/view/:id`, `/production/boms/edit/:id`, `/production/boms/explode/:id` |
| BOMs | POST | `/production/boms/store`, `/production/boms/update/:id`, `/production/boms/approve/:id` |
| Work orders | GET | `/production/work-orders`, `/production/work-orders/create`, `/production/work-orders/view/:id` |
| Work orders | POST | `/production/work-orders/store`, `/production/work-orders/start/:id`, `/production/work-orders/complete/:id` |
| Job cards | GET | `/production/job-cards`, `/production/job-cards/view/:id` |
| Job cards | POST | `/production/job-cards/start/:id`, `/production/job-cards/complete/:id` |
| MRP | GET/POST | `/production/mrp`, POST `/production/mrp/run` |
| Reports | GET | `/production/reports`, `/production/reports/export/:any` |

**Sidebar PRODUCTION** also uses **`SimpleModuleController`** CRUD on **`/production-planning`**, **`/production-tracking`**, **`/quality-control`** (index, create, POST store, edit/:id, POST update/:id, delete/:id).

**Related:** **`/production-settings`** — §4.8; **`/bom`** — §4.8; **`/manufacturing`** (`ManufacturingController`): GET index/create, POST store, GET show/:id, start/:id, complete/:id.

**Known issues:** several `/production/*` screens were **500** in smoke — `test.fixme` until stable. Root **`/work-orders`** may **404**; use **`/production/work-orders`** (§4.0).

---

### 4.4 Purchase — hub (`PurchaseManagementController`, prefix `/purchase/...`)

| Area | Method | Paths (all prefixed with `/purchase`) |
|------|--------|----------------------------------------|
| Dashboard | GET | `/purchase` |
| Suppliers | GET | `/suppliers`, `/suppliers/create`, `/suppliers/edit/:id`, `/suppliers/performance/:id` |
| Suppliers | POST | `/suppliers/store`, `/suppliers/update/:id`, `/suppliers/activate/:id`, `/suppliers/deactivate/:id` |
| Requisitions | GET | `/requisitions`, `/requisitions/create`, `/requisitions/view/:id`, `/requisitions/edit/:id`, `/requisitions/print/:id` |
| Requisitions | POST | `/requisitions/store`, `/requisitions/update/:id`, `/requisitions/approve/:id`, `/requisitions/reject/:id` |
| Orders | GET | `/orders`, `/orders/create`, `/orders/view/:id`, `/orders/edit/:id`, `/orders/print/:id` |
| Orders | POST | `/orders/store`, `/orders/update/:id`, `/orders/approve/:id` |
| GRN | GET | `/grn`, `/grn/create`, `/grn/view/:id`, `/grn/edit/:id`, `/grn/print/:id` |
| GRN | POST | `/grn/store`, `/grn/update/:id` |
| Invoices | GET | `/invoices`, `/invoices/create`, `/invoices/view/:id`, `/invoices/edit/:id`, `/invoices/print/:id` |
| Invoices | POST | `/invoices/store`, `/invoices/update/:id` |
| Debit notes | GET | `/debit-notes`, `/debit-notes/create`, `/debit-notes/view/:id`, `/debit-notes/edit/:id`, `/debit-notes/print/:id` |
| Debit notes | POST | `/debit-notes/store`, `/debit-notes/update/:id`, `/debit-notes/approve/:id`, `/debit-notes/reject/:id`, `/debit-notes/process/:id` |
| Reports | GET | `/reports`, `/reports/export/:any` |

*(In the second column, prepend `/purchase` to each path, e.g. `/purchase/suppliers`.)*

**Sidebar supplier master:** `/supplier` — §4.8.

---

### 4.5 Purchase — standalone controllers (sidebar + parallel to hub)

#### `PurchaseRequisitionController` — `/purchase-requisition`

| Method | Path |
|--------|------|
| GET | `/purchase-requisition`, `/create`, `/edit/:id`, `/delete/:id` |
| POST | `/store`, `/update/:id` |

#### `PurchaseOrderController` — `/purchase-order`

| Method | Path |
|--------|------|
| GET | `/purchase-order`, `/create`, `/show/:id`, `/edit/:id`, `/approve/:id`, `/order/:id`, `/receive/:id`, `/cancel/:id`, `/print/:id`, `/export` |
| POST | `/store`, `/update/:id` |
| DELETE | `/delete/:id` |

#### `PurchaseReturnController` — `/purchase-return`

| Method | Path |
|--------|------|
| GET | index, `/create`, `/show/:id`, `/edit/:id`, `/approve/:id`, `/process/:id`, `/complete/:id`, `/cancel/:id`, `/print/:id`, `/export`, `/get-po-items/:id`, `/get-products` |
| POST | `/store`, `/update/:id` |
| DELETE | `/delete/:id` |

#### `PurchaseBillController` — `/purchase-bill`

| Method | Path |
|--------|------|
| GET | index, `/create`, `/show/:id`, `/edit/:id`, `/delete/:id`, `/overdue`, `/print/:id`, `/download/:id`, `/export`, `/get-products`, `/get-purchase-orders` |
| POST | `/store`, `/update/:id`, `/update-status/:id`, `/record-payment/:id` |

#### `GoodsReceiptController` — `/goods-receipt`

| Method | Path |
|--------|------|
| GET | index, `/create`, `/show/:id`, `/edit/:id`, `/delete/:id`, `/approve/:id`, `/print/:id`, `/export`, `/get-po-items/:id` |
| POST | `/store`, `/update/:id` |

#### `SupplierInvoiceController` — `/supplier-invoice`

| Method | Path |
|--------|------|
| GET | index, `/create`, `/show/:id`, `/edit/:id`, `/delete/:id`, `/approve/:id`, `/print/:id`, `/export`, `/overdue` |
| POST | `/store`, `/update/:id`, `/record-payment/:id` |

#### `DebitNoteController` — `/debit-note`

| Method | Path |
|--------|------|
| GET | index, `/create`, `/show/:id`, `/edit/:id`, `/delete/:id`, `/approve/:id`, `/print/:id`, `/export` |
| POST | `/store`, `/update/:id` |

#### `PurchaseReportController` — `/purchase-report`

| Method | Path |
|--------|------|
| GET | `/purchase-report`, `/purchase-report/pending-orders`, `/purchase-report/supplier-history`, `/purchase-report/price-trends`, `/purchase-report/quality-metrics`, `/purchase-report/cost-analysis`, `/purchase-report/export` |

#### `VendorPaymentController` — `/vendor-payment` (sidebar)

| Method | Path |
|--------|------|
| GET | `/vendor-payment`, `/vendor-payment/create`, `/vendor-payment/show/:id`, `/vendor-payment/edit/:id`, `/vendor-payment/delete/:id`, `/vendor-payment/export` |
| POST | `/vendor-payment/store`, `/vendor-payment/update/:id` |

*(Other standalone tables above: paths after the first cell are relative to that controller’s base URI, e.g. `/purchase-bill` + `/create` → `/purchase-bill/create`.)*

**Supplier master:** `/supplier` — §4.8.

---

### 4.6 Sales & distribution — grouped (`SalesDistributionController`, `/sales/...`)

**Dashboard:** GET `/sales`.

| Submodule | GET paths | POST paths (same base) |
|-----------|-----------|-------------------------|
| Customers | `/sales/customers`, `/create`, `/view/:id`, `/edit/:id`, `/export` | `/store`, `/update/:id`, `/toggle-status/:id` |
| Leads | `/sales/leads`, `/create`, `/view/:id`, `/edit/:id`, `/export` | `/store`, `/update/:id`, `/update-status/:id`, `/assign/:id`, `/convert/:id` |
| Quotations | `/sales/quotations`, `/create`, `/view/:id`, `/edit/:id`, `/convert/:id`, `/print/:id`, `/export` | `/store`, `/update/:id`, `/update-status/:id`, `/convert/:id` |
| Orders | `/sales/orders`, `/create`, `/view/:id`, `/edit/:id`, `/print/:id`, `/export` | `/store`, `/update/:id`, `/update-status/:id` |
| Dispatch | `/sales/dispatch`, `/create`, `/view/:id`, `/edit/:id`, `/print/:id`, `/export` | `/store`, `/update/:id`, `/update-status/:id` |
| Invoices | `/sales/invoices`, `/create`, `/view/:id`, `/edit/:id`, `/print/:id`, `/export` | `/store`, `/update/:id`, `/update-status/:id` |
| Returns | `/sales/returns`, `/create`, `/view/:id`, `/edit/:id`, `/print/:id`, `/export` | `/store`, `/update/:id`, `/update-status/:id` |
| Payments | `/sales/payments`, `/create`, `/view/:id`, `/edit/:id`, `/print/:id`, `/export` | `/store`, `/update/:id`, `/update-status/:id` |
| Distributors | `/sales/distributors`, `/create`, `/view/:id`, `/edit/:id`, `/export` | `/store`, `/update/:id`, `/toggle-status/:id` |
| Reports | `/sales/reports`, `/customer`, `/product`, `/region`, `/trends`, `/export` | — |

*(In the GET column, paths after the first entry are relative to the submodule base, e.g. `/sales/customers` + `/create` → `/sales/customers/create`.)*

**AJAX:** GET `/sales/api/customers`, `/sales/api/products`, `/sales/api/quotations`, `/sales/api/orders`, `/sales/api/dispatches`, `/sales/api/invoices`, `/sales/api/returns`, `/sales/api/payments`, `/sales/api/distributors`.

**Known issues:** Many grouped actions were **404** when methods were stubs — `test.fixme` or use §4.7 standalone URLs for smoke.

---

### 4.7 Sales — standalone controllers (sidebar + aliases)

**Customer master:** `/customer` — §4.8.

#### `SalesOrderController` — `/sales-orders` and `/sales-order`

Registered twice: use **`/sales-orders/...`** or **`/sales-order/...`** with the same actions: index, create, show/:id, edit/:id, delete/:id, export, print/:id, get-products, get-customers; POST store, update/:id, update-status/:id. Extra on singular base: **`/sales-order/get-finished-goods-dropdown`**.

#### `QuotationController` — `/quotation`

GET index, create, show/:id, edit/:id, delete/:id, export, print/:id, get-products, get-customers, convert-to-order/:id; POST store, update/:id, update-status/:id.

#### `InvoiceController` — `/invoice`, mirror **`/sales-invoice/...`**, and **`/finance/...`** (same controller)

GET index, create, show/:id, edit/:id, delete/:id, export, print/:id, get-sales-order-items/:id, get-products, get-customers; POST store, update/:id, update-status/:id, record-payment/:id.

#### `DispatchController` — `/dispatch`

GET index, create, show/:id, edit/:id, delete/:id, export, print/:id, get-sales-orders; POST store, update/:id, update-status/:id.

#### `SalesReturnController` — `/sales-return`

GET index, create, show/:id, edit/:id, delete/:id, export, print/:id, get-invoices, get-products, get-customers; POST store, update/:id, update-status/:id.

#### `CustomerPaymentController` — `/customer-payment`

GET index, create, show/:id, edit/:id, delete/:id, export, print/:id, get-invoices, get-customers; POST store, update/:id.

---

### 4.8 Master settings (sidebar — full route map)

These nine items match **MASTER SETTINGS** in [`app/Views/partials/sidebar.php`](app/Views/partials/sidebar.php). Paths are relative to **baseURL**. Controllers are the primary ones in `app/Config/Routes.php` unless noted.

**Smoke (GET):** hit `/…` (index) and `/…/create` for each entity below. **Deep tests:** parameterized `show|edit|delete/:num` — use a seeded ID or skip.

#### Supplier Master — `SupplierController`

| Test ID | Method | Path |
|---------|--------|------|
| `mat-supplier` | GET | `/supplier` |
| `mat-supplier-create` | GET | `/supplier/create` |
| `mat-supplier-store` | POST | `/supplier/store` |
| `mat-supplier-show` | GET | `/supplier/show/:id` |
| `mat-supplier-edit` | GET | `/supplier/edit/:id` |
| `mat-supplier-update` | POST | `/supplier/update/:id` |
| `mat-supplier-delete` | GET | `/supplier/delete/:id` |
| `mat-supplier-toggle` | POST | `/supplier/toggle-status/:id` |
| `mat-supplier-export` | GET | `/supplier/export` |
| `mat-supplier-print` | GET | `/supplier/print/:id` |
| `mat-supplier-by-cat` | GET | `/supplier/get-by-category` |
| `mat-supplier-outstanding` | GET | `/supplier/outstanding-payments` |

**Alternate hub (not the sidebar link):** `/purchase/suppliers`, `/purchase/suppliers/create`, … — `PurchaseManagementController`.

**Legacy placeholder:** `/supplier-master` — `SimpleModuleController` (generic CRUD).

#### Customer Master — `CustomerController`

| Test ID | Method | Path |
|---------|--------|------|
| `mat-customer` | GET | `/customer` |
| `mat-customer-create` | GET | `/customer/create` |
| `mat-customer-store` | POST | `/customer/store` |
| `mat-customer-show` | GET | `/customer/show/:id` |
| `mat-customer-edit` | GET | `/customer/edit/:id` |
| `mat-customer-update` | POST/PUT | `/customer/update/:id` |
| `mat-customer-delete` | GET | `/customer/delete/:id` |
| `mat-customer-toggle` | POST | `/customer/toggle-status/:id` |
| `mat-customer-export` | GET | `/customer/export` |
| `mat-customer-print` | GET | `/customer/print/:id` |
| `mat-customer-search` | GET | `/customer/search` |
| `mat-customer-get` | GET | `/customer/get/:id` |
| `mat-customer-zones` | GET | `/customer/get-sales-zones`, `/customer/get-by-zone` |
| `mat-customer-regions` | GET | `/customer/get-sales-regions`, `/customer/get-by-region` |
| `mat-customer-outstanding` | GET | `/customer/outstanding-payments` |
| `mat-customer-perf` | GET | `/customer/performance-report/:id` |

**Alternate hub:** `/sales/customers`, … — `SalesDistributionController` (may 404 if unimplemented).

#### Material Master — `ProductController` (sidebar label: Material Master)

| Test ID | Method | Path |
|---------|--------|------|
| `mat-product` | GET | `/product` |
| `mat-product-create` | GET | `/product/create` |
| `mat-product-store` | POST | `/product/store` |
| `mat-product-show` | GET | `/product/show/:id` |
| `mat-product-edit` | GET | `/product/edit/:id` |
| `mat-product-update` | POST | `/product/update/:id` |
| `mat-product-delete` | GET | `/product/delete/:id` |
| `mat-product-toggle` | POST | `/product/toggle-status/:id` |
| `mat-product-export` | GET | `/product/export` |
| `mat-product-search` | GET | `/product/search` |
| `mat-product-details` | GET | `/product/details/:id`, `/product/stock/:id`, `/product/performance/:id` |
| `mat-product-by` | GET | `/product/get-by-category`, `/product/get-by-material-type` |
| `mat-product-fg-dropdown` | GET | `/product/finished-goods-dropdown` |

#### Category Master — `CategoryController`

| Test ID | Method | Path |
|---------|--------|------|
| `mat-category` | GET | `/category` |
| `mat-category-create` | GET | `/category/create` |
| `mat-category-store` | POST | `/category/store` |
| `mat-category-show` | GET | `/category/show/:id` |
| `mat-category-edit` | GET | `/category/edit/:id` |
| `mat-category-update` | POST | `/category/update/:id` |
| `mat-category-delete` | GET | `/category/delete/:id` |
| `mat-category-toggle` | POST | `/category/toggle-status/:id` |
| `mat-category-export` | GET | `/category/export` |
| `mat-category-by-type` | GET | `/category/get-by-type` |

#### Production Settings — `ProductionSettingsController`

| Test ID | Method | Path |
|---------|--------|------|
| `mat-prod-settings` | GET | `/production-settings` |
| `mat-prod-settings-create` | GET | `/production-settings/create` |
| `mat-prod-settings-store` | POST | `/production-settings/store` |
| `mat-prod-settings-show` | GET | `/production-settings/show/:id` |
| `mat-prod-settings-edit` | GET | `/production-settings/edit/:id` |
| `mat-prod-settings-update` | POST | `/production-settings/update/:id` |
| `mat-prod-settings-delete` | GET | `/production-settings/delete/:id` |
| `mat-prod-settings-toggle` | POST | `/production-settings/toggle-status/:id` |
| `mat-prod-settings-calc` | POST | `/production-settings/calculate-production/:id` |
| `mat-prod-settings-mat-req` | POST | `/production-settings/get-material-requirements/:id` |
| `mat-prod-settings-avail` | POST | `/production-settings/check-availability/:id` |

#### BOM Management — dual surface

| Surface | Controller | Typical paths |
|---------|------------|----------------|
| **Sidebar link** | `SimpleModuleController` | `/bom`, `/bom/create`, POST `/bom/store`, `/bom/edit/:id`, POST `/bom/update/:id`, `/bom/delete/:id` |
| **Production module** | `ProductionController` | `/production/boms`, `/production/boms/create`, `/production/boms/view/:id`, `/production/boms/edit/:id`, approve/explode routes |

For ERP BOM workflows, prefer **`/production/boms`** in tests once stable; keep **`/bom`** for parity with the menu.

#### Warehouse Master — `WarehouseController`

| Test ID | Method | Path |
|---------|--------|------|
| `mat-warehouse` | GET | `/warehouse` |
| `mat-warehouse-create` | GET | `/warehouse/create` |
| `mat-warehouse-store` | POST | `/warehouse/store` |
| `mat-warehouse-show` | GET | `/warehouse/show/:id` |
| `mat-warehouse-edit` | GET | `/warehouse/edit/:id` |
| `mat-warehouse-update` | POST | `/warehouse/update/:id` |
| `mat-warehouse-delete` | GET | `/warehouse/delete/:id` |
| `mat-warehouse-toggle` | POST | `/warehouse/toggle-status/:id` |
| `mat-warehouse-export` | GET | `/warehouse/export` |
| `mat-warehouse-search` | GET | `/warehouse/search` |
| `mat-warehouse-list` | GET | `/warehouse/get-warehouses` |

**Alternate hub:** `/inventory/warehouses`, … — `InventoryManagementController` (see §4.2).

#### Department Master — `DepartmentController`

| Test ID | Method | Path |
|---------|--------|------|
| `mat-department` | GET | `/department` |
| `mat-department-create` | GET | `/department/create` |
| `mat-department-store` | POST | `/department/store` |
| `mat-department-show` | GET | `/department/show/:id` |
| `mat-department-edit` | GET | `/department/edit/:id` |
| `mat-department-update` | POST | `/department/update/:id` |
| `mat-department-delete` | GET | `/department/delete/:id` |
| `mat-department-toggle` | POST | `/department/toggle-status/:id` |
| `mat-department-export` | GET | `/department/export` |
| `mat-department-search` | GET | `/department/search` |
| `mat-department-list` | GET | `/department/get-departments` |

**HR org chart:** `/hr/departments`, `/hr/department/create`, … — see §4.9.

#### Employee Master — `EmployeeController`

| Test ID | Method | Path |
|---------|--------|------|
| `mat-employee` | GET | `/employee` |
| `mat-employee-create` | GET | `/employee/create` |
| `mat-employee-store` | POST | `/employee/store` |
| `mat-employee-show` | GET | `/employee/show/:id` |
| `mat-employee-edit` | GET | `/employee/edit/:id` |
| `mat-employee-update` | POST | `/employee/update/:id` |
| `mat-employee-delete` | GET | `/employee/delete/:id` |
| `mat-employee-toggle` | POST | `/employee/toggle-status/:id` |
| `mat-employee-export` | GET | `/employee/export` |
| `mat-employee-search` | GET | `/employee/search` |
| `mat-employee-by-dept` | GET | `/employee/get-by-department` |

**HR module:** `/hr/employees`, `/hr/employee/create`, … — see §4.9.

---

### 4.9 HR

| Test ID | Path |
|---------|------|
| `hr-root` | `/hr` |
| `hr-employees` | `/hr/employees` |
| `hr-emp-create` | `/hr/employee/create` |
| `hr-departments` | `/hr/departments` |
| `hr-dept-create` | `/hr/department/create` |
| `hr-analytics` | `/hr/analytics` |
| `hr-reports` | `/hr/reports` |
| `hr-attendance` | `/hr/attendance` |
| `hr-leave` | `/hr/leave` |
| `hr-payroll` | `/hr/payroll` |
| `hr-salary` | `/hr/salary` |
| `hr-docs` | `/hr/documents` |
| `hr-training` | `/hr/training` |
| `hrm-alias` | `/hrm` |

**Aliases:** `/attendance`, `/payroll`, `/leave-management`, `/salary-management`, `/documents`, `/training` — sidebar uses `/salary` (see §4.0); prefer `/salary-management` for a passing smoke unless a root alias is added.

**Edit/delete (need id):** `/hr/employee/edit/:id`, `/hr/employee/delete/:id`, `/hr/department/edit/:id`, `/hr/department/delete/:id`

---

### 4.10 Accounting / finance

| Test ID | Path |
|---------|------|
| `acc-root` | `/accounting` |
| `acc-invoices` | `/accounting/invoices` |
| `acc-bills` | `/accounting/bills` |
| `acc-ar` | `/accounting/receivables` or `/accounting/ar` |
| `acc-ap` | `/accounting/payables` or `/accounting/ap` |
| `acc-journal` | `/accounting/journal` |
| `acc-coa` | `/accounting/coa` |
| `acc-bank` | `/accounting/bank-accounts` |
| `acc-expenses` | `/accounting/expenses` |
| `acc-taxes` | `/accounting/taxes` |
| `acc-reports` | `/accounting/reports` |
| `accounts-alias` | `/accounts` |
| `vendor-pay` | `/vendor-payment` |

**Known issues:** Several accounting sub-views were missing or 500 in past runs — quarantine until fixed.

**Sidebar shortcuts (§4.0):** Menu items `/general-ledger`, `/accounts-payable`, `/accounts-receivable`, `/bank-reconciliation`, `/journal-entries`, `/financial-reports` are not registered at those paths — test the `/accounting/...` equivalents above until menu hrefs are fixed.

---

### 4.11 GST

| Test ID | Path |
|---------|------|
| `gst-root` | `/gst` |
| `gst-report` | `/gst/report` |

---

### 4.12 Maintenance & gate / simple modules

| Test ID | Path |
|---------|------|
| `maint` | `/maintenance` |
| `gate-entry` | `/gate-entry` |
| `gate-exit` | `/gate-exit` |
| `visitor` | `/visitor-management` |
| `reports-simple` | `/reports` |
| `sales-report-simple` | `/sales-report` |
| `purchase-report-simple` | `/purchase-report` |
| `all-features` | `/all-features` |

Many use `SimpleModuleController` — expect generic list/placeholder UI.

---

### 4.13 Settings, company, help

| Test ID | Path |
|---------|------|
| `company-profile` | `/company-profile` |
| `logo-settings` | `/logo-settings` |
| `module-assignments` | `/module-assignments` |
| `dept-assignment` | `/department-assignment` |
| `system-settings` | `/system-settings` |
| `user-management` | `/user-management` |
| `role-perms` | `/role-permissions` |
| `settings-mod-post` | POST targets under `/settings/...` (forms in module assignments) |
| `help-root` | `/help` |
| `help-support` | `/help/support` |
| `support-short` | `/support` |
| `help-doc` | `/help/documentation` |
| `help-faq` | `/help/faq` |
| `help-contact` | `/help/contact` |

**Known issues:** Company profile / settings often **500** (e.g. missing `company_profile` table or role `superadmin` vs `super_admin`). Help subpages may 500 if views missing.

---

### 4.14 Installer (usually skip in regression)

| Path | Notes |
|------|--------|
| `/installer` | Only when not installed; installed app redirects away |

---

### 4.15 Reports — all registered surfaces (cross-module)

Use this when mapping the **REPORTS** sidebar (§4.0) or building a report-only Playwright suite. Paths are full from site root.

| Domain | Controller / note | GET paths |
|--------|-------------------|-----------|
| **Production** | `ProductionController` | `/production/reports`, `/production/reports/export/:any` |
| **Purchase (hub)** | `PurchaseManagementController` | `/purchase/reports`, `/purchase/reports/export/:any` |
| **Purchase (standalone)** | `PurchaseReportController` | `/purchase-report`, `/purchase-report/pending-orders`, `/purchase-report/supplier-history`, `/purchase-report/price-trends`, `/purchase-report/quality-metrics`, `/purchase-report/cost-analysis`, `/purchase-report/export` |
| **Inventory** | `InventoryManagementController` | `/inventory/reports`, `/inventory/reports/export/:any`, `/inventory/reports/stock-aging`, `/inventory/reports/stock-valuation`, `/inventory/reports/movement-analysis` |
| **Sales (grouped)** | `SalesDistributionController` | `/sales/reports`, `/sales/reports/customer`, `/sales/reports/product`, `/sales/reports/region`, `/sales/reports/trends`, `/sales/reports/export` |
| **HR** | `HRController` | `/hr/reports` |
| **Accounting** | `AccountingController` | `/accounting/reports`, `/accounts/reports`, `/accounting/export/report` |
| **GST** | (see §4.11) | `/gst/report` |
| **Customer (single)** | `CustomerController` | `/customer/performance-report/:id` |
| **Generic placeholder** | `SimpleModuleController` | `/reports` (+ create/edit/delete CRUD), `/sales-report`, `/purchase-report` (index only unless using `PurchaseReportController` subpaths above), `/all-features` |

**Sidebar label → real URL (when href does not exist):** `/sales-reports` → `/sales/reports`; `/purchase-reports` → `/purchase/reports` or `/purchase-report`; `/production-reports` → `/production/reports`; `/inventory-reports` → `/inventory/reports`; `/hr-reports` → `/hr/reports`; `/financial-reports` (menu) → `/accounting/reports` (and `/accounting/export/report` if testing export); `/custom-reports` → no dedicated route (use `/reports` placeholder or add route).

---

## 5. Example spec skeleton (TypeScript)

```ts
import { test, expect } from '@playwright/test';

const paths = [
  { id: 'dashboard', path: '/dashboard' },
  { id: 'hr-employees', path: '/hr/employees' },
  { id: 'product', path: '/product' },
  // …append from tables above
];

test.describe('smoke @authenticated', () => {
  test.beforeEach(async ({ page }) => {
    // either use storageState from global setup, or:
    await page.goto('/login');
    await page.fill('input[name="username"]', process.env.E2E_USER!);
    await page.fill('input[name="password"]', process.env.E2E_PASS!);
    await page.click('button[type="submit"]');
    await expect(page).toHaveURL(/dashboard/);
  });

  for (const { id, path } of paths) {
    test(`${id} loads`, async ({ page }) => {
      const res = await page.goto(path, { waitUntil: 'domcontentloaded' });
      expect(res?.status(), path).toBeLessThan(400);
      await expect(page).not.toHaveURL(/login/);
    });
  }
});
```

Use `test.fixme()` for paths in the “Known issues” sections until the app is fixed.

---

## 6. Deeper tests (per module ideas)

After smoke passes, add **one happy path per module** where safe:

| Module | Idea |
|--------|------|
| **Customer** | Open create form → fill minimal required → submit → expect success flash or list row |
| **HR employee** | Create employee with generated code → list shows row |
| **Purchase order** | Create draft PO if UI allows without external APIs |
| **Inventory stock** | Open stock-in form → assert fields visible (skip submit if side effects) |

Use **test isolation**: prefer a dedicated test company DB or reset seeds; avoid production.

---

## 7. CI notes

- Start **Apache + MySQL** (or your stack) before `npx playwright test`.
- Use `webServer` in Playwright config only if you run PHP’s built-in server instead of XAMPP.
- Run headed locally: `npx playwright test --headed`.

---

## 8. Quick reference — file locations

- Routes: `app/Config/Routes.php`
- Login view: `app/Views/auth/login.php` (`username`, `password`)
- Layout: `app/Views/layouts/main.php`

This plan matches the codebase structure as of the last audit. **Sidebar parity** is §4.0 (`app/Views/partials/sidebar.php`); **production / purchase / sales** detail is §4.3–§4.7; **all report URLs** are §4.15. Re-run `php writable/module_smoke_test.php` after major route changes to refresh “known issues.”
