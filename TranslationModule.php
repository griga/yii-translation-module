<?php

class TranslationModule extends CWebModule
{

    /**
     * @property string the path to the layout file to use for displaying Rights.
     */
    public $layout = 'translation.views.layouts.main';

    /**
     * @зкщзукен array the list of languages 'shortCode'=>'label' ('en'=>'English')
     */
    public $languages = array();

    /**
     * @property string source language in messages files
     */
    public $sourceLanguage;

    /**
     * @property Translation operator object
     */
    public $translation;

    /**
     * @property boolean whether to enable debug mode.
     */
    public $debug = true;

    private $_assetsUrl;

    public function init()
	{
        Yii::app()->getComponent('bootstrap');

		// import the module-level models and components
		$this->setImport(array(
//			'translation.models.*',
			'translation.components.*',
		));

        // Normally the default controller is Module.
        $this->defaultController = 'module';

        $this->translation = new Translation();
	}


    /**
     * Registers the necessary scripts.
     */
    public function registerScripts()
    {
        // Get the url to the module assets
        $assetsUrl = $this->getAssetsUrl();
        cs()->registerCoreScript('jquery');
        cs()->registerScriptFile($assetsUrl.'/js/knockout-2.2.1.js');
        cs()->registerCssFile($assetsUrl.'/css/style.css');
        $ajaxImgUrl = $assetsUrl . '/img/ajax-loader.gif';
        $js = <<<"JS"
$(document).ajaxStart(function() {\$(document.body).css('position','relative').append($('<div>',{class:'loader-wrapper'}).css({position: 'fixed',			zIndex: '15000',			left: '0',			top: '0',			right: '0',			bottom: '0',			background: 'rgba(200, 200, 200, 0.3)',			display: 'none'		}).append($('<div>',{class:'loader-inner'}).css({			width: '40px',			height: '40px',			margin: '170px auto'}).append($('<img>',{src:"{$ajaxImgUrl}",height:"40",width:"40"}))));$(".loader-wrapper").fadeIn();
}).ajaxStop(function() {\$(".loader-wrapper").fadeOut('fast',function(){\$(this).remove()});});
JS;
        Yii::app()->clientScript->registerScript('ajaxLoader', $js, CClientScript::POS_READY);
    }

    /**
     * Publishes the module assets path.
     * @return string the base URL that contains all published asset files of Rights.
     */
    public function getAssetsUrl()
    {
        if( $this->_assetsUrl===null )
        {
            $assetsPath = Yii::getPathOfAlias('translation.assets');

            // We need to republish the assets if debug mode is enabled.
            if( $this->debug )
                $this->_assetsUrl = Yii::app()->getAssetManager()->publish($assetsPath, false, -1, true);
            else
                $this->_assetsUrl = Yii::app()->getAssetManager()->publish($assetsPath);
        }

        return $this->_assetsUrl;
    }
}
