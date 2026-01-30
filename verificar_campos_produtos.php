<?php
/**
 * Script para verificar se todos os campos de produtos estão sendo salvos corretamente
 * Execute via: php verificar_campos_produtos.php
 */

// Carregar CodeIgniter
define('BASEPATH', true);
require_once __DIR__ . '/index.php';

// Conectar ao banco de dados
$config = require __DIR__ . '/application/config/database.php';
$db_config = $config['default'];

try {
    $pdo = new PDO(
        "mysql:host={$db_config['hostname']};dbname={$db_config['database']};charset={$db_config['char_set']}",
        $db_config['username'],
        $db_config['password']
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== VERIFICAÇÃO DE CAMPOS DE PRODUTOS ===\n\n";
    
    // Verificar se a tabela produtos existe
    $stmt = $pdo->query("SHOW TABLES LIKE 'produtos'");
    if ($stmt->rowCount() == 0) {
        echo "❌ ERRO: Tabela 'produtos' não existe!\n";
        exit(1);
    }
    echo "✅ Tabela 'produtos' existe\n\n";
    
    // Listar todos os campos da tabela produtos
    echo "=== CAMPOS DA TABELA PRODUTOS ===\n";
    $stmt = $pdo->query("DESCRIBE produtos");
    $campos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $campos_esperados = [
        'idProdutos',
        'imagem',
        'nome',
        'codDeBarra',
        'descricao',
        'descricao_completa',
        'processador',
        'memoria_ram',
        'armazenamento',
        'tela',
        'sistema_operacional',
        'cor',
        'marca',
        'modelo',
        'unidade',
        'precoCompra',
        'precoVenda',
        'estoque',
        'estoqueMinimo',
        'saida',
        'entrada'
    ];
    
    $campos_encontrados = [];
    foreach ($campos as $campo) {
        $campos_encontrados[] = $campo['Field'];
        echo sprintf("  - %s (%s)\n", $campo['Field'], $campo['Type']);
    }
    
    echo "\n=== VERIFICAÇÃO DE CAMPOS ESPERADOS ===\n";
    $campos_faltando = [];
    foreach ($campos_esperados as $campo_esperado) {
        if (in_array($campo_esperado, $campos_encontrados)) {
            echo "✅ Campo '{$campo_esperado}' existe\n";
        } else {
            echo "❌ Campo '{$campo_esperado}' NÃO existe\n";
            $campos_faltando[] = $campo_esperado;
        }
    }
    
    if (!empty($campos_faltando)) {
        echo "\n⚠️  ATENÇÃO: Os seguintes campos estão faltando:\n";
        foreach ($campos_faltando as $campo) {
            echo "   - {$campo}\n";
        }
        echo "\n💡 Execute a migration para adicionar os campos faltantes:\n";
        echo "   php index.php tools migrate\n";
    } else {
        echo "\n✅ Todos os campos esperados existem na tabela!\n";
    }
    
    // Verificar um produto de exemplo (se existir)
    echo "\n=== VERIFICAÇÃO DE DADOS DE EXEMPLO ===\n";
    $stmt = $pdo->query("SELECT * FROM produtos LIMIT 1");
    $produto = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($produto) {
        echo "Produto encontrado (ID: {$produto['idProdutos']}):\n";
        foreach ($campos_esperados as $campo) {
            if (isset($produto[$campo])) {
                $valor = $produto[$campo];
                if ($valor === null || $valor === '') {
                    echo "  - {$campo}: (vazio/null)\n";
                } else {
                    $valor_truncado = mb_substr($valor, 0, 50);
                    echo "  - {$campo}: {$valor_truncado}\n";
                }
            }
        }
    } else {
        echo "Nenhum produto encontrado na tabela.\n";
    }
    
    echo "\n=== VERIFICAÇÃO CONCLUÍDA ===\n";
    
} catch (PDOException $e) {
    echo "❌ ERRO ao conectar ao banco de dados: " . $e->getMessage() . "\n";
    exit(1);
}
