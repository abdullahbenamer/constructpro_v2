  <div class="row">
      <div class="col-12">
         <div class="card mb-3">
        <div class="card-body">

        <h3 class="mb-1">
            Project: <?= strtoupper(htmlspecialchars($project->title)) ?>
        </h3>

         <div class="text-muted">
    <h4>Customer:</h4>
    <h4><?= strtoupper(htmlspecialchars($project->customer_name)) ?></h4>
   
     <a href="<?= URLROOT ?>/customers/details/<?= $project->customer_id ?>" class="fw-bold text-decoration-none"><i class="fas fa-address-card"></i> Go to Customer Profile</a>

</div>

    </div>
</div>
          <div class="d-flex justify-content-between align-items-center mb-3">
              <a href="<?= URLROOT ?>/projects" class="btn btn-success">
                  Go Back to Projects
              </a>
          </div>

          <div class="d-flex justify-content-between align-items-center mb-3">
              <h3><i class="fas fa-coins"></i> Project Costs</h3>

              <a href="<?= URLROOT ?>/project-costs/create/<?= $project_id ?>" class="btn btn-primary">
                  <i class="fas fa-plus"></i> Add Cost
              </a>
          </div>
          <div>
  
<a href="<?= URLROOT ?>/projectcosts/finance/<?= $project->id ?>"
   class="btn btn-primary">
    <i class="fas fa-money-check-dollar"></i>
    Advance Payment
</a>

<a href="<?= URLROOT ?>/projectcosts/ledger/<?= $project->id ?>"
   class="btn btn-secondary">
    <i class="fas fa-book"></i>
    Finance Ledger
</a>

<a href="<?= URLROOT ?>/projectcosts/financeDashboard/<?= $project->id ?>"
   class="btn btn-info">
    <i class="fas fa-chart-line"></i>
    Project Dashboard
</a>

<!-- Documents -->
 <a href="<?= URLROOT ?>/projects/documents/<?= $project->id ?>"
   class="btn btn-secondary">
    <i class="fas fa-folder-open"></i>
    Documents
</a>
          </div>
          <br>
          <!-- -->
          <div class="card">
              <div class="card-header d-flex justify-content-between">
                  <strong>Total Costs: $<?= number_format($total_cost ?? 0, 2) ?></strong>
                  <span class="badge bg-info"><?= count($costs ?? []) ?> items</span>
              </div>

              <div class="table-responsive">
                  <table class="table table-striped mb-0">
                      <thead>
                          <tr>
                              <th>Type</th>
                             <th>Item / Description</th>
                              <th>SKU/Barcode</th>
                              <th>Source Location</th>
                              <th>Qty</th>
                              <th>Cost Price</th>
                              <th>Total Cost</th>
                              <th>Issue Date</th>
                              <th>Actions</th>
                          </tr>
                      </thead>
                      <tbody>
                          <?php foreach ($costs ?? [] as $cost) : ?>
                              <tr>
                                  <td><span class="badge bg-info"><?= ucfirst($cost->cost_type) ?></span></td>
                                <td>
    <?php if (!empty($cost->item_name)): ?>
        <strong>
            <?= htmlspecialchars($cost->item_name) ?>
        </strong>
    <?php endif; ?>

    <?php if (!empty($cost->description)): ?>
        <div class="text-muted small">
            <?= htmlspecialchars($cost->description) ?>
        </div>
    <?php endif; ?>
</td>
                                  <td><?= htmlspecialchars($cost->sku ?? 'N/A') ?></td>
                                  <td>
    <?php if (!empty($cost->location_code)): ?>
        <span class="badge bg-secondary">
            <?= htmlspecialchars($cost->location_code)?>
        </span>
        <br>
        <small><?= htmlspecialchars($cost->location_name) ?></small>
    <?php else: ?>
        <span class="text-muted">N/A</span>
    <?php endif; ?>
</td>
                                  <td><?= $cost->quantity ?></td>
                                  <td>$<?= number_format($cost->unit_price, 2) ?></td>
                                  <td><strong>$<?= number_format($cost->quantity * $cost->unit_price, 2) ?></strong></td>
                                  <td><?= date('M j', strtotime($cost->created_at)) ?></td>
                                  <td>
                                      <a href="<?= URLROOT ?>/project-costs/edit/<?= $cost->id ?>/<?= $project_id ?>" class="btn btn-sm btn-warning">
                                          Edit
                                      </a>

                                      <a href="<?= URLROOT ?>/project-costs/delete/<?= $cost->id ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this cost?')">
                                          Delete
                                      </a>
                                  </td>
                              </tr>
                          <?php endforeach; ?>
                      </tbody>
                  </table>
              </div>
          </div>
          <?php if (empty($costs)) : ?>
              <div class="alert alert-info">
                  <i class="fas fa-info-circle"></i> No costs added yet. 
                  <a href="<?= URLROOT ?>/project-costs/create/<?= $project_id ?>">Add first cost</a>
                  
              </div>
          <?php endif; ?>
      </div>
  </div>