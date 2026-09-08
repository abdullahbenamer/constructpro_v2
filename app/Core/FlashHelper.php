<?php
// Alert Messages helper
class FlashHelper
{
    public static function error($msg)
    {
        $_SESSION['error'] = $msg;
    }

    public static function success($msg)
    {
        $_SESSION['success'] = $msg;
    }

    public static function warning($msg)
    {
        $_SESSION['warning'] = $msg;
    }
}