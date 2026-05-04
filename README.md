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
> 2. Log in at `/admin/login` with `admin@filamentphp.com` / `password`.
> 3. Navigate to **Blog → Posts**. Note the badge counts on the **All**,
>    **Published**, and **Draft** tabs.
> 4. Click **New post** and create a post with `Published date` left empty
>    (so it lands in the **Draft** tab).
> 5. Save. The new row appears in the table.
> 6. **Observed:** The **All** and **Draft** badge counts do **not** increase.
>    They stay at the values fetched on initial page load.
> 7. **Expected:** The badges should re-fetch after the action commits.
>    A full page reload shows the correct numbers.
>
> Same behaviour occurs with EditAction (e.g. set/clear a published date —
> the row moves between tabs but counts don't update) and DeleteAction.
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
