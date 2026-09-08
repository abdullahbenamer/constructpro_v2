<div class="container-fluid mt-4">


    <!-- PAGE HEADER -->

    <div class="d-flex justify-content-between align-items-center mb-3">


        <div>

            <h4 class="mb-0">

                <i class="fas fa-layer-group"></i>

                Resource Categories

            </h4>


            <small class="text-muted">

                Manage resource classification

            </small>


        </div>



        <a href="<?= URLROOT ?>/ResourceCategories/create"
           class="btn btn-primary">


            <i class="fas fa-plus"></i>

            New Category


        </a>


    </div>





    <div class="card shadow-sm">


        <div class="card-body">


            <div class="table-responsive">


                <table class="table table-bordered table-hover align-middle">


                    <thead class="table-light">


                        <tr>

                            <th width="120">
                                Code
                            </th>

                            <th>
                                Category Name
                            </th>

                            <th>
                                Arabic Name
                            </th>

                            <th width="120">
                                Status
                            </th>

                            <th width="150">
                                Actions
                            </th>

                        </tr>


                    </thead>



                    <tbody>


                    <?php if (!empty($data['categories'])): ?>


                        <?php foreach($data['categories'] as $category): ?>


                            <tr>


                                <td>

                                    <strong>
                                        <?= $category->category_code ?>
                                    </strong>

                                </td>


                                <td>

                                    <?= $category->category_name ?>

                                </td>


                                <td>

                                    <?= $category->category_name_a ?: '-' ?>

                                </td>


                                <td>


                                    <?php if($category->status == 'ACTIVE'): ?>


                                        <span class="badge bg-success">
                                            ACTIVE
                                        </span>


                                    <?php else: ?>


                                        <span class="badge bg-secondary">
                                            INACTIVE
                                        </span>


                                    <?php endif; ?>


                                </td>



                                <td>


                                    <a href="<?= URLROOT ?>/ResourceCategories/edit/<?= $category->id ?>"
                                       class="btn btn-sm btn-warning">


                                        <i class="fas fa-edit"></i>


                                    </a>



                                    <a href="<?= URLROOT ?>/ResourceCategories/delete/<?= $category->id ?>"
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('Delete this category?');">


                                        <i class="fas fa-trash"></i>


                                    </a>


                                </td>


                            </tr>


                        <?php endforeach; ?>


                    <?php else: ?>


                        <tr>

                            <td colspan="5"
                                class="text-center text-muted">

                                No categories found.

                            </td>

                        </tr>


                    <?php endif; ?>


                    </tbody>


                </table>


            </div>


        </div>


    </div>


</div>