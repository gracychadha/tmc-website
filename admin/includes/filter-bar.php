<?php
/**
 * Reusable Filter Bar Component
 *
 * Usage: Include this file after defining $filterConfig array.
 *
 * $filterConfig = [
 *     'table_id'       => 'datatable',       // CSS class or ID of the DataTable
 *     'page_type'      => 'contact',         // 'contact' or 'subscriber' (used for JS targeting)
 *     'show_filters'   => ['name','email','phone','subject','date_range'],  // which filters to show
 *     'sort_enabled'   => true,              // show sort dropdown
 * ];
 */

$fc = $filterConfig ?? [];
$pageType      = $fc['page_type'] ?? 'contact';
$tableClass    = $fc['table_id'] ?? 'datatable';
$showFilters   = $fc['show_filters'] ?? [];
$sortEnabled   = $fc['sort_enabled'] ?? true;
?>

<!-- Filter Dropdown -->
<div class="dropdown mb-3 me-2">
    <a href="javascript:void(0);" class="btn btn-outline-light bg-white dropdown-toggle" data-bs-toggle="dropdown" data-bs-auto-close="outside">
        <i class="ti ti-filter me-2"></i>Filter
    </a>
    <div class="dropdown-menu filter-dropdown p-3" data-page-type="<?php echo htmlspecialchars($pageType); ?>">
        <div class="row g-3 align-items-end">

            <?php if (in_array('name', $showFilters)): ?>
            <div class="col-lg-3 col-sm-6">
                <label class="form-label fw-semibold">Name</label>
                <input type="text" class="form-control filter-input" data-filter-column="name" placeholder="Search by name">
            </div>
            <?php endif; ?>

            <?php if (in_array('email', $showFilters)): ?>
            <div class="col-lg-3 col-sm-6">
                <label class="form-label fw-semibold">Email</label>
                <input type="text" class="form-control filter-input" data-filter-column="email" placeholder="Search by email">
            </div>
            <?php endif; ?>

            <?php if (in_array('phone', $showFilters)): ?>
            <div class="col-lg-3 col-sm-6">
                <label class="form-label fw-semibold">Phone</label>
                <input type="text" class="form-control filter-input" data-filter-column="phone" placeholder="Search by phone">
            </div>
            <?php endif; ?>

            <?php if (in_array('subject', $showFilters)): ?>
            <div class="col-lg-3 col-sm-6">
                <label class="form-label fw-semibold">Subject</label>
                <input type="text" class="form-control filter-input" data-filter-column="subject" placeholder="Search by subject">
            </div>
            <?php endif; ?>

            <?php if (in_array('date_range', $showFilters)): ?>
            <div class="col-lg-3 col-sm-6">
                <label class="form-label fw-semibold">From Date</label>
                <input type="date" class="form-control filter-date-from" data-filter-column="date">
            </div>
            <div class="col-lg-3 col-sm-6">
                <label class="form-label fw-semibold">To Date</label>
                <input type="date" class="form-control filter-date-to" data-filter-column="date">
            </div>
            <?php endif; ?>

        </div>
        <div class="d-flex justify-content-end mt-3 gap-2">
            <button type="button" class="btn btn-light btn-sm filter-reset" data-page-type="<?php echo htmlspecialchars($pageType); ?>">
                <i class="ti ti-refresh me-1"></i>Reset
            </button>
            <button type="button" class="btn btn-primary btn-sm filter-apply" data-page-type="<?php echo htmlspecialchars($pageType); ?>">
                <i class="ti ti-check me-1"></i>Apply Filters
            </button>
        </div>
    </div>
</div>
<!-- /Filter Dropdown -->

<?php if ($sortEnabled): ?>
<!-- Sort Dropdown -->
<div class="dropdown mb-3">
    <a href="javascript:void(0);" class="btn btn-outline-light bg-white dropdown-toggle" data-bs-toggle="dropdown">
        <i class="ti ti-sort-ascending-2 me-2"></i>Sort by A-Z
    </a>
    <ul class="dropdown-menu sort-dropdown p-3" data-page-type="<?php echo htmlspecialchars($pageType); ?>">
        <li><a href="javascript:void(0);" class="dropdown-item rounded-1 active" data-sort="asc">Ascending (A-Z)</a></li>
        <li><a href="javascript:void(0);" class="dropdown-item rounded-1" data-sort="desc">Descending (Z-A)</a></li>
        <li><a href="javascript:void(0);" class="dropdown-item rounded-1" data-sort="newest">Recently Added</a></li>
        <li><a href="javascript:void(0);" class="dropdown-item rounded-1" data-sort="oldest">Oldest First</a></li>
    </ul>
</div>
<!-- /Sort Dropdown -->
<?php endif; ?>
