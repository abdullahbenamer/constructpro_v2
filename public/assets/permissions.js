<script>
// permissions.js

function toggleGroup(group) {
    document
        .querySelectorAll('input[value][data-group="' + group + '"]')
        .forEach(cb => cb.checked = true);
}
</script>