/*
  ================================================
  Класс диалога редактирования информации о зале
  ================================================
*/
class DlgEditHallInfo
{
  constructor(dlg,opts,hallIdx) {
    this.dlg=dlg;
    this.hallIdx=hallIdx;
    this.prepareDlg();
    this.setupEvents(opts);
  }

  setupEvents(opts) {
    this.boundBtnStoreClick=this.btnStoreClick.bind(this);
    this.boundBtnCancelClick=this.btnCancelClick.bind(this);
    this.boundChBxIgnoreChange=this.chbxIgnoreChange.bind(this);

    document.getElementById('cb-ignore-hall').addEventListener('change',this.boundChBxIgnoreChange);

    opts.buttons=[
      {text: 'Сохранить', click: this.boundBtnStoreClick},
      {text: 'Отмена', click: this.boundBtnCancelClick}
    ];
  }

  prepareDlg() {
    $('#hall-path-info').text($('#hall-path-'+this.hallIdx).text());
    $('#ed-hall-name').val($('#lbl-hall-name-'+this.hallIdx).html());
    $('#ed-hall-descr').val($('#hall-descr-'+this.hallIdx).html());
    $('#ed-hall-thumb').val($('#hall-thumb-'+this.hallIdx).attr('alt'));
  }

  chbxIgnoreChange(ev) {
    if (ev.currentTarget.checked) {
      document.getElementById('ignore-hall-warn').style.display='block';
    } else {
      document.getElementById('ignore-hall-warn').style.display='none';
    }
  }

  btnStoreClick(ev) {
    $.post(
      'php/StoreSubhallInfo.php',
      {
        'path': $('#hall-path-info').text(),
        'name': $('#ed-hall-name').val(),
        'descr': $('#ed-hall-descr').val(),
        'ignore': $('#cb-ignore-hall').is(':checked'),
        'thumb': $('#ed-hall-thumb').val(),
      },
      (ans) => {
        if (ans.err)
          dlgCtrl.showDlg(
            'error',
            `<b>При сохранении произошла ошибка:</b><br>${ans.msg}`,
            _ERROR
          );
        else {
          if ($('#cb-ignore-hall').is(':checked'))
            // удаляем зал из списка, если ignore :checked
            $('#hall-'+this.hallIdx).remove();
          else {
            // корректируем данные в списке залов
            $('#lbl-hall-name-'+this.hallIdx).html($('#ed-hall-name').val());
            $('#hall-descr-'+this.hallIdx).html($('#ed-hall-descr').val());
            let
              thumbSrc='',
              thumbAlt=$('#ed-hall-thumb').val().trim();
            if (thumbAlt)
              thumbSrc='php/GetHallThumb.php?th='+encodeURI($('#hall-path-info').text()+thumbAlt);
            $('#hall-thumb-'+this.hallIdx)
              .attr('alt',thumbAlt)
              .attr('src',thumbSrc);
          }
          // фух! наконец, закрываем этот диалог
          dlgCtrl.destroyDlg(this.dlg);
        }
      },
      'json'
    )
    .fail((xhr,status,error) => {
      ///_log(status,error,xhr); //dbg
      dlgCtrl.showDlg(
        'error',
        `<b>При обращении к серверу произошла ошибка:</b><br>${xhr.status} - ${xhr.statusText}.${xhr.responseText}`,
        _ERROR
      );
    });
  }

  btnCancelClick(ev) {
    dlgCtrl.destroyDlg(this.dlg);
  }
}
