<h3>Project Advance Payments</h3>

<h5>Project: <?= $project->title ?></h5>
<br>
         <div class="text-muted">
    Customer:
      <a href="<?= URLROOT ?>/customers/info/<?= $project->customer_id ?>"  target="_blank" 
   class="fw-bold text-decoration-none">
    <?= htmlspecialchars($project->customer_name) ?>

        <i class="fas fa-address-card"></i> Profile
    </a>
</div>
<br>
<div class="d-flex justify-content-between align-items-center mb-3">
              <a href="<?= URLROOT ?>/projects" class="btn btn-secondary">
                  Go Back to Projects
              </a>
          </div>
          <br>
<div>
     <a href="<?= URLROOT ?>/projectAdvance/create/<?= $project->id ?>"
   class="btn btn-primary"> <i class="fas fa-money-check-dollar"></i> 
   Add Advance Payments
</a>
</div>
<br>
<div class="alert alert-info">
    <strong>Total In:</strong> <?= $balance->total_in ?? 0 ?><br>
    <strong>Total Out:</strong> <?= $balance->total_out ?? 0 ?><br>
    <strong>Balance:</strong> <?= $balance->balance ?? 0 ?>
</div>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Date</th>
            <th>Type</th>
            <th>Amount</th>
            <th>Reference</th>
        </tr>
    </thead>

    <tbody>

    <?php if (!empty($advances)): ?>

        <?php foreach ($advances as $a): ?>

            <tr>
                <td><?= date('d M Y', strtotime($a->advance_date)) ?></td>
                <td>
                    <span class="badge bg-info">
                        Advance
                    </span>
                </td>
                <td class="text-end">
                    <?= number_format($a->amount, 2) ?>
                </td>
                <td>
                    <?= htmlspecialchars($a->reference) ?>
                </td>
            </tr>

        <?php endforeach; ?>

    <?php else: ?>

        <tr>
            <td colspan="4" class="text-center py-5 text-muted">
                <i class="fas fa-money-check-alt fa-3x mb-3 d-block text-secondary"></i>

                <strong>No Customer advances found.</strong>

                <br>

                <small>
                    No advance payments have been recorded for this customer yet.
                </small>
            </td>
        </tr>

    <?php endif; ?>

</tbody>
</table>