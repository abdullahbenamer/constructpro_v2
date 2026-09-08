<?php

$role_id   = $_SESSION['role_id'] ?? null;
$role_name = $_SESSION['role_name'] ?? '';

?>

<!-- =========================================================
     GLOBAL BOOTSTRAP ALERT BLOCK
========================================================= -->

<?php if (!empty($_SESSION['success'])) : ?>

    <div class="alert alert-success alert-dismissible fade show">
        <?= $_SESSION['success'] ?>

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert">
        </button>
    </div>

    <?php unset($_SESSION['success']); ?>

<?php endif; ?>


<?php if (!empty($_SESSION['error'])) : ?>

    <div class="alert alert-danger alert-dismissible fade show">
        <?= $_SESSION['error'] ?>

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert">
        </button>
    </div>

    <?php unset($_SESSION['error']); ?>

<?php endif; ?>


<?php if (!empty($_SESSION['warning'])) : ?>

    <div class="alert alert-warning alert-dismissible fade show">
        <?= $_SESSION['warning'] ?>

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert">
        </button>
    </div>

    <?php unset($_SESSION['warning']); ?>

<?php endif; ?>


<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>
        <?= isset($title) ? $title . ' - ' : '' ?>
        Construction Professional
    </title>


    <!-- Bootstrap -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
        rel="stylesheet">


    <!-- Select2 -->

    <link
        href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css"
        rel="stylesheet">


    <!-- Font Awesome -->

    <link
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
        rel="stylesheet">


    <!-- Fonts -->

    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Tajawal:wght@300;400;500;700;800&display=swap"
        rel="stylesheet">


    <!-- =====================================================
         SIDEBAR CSS
    ====================================================== -->

    <style>
        /*
        |--------------------------------------------------------------------------
        | GLOBAL
        |--------------------------------------------------------------------------
        */

        body {

            padding-left: 260px;

            transition: padding-left 0.2s ease;

        }


        /*
        |--------------------------------------------------------------------------
        | SIDEBAR
        |--------------------------------------------------------------------------
        */

        .app-sidebar {

            position: fixed;

            top: 0;

            left: 0;

            width: 260px;

            height: 100vh;

            z-index: 1050;

            display: flex;

            flex-direction: column;

            overflow-y: auto;

            overflow-x: visible;

            background-color: #0d6efd;

            box-shadow: 3px 0 12px rgba(0, 0, 0, 0.12);

        }


        /*
        |--------------------------------------------------------------------------
        | SIDEBAR BRAND
        |--------------------------------------------------------------------------
        */

        .app-sidebar .navbar-brand {

            display: block;

            width: 100%;

            padding: 20px 18px;

            margin: 0;

            font-size: 1.05rem;

            line-height: 1.4;

            white-space: normal;

        }


        /*
        |--------------------------------------------------------------------------
        | SIDEBAR NAV
        |--------------------------------------------------------------------------
        */

        .app-sidebar .navbar-nav {

            width: 100%;

            padding: 5px 10px 15px 10px;

            flex-direction: column;

        }


        .app-sidebar .nav-item {

            width: 100%;

            display: block;

        }


        .app-sidebar .nav-link {

            display: flex;

            align-items: center;

            gap: 9px;

            width: 100%;

            padding: 10px 12px;

            border-radius: 6px;

            color: rgba(255, 255, 255, 0.95);

            white-space: normal;

        }


        .app-sidebar .nav-link:hover {

            background-color: rgba(255, 255, 255, 0.12);

            color: #ffffff;

        }


        .app-sidebar .nav-link.active {

            background-color: rgba(255, 255, 255, 0.18);

            color: #ffffff;

        }


        /*
        |--------------------------------------------------------------------------
        | DROPDOWNS
        |--------------------------------------------------------------------------
        */

        .app-sidebar .dropdown {

            position: relative;

        }


        .app-sidebar .dropdown-menu {

            min-width: 230px;

            margin: 0;

            border: none;

            border-radius: 6px;

            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.18);

            z-index: 9999 !important;

        }


        /*
|--------------------------------------------------------------------------
| DESKTOP SIDEBAR DROPDOWNS
|--------------------------------------------------------------------------
*/

        @media (min-width: 992px) {

            .app-sidebar,
            .app-sidebar .container-fluid,
            .app-sidebar .navbar-collapse,
            .app-sidebar .navbar-nav,
            .app-sidebar .nav-item,
            .app-sidebar .dropdown {

                overflow: visible !important;

            }


            .app-sidebar .dropend>.dropdown-menu {

                position: absolute !important;

                top: 0 !important;

                left: 100% !important;

                margin-top: 0 !important;

                margin-left: 0.125rem !important;

                z-index: 9999 !important;

            }

        }

        .app-sidebar .dropdown-item {

            padding: 9px 14px;

        }


        .app-sidebar .dropdown-item i {

            width: 20px;

        }


        /*
        |--------------------------------------------------------------------------
        | USER AREA
        |--------------------------------------------------------------------------
        */

        .sidebar-user {

            margin-top: auto;

            padding: 10px;

        }


        .sidebar-user .nav-link {

            margin-bottom: 5px;

        }


        .sidebar-logout {

            width: 100%;

        }


        /*
        |--------------------------------------------------------------------------
        | COMPANY HEADER
        |--------------------------------------------------------------------------
        */

        .company-header {

            margin-left: 0;

        }


        /*
        |--------------------------------------------------------------------------
        | SIDEBAR SCROLLBAR
        |--------------------------------------------------------------------------
        */

        .app-sidebar::-webkit-scrollbar {

            width: 6px;

        }


        .app-sidebar::-webkit-scrollbar-thumb {

            background: rgba(255, 255, 255, 0.25);

            border-radius: 10px;

        }


        .app-sidebar::-webkit-scrollbar-track {

            background: transparent;

        }


        /*
        |--------------------------------------------------------------------------
        | MOBILE
        |--------------------------------------------------------------------------
        */

        @media (max-width: 991.98px) {

            body {

                padding-left: 0;

            }


            .app-sidebar {

                position: fixed;

                top: 0;

                left: 0;

                width: 260px;

                height: 100vh;

                z-index: 1050;

                display: flex;

                flex-direction: column;

                overflow: visible;

                background-color: #0d6efd;

                box-shadow: 3px 0 12px rgba(0, 0, 0, 0.12);

            }


            .app-sidebar .navbar-brand {

                padding: 14px 15px;

            }


            .app-sidebar .navbar-toggler {

                margin: 10px 15px;

            }


            .app-sidebar .navbar-collapse {
                max-height: calc(100vh - 75px);
                overflow-y: auto;
            }


            .app-sidebar .navbar-nav {

                padding-bottom: 15px;

            }


            .app-sidebar .dropdown-menu {

                position: static !important;

                transform: none !important;

                width: 100%;

                margin-top: 2px;

                box-shadow: none;

                background-color: rgba(0, 0, 0, 0.08);

            }


            .app-sidebar .dropdown-item {

                color: #ffffff;

            }


            .app-sidebar .dropdown-item:hover,

            .app-sidebar .dropdown-item.active {

                background-color: rgba(255, 255, 255, 0.12);

                color: #ffffff;

            }


            .sidebar-user {

                margin-top: 10px;

            }

        }
    </style>

</head>


<body>


    <!-- =========================================================
     LEFT SIDEBAR NAVIGATION
========================================================= -->

    <nav class="navbar navbar-expand-lg navbar-dark app-sidebar">


        <div class="container-fluid d-flex flex-column align-items-stretch">


            <!-- =================================================
             BRAND
        ================================================== -->

            <a
                class="navbar-brand fw-bold"
                href="<?= URLROOT ?>/">

                <i class="fas fa-city"></i>

                ConstructPro System

                <i class="fas fa-drafting-compass"></i>

            </a>


            <!-- =================================================
             MOBILE TOGGLER
        ================================================== -->

            <button
                class="navbar-toggler d-lg-none"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarNav"
                aria-controls="navbarNav"
                aria-expanded="false"
                aria-label="Toggle navigation">

                <span class="navbar-toggler-icon"></span>

            </button>


            <!-- =================================================
             NAVIGATION
        ================================================== -->

            <div
                class="collapse navbar-collapse"
                id="navbarNav">


                <ul class="navbar-nav me-auto">


                    <!-- =================================================
                     DASHBOARD
                ================================================= -->

                    <?php if (AuthHelper::canView('dashboard.view')) : ?>

                        <li class="nav-item">

                            <a
                                class="nav-link <?= App::$current_url == 'dashboard' ? 'active' : '' ?>"
                                href="<?= URLROOT ?>/dashboard">

                                <i class="fas fa-tachometer-alt"></i>

                                Dashboard

                            </a>

                        </li>

                    <?php endif; ?>


                    <!-- =================================================
                     PROJECTS
                ================================================= -->

                    <?php if (
                        AuthHelper::canView('projects.view') ||
                        AuthHelper::canView('customers.view')
                    ) : ?>

                        <li class="nav-item dropdown dropend">

                            <a
                                class="nav-link dropdown-toggle"
                                href="#"
                                id="projectsDropdown"
                                role="button"
                                data-bs-toggle="dropdown"
                                aria-expanded="false">

                                <i class="fas fa-project-diagram"></i>

                                Projects

                            </a>


                            <ul
                                class="dropdown-menu"
                                aria-labelledby="projectsDropdown">


                                <?php if (AuthHelper::canView('projects.view')) : ?>

                                    <li>

                                        <a
                                            class="dropdown-item <?= App::$current_url == 'projects' ? 'active' : '' ?>"
                                            href="<?= URLROOT ?>/projects">

                                            <i class="fas fa-project-diagram"></i>

                                            Projects

                                        </a>

                                    </li>

                                <?php endif; ?>


                                <?php if (AuthHelper::canView('projects.view')) : ?>

                                    <li>

                                        <a
                                            class="dropdown-item <?= App::$current_url == 'ResourceRequisitions' ? 'active' : '' ?>"
                                            href="<?= URLROOT ?>/ResourceRequisitions">

                                            <i class="fas fa-file"></i>

                                            Resource Requisitions

                                        </a>

                                    </li>

                                <?php endif; ?>


                                <?php if (AuthHelper::canView('customers.view')) : ?>

                                    <li>

                                        <a
                                            class="dropdown-item <?= App::$current_url == 'customers' ? 'active' : '' ?>"
                                            href="<?= URLROOT ?>/customers">

                                            <i class="fas fa-users"></i>

                                            Customers

                                        </a>

                                    </li>

                                <?php endif; ?>


                            </ul>

                        </li>

                    <?php endif; ?>


                    <!-- =================================================
                     INVENTORY
                ================================================== -->

                    <?php if (

                        AuthHelper::canView('inventory.view') ||

                        AuthHelper::canView('inventory-movements.view') ||

                        AuthHelper::canView('inventory-locations.view') ||

                        AuthHelper::canView('stock-transfers.view') ||

                        AuthHelper::canView('inventory-reservations.view')

                    ) : ?>


                        <li class="nav-item dropdown dropend">


                            <a
                                class="nav-link dropdown-toggle"
                                href="#"
                                data-bs-toggle="dropdown"
                                aria-expanded="false">

                                <i class="fas fa-warehouse"></i>

                                Inventory

                            </a>


                            <ul class="dropdown-menu">


                                <?php if (AuthHelper::canView('inventory.view')) : ?>

                                    <li>

                                        <a
                                            class="dropdown-item"
                                            href="<?= URLROOT ?>/inventory">

                                            <i class="fas fa-boxes"></i>

                                            Inventory List

                                        </a>

                                    </li>

                                <?php endif; ?>


                                <?php if (AuthHelper::canView('inventory-locations.view')) : ?>

                                    <li>

                                        <a
                                            class="dropdown-item"
                                            href="<?= URLROOT ?>/inventory-locations">

                                            <i class="fas fa-map-marker-alt"></i>

                                            Locations (WareHouse)

                                        </a>

                                    </li>

                                <?php endif; ?>


                                <?php if (AuthHelper::canView('inventory-reservations.view')) : ?>

                                    <li>

                                        <a
                                            class="dropdown-item"
                                            href="<?= URLROOT ?>/inventoryreservations">

                                            <i class="fas fa-lock"></i>

                                            Material Reservations

                                        </a>

                                    </li>

                                <?php endif; ?>


                                <?php if (AuthHelper::canView('inventory-transfers.view')) : ?>

                                    <li>

                                        <a
                                            class="dropdown-item"
                                            href="<?= URLROOT ?>/inventory-transfers">

                                            <i class="fas fa-random"></i>

                                            Stock Transfers

                                        </a>

                                    </li>

                                <?php endif; ?>


                                <?php if (AuthHelper::canView('inventory-movements.view')) : ?>

                                    <li>

                                        <a
                                            class="dropdown-item"
                                            href="<?= URLROOT ?>/inventory-movements">

                                            <i class="fas fa-exchange-alt"></i>

                                            Stock Movements (Report)

                                        </a>

                                    </li>

                                <?php endif; ?>


                            </ul>

                        </li>

                    <?php endif; ?>


                    <!-- =================================================
                     PROCUREMENT
                ================================================== -->

                    <?php if (

                        AuthHelper::canView('purchase-orders.view') ||

                        AuthHelper::canView('suppliers.view')

                    ) : ?>


                        <li class="nav-item dropdown dropend">


                            <a
                                class="nav-link dropdown-toggle"
                                href="#"
                                data-bs-toggle="dropdown"
                                aria-expanded="false">

                                <i class="fas fa-shopping-cart"></i>

                                Procurement

                            </a>


                            <ul class="dropdown-menu">


                                <?php if (AuthHelper::canView('quotation.view')) : ?>

                                    <li>

                                        <a
                                            class="dropdown-item"
                                            href="<?= URLROOT ?>/supplierquotations">

                                            <i class="fas fa-file-invoice-dollar"></i>

                                            Supplier Quotations

                                        </a>

                                    </li>

                                <?php endif; ?>


                                <?php if (AuthHelper::canView('purchase-orders.view')) : ?>

                                    <li>

                                        <a
                                            class="dropdown-item"
                                            href="<?= URLROOT ?>/purchaseorders">

                                            <i class="fas fa-file-invoice"></i>

                                            Purchase Orders

                                        </a>

                                    </li>

                                <?php endif; ?>


                                <?php if (AuthHelper::canView('inventory-movements.create')) : ?>

                                    <li>

                                        <a
                                            class="dropdown-item"
                                            href="<?= URLROOT ?>/inventory-movements/receive">

                                            <i class="fas fa-truck-loading"></i>

                                            Receive Stock (from PO)

                                        </a>

                                    </li>

                                <?php endif; ?>


                                <?php if (AuthHelper::canView('goods-returns.create')) : ?>

                                    <li>

                                        <a
                                            class="dropdown-item"
                                            href="<?= URLROOT ?>/goods-returns/create">

                                            <i class="fas fa-undo-alt"></i>

                                            Return Goods (from Warehouse)

                                        </a>

                                    </li>

                                <?php endif; ?>


                                <?php if (AuthHelper::canView('goods-returns.view')) : ?>

                                    <li>

                                        <a
                                            class="dropdown-item"
                                            href="<?= URLROOT ?>/goods-returns/index">

                                            <i class="fas fa-recycle"></i>

                                            Goods Returns (Report)

                                        </a>

                                    </li>

                                <?php endif; ?>


                                <?php if (AuthHelper::canView('suppliers.view')) : ?>

                                    <li>

                                        <a
                                            class="dropdown-item"
                                            href="<?= URLROOT ?>/suppliers">

                                            <i class="fas fa-truck"></i>

                                            Suppliers

                                        </a>

                                    </li>

                                <?php endif; ?>


                            </ul>

                        </li>

                    <?php endif; ?>


                    <!-- =================================================
                     SERVICES - CURRENTLY DISABLED
                ================================================== -->

                    <!--

                <?php if (AuthHelper::canView('services.view')) : ?>

                    <li class="nav-item">

                        <a
                            class="nav-link"
                            href="<?= URLROOT ?>/services">

                            <i class="fas fa-tools"></i>

                            Services

                        </a>

                    </li>

                <?php endif; ?>

                -->


                    <!-- =================================================
                     FINANCE
                ================================================== -->

                    <?php if (AuthHelper::canView('finance.view')) : ?>


                        <li class="nav-item dropdown dropend">


                            <a
                                class="nav-link dropdown-toggle"
                                href="#"
                                data-bs-toggle="dropdown"
                                aria-expanded="false">

                                <i class="fas fa-coins"></i>

                                Finance

                            </a>


                            <ul class="dropdown-menu">


                                <?php if (AuthHelper::canView('costs.view')) : ?>

                                    <li>

                                        <a
                                            class="dropdown-item"
                                            href="<?= URLROOT ?>/costs">

                                            <i class="fas fa-money-bill-wave"></i>

                                            Projects Cost (Report)

                                        </a>

                                    </li>

                                <?php endif; ?>


                                <?php if (AuthHelper::canView('reports.view')) : ?>

                                    <li>

                                        <a
                                            class="dropdown-item"
                                            href="<?= URLROOT ?>/reports">

                                            <i class="fas fa-chart-line"></i>

                                            Portfolio Dashboard

                                        </a>

                                    </li>

                                <?php endif; ?>


                            </ul>

                        </li>

                    <?php endif; ?>


                    <!-- =================================================
                     USER AREA
                ================================================== -->

                    <div class="sidebar-user">


                        <?php if (isset($_SESSION['user_name'])) : ?>

                            <li class="nav-item">

                                <span class="nav-link text-light">

                                    <i class="fas fa-user-circle"></i>

                                    <?= $_SESSION['user_name'] ?>

                                    (<?= $role_name ?>)

                                </span>

                            </li>

                        <?php endif; ?>


                        <!-- ADMIN PANEL -->

                        <?php if (AuthHelper::canView('admin.access')) : ?>

                            <li class="nav-item">

                                <a
                                    class="nav-link"
                                    href="<?= URLROOT ?>/admin">

                                    <i class="fas fa-user-shield"></i>

                                    Admin Panel

                                </a>

                            </li>

                        <?php endif; ?>


                        <!-- LOGOUT -->

                        <?php if (isset($_SESSION['user_id'])) : ?>

                            <li class="nav-item mt-1">

                                <a
                                    href="<?= URLROOT ?>/auth/logout"
                                    class="btn btn-danger btn-sm sidebar-logout">

                                    <i class="fas fa-sign-out-alt"></i>

                                    Logout

                                </a>

                            </li>

                        <?php endif; ?>


                    </div>


                </ul>


            </div>


        </div>

    </nav>


    <!-- =========================================================
     END OF NAVIGATION
========================================================= -->


    <!-- =========================================================
     COMPANY HEADER
========================================================= -->

    <div class="d-flex align-items-center mt-4 ms-4 company-header">


        <?php if (!empty($settings->logo)): ?>

            <img
                src="<?= URLROOT ?>/<?= $settings->logo ?>"
                style="height:100px; margin-right:10px;">

        <?php endif; ?>


        <div
            style="font-family: 'Tajawal', 'Roboto', sans-serif;">

            <strong class="fs-4">

                <?= htmlspecialchars($settings->company_name) ?>

            </strong>


            <br>


            <i class="fas fa-location-dot"></i>

            <small>

                <?= htmlspecialchars($settings->address) ?>

            </small>


            <br>


            <i class="fas fa-mobile"></i>

            <small>

                <?= htmlspecialchars($settings->contacts) ?>

            </small>


        </div>


    </div>


    <!-- =========================================================
     MAIN CONTENT
========================================================= -->

    <div class="container mt-4">