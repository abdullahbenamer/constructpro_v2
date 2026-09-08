</div>
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Select2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    // Only for resource requistion items edit.php
    // $(document).ready(function() {
    //     $('#resource_id').select2({
    //         placeholder: "-- Select Resource --",
    //         allowClear: true,
    //         width: '100%'
    //     });

    //     $('#resource_id').on('change', function() {
    //        let option = $(this).find(':selected');
    //         $('#description').val(
    //             option.data('description') || ''
    //         );
    //         $('#uom').val(
    //             option.data('unit') || ''
    //         );
    //     });
    // });
</script>

<script>
     // Only for resource requistion items create.php
// $(document).ready(function () {
//     $('#resource_id').select2({
//         placeholder: "-- Select Resource --",
//         allowClear: true,
//         width: '100%'
//     });

//     $('#resource_id').on('change', function () {
//         let option = this.options[this.selectedIndex];
//         $('#description').val(
//             option.getAttribute('data-description') || ''
//         );
//         $('#uom').val(
//             option.getAttribute('data-unit') || ''
//         );
//     });

// });
</script>

<!-- Footer -->
<div style="margin-top: 25px; padding-top: 25px;">
    <div class="card text-center">

        <div class="card-header">
        </div>

        <div class="card-body">

            <p class="card-text">
                Dedicated to Quality
            </p>

            <h5 class="card-title">
                <i class="fas fa-city"></i>
                Construction Professional System
                <i class="fas fa-drafting-compass"></i>
            </h5>

            <a href="<?= URLROOT ?>/" class="btn btn-primary">
                <i class="fas fa-home"></i>
                Home
            </a>

        </div>

    </div>
</div>


</body>
</html>