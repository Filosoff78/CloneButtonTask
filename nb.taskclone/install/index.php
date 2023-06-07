<?php

use Bitrix\Main\EventManager;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;

/**
 * Class nb_taskclone
 */
class nb_taskclone extends CModule
{
    /**
     * NB_TASKCLONE constructor.
     */
    public function __construct()
    {
        $arModuleVersion = include 'version.php';

        $this->MODULE_ID = 'nb.taskclone';

        $this->MODULE_NAME = GetMessage('NB_TASKCLONE_MODULE_NAME');
        $this->MODULE_DESCRIPTION = GetMessage('NB_TASKCLONE_MODULE_DESC');
        $this->MODULE_VERSION = $arModuleVersion['VERSION'];
        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];

        $this->MODULE_SORT = 1;
        $this->SHOW_SUPER_ADMIN_GROUP_RIGHTS = 'Y';
        $this->MODULE_GROUP_RIGHTS = 'Y';

        $this->PARTNER_NAME = GetMessage('NB_TASKCLONE_PARTNER_NAME');
        $this->PARTNER_URI = GetMessage('NB_TASKCLONE_PARTNER_URI');
    }

    /**
     *
     */
    public function DoInstall()
    {
        try {
            global $APPLICATION;

            if (!$this->isVersionD7()) {
                return $APPLICATION->ThrowException(Loc::getMessage('NB_TASKCLONE_INSTALL_ERROR_VERSION'));
            }

            ModuleManager::registerModule($this->MODULE_ID);

            $this->InstallDB();
            $this->InstallEvents();
            $this->InstallFiles();
        } catch (Exception $e) {
            ShowError($e->getMessage());
        }
    }

    /**
     *
     */
    public function DoUninstall()
    {
        $this->UnInstallFiles();
        $this->UnInstallEvents();
        $this->UnInstallDB();
    }

    /**
     * @return bool
     */
    public function InstallEvents(): bool
    {
        $eventManager = EventManager::getInstance();

        $eventManager->registerEventHandler(
            'main',
            'OnEpilog',
            $this->MODULE_ID,
            '\NB\TaskClone\Handlers\Epilog',
            'includeJsLibraries'
        );
        return true;
    }

    /**
     * @return bool
     */
    public function UnInstallEvents(): bool
    {
        $eventManager = EventManager::getInstance();

        $eventManager->unRegisterEventHandler(
            'main',
            'OnEpilog',
            $this->MODULE_ID,
            '\NB\TaskClone\Handlers\Epilog',
            'includeJsLibraries'
        );
        return true;
    }

    /**
     * @return bool
     */
    private function isVersionD7(): bool
    {
        return CheckVersion(ModuleManager::getVersion('main'), '14.00.00');
    }
}
