<?php

class Admin extends Controller
{

    public function __construct()
    {
        AuthHelper::check();

        // ✅ allow admin role directly
        if (($_SESSION['role_id'] ?? 0) != 1) {
            AuthHelper::can('admin.access'); // optional for others
        }
    }

    // 👉 /admin
    public function index()
    {
        $this->view('admin/dashboard');
    }

    // 👉 /admin/users
    public function users()
    {
        AuthHelper::can('users.view');

        $userModel = $this->model('User');
        $data['users'] = $userModel->getAllUsers();

        $this->view('admin/users/index', $data);
    }

    // 👉 /admin/roles
    public function roles()
    {
        $roleModel = $this->model('Role');

        // ✅ HANDLE CREATE ROLE
        if ($_POST) {
            if (!empty($_POST['name'])) {
                $roleModel->create($_POST['name']);
            }

            header('Location: ' . URLROOT . '/admin/roles');
            exit;
        }

        // ✅ FIRST: load roles
        $data['roles'] = $roleModel->getAll();

        // 🔥 SECOND: attach permissions to each role
        foreach ($data['roles'] as &$role) {

            $perms = $roleModel->getPermissionsNames($role->id);

            $role->permissions = array_map(function ($p) {
                return $p->name;
            }, $perms);
        }

        // ✅ finally send to view
        $this->view('admin/roles/index', $data);
    }

    // 👉 /admin/permissions
    public function permissions()
    {
        AuthHelper::can('admin.access'); // or remove if not ready

        $permModel = $this->model('Permission');

        // ✅ HANDLE FORM SUBMIT
        if ($_POST) {
            if (!empty($_POST['name'])) {
                $permModel->create($_POST['name']);
            }

            header('Location: ' . URLROOT . '/admin/permissions');
            exit;
        }

        // ✅ LOAD DATA
        $data['permissions'] = $permModel->getAll();

        $this->view('admin/permissions/index', $data);
    }

    // Create User
    public function createUser()
    {
        AuthHelper::can('users.create');

        $userModel     = $this->model('User');
        $roleModel     = $this->model('Role');
        $locationModel = $this->model('InventoryLocationStock');

        if ($_POST) {

            $locations = $_POST['locations'] ?? [];
            $default   = $_POST['default_location_id'] ?? null;

            if (
                $default &&
                !in_array($default, $locations)
            ) {
                die('Default warehouse must be one of the assigned warehouses');
            }

            $user_id = $userModel->createUser($_POST);

            $userModel->saveLocations(
                $user_id,
                $locations
            );

            header('Location: ' . URLROOT . '/admin/users');
            exit;
        }

        $data['roles'] = $roleModel->getAll();

        $data['locations'] =
            $locationModel->getLocations();

        $this->view(
            'admin/users/create',
            $data
        );
    }
  
    public function editUser($id)
{
    AuthHelper::can('users.edit');

    $userModel     = $this->model('User');
    $roleModel     = $this->model('Role');
    $locationModel = $this->model('InventoryLocationStock');

    $data['locations'] = $locationModel->getLocations();
    $data['assigned_locations'] = $userModel->getLocationIds($id);

    $data['user'] = $userModel->getById($id);
    $data['roles'] = $roleModel->getAll();

    if (!$data['user']) {

        FlashHelper::error('User not found');

        header(
            'Location: ' . URLROOT . '/admin/users'
        );
        exit;
    }

    // =========================
    // POST HANDLER
    // =========================
    if ($_POST) {

        $locations =
            $_POST['locations'] ?? [];

        $default =
            $_POST['default_location_id'] ?? null;

        if (
            $default !== null &&
            !in_array($default, $locations)
        ) {

            FlashHelper::error(
                'Default warehouse must be one of the assigned warehouses.'
            );

            header(
                'Location: ' . URLROOT . '/admin/editUser/' . $id
            );
            exit;
        }

        $updateData = [
            'name' => $_POST['name'],
            'email' => $_POST['email'],
            'role_id' => $_POST['role_id'],
            'default_location_id' =>
                $_POST['default_location_id'] ?? null
        ];

        if (!empty($_POST['password'])) {

            $updateData['password'] =
                password_hash(
                    $_POST['password'],
                    PASSWORD_DEFAULT
                );
        }

        $userModel->update(
            $id,
            $updateData
        );

        $userModel->saveLocations(
            $id,
            $locations
        );

        FlashHelper::success(
            'User updated successfully.'
        );

        header(
            'Location: ' . URLROOT . '/admin/users'
        );
        exit;
    }

    $this->view(
        'admin/users/edit',
        $data
    );
}

 public function assignPermissions($role_id)
{
    $roleModel = $this->model('Role');
    $permModel = $this->model('Permission');

    // SAVE permissions (POST)
    if ($_POST) {

        $roleModel->clearPermissions($role_id);

        if (!empty($_POST['permissions'])) {
            foreach ($_POST['permissions'] as $perm_id) {
                $roleModel->assignPermission($role_id, $perm_id);
            }
        }

        header('Location: ' . URLROOT . '/admin/roles');
        exit;
    }

    // LOAD ROLE DETAILS
    $data['role'] = $roleModel->getById($role_id);

    // LOAD ALL PERMISSIONS
    $data['permissions'] = $permModel->getAll();

    // LOAD CURRENT ASSIGNED PERMISSIONS
    $assigned = $roleModel->getPermissions($role_id);

    $data['assigned'] = array_map(function ($item) {
        return $item->permission_id;
    }, $assigned);

    $data['role_id'] = $role_id;

    $this->view('admin/roles/assign_permissions', $data);
}

    // Edit Permissions
    public function editPermission($id)
    {
        $permModel = $this->model('Permission');

        if ($_POST) {
            $permModel->update($id, $_POST['name']);

            header('Location: ' . URLROOT . '/admin/permissions');
            exit;
        }

        $data['permission'] = $permModel->getById($id);

        $this->view('admin/permissions/edit', $data);
    }

    public function deletePermission($id)
    {
        $permModel = $this->model('Permission');
        $permModel->delete($id);

        header('Location: ' . URLROOT . '/admin/permissions');
        exit;
    }

    // Edit Roles
    public function editRole($id)
    {
        $roleModel = $this->model('Role');

        if ($_POST) {
            $roleModel->update($id, $_POST['name']);

            header('Location: ' . URLROOT . '/admin/roles');
            exit;
        }

        $data['role'] = $roleModel->getById($id);

        $this->view('admin/roles/edit', $data);
    }

    public function deleteRole($id)
    {
        $roleModel = $this->model('Role');
        $roleModel->delete($id);

        header('Location: ' . URLROOT . '/admin/roles');
        exit;
    }

    // company profile
     public function settings()
{
    $settingsModel = $this->model('Settings');

    $data['settings'] = $settingsModel->get();

    $this->view('admin/settings/index', $data);
}

    public function saveSettings()
    {
        $settingsModel = $this->model('Settings');

        $logoPath = null;

        if (!empty($_FILES['logo']['name'])) {
            $file = time() . '_' . $_FILES['logo']['name'];
            $path = 'uploads/' . $file;
            move_uploaded_file($_FILES['logo']['tmp_name'], $path);
            $logoPath = $path;
        }

        $settingsModel->save([
            'company_name' => $_POST['company_name'],
            'address'      => $_POST['address'],
            'contacts'     => $_POST['contacts'],
            'logo'         => $logoPath
        ]);

        FlashHelper::success("Settings updated");

        header("Location: " . URLROOT . "/admin/settings");
        exit;
    }
}

