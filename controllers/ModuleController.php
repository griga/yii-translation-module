<?php

class ModuleController extends Controller
{

    /**
     * @return array of controller filters merged with parent controller filters
     * */
    public function filters()
    {
        return CMap::mergeArray(parent::filters(), array(
            'rights',
        ));
    }

    public $layout;

    public function init()
    {
        $this->layout = $this->module->layout;
        parent::init();
    }


    public function actionIndex()
    {
        $this->render('index');
    }

    public function actionSave()
    {
        header('Cache-Control: no-cache, must-revalidate');
        header('Content-type: application/json');
        if (isset($_POST['phrase'])) {
            echo CJavaScript::jsonEncode($this->module->translation->save($_POST['phrase']));
        }

        Yii::app()->end();
    }

    public function actionDelete()
    {
        header('Cache-Control: no-cache, must-revalidate');
        header('Content-type: application/json');
        if (isset($_POST['phrase'])) {
            echo CJavaScript::jsonEncode($this->module->translation->delete($_POST['phrase']));
        }

        Yii::app()->end();
    }
}