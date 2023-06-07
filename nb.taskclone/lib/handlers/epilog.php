<?php

namespace NB\TaskClone\Handlers;

use NB\TaskClone\Helper;

class Epilog
{
    /**
     * Массив шаблон - js файл
     * @var string[]
     */
    private static $arJsPaths = [
        'detail_task' => 'taskCloneButton.js',
    ];

    /**
     * Массив шаблон - лэнг файл
     * @var string[]
     */
    private static $arLangPaths = [
        'detail_task' => 'lang/' . LANGUAGE_ID . '/assets/js/taskCloneButton.php'
    ];

    /**
     * Подключение js библиотек, подключается событием
     * @return void
     */
    public static function includeJsLibraries()
    {
        $urlParse = Helper::urlParser();

        if (!$urlParse) {
            return;
        }

        if ($urlParse['module'] === 'intranet' && in_array($urlParse['componentPath'], array_keys(self::$arJsPaths))) {
            Helper::insertJs(
                Helper::GetPath(true) . 'assets/js/' . self::$arJsPaths[$urlParse['componentPath']],
                'taskCloneButton',
                $urlParse['arVariables'],
                [],
                '',
                Helper::GetPath(true) . self::$arLangPaths[$urlParse['componentPath']],
            );
        }
    }
}
