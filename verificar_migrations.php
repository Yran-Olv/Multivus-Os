<?php
/**
 * Script para verificar status das migrations
 * Execute: php verificar_migrations.php
 */

// Carregar configuração do CodeIgniter
define('BASEPATH', true);
require_once 'application/config/database.php';

// Conectar ao banco
$db = $db['default'];
$conn = new mysqli($db['hostname'], $db['username'], $db['password'], $db['database']);

if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

// Verificar se a tabela migrations existe
$result = $conn->query("SHOW TABLES LIKE 'migrations'");
if ($result->num_rows == 0) {
    echo "❌ Tabela 'migrations' não existe. As migrations ainda não foram executadas.\n";
    echo "Execute: php index.php tools migrate\n\n";
    exit;
}

// Buscar migrations executadas
$executadas = [];
$result = $conn->query("SELECT version FROM migrations ORDER BY version");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $executadas[] = $row['version'];
    }
}

// Listar todas as migrations disponíveis
$migrations_dir = 'application/database/migrations/';
$migrations_disponiveis = [];
if (is_dir($migrations_dir)) {
    $files = scandir($migrations_dir);
    foreach ($files as $file) {
        if (preg_match('/^(\d+)_(.+)\.php$/', $file, $matches)) {
            $migrations_disponiveis[] = [
                'version' => $matches[1],
                'file' => $file,
                'name' => $matches[2]
            ];
        }
    }
}

// Ordenar por versão
usort($migrations_disponiveis, function($a, $b) {
    return strcmp($a['version'], $b['version']);
});

echo "═══════════════════════════════════════════════════════════════\n";
echo "  STATUS DAS MIGRATIONS\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

$pendentes = [];
$total = count($migrations_disponiveis);
$executadas_count = count($executadas);

foreach ($migrations_disponiveis as $migration) {
    $status = in_array($migration['version'], $executadas) ? '✅' : '❌';
    $status_text = in_array($migration['version'], $executadas) ? 'EXECUTADA' : 'PENDENTE';
    
    echo sprintf("%s [%s] %s - %s\n", 
        $status, 
        $migration['version'], 
        $status_text,
        $migration['name']
    );
    
    if (!in_array($migration['version'], $executadas)) {
        $pendentes[] = $migration;
    }
}

echo "\n═══════════════════════════════════════════════════════════════\n";
echo "  RESUMO\n";
echo "═══════════════════════════════════════════════════════════════\n";
echo "Total de migrations: $total\n";
echo "Executadas: $executadas_count\n";
echo "Pendentes: " . count($pendentes) . "\n\n";

if (count($pendentes) > 0) {
    echo "⚠️  MIGRATIONS PENDENTES:\n";
    foreach ($pendentes as $migration) {
        echo "   - {$migration['file']}\n";
    }
    echo "\nPara executar as migrations pendentes, execute:\n";
    echo "   php index.php tools migrate\n";
} else {
    echo "✅ Todas as migrations foram executadas!\n";
}

$conn->close();
