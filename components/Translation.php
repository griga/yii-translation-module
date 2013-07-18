<?php
/** Created by griga at 17.07.13 | 12:20.
 *
 */

class Translation
{
    private $_categoryFilePath = null;
    private $_data = array();

    public function save($phrase)
    {
        $message = array();
        /** @var TranslationModule $module */
        $module = app()->getModule('translation');
        if (isset($phrase['category'])) {
            foreach ($module->languages as $lang => $name) {
                if ($module->sourceLanguage && $lang !== $module->sourceLanguage) {
                    $this->setCategoryFilePath($lang, $phrase['category']);
                    $this->setData();
                    unset($this->_data[$phrase[$module->sourceLanguage]]);
                    $this->_data[$phrase[$module->sourceLanguage]] = htmlspecialchars($phrase[$lang]);
                    $this->dump();
                }
            }
            $message['success'] = 'Объект с ключем <strong>'.$phrase[$module->sourceLanguage] . '</strong> сохранен.';
        } else {
            $message['error'] = 'не правильный запрос. Не задана категория.';
        }
        return $message;
    }

    public function dump(){
        file_put_contents($this->_categoryFilePath, '<?php return ' . var_export($this->_data, TRUE) . '; ?>');
    }

    public function delete($phrase)
    {
        $message = array();
        /** @var TranslationModule $module */
        $module = app()->getModule('translation');
        if (isset($phrase['category'])) {
            foreach ($module->languages as $lang => $name) {
                if ($module->sourceLanguage && $lang !== $module->sourceLanguage) {
                    $this->setCategoryFilePath($lang, $phrase['category']);
                    $this->setData();
                    unset($this->_data[$phrase['key']]);
                    $this->dump();
                }
            }
            $message['success'] = 'Объект с ключем <strong>'.$phrase['key'].'</strong> удален.';
        } else {
            $message['error'] = 'Не правильный запрос. Не задана категория.';
        }
        return $message;

    }


    public function getTranslationData()
    {
        $module = app()->getModule('translation');
        $data = array();
        
        foreach ($module->languages as $lang => $name) {
            if ($module->sourceLanguage && $lang !== $module->sourceLanguage) {
                $langDir = $this->getLangDir($lang);
                foreach (new DirectoryIterator($langDir) as $fileInfo) {
                    /** @var SplFileInfo $fileInfo */
                    if ($fileInfo->isDot()) continue;
                    else {
                        $category = $fileInfo->getBasename('.php');
                        if (!isset($data[$category])) {
                            $data[$category] = array();
                        }
                        $langFile = require_once($fileInfo->getRealPath());
                        ksort($langFile);
                        foreach ($langFile as $phrase => $translation) {
                            if (!isset($data[$category][$phrase])) {
                                $data[$category][$phrase] = array(
                                    $module->sourceLanguage => $phrase
                                );
                            }
                            $data[$category][$phrase][$lang] = $translation;
                        }
                    }
                }
            }
        }
        return $data;
    }

    private function setData(){
        if (file_exists($this->_categoryFilePath)) {
            $this->_data = require_once($this->_categoryFilePath);
        } else {
            $this->_data = array();
        }
    }

    private function setCategoryFilePath($lang, $category)
    {
        $this->_categoryFilePath = $this->getLangDir($lang) . DIRECTORY_SEPARATOR . $category . '.php';
    }
    
    private function getLangDir($lang)
    {
        $langDir = Yii::getPathOfAlias('application.messages').DIRECTORY_SEPARATOR . $lang;
        if (!is_dir($langDir)) {
            mkdir($langDir);
        }
        return $langDir;
    }

}