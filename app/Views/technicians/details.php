<h2>
    Technician Details
</h2>

<div class="card">

    <div class="card-body">

        <h4>
            <?= htmlspecialchars($technician->name) ?>
        </h4>

        <p>
            <strong>Email:</strong>
            <?= htmlspecialchars($technician->email) ?>
        </p>

        <p>
            <strong>Phone:</strong>
            <?= htmlspecialchars($technician->phone) ?>
        </p>

        <p>
            <strong>Specialty:</strong>
            <?= htmlspecialchars($technician->specialty) ?>
        </p>

        <p>
            <strong>Status:</strong>
            <?= htmlspecialchars($technician->status) ?>
        </p>

    </div>

</div>

<a href="<?= URLROOT ?>/services"
   class="btn btn-secondary mt-3">

    Back

</a>