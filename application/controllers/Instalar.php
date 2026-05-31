<?php
defined('BASEPATH') OR exit('Acesso directo ao script não permitido');

/**
 * Instalar — Assistente de instalação de 1 clique.
 * ----------------------------------------------------------------------
 * Permite que quem recebe o código ponha a aplicação a funcionar sem ter
 * de mexer no phpMyAdmin: cria a base de dados, as tabelas (schema.sql) e
 * as contas/dados de demonstração (seed.sql) directamente pelo navegador.
 *
 * Usa uma ligação mysqli própria (independente do CodeIgniter) para
 * funcionar mesmo quando a base de dados ainda não existe. A codificação
 * é forçada para utf8mb4, garantindo acentuação correcta.
 *
 * Por ser uma ferramenta de configuração, só funciona fora de produção.
 */
class Instalar extends MY_Controller
{
    private $host;
    private $user;
    private $pass;
    private $port;
    private $nome_bd;

    public function __construct()
    {
        parent::__construct();
        $this->host    = getenv('DB_HOST') ?: 'localhost';
        $this->user    = getenv('DB_USER') ?: 'root';
        $this->pass    = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';
        $this->port    = (int) (getenv('DB_PORT') ?: 3306);
        $this->nome_bd = getenv('DB_NAME') ?: 'delivery_mz';
    }

    public function index()
    {
        $estado = $this->_diagnostico();
        $this->render('instalar', ['estado' => $estado, 'nome_bd' => $this->nome_bd], 'Instalação');
    }

    public function executar()
    {
        if (ENVIRONMENT === 'production') {
            show_error('O instalador está desactivado em produção.', 403, 'Indisponível');
        }

        $erros = [];

        // 1) Ligar ao servidor MySQL (sem seleccionar base de dados).
        mysqli_report(MYSQLI_REPORT_OFF);
        $conn = @mysqli_connect($this->host, $this->user, $this->pass, '', $this->port);
        if (!$conn) {
            $this->session->set_flashdata('erro', 'Não foi possível ligar ao MySQL: ' . mysqli_connect_error()
                . ' — verifique as credenciais em application/config/database.php (ou as variáveis DB_USER/DB_PASS).');
            return redirect('instalar');
        }
        $conn->set_charset('utf8mb4');

        // 2) Criar a base de dados, se necessário.
        $bd = $conn->real_escape_string($this->nome_bd);
        if (!$conn->query("CREATE DATABASE IF NOT EXISTS `{$bd}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci")) {
            $erros[] = 'Criação da base de dados: ' . $conn->error;
        }
        $conn->select_db($this->nome_bd);

        // 3) Executar o schema e depois o seed.
        $erros = array_merge($erros, $this->_executar_ficheiro($conn, APPPATH . 'sql/schema.sql'));
        $erros = array_merge($erros, $this->_executar_ficheiro($conn, APPPATH . 'sql/seed.sql'));
        $conn->close();

        if (empty($erros)) {
            $this->session->set_flashdata('sucesso', 'Instalação concluída! Base de dados, tabelas e dados de demonstração criados.');
        } else {
            $this->session->set_flashdata('erro', 'Instalação terminada com avisos: ' . implode(' | ', $erros));
        }
        redirect('instalar');
    }

    // ----------------------------------------------------- Auxiliares

    /** Diagnóstico do estado actual (ligação, base de dados, dados). */
    private function _diagnostico()
    {
        mysqli_report(MYSQLI_REPORT_OFF);
        $r = ['ligado' => false, 'bd_existe' => false, 'instalado' => false, 'contas' => 0, 'mensagem' => ''];

        $conn = @mysqli_connect($this->host, $this->user, $this->pass, '', $this->port);
        if (!$conn) {
            $r['mensagem'] = mysqli_connect_error();
            return $r;
        }
        $r['ligado'] = true;
        $conn->set_charset('utf8mb4');

        $bd = $conn->real_escape_string($this->nome_bd);
        $res = $conn->query("SELECT SCHEMA_NAME FROM information_schema.SCHEMATA WHERE SCHEMA_NAME='{$bd}'");
        $r['bd_existe'] = $res && $res->num_rows > 0;

        if ($r['bd_existe'] && $conn->select_db($this->nome_bd)) {
            $res = $conn->query("SELECT COUNT(*) AS n FROM usuarios");
            if ($res) {
                $r['instalado'] = true;
                $r['contas'] = (int) $res->fetch_assoc()['n'];
            }
        }
        $conn->close();
        return $r;
    }

    /** Executa todas as instruções de um ficheiro .sql via multi_query. */
    private function _executar_ficheiro($conn, $caminho)
    {
        $erros = [];
        if (!is_file($caminho)) {
            return ['Ficheiro não encontrado: ' . basename($caminho)];
        }
        $sql = file_get_contents($caminho);

        if ($conn->multi_query($sql)) {
            do {
                if ($res = $conn->store_result()) {
                    $res->free();
                }
            } while ($conn->more_results() && $conn->next_result());
        }
        if ($conn->errno) {
            $erros[] = basename($caminho) . ': ' . $conn->error;
        }
        return $erros;
    }
}
