<h2><i class="fas fa-cog"></i> System Settings</h2>

<form method="POST" enctype="multipart/form-data"
      action="<?= URLROOT ?>/admin/saveSettings">

    <div class="mb-3">
        <label>Company Name</label>
        <input type="text"
               name="company_name"
               value="<?= $settings->company_name ?? '' ?>"
               class="form-control">
    </div>

    <div class="mb-3">
        <label>Address</label>
        <input type="text"
               name="address"
               value="<?= $settings->address ?? '' ?>"
               class="form-control">
    </div>

    <div class="mb-3">
        <label>Contacts</label>
        <input type="text"
               name="contacts"
               value="<?= $settings->contacts ?? '' ?>"
               class="form-control">
    </div>

    <div class="mb-3">
        <label>Company Logo</label>

        <?php if (!empty($settings->logo)): ?>
            <br>
            <img src="<?= URLROOT ?>/<?= $settings->logo ?>"
                 style="height:60px;margin-bottom:10px;">
        <?php endif; ?>

        <input type="file" name="logo" class="form-control">
    </div>

    <button class="btn btn-primary">
        Save Settings
    </button>

</form>