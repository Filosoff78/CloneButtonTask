<?php

namespace NB\TaskClone\Controller;

use Bitrix\Main\Engine\Controller;
use Bitrix\Tasks\Internals\TaskTable;
use NB\TaskClone\Helper;

class Main extends Controller
{
    /**
     * Акшен создание подзадачи
     * @param int $taskId
     * @return int|null
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function addSubtaskAction(int $taskId): ?int
    {
        Helper::initModules(['tasks']);


        $arFields = TaskTable::query()
            ->where('ID', $taskId)
            ->setSelect(['TITLE', 'DESCRIPTION', 'RESPONSIBLE_ID', 'CREATED_BY', 'SITE_ID'])
            ->fetch();

        $arFields['PARENT_ID'] = $taskId;

        $result = TaskTable::add($arFields);

        if ($result->isSuccess()) {
            return $result->getId();
        } else {
            return false;
        }
    }
}
