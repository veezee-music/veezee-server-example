<?php

namespace App\Controllers\Admin;

use App\Services\GenresService;
use MongoDB\BSON\ObjectID;
use Soda\Core\Http\Controller;

class GenresController extends Controller
{
    private $_admin_url;
    private $dm;

    public function __construct()
    {
        parent::__construct();
        $this->_admin_url = ADMIN_URL;
        $this->dm = $this->getMongoDB()->getDM();
    }

    public function beforeActionExecution($action_name, $action_arguments)
    {
        parent::beforeActionExecution($action_name, $action_arguments);
    }

    protected function index()
    {
        $list = $this->dm->selectCollection(GENRES)->find([])->toArray();

        return $this->render('admin.genres.index', ['opr' => getOpR(), 'list' => $list]);
    }

    protected function new()
    {
        return $this->render('admin.genres.new', ['opr' => getOpR()]);
    }

    protected function newPost()
    {
        $form = $this->getRequest()->request->all();

        try {
            GenresService::createGenre($form);
            setOpR(true, 'Success.');
        } catch (\Exception $e) {
            setOpR(false, $e->getMessage());
        }

        return redirect("/$this->_admin_url/genres");
    }

    protected function edit($_id)
    {
        $genre = $this->dm->selectCollection(GENRES)->findOne(['_id' => new ObjectID($_id)]);

        return $this->render('admin.genres.edit', ['opr' => getOpR(), 'genre' => $genre]);
    }

    protected function editPost($_id)
    {
        $form = $this->getRequest()->request->all();

        try {
            GenresService::updateGenre($_id, $form);
            setOpR(true, 'Success.');
        } catch (\Exception $e) {
            setOpR(false, $e->getMessage());
        }

        return redirect("/$this->_admin_url/genres/edit/$_id");
    }
}