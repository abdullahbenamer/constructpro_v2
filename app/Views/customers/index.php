<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customers</title>
</head>

<body>
    <div class="row">
        <div class="col-12">
            <h2><i class="fas fa-users"></i> Customers (<?= count($customers) ?>)</h2>
            <a href="<?= URLROOT ?>/customers/create" class="btn btn-primary mb-3">
                <i class="fas fa-plus"></i> Add Customer
            </a>
            <div class="table-responsive">

                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Company</th>
                            <th>Contact</th>
                            <th>Phone</th>
                            <th>E-mail</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php if (empty($customers)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <i class="fas fa-users-slash fa-3x text-secondary mb-3"></i>
                                    <h5 class="mt-3 text-muted">
                                        No customers available.
                                    </h5>
                                    <p class="text-muted mb-3">
                                        You haven't added any customers yet.
                                        Create your first customer to get started.
                                    </p>
                                    <a href="<?= URLROOT ?>/customers/create" class="btn btn-primary">
                                        <i class="fas fa-plus"></i>
                                        Add Customer
                                    </a>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($customers as $customer) : ?>
                                <tr>
                                    <td>
                                        <a class="fw-bold text-decoration-none" href="<?= URLROOT ?>/customers/details/<?= $customer->id ?>">
                                            <i class="fas fa-building"></i> -
                                            <?= htmlspecialchars($customer->company) ?>
                                        </a>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($customer->name) ?>
                                    </td>
                                   
                                    <td><?= htmlspecialchars($customer->phone) ?></td>
                                     <td>
                                        <small><?= htmlspecialchars($customer->email) ?></small>
                                    </td>

                                    <td>
                                        <span class="badge bg-success">Active</span>
                                    </td>

                                    <td>
                                        <a href="<?= URLROOT ?>/customers/edit/<?= $customer->id ?>"
                                            class="btn btn-sm btn-warning">
                                            Edit
                                        </a>

                                        <a href="<?= URLROOT ?>/customers/delete/<?= $customer->id ?>"
                                            class="btn btn-sm btn-danger"
                                            onclick="return confirm('Delete this customer?')">
                                            Delete
                                        </a>
                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        <?php endif; ?>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>