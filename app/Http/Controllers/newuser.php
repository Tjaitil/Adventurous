<?php
class newuser extends controller
{
    public $error = array('profiencyErr' => '');

    function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        if (isset($_SESSION['gamedata'])) {
            // header("Location: /main");
            exit();
        }
        $this->post();
        $this->renderWE('newuser', 'New User', false, $this->error);
    }

    public function post()
    {
        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            if (empty($_POST['profiency'])) {
                $this->error['profiencyErr'] = "Please select a profiency!";
            }
            if (empty($this->error['profiencyErr'])) {
                $this->loadModel('NewUser', true);
                $this->model->selectProfiency(trim($_POST['profiency']));
                // header("Location: /main");
                exit();
            }
        }
    }
}
