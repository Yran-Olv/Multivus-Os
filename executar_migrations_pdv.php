<?php
/**
 * Script para executar as migrations do PDV
 * 
 * Acesse via: http://localhost:8000/executar_migrations_pdv.php?execute=1
 * 
 * OU use a interface web: /index.php/mapos/configurar > Aba "Atualização" > Botão "Banco de Dados"
 */

// Verificar se está sendo executado via web
if (!isset($_GET['execute'])) {
    ?>
    <!DOCTYPE html>
    <html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Executar Migrations do PDV</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                max-width: 800px;
                margin: 50px auto;
                padding: 20px;
                background: #f5f5f5;
            }
            .container {
                background: white;
                padding: 30px;
                border-radius: 8px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            }
            h1 {
                color: #333;
                border-bottom: 3px solid #4CAF50;
                padding-bottom: 10px;
            }
            .warning {
                background: #fff3cd;
                border: 1px solid #ffc107;
                color: #856404;
                padding: 15px;
                border-radius: 4px;
                margin: 20px 0;
            }
            .info {
                background: #d1ecf1;
                border: 1px solid #bee5eb;
                color: #0c5460;
                padding: 15px;
                border-radius: 4px;
                margin: 20px 0;
            }
            .btn {
                display: inline-block;
                padding: 12px 24px;
                background: #4CAF50;
                color: white;
                text-decoration: none;
                border-radius: 4px;
                margin: 10px 5px;
                border: none;
                cursor: pointer;
                font-size: 16px;
            }
            .btn:hover {
                background: #45a049;
            }
            .btn-danger {
                background: #f44336;
            }
            .btn-danger:hover {
                background: #da190b;
            }
            .result {
                margin-top: 20px;
                padding: 15px;
                border-radius: 4px;
            }
            .success {
                background: #d4edda;
                border: 1px solid #c3e6cb;
                color: #155724;
            }
            .error {
                background: #f8d7da;
                border: 1px solid #f5c6cb;
                color: #721c24;
            }
            code {
                background: #f4f4f4;
                padding: 2px 6px;
                border-radius: 3px;
                font-family: monospace;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>🚀 Executar Migrations do PDV</h1>
            
            <div class="warning">
                <strong>⚠️ Atenção:</strong> Este script executará as migrations do PDV. 
                Certifique-se de ter feito backup do banco de dados antes de continuar.
            </div>
            
            <div class="info">
                <strong>ℹ️ Informação:</strong> Este script executará todas as migrations pendentes, 
                incluindo a criação das tabelas do PDV:
                <ul>
                    <li><code>formas_pagamento</code></li>
                    <li><code>pagamentos_venda</code></li>
                    <li><code>caixas</code></li>
                    <li><code>turnos_caixa</code></li>
                    <li><code>cancelamentos_venda</code></li>
                    <li><code>cupons_desconto</code></li>
                </ul>
            </div>
            
            <div style="text-align: center; margin: 30px 0;">
                <a href="?execute=1" class="btn" onclick="return confirm('Tem certeza que deseja executar as migrations? Certifique-se de ter feito backup!');">
                    ▶️ Executar Migrations
                </a>
                <a href="index.php/mapos/configurar" class="btn btn-danger">
                    ❌ Cancelar
                </a>
            </div>
            
            <div class="info" style="margin-top: 30px;">
                <strong>💡 Dica:</strong> Você também pode executar as migrations através da interface web:
                <ol>
                    <li>Acesse <code>/index.php/mapos/configurar</code></li>
                    <li>Clique na aba "Atualização"</li>
                    <li>Clique no botão "Banco de Dados"</li>
                    <li>Confirme a atualização</li>
                </ol>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Se chegou aqui, está executando as migrations
// Redirecionar para o método do Mapos que executa as migrations
header('Location: ' . (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/index.php/mapos/atualizarBanco');
exit;
