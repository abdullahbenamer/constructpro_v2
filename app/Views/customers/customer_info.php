<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customers</title>
</head>

<body>
    <div class="row">
    <div class="col-md-12">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>
                <i class="fas fa-building"></i>
                Customer Information
            </h2>

            <div>
                <a href="<?= URLROOT ?>/customers/edit/<?= $customer->id ?>"
                    class="btn btn-warning">
                    <i class="fas fa-edit"></i> Edit
                </a>

                <a href="<?= URLROOT ?>/customers"
                    class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">

                <div class="row">

                    <div class="col-md-6">
                        <h5 class="border-bottom pb-2">Company Information</h5>

                        <p>
                            <strong>Company:</strong><br>
                            <?= htmlspecialchars($customer->company) ?>
                        </p>

                        <p>
                            <strong>Status:</strong><br>
                            <span class="badge bg-success">Active</span>
                        </p>
                    </div>

                    <div class="col-md-6">
                        <h5 class="border-bottom pb-2">Contact Information</h5>

                        <p>
                            <strong>Contact Person:</strong><br>
                            <?= htmlspecialchars($customer->name) ?>
                        </p>

                        <p>
                            <strong>Email:</strong><br>
                            <a href="mailto:<?= $customer->email ?>">
                                <?= htmlspecialchars($customer->email) ?>
                            </a>
                        </p>

                        <p>
                            <strong>Phone:</strong><br>
                            <?= htmlspecialchars($customer->phone) ?>
                        </p>
                    </div>

                </div>

            </div>
        </div>

    </div>
</div>
</body>

</html>