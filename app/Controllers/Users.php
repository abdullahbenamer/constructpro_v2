<?php
class Users extends Controller
{

    public function index()
    {
        AuthHelper::can('users.view'); // ✅ permission check

        $userModel = $this->model('User');
        $data['users'] = $userModel->getAllUsers();

        $this->view('users/index', $data);
    }

    public function create()
    {
        AuthHelper::can('users.create');

        $userModel = $this->model('User');
        $roleModel = $this->model('Role');
        $locationModel = $this->model('InventoryLocationStock');

        if ($_POST) {
  if (empty(trim($_POST['full_name']))) {
            die('Full Name is required');
            
            $locations = $_POST['locations'] ?? [];
            $default   = $_POST['default_location_id'] ?? null;

            if ($default && !in_array($default, $locations)) {
                die("Default warehouse must be one of the allowed warehouses");
            }

            $user_id = $userModel->createUser($_POST);

            $userModel->saveLocations($user_id, $locations);

            header('Location: ' . URLROOT . '/users');
            exit;
        }

        $data['roles'] = $roleModel->getAll();
        $data['locations'] = $locationModel->getLocations();

        $this->view('users/create', $data);
    }
    }
    public function edit($id)
    {
        AuthHelper::can('users.edit');

        $userModel = $this->model('User');

        if ($_POST) {

            if (!empty($_POST['password'])) {

                $_POST['password'] =
                    password_hash(
                        $_POST['password'],
                        PASSWORD_DEFAULT
                    );
            } else {
                unset($_POST['password']);
            }

            $userModel->update($id, $_POST);

            $locations = $_POST['locations'] ?? [];

            $default = $_POST['default_location_id'] ?? null;

            if ($default && !in_array($default, $locations)) {

                die("Default warehouse must be one of the allowed warehouses");
            }

            $userModel->saveLocations(
                $id,
                $_POST['locations'] ?? []
            );

            header('Location: ' . URLROOT . '/users');
            exit;
        }

        $item = $userModel->getById($id);

        if (!$item) {
            header('Location: ' . URLROOT . '/users');
            exit;
        }

        $locationModel =
            $this->model('InventoryLocationStock');

        $data['user'] = $item;
        $data['locations'] = $locationModel->getLocations();
        $data['assigned_locations'] = $userModel->getLocationIds($id);

        $this->view('users/edit', $data);
    }

    public function delete($id)
    {
        AuthHelper::can('users.delete');

        $userModel = $this->model('User');

        $user = $userModel->getById($id);

        if (!$user) {

            header('Location: ' . URLROOT . '/users');
            exit;
        }

        // =====================================
        // PREVENT SELF DELETE
        // =====================================

        if ($user->id == $_SESSION['user_id']) {

            die("❌ You cannot delete your own account");
        }

        $role_name = $userModel->getRoleName($user->role_id);

        // =====================================
        // PREVENT DELETING LAST ADMIN
        // =====================================

        if ($role_name === 'ADMIN') {

            $admin_count = $userModel->countAdmins();

            if ($admin_count <= 1) {

                die("❌ Cannot delete the last admin account");
            }
        }

        // =====================================
        // DELETE USER
        // =====================================

        $userModel->delete($id);

        header('Location: ' . URLROOT . '/users');
        exit;
    }

    public function profile($id)
    {
        AuthHelper::can('users.view');

        $userModel = $this->model('User');

        $user = $userModel->getById($id);

        if (!$user) {

            header('Location: ' . URLROOT . '/users');
            exit;
        }

        $data['user'] = $user;

        $this->view('users/profile', $data);
    }

   public function details($id)
{
    AuthHelper::can('users.view');

    $userModel = $this->model('User');

    $user = $userModel->getUserById((int)$id);

    if (!$user) {
        header("Location: " . URLROOT . "/users");
        exit;
    }

    $data = [
        'user' => $user
    ];

    $this->view('users/details', $data);
}
}

