<?php

class PriorityHelper
{
    public static function all()
    {
        return [
            'LOW',
            'NORMAL',
            'HIGH',
            'URGENT',
            'CRITICAL'
        ];
    }

    public static function badge($priority)
    {
        switch (strtoupper($priority)) {

            case 'LOW':
                return 'bg-success';

            case 'NORMAL':
                return 'bg-primary';

            case 'HIGH':
                return 'bg-warning text-dark';

            case 'URGENT':
                return 'bg-danger';

            case 'CRITICAL':
                return 'bg-dark';

            default:
                return 'bg-secondary';
        }
    }
}

// /-------------------------------

// Then every view becomes:

// <select name="priority" class="form-select">

// <?php foreach (PriorityHelper::all() as $priority): ?>

// <option value="<?//= $priority ?>"
//     <?//= ($requisition->priority == $priority) ? 'selected' : '' ?>>

//     <?//= $priority ?>

// </option>

// <?php //endforeach; ?>

// </select>

<!-- //////////////////////// -->
<!-- 
badges become simply:

<span class="badge <?//= PriorityHelper::badge($req->priority) ?>">
    <?//= $req->priority ?>
</span> -->