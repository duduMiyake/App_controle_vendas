<?php
//classe dashboard
class Dashboard
{
    public $data_inicio;
    public $data_fim;
    public $numero_vendas;
    public $total_vendas;
    public $clientes_ativos;
    public $clientes_inativos;
    public $total_reclamacoes;
    public $total_elogios;
    public $total_sugestoes;
    public $total_despesas;

    public function __get($atribute) {
        return $this->$atribute;  
    }

    public function __set($atribute, $valor) {
        $this->$atribute = $valor; 
        return $this; 
    }   
}

//classe conexao com o BD
class Conexao {
    private $host = 'host';
    private $dbname = 'dbname';
    private $user = 'user';
    private $password = 'password';

    public function conectar() {
        try {
            
            $conexao = new PDO (
                "mysql:host=$this->host;dbname=$this->dbname",
                "$this->user",
                "$this->password"
            );

            $conexao->exec('set charset utf8');

            return $conexao;

        } catch(PDOException $e) {
            echo '<p>' . $e->getMessage() . '</p>';
        }
    }
}

class Bd {
    private $conexao;
    private $dashboard;

    public function __construct(Conexao $conexao, Dashboard $dashboard) {
        $this->conexao = $conexao->conectar();
        $this->dashboard = $dashboard;
    }

    public function getNumeroVendas() {
        $query = '
            select
                count(*) as numero_vendas 
            from 
                tb_vendas 
            where 
                data_venda between :data_inicio and :data_fim';

        $stmt = $this->conexao->prepare($query);
        $stmt->bindValue(':data_inicio', $this->dashboard->__get('data_inicio'));
        $stmt->bindValue(':data_fim', $this->dashboard->__get('data_fim'));
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_OBJ)->numero_vendas;
    }

    public function getTotalVendas() {
        $query = '
            select
                SUM(total) as total_vendas 
            from 
                tb_vendas 
            where 
                data_venda between :data_inicio and :data_fim';

        $stmt = $this->conexao->prepare($query);
        $stmt->bindValue(':data_inicio', $this->dashboard->__get('data_inicio'));
        $stmt->bindValue(':data_fim', $this->dashboard->__get('data_fim'));
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_OBJ)->total_vendas;
    }

    public function getClientesAtivos() {
        $query = '
            SELECT
                COUNT(*) as clientes_ativos
            FROM 
                tb_clientes
            WHERE 
                cliente_ativo = 1';

        $stmt = $this->conexao->prepare($query);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_OBJ)->clientes_ativos;
    }

    public function getClientesInativos() {
        $query = '
            SELECT
                COUNT(*) as clientes_inativos
            FROM 
                tb_clientes
            WHERE 
                cliente_ativo = 0';

        $stmt = $this->conexao->prepare($query);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_OBJ)->clientes_inativos;
    }

    //reclamacoes = 1 / elogios = 2 / sugestoes = 3
    public function getTotalReclamacoes() {
        $query = '
            SELECT 
                COUNT(*) as reclamacoes
            FROM 
                tb_contatos 
            WHERE 
                tipo_contato = 1';

        $stmt = $this->conexao->prepare($query);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_OBJ)->reclamacoes;
    }

    public function getTotalElogios() {
        $query = '
            SELECT 
                COUNT(*) as elogios
            FROM 
                tb_contatos 
            WHERE 
                tipo_contato = 2';

        $stmt = $this->conexao->prepare($query);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_OBJ)->elogios;
    }

    public function getTotalSugestoes() {
        $query = '
            SELECT 
                COUNT(*) as sugestoes
            FROM 
                tb_contatos 
            WHERE 
                tipo_contato = 3';

        $stmt = $this->conexao->prepare($query);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_OBJ)->sugestoes;
    }

    public function getTotalDespesas() {
        $query = '
            select
                SUM(total) as total_despesas 
            from 
                tb_despesas 
            where 
                data_despesa between :data_inicio and :data_fim';

        $stmt = $this->conexao->prepare($query);
        $stmt->bindValue(':data_inicio', $this->dashboard->__get('data_inicio'));
        $stmt->bindValue(':data_fim', $this->dashboard->__get('data_fim'));
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_OBJ)->total_despesas;
    }
    
}

//logica script
$dashboard = new Dashboard();

$conexao = new Conexao();

$competencia = explode('-', $_GET['competencia']);
$ano = $competencia[0];
$mes = $competencia[1];

$dias_do_mes = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);

$dashboard->__set('data_inicio', $ano . '-' . $mes . '-01');
$dashboard->__set('data_fim', $ano . '-' . $mes . '-' . $dias_do_mes);

$bd = new Bd($conexao, $dashboard);

$dashboard->__set('numero_vendas', $bd->getNumeroVendas());
$dashboard->__set('total_vendas', $bd->getTotalVendas());
$dashboard->__set('clientes_ativos', $bd->getClientesAtivos());
$dashboard->__set('clientes_inativos', $bd->getClientesInativos());
$dashboard->__set('total_reclamacoes', $bd->getTotalReclamacoes());
$dashboard->__set('total_elogios', $bd->getTotalElogios());
$dashboard->__set('total_sugestoes', $bd->getTotalSugestoes());
$dashboard->__set('total_despesas', $bd->getTotalDespesas());

// print_r($dashboard);
echo json_encode($dashboard);
?>
