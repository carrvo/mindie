<?php
# SelfAuth MIT - `error_page` and `$configfile` parts copied
# MIndie-IdP MIT - `error_page` and `$configfile` parts copied

function error_page($header, $body, $http = '400 Bad Request')
{
    $protocol = isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0';
    header($protocol . ' ' . $http);
    $html = <<<HTML
<!doctype html>
<html>
    <head>
        <style>
            .error{
                width:100%;
                text-align:center;
                margin-top:10%;
            }
        </style>
        <title>Error: $header</title>
    </head>
    <body>
        <div class='error'>
            <h1>Error: $header</h1>
            <p>$body</p>
        </div>
    </body>
</html>
HTML;
    die($html);
}

$configdir = getenv('SELFAUTH_CONFIG');
if (empty($configdir)) {
    error_page(
        'Configuration Error',
        'Endpoint not yet configured, visit <a href="' . getenv('SELFAUTH_SETUP_PATH') . '">setup</a> for instructions on how to set it up.'
    );
}
if (getenv('SELFAUTH_MULTIUSER')) {
    $userfile = '.php';
    if (isset($_SERVER['PHP_AUTH_USER'])) {
        $app_user = rawurlencode($_SERVER['PHP_AUTH_USER']);
        $userfile = "config_$app_user.php";
    }
}
else {
    $userfile = 'config.php';
}
$configfile = $configdir . '/' . $userfile;

unlink($configfile);

header('Location: '.getenv('SELFAUTH_SETUP_PATH'));
?>
