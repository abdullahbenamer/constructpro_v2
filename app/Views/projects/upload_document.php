<h2>

<i class="fas fa-upload"></i>

Upload Project Documents

</h2>

<hr>

<h5>

Project:

<strong><?= strtoupper(htmlspecialchars($project->title)) ?></strong>

</h5>

<form method="POST" enctype="multipart/form-data">

<div class="row">

<div class="col-md-4">

<label class="form-label">

Category

</label>

<select name="category" id="category" class="form-select" required>
    
<option value="">-- Select Documnet Category--</option>

<option value="contract">Contract</option>

<option value="drawing">Drawing</option>

<option value="quotation">Quotation</option>

<option value="invoice">Invoice</option>

<option value="receipt">Receipt</option>

<option value="purchase_order">Purchase Order</option>

<option value="inspection">Inspection</option>

<option value="report">Report</option>

<option value="photo">Photo</option>

<option value="certificate">Certificate</option>

<option value="permit">Permit</option>

<option value="manual">Manual</option>

<option value="other">Other</option>

</select>

</div>

<div class="col-md-8">

<label class="form-label">

Title

</label>

<input type="text" name="title"  id="title" class="form-control" required>

</div>

</div>

<br>

<div class="mb-3">

<label>

Description

</label>

<textarea
    name="description"
    class="form-control"
    rows="3"></textarea>

</div>

<div class="row">

<div class="col-md-4">

<label>

Document Date <small class="text-danger">(Actual Date of the Document)</small>

</label>

<input type="date" name="document_date"  class="form-control"  value="<?= date('Y-m-d') ?>">

</div>

</div>

<br>

<div class="mb-3">

<label>

Select Files <small>(You can upload multiple files at once)</small>

</label>

<input type="file" name="documents[]" class="form-control"  multiple required>

<small class="text-muted">

PDF, Images, Word, Excel, ZIP...

</small>

</div>

<button
    class="btn btn-success">

<i class="fas fa-upload"></i>

Upload

</button>

<a
    href="<?= URLROOT ?>/projects/documents/<?= $project->id ?>"
    class="btn btn-secondary">

Cancel

</a>

</form>

<script>
    // Title auto-fill with the selected category prefix
const category = document.getElementById('category');
const title    = document.getElementById('title');

function formatCategory(text)
{
    return text.replace(/_/g, ' ').toUpperCase();
}

category.addEventListener('change', function() {

    const prefix = formatCategory(this.value) + ' - ';

    // only set if user hasn't typed anything yet
    if (title.dataset.userEdited !== "1") {
        title.value = prefix;
    }

});

// detect user manual typing
title.addEventListener('input', function() {
    this.dataset.userEdited = "1";
});
</script>