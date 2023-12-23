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
    this.boundBtnDescrClick=this.btnDescrClick.bind(this);

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
    @param {array} halls
  */
  fillDlgData(halls) {
    var
      hh=[],
      hallIdx=getCookie('hi'),
      elHalls=document.getElementById('halls-list');

    if (!hallIdx) {
      hallIdx=1;
      setCookie('hi',1);
    }

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
      l=this.createHallLabel(hall,r.id),
      d=this.createHallDescr(hall,idx),
      b=this.createHallBtn(idx);

    h.id='hall-'+idx.toString();
    h.classList.add('hall-div');
    if (hall['subhall']) h.classList.add('subhall');
    h.append(r,l,b,d);

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

  createHallLabel(hall,forId) {
    var
      l=document.createElement('label');

    l.setAttribute('for',forId);
    l.innerHTML=(hall['subhall'] ? '&nbsp;&nbsp;' : '')+'&nbsp;&nbsp;&nbsp;'+hall['name'];

    return l;
  }

  createHallBtn(idx) {
    var
      b=document.createElement('span');

    b.id='btn-info-'+idx.toString();
    b.classList.add('btn-info');
    b.innerHTML='&#9668;';

    return b;
  }

  createHallDescr(hall,idx) {
    var
      d=document.createElement('div');

    d.id=`info-${idx}`;
    d.classList.add('hall-info');
    d.innerHTML=`<p>${hall['descr']}</p><p>${hall['path']}</p>`;

    return d;
  }

  bindEvents() {
    var
      btns=document.querySelectorAll('#halls-list .btn-info');

    Array.from(btns).forEach((btn) =>
      btn.addEventListener('click',this.boundBtnDescrClick));
  }

  btnDescrClick(ev) {
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
      ev.target.innerHTML='&#9668;';
    }
  }

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
