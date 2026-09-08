<h2>Employee Details</h2>

<div class="card">
    <div class="card-body">

        <h4>
            <?= htmlspecialchars($data['user']->full_name) ?>
        </h4>

        <p>
            <strong>Email:</strong>
            <?= htmlspecialchars($data['user']->email) ?>
        </p>

        <p>
            <strong>Mobile:</strong>
            <?= htmlspecialchars($data['user']->mobile) ?>
        </p>

        <p>
    <strong>Role:</strong>
    <?= htmlspecialchars($data['user']->role_name ?? '-') ?>
</p>

        <p>
            <strong>Status:</strong>
            <?= htmlspecialchars($data['user']->status ?? 'Active') ?>
        </p>

    </div>
</div>

<a href="<?= URLROOT ?>/users"
   class="btn btn-secondary mt-3">
    Back
</a>