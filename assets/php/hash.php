<?php
class password
{
    public static function hash($password)
    {
        return password_hash($password, PASSWORD_DEFAULT, ['cost' => 15]);
    }
    public static function verify($password, $hash)
    {
        return password_verify($password, $hash);
    }
}
?>
