<?php

class AuthHelper
{
    public static function check()
    {
        if (!isset($_SESSION['user_id'])) {

            FlashHelper::error('Please login first');

            header('Location: ' . URLROOT . '/auth/login');
            exit;
        }

        return true;
    }

    public static function role($roles = [])
    {
        self::check();

        // Super Admin
        if (($_SESSION['role_id'] ?? 0) == 1) {
            return true;
        }

        if (!in_array($_SESSION['role'], $roles)) {

            FlashHelper::error('Access Denied');

            header('Location: ' . URLROOT . '/dashboard');
            exit;
        }

        return true;
    }

    public static function can($permission)
    {
        self::check();

        // Super Admin bypass
        if (($_SESSION['role_id'] ?? 0) == 1) {
            return true;
        }

        $db = new Database();

        $result = $db->query(
            "
            SELECT 1
            FROM role_permissions rp
            JOIN permissions p
                ON p.id = rp.permission_id
            WHERE rp.role_id = ?
            AND p.name = ?
            ",
            [
                $_SESSION['role_id'],
                $permission
            ]
        )->fetch();

        if (!$result) {

            FlashHelper::error(
                'Sorry, You do not have permission to perform this action'
            );

            header('Location: ' . URLROOT . '/dashboard');
            exit;
        }

        return true;
    }

    public static function canView($permission)
    {
        if (!isset($_SESSION['user_id'])) {
            return false;
        }

        if (($_SESSION['role_id'] ?? 0) == 1) {
            return true;
        }

        $db = new Database();

        $result = $db->query(
            "
            SELECT 1
            FROM role_permissions rp
            JOIN permissions p
                ON p.id = rp.permission_id
            WHERE rp.role_id = ?
            AND p.name = ?
            ",
            [
                $_SESSION['role_id'],
                $permission
            ]
        )->fetch();

        return (bool)$result;
    }
}