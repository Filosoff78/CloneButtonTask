<?php

namespace NB\TaskClone;

use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Diag\Debug;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Page\Asset;
use Bitrix\Main\SystemException;

class Helper
{
    /**
     * Подключение модулей
     * Loader::includeModule()
     * @param array $modules
     * @throws SystemException
     */
    public static function initModules(array $modules)
    {
        if (empty($modules)) {
            throw new SystemException(Loc::getMessage("HELPER_MODULE_EMPTY"));
        } else {
            foreach ($modules as $module) {
                if (!Loader::includeModule($module)) {
                    throw new SystemException('module ' . $module . ' not installed');
                }
            }
        }
    }

    /**
     * Получение id модуля
     * @return string
     */
    public static function getModuleId(): string
    {
        return 'nb.taskclone';
    }

    /**
     * Проверка вхождения url ссылки
     * @return array|null
     */
    public static function urlParser(): ?array
    {
        $arUrlTemplates = [
            'path_user' => ltrim(
                Option::get(
                    'intranet',
                    'path_user',
                    '/company/personal/user/#USER_ID#/',
                    SITE_ID
                ),
                '/'
            ),
        ];

        $arUrlTemplates['detail_task'] = $arUrlTemplates['path_user'] . 'tasks/task/view/#TASK_ID#/';

        $componentPath = \CComponentEngine::parseComponentPath('/', $arUrlTemplates, $arVariables);

        if (!$componentPath) {
            return null;
        }

        return [
            'module' => 'intranet',
            'componentPath' => $componentPath,
            'arVariables' => $arVariables
        ];
    }

    /**
     * Вывод в консоль браузера
     * @param $string
     * @return void
     */
    public static function writeToConsole($string)
    {
        if (is_array($string)) {
            Asset::getInstance()->addString("<script>console.log(" . \CUtil::PhpToJSObject($string) . ");</script>");
        } else {
            Asset::getInstance()->addString("<script>console.log(" . $string . ");</script>");
        }
    }

    /**
     * Создание js расширения
     * @param string $path
     * @param string $libName
     * @param array $data
     * @param array $arRelLibs
     * @param string $csspath
     * @param string $langPath
     * @return false|void
     */
    public static function insertJs(
        string $path,
        string $libName,
        array $data = [],
        array $arRelLibs = [],
        string $csspath = '',
        string $langPath = ''
    ) {
        $documentRoot = Application::getDocumentRoot();

        if (!$path or !file_exists($documentRoot . $path)) {
            self::writeToConsole($path . ' not found');
            return false;
        }

        if (!\CJSCore::IsExtRegistered($libName)) {
            $arLib = [
                'js' => $path
            ];
            if ($csspath != '') {
                if (!$csspath or !file_exists($documentRoot . $csspath)) {
                    self::writeToConsole($csspath . ' not found');
                } else {
                    $arLib['css'] = $csspath;
                }
            }
            if (!empty($arRelLibs)) {
                $arLib['rel'] = $arRelLibs;
            }

            if ($langPath) {
                $arLib['lang'] = $langPath;
                Debug::dump($langPath);
            }

            \CJSCore::RegisterExt($libName, $arLib);
        }
        \CJSCore::Init([$libName]);

        if (!empty($data)) {
            Asset::getInstance()->addString(
                '
            <script>
                BX.ready(function () 
                {
                    BX.namespace("BX.' . $libName . '");
                    BX.' . $libName . '.Data = ' . \CUtil::PhpToJSObject($data, false, false, true) . ';
                })
            </script>'
            );
        }
    }

    /**
     * Определяем место размещения модуля
     *
     * @param bool $notDocumentRoot
     * @return mixed|string
     */
    public static function GetPath($notDocumentRoot = false)
    {
        if ($notDocumentRoot) {
            return str_ireplace(Application::getDocumentRoot(), '', dirname(__DIR__)) . '/';
        }

        return dirname(__DIR__) . '/';
    }
}
