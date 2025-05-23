<?php
class PasswordGenerator {
    public static function generate($params) {
        $charsets = [
            'lower' => 'abcdefghijklmnopqrstuvwxyz',
            'upper' => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
            'numbers' => '0123456789',
            'special' => '!@#$%^&*()_+-=[]{}|;:,.<>?'
        ];

        $password = '';
        foreach ($params as $type => $count) {
            if ($count > 0 && isset($charsets[$type])) {
                $chars = str_shuffle($charsets[$type]);
                $password .= substr($chars, 0, $count);
            }
        }
        
        return str_shuffle($password);
    }
}
?>