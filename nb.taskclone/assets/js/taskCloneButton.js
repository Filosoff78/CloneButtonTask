BX.ready(() => {
  BX.addCustomEvent('onPopupFirstShow', (p) => {
    let menuId = 'task-view-b';
    if (p.uniquePopupId === `menu-popup-${menuId}`) {
      let menu = BX.PopupMenu.getMenuById(menuId);

      menu.addMenuItem({
        text: 'Клонировать',
        className: 'menu-popup-item-copy',
        onclick: function (event, item) {
          item.setText(BX.message('NB_TASKCLONE_BUTTON_LOAD'));
          item.disable();
          BX.ajax.runAction('nb:taskclone.api.main.addSubtask', {
            data: {
              taskId: BX.taskCloneButton.Data.TASK_ID
            },
          }).then((r) => {
            item.setText(BX.message('NB_TASKCLONE_BUTTON_NAME'));
            item.enable();
            BX.SidePanel.Instance.open(`/company/personal/user/${BX.taskCloneButton.Data.USER_ID}/tasks/task/view/${r.data}/`)
          }, (r) => console.log(r));
        }
      });
    }
  });
});
