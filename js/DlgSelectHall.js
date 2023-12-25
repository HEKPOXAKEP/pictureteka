/*
  Класс диалога выбора зала
*/
class DlgSelectHall
{
  constructor(dlg,opts) {
    this.dlg=dlg;
    this.prepareDlg();
    this.setupEvents(opts);
  }

  setupEvents(opts) {
    this.boundBtnOkClick=this.btnOkClick.bind(this);
    this.boundBtnCancelClick=this.btnCancelClick.bind(this);
    this.boundBtnInfoClick=this.btnInfoClick.bind(this);
    this.boundBtnEditInfoClick=this.btnEditInfoClick.bind(this);
    this.boundLoadThumb=this.loadThumb.bind(this);

    opts.buttons=[
      {text: 'Ok', click: this.boundBtnOkClick},
      {text: 'Отмена', click: this.boundBtnCancelClick}
    ];

    opts.width=800;
    opts.height=500;
  }

  async prepareDlg() {
    var
      ok,
      halls;

    const
      resp=await fetch(
        'php/GetHallsList.php?op=get',{
          method: 'GET',
          credentials: 'include',
          headers: {
            'Accept': 'application/json'
          },
        });
    ///_log(resp);  //dbg

    if (!resp.ok) {
      dlgCtrl.showDlg(
        'error',
        `<p><b>Загрузка списка залов провалилась.</b></p><br>`+
        `<p><b>URL: </b>${resp.url}</p><p><b>Статус: </b>${resp.status}&nbsp;${resp.statusText}</p>`,
        'Ошибка!');
    } else {
      const
        r=resp.clone();
      try {
        halls = await resp.json();
        if (halls['err']) {
          dlgCtrl.showDlg('error',halls['msg'],'Ошибка');
        } else {
          ok=true;
        }
        ///_info('got: ', halls); //dbg
      } catch (e) {
        dlgCtrl.showDlg('error',await r.text(),'Ошибка!');
      }
    }

    if (!ok) return false;

    this.fillDlgData(halls);
  }

  /*
    Заполняем список залов
  */
  fillDlgData(halls) {
    var
      hh=[],
      hallIdx=app.hallIdx,
      elHalls=document.getElementById('halls-list');

    elHalls.tabIndex=-1;

    // create array from halls object
    for (let i in halls) hh[i]=halls[i];

    hh.forEach((hall,idx) => {
      elHalls.appendChild(this.createHallElement(hall,idx,hallIdx));
    });

    this.bindEvents();
    elHalls.focus();
  }

  createHallElement(hall,idx,hallIdx) {
    const
      h=document.createElement('div'),
      r=this.createHallRadio(idx,hallIdx),
      l=this.createHallLabel(hall,idx,r.id),
      d=this.createHallDescr(hall,idx),
      b=this.createHallInfoBtn(idx);

    h.id='hall-'+idx.toString();
    h.classList.add('hall-div');

    if (hall['subhall']) {
      h.classList.add('subhall');
      h.append(r,l,b,this.createEditHallInfoBtn(idx),d);
    } else {
      h.append(r, l, b, d);
    }

    return h;
  }

  createHallRadio(idx,hallIdx) {
    var
      r=document.createElement('input');

    r.id=`radio-${idx}`;
    r.type='radio';
    r.name='hall';
    r.value=idx.toString();
    if (idx ===Number(hallIdx)) r.checked=true;

    return r;
  }

  createHallLabel(hall,idx,forId) {
    var
      l=document.createElement('label');

    l.id='lbl-hall-name-'+idx.toString();
    l.setAttribute('for',forId);
    if (hall['subhall']) l.classList.add('lbl-subhall');
    l.innerHTML=hall['name'];

    return l;
  }

  createHallInfoBtn(idx) {
    var
      b=document.createElement('span');

    b.id='btn-info-'+idx.toString();
    b.classList.add('btn-info');
    b.innerHTML='&#9658;';

    return b;
  }

  createEditHallInfoBtn(idx) {
    var
      b=document.createElement('span');

    b.id='btn-edit-info-'+idx.toString();
    b.classList.add('btn-edit-info');
    b.innerHTML='&#9998;';

    return b;
  }

  createHallDescr(hall,idx) {
    const
      d=document.createElement('div'),
      dimg=document.createElement('img'),
      dtxt=document.createElement('div');

    d.id=`info-${idx}`;
    d.classList.add('hall-info');

    dimg.id=`hall-thumb-${idx}`;
    dimg.classList.add('hall-thumb');
    dimg.loading='lazy';
    //dimg.addEventListener('load',this.boundLoadThumb);
    if (hall['thumb']) {
      dimg.alt=hall['thumb'];
      dimg.src='php/GetHallThumb.php?th='+encodeURI(hall['path'] + hall['thumb']);
    } else {
      dimg.alt='';
      dimg.src='';
    }

    dtxt.classList.add('hall-info-txt');
    dtxt.innerHTML=`<div id='hall-descr-${idx}'>${hall['descr']}</div><div id='hall-path-${idx}'>${hall['path']}</div>`;

    d.append(dimg,dtxt)

    return d;
  }

  bindEvents() {
    var
      btns=document.querySelectorAll('#halls-list .btn-info');

    Array.from(btns).forEach((btn) =>
      btn.addEventListener('click',this.boundBtnInfoClick));

    btns=document.querySelectorAll('#halls-list .btn-edit-info');

    Array.from(btns).forEach((btn) =>
      btn.addEventListener('click',this.boundBtnEditInfoClick));
  }

  loadThumb(ev) {
    //ev.stopImmediatePropagation();
    //ev.preventDefault();
    _log(ev);
  }

  btnInfoClick(ev) {
    ev.preventDefault();

    const
      // all arrow-buttons have an Id of the form "btn-info-nnn"
      a=ev.target.id.split('-'),
      d=document.getElementById(a.slice(-2,-1)+'-'+a.slice(-1));

    if ((!d.style.display)||(d.style.display ==='none')) {
      // show
      d.style.display='block';
      ev.target.innerHTML='&#9660;';
    }  else {
      // hide
      d.style.display='none';
      ev.target.innerHTML='&#9658;';
    }
  }

  btnEditInfoClick(ev) {
    ev.preventDefault();

    const
      a=ev.target.id.split('-');

    dlgCtrl.showCustomDlg(
      'dlg-edit-hall-info',
      {
        oCss: {href: 'css/DlgEditHallInfo.css'},
        oHtml: {url: 'html/DlgEditHallInfo.html'},
        oJs: {src: 'js/DlgEditHallInfo.js'}
      },
      'Информация о зале',
      null,
      (dlg,opts) => new DlgEditHallInfo(dlg,opts,a.slice(-1))
    );
  } //btnEditInfoClick

  btnOkClick(ev) {
    //dlgCtrl.destroyDlg(this.dlg);
    dlgCtrl.showDlg(
      'warn',
      'Пока не реализовано, сорян. :-)',
      'Under construction...'
    );
  }

  btnCancelClick(ev) {
    dlgCtrl.destroyDlg(this.dlg);
  }
}
