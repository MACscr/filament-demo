# Filament Demo — Deferred tab badge staleness reproduction

> **This branch is a minimal reproduction of a Filament v5 bug.**
>
> Tab badges configured with `->deferBadge()` go stale after data-mutating
> actions (CreateAction, EditAction, DeleteAction). The badge counts are only
> fetched once on Alpine `init()` and are never refreshed when the underlying
> data changes — so the table updates but the tab counts stay wrong until you
> hard-reload the page.
>
> See `app/Filament/Resources/Blog/Posts/Pages/ListPosts.php` for the configured
> tabs (All / Published / Draft, all using `->deferBadge()`).
>
> ## Steps to reproduce
>
> 1. Set up the demo (see "Getting started" below).
> 2. Log in at `/login` with `admin@filamentphp.com` / `demo.Filament@2021!`.
> 3. Navigate to **Blog → Posts**. Note the badge counts on the **All**,
>    **Published**, and **Draft** tabs (e.g. `All 81 / Published 55 / Draft 26`).
> 4. On any **Published** row, open the row Actions menu and click **Unpublish**
>    (the existing `toggle_publish` row action — see
>    `app/Filament/Resources/Blog/Posts/Tables/PostsTable.php`). It calls
>    `$record->update(['published_at' => null])`, an in-page Livewire commit.
> 5. The row's status flips to **Draft** and a "Post unpublished" notification
>    appears — the action committed and the table refreshed.
> 6. **Observed:** The tab badges stay at `Published 55 / Draft 26`. They are
>    not re-fetched.
> 7. Hard-reload the page (`Cmd+R`). The badges now show `Published 54 / Draft 27`,
>    confirming the data changed but the deferred badges never updated.
>
> Any in-page data-mutating action shows the same staleness — `EditAction`
> changing `published_at`, `DeleteAction`, a modal-mode `CreateAction` on the
> table, etc. (The page-level `CreateAction` in this demo navigates to a
> separate `/create` page, so the return trip is a fresh page load and hides
> the bug — that's why the row action is the cleanest repro.)
>
> ## Background
>
> Filament v4 originally re-fetched deferred badges on every Livewire `commit`
> via `Livewire.hook('commit', …)`. That caused over-fetching loops on every
> sort / search / filter / paginate (issues
> [#19583](https://github.com/filamentphp/filament/issues/19583),
> [#19574](https://github.com/filamentphp/filament/issues/19574),
> [#19590](https://github.com/filamentphp/filament/issues/19590)).
>
> PR [#19735](https://github.com/filamentphp/filament/pull/19735) fixed those
> by **removing the commit-hook refresh entirely** — but that also removed
> refresh on legitimate data mutations, which is what this repro shows.

## Getting started

Clone and install:

```sh
git clone https://github.com/filamentphp/demo.git filament-demo && cd filament-demo
composer install
composer setup
```

Start the dev server:

```sh
php artisan serve
```

Then log in at the URL shown in the terminal:

- **Email:** admin@filamentphp.com
- **Password:** password

## What's in the box

### Shop

Products, orders, customers, brands, and categories: the kind of CRUD you'd find in any e-commerce admin. The shop module demonstrates:

- **Order wizard**: multi-step create form with inline customer creation
- **Repeaters**: line items on orders and expenses with reactive totals
- **Query builder filters**: advanced filtering with text, number, date, and boolean constraints
- **Column summarizers**: footer totals for prices and quantities
- **Table grouping**: group orders by status, customer, or date
- **Drag-and-drop reordering**: sortable brand list
- **Media uploads**: multiple product images via Spatie Media Library
- **Soft deletes**: restore and force-delete on orders and customers
- **Dashboard**: filterable stats, charts, and a latest orders widget with live polling

### Blog

Posts, authors, and categories with a focus on content management patterns:

- **Rich text editing**: WYSIWYG content editor with prose rendering on the view page
- **Sub-navigation**: tabbed navigation between View, Edit, and Comments on posts
- **Manage related records**: dedicated comments page without leaving the post context
- **Tags**: Spatie Tags integration on posts
- **Import and export actions**: CSV import on categories, export on authors
- **Column layouts**: split and stack layouts on the authors table
- **Simple resources**: authors and categories managed with modals, no separate pages

### HR

Employees, departments, projects, tasks, timesheets, leave requests, and expenses: a more complex module showing how Filament scales:

- **Tabbed forms**: Personal, Employment, and Documents tabs on employees
- **Builder blocks**: milestone, task group, and checkpoint blocks in the project plan
- **Conditional fields**: salary vs. hourly rate based on employment type
- **Inline editing**: change leave request status directly in the table
- **Status workflows**: expense approval, rejection, and reimbursement actions
- **Expense line items**: repeatable entries with table layout in the infolist
- **Key-value editor**: freeform metadata on employees
- **Checkbox lists**: multi-select skills grid
- **Dashboard**: headcount stats, leave overview, timesheet trends, and budget charts

## Patterns worth looking at

Here are some specific things to poke at if you're learning Filament:

| Pattern | Where to find it |
|---|---|
| Wizard form | Create a new order |
| Reactive calculations | Edit an expense's line items |
| Builder blocks | Edit a project's Plan tab |
| Action groups with custom actions | Any table row with "..." menu |
| Slide-over modals | Ship an order |
| Modal forms with actions | Send email to a customer |
| Infolist with repeatable entries | View an expense |
| Sub-navigation | View or edit any post |
| Conditional field visibility | Change employment type on an employee |
| Dashboard filters | Shop dashboard date range and customer type |
| Global search | Press Cmd+K anywhere |
| Keyboard shortcuts | Cmd+Shift+P to quick-publish a post |
| Navigation badges | Check sidebar counts on orders, leave requests, and expenses |
