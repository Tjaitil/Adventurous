<?php

namespace App\libs;

use App\libs\Response;
use App\Http\Resources\Resource;
use Exception;
use \PDO;
use \PDOStatement;
use \ReflectionClass;

global $_SESSION;

class model
{
    public database $db;
    // $updateGamedata is the variable that holds updateGamedata_model if it is being instantiated with $this->loadModel from foo_model
    protected $UpdateGamedata;
    // $Artefact_model is the variable that holds updateGamedata_model if it is being instantiated with $this->loadModel from foo_model
    protected $ArtefactModel;
    protected $hungerModel;
    protected $errorHandler;
    protected $session;
    protected $username = "";

    protected string $statement = "";
    private string $whereStatement = "";
    private bool $where_added_to_statement = false;
    protected PDOStatement $stmt;
    protected string $table = "";
    protected array $query_data = [];
    protected array $query_index_data = [];
    protected array $last_query_data = [];
    protected $where_columns = [];
    protected $registered_relations = [];

    protected $fetched_rows = [];
    protected $current_fetch_row_index = 0;

    /**
     * Hunger_model
     *
     * @var class
     */
    protected $Hunger_model;

    public $response;

    function __construct($table = null)
    {
        $this->includeDB();
        // if(!isset($this->hungerModel)) {
        // $this->Hunger_model = Hunger_model::getSelf();
        // }
        if ($table !== null) {
            $this->setTable($table);
        }

        $this->errorHandler = new errorhandler();
        $this->response = new Response();
        $this->getSession();
    }

    public function getTableName()
    {
        return $this->table;
    }

    protected function getWhereStatement()
    {
        return $this->where_columns;
    }

    protected function lastQueryData()
    {
    }

    private function resetQuery()
    {
        $this->statement = "";
        $this->query_data = [];
    }

    public function queryUpdate()
    {
        $this->resetQuery();
        $this->statement .= 'UPDATE ' . $this->table . " SET";
        return $this;
    }

    protected function setTable(string $table)
    {
        $this->table = $table;
    }

    public function addQueryData(...$data)
    {
        foreach ($data as $key => $value) {
            $this->query_data[] = $value;
        }
        return $this;
    }

    public function updateColumn(...$columns)
    {
        $index = 0;
        foreach ($columns as $key => $value) {
            if (count($columns) === 1 || count($columns) === $index + 1) {
                $this->statement .= " $value=? ";
            } else {
                $this->statement .= " $value=?,";
            }
            $index++;
        }

        return $this;
    }

    public function and()
    {
        $this->statement .= ', ';
        return $this;
    }

    public function whereAnd()
    {
        $this->statement .= ' AND ';
        return $this;
    }

    public function select(...$columns)
    {
        $this->statement .= " SELECT ";
        if (isset($columns)) {
            $this->statement .= "*";
        } else {
            foreach ($columns as $key => $value) {
                $this->statement .= " $value ";
            }
        }

        $this->statement .= " FROM " . $this->table;
        return $this;
    }

    public function where(string|array $statement, array $data = [])
    {
        if (!$this->where_added_to_statement) {
            $this->statement .= " WHERE ";
            $this->where_added_to_statement = true;
        }

        if (\is_string($statement)) {
            $this->statement .= $statement . '=?';
        } else {
            $index = 0;
            $columns = [...$statement];

            foreach ($columns as $key => $column) {
                if (count($columns) === $index + 1) {
                    $this->statement .= ' ' . $column . '=? ';
                } else {
                    $this->statement .= ' ' . $column . '=?' . ' AND ';
                }
                $index++;
            }
        }

        foreach ($data as $key => $value) {
            $this->addQueryData($value);
        }

        return $this;
    }

    public function whereIn(string $statement, array $data)
    {
        foreach ($data as $key => $value) {
            $this->addQueryData($value);
        }
        $in  = str_repeat('?,', count($data) - 1) . '?';

        if (!$this->where_added_to_statement) {
            $this->statement .= " WHERE ";
            $this->where_added_to_statement = true;
        }

        $this->statement .= ' ' . $statement . " IN($in)";
        return $this;
    }

    public function execute()
    {
        $this->stmt = $this->db->conn->prepare($this->statement);
        $this->stmt->execute($this->query_data);
    }

    /**
     * Get data from statement
     *
     * @return array
     */
    public function get()
    {
        $this->execute();

        $data = $this->fetched_rows = $this->stmt->fetchAll(PDO::FETCH_ASSOC);


        foreach ($this->registered_relations as $key => $value) {
            $this->{$value};
        }


        return $data;
    }

    public function save($data)
    {
        if ($data instanceof Resource) {
            $data = $data->toArray();
        }

        if (empty($this->fetched_rows)) {
            throw new Exception("Empty dataset");
        }

        $diff = \array_diff($data, $this->fetched_rows[$this->current_fetch_row_index]);
        if (empty($diff)) {
            throw new Exception("Empty query");
        } else {
            $this->queryUpdate()
                ->updateColumn(...array_keys($diff))
                ->addQueryData(...$diff);
        }

        $this->current_fetch_row_index++;
        return $this;
    }


    public function retrieveWith(string $relation)
    {
        $this->registered_relations[$relation];
        return $this;
    }


    public function hasOne($model, string $primary_key, string $secondary_key)
    {
        return $model->select('*')->where($this->getWhereStatement())->addQueryData($this->query_data);
    }

    public function includeDB()
    {
        if (!isset($this->db)) {
            $this->db = database::getInstance();
        }
    }

    protected function getSession()
    {
        $this->session = $_SESSION['gamedata'] ?? [];
        $this->username = $_SESSION['username'];
    }

    /**
     * Undocumented function
     *
     * @param bool $UpdateGamedata If UpdateGamedata_model should be loaded
     * @param bool $ArtefactModel If ArtefactModel should be loaded
     * @param bool $hungerModel I hungerModel should be loaded
     * @return void
     */
    protected function commonModels($UpdateGamedata = false, $ArtefactModel = false, $hungerModel = false)
    {
        // Load common models
        if ($UpdateGamedata === true) {
            $this->UpdateGamedata = $this->loadModel('UpdateGamedata', true);
        }
        if ($ArtefactModel === true) {
            $this->ArtefactModel = $this->loadModel('Artefact', true);
        }
        if ($hungerModel === true) {
            $this->hungerModel = $this->loadModel('Hunger', true, true);
        }
    }

    /**
     * Load model
     * @param string $model Modelname
     * @param bool $directoryUP Deprecated variable
     * @param bool $db Instantiate new model
     * 
     * @return 
     */
    protected function loadModel($model, $directoryUP = true, $db = false)
    {
        $model = $model . '_model';
        // Check if model needs a database connection
        // This will apply only when the model is called from another models
        $reflection_class = new ReflectionClass($model);
        $db_match = false;

        foreach ($reflection_class->getConstructor()->getParameters() as $params) {
            if ($params->name === "db") {
                $db_match = true;
            }
        }

        if ($db_match === true) {
            // return new $model($_SESSION['gamedata'], $this->db);    
            return $reflection_class->newInstance($_SESSION['gamedata'], $this->db);
        } else {
            // return new $model($_SESSION['gamedata']);    
            return $reflection_class->newInstance($_SESSION['gamedata']);
        }
    }
}
