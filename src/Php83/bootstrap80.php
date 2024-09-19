<?php

if (extension_loaded('mbstring')) {
    if (!function_exists('mb_str_pad')) {
        function mb_str_pad(string $string, int $length, string $pad_string = ' ', int $pad_type = STR_PAD_RIGHT, ?string $encoding = null): string { return p\Php83::mb_str_pad($string, $length, $pad_string, $pad_type, $encoding); }
    }
}

if (\PHP_VERSION_ID >= 80100) {
    return require __DIR__.'/bootstrap81.php';
}

if (!function_exists('ldap_exop_sync') && function_exists('ldap_exop')) {
    function ldap_exop_sync($ldap, string $request_oid, ?string $request_data = null, ?array $controls = null, &$response_data = null, &$response_oid = null): bool { return ldap_exop($ldap, $request_oid, $request_data, $controls, $response_data, $response_oid); }
}

if (!function_exists('ldap_connect_wallet') && function_exists('ldap_connect')) {
    function ldap_connect_wallet(?string $uri, string $wallet, string $password, int $auth_mode = \GSLC_SSL_NO_AUTH) { return ldap_connect($uri, $wallet, $password, $auth_mode); }
}
