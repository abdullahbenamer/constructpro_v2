<div class="container-fluid mt-4">

    <div class="d-flex justify-content-between align-items-center mb-3">

        <div>

            <h4 class="mb-0">

                <i class="fas fa-plus-circle"></i>
                New Unit

            </h4>

            <small class="text-muted">

                Create a Unit of Measure

            </small>

        </div>

        <a href="<?= URLROOT ?>/Units"
           class="btn btn-secondary">

            <i class="fas fa-arrow-left"></i>
            Back

        </a>

    </div>


    <div class="card shadow-sm">

        <div class="card-body">

            <form method="POST"
                  action="<?= URLROOT ?>/Units/store">

                <div class="row">

                    <div class="col-md-3 mb-3">

                        <label class="form-label">

                            Unit Code

                        </label>

                        <input type="text"
                               name="unit_code"
                               class="form-control"
                               required>

                    </div>

                    <div class="col-md-5 mb-3">

                        <label class="form-label">

                            Unit Name

                        </label>

                        <input type="text"
                               name="unit_name"
                               class="form-control"
                               required>

                    </div>

                    <div class="col-md-4 mb-3">

                        <label class="form-label">

                            Arabic Name

                        </label>

                        <input type="text"
                               name="unit_name_a"
                               class="form-control">

                    </div>

                </div>


                <div class="mb-3">

                    <label class="form-label">

                        Description

                    </label>

                    <textarea name="description"
                              class="form-control"
                              rows="4"></textarea>

                </div>


                <div class="row">

                    <div class="col-md-3">

                        <label class="form-label">

                            Status

                        </label>

                        <select name="status"
                                class="form-select">

                            <option value="ACTIVE">

                                ACTIVE

                            </option>

                            <option value="INACTIVE">

                                INACTIVE

                            </option>

                        </select>

                    </div>

                </div>


                <hr>

                <button class="btn btn-primary">

                    <i class="fas fa-save"></i>
                    Save Unit

                </button>

            </form>

        </div>

    </div>

</div>