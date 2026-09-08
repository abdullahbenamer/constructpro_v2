<?php
class Auth extends Controller
{

    public function login()
    {
        // already logged in → redirect
        if (isset($_SESSION['user_id'])) {
            header('Location: ' . URLROOT . '/dashboard');
            exit;
        }

        if ($_POST) {

            $userModel = $this->model('User');

            $result = $userModel->login($_POST['email'], $_POST['password']);

            if ($result) {

                // redirect based on role
                switch ($_SESSION['role']) {

                    case 'admin':
                        header('Location: ' . URLROOT . '/admin/dashboard');
                        break;

                    case 'manager':
                        header('Location: ' . URLROOT . '/manager/dashboard');
                        break;

                    case 'engineer':
                        header('Location: ' . URLROOT . '/engineer/dashboard');
                        break;

                    case 'technician':
                        header('Location: ' . URLROOT . '/technician/dashboard');
                        break;

                    default:
                        header('Location: ' . URLROOT . '/dashboard');
                }

                exit;
            } else {
                $data['error'] = "Invalid email or password";
            }
        }

        $this->view('auth/login', $data ?? []);
    }

    public function logout()
    {
        session_destroy();
        header('Location: ' . URLROOT . '/auth/login');
        exit;
    }
}
