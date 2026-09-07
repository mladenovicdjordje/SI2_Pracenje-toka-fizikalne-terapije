(function () {
    const dani = ['Po', 'Ut', 'Sr', 'Če', 'Pe', 'Su', 'Ne'];
    const meseci = ['januar', 'februar', 'mart', 'april', 'maj', 'jun', 'jul', 'avgust', 'septembar', 'oktobar', 'novembar', 'decembar'];

    function pad(n) { return String(n).padStart(2, '0'); }

    function parsePrikaz(s) {
        const m = /^(\d{2})\/(\d{2})\/(\d{4})$/.exec((s || '').trim());
        if (!m) return null;
        const d = new Date(Number(m[3]), Number(m[2]) - 1, Number(m[1]));
        return isNaN(d.getTime()) ? null : d;
    }

    function formatPrikaz(d) {
        return pad(d.getDate()) + '/' + pad(d.getMonth() + 1) + '/' + d.getFullYear();
    }

    function napraviKalendar(polje) {
        document.querySelectorAll('.kalendar-popup').forEach((el) => el.remove());
        const start = parsePrikaz(polje.value) || new Date();
        let god = start.getFullYear();
        let mes = start.getMonth();

        const box = document.createElement('div');
        box.className = 'kalendar-popup';

        function crtaj() {
            const prvi = new Date(god, mes, 1);
            const startIdx = (prvi.getDay() + 6) % 7;
            const brojDana = new Date(god, mes + 1, 0).getDate();
            let html = '<div class="kal-glava">';
            html += '<button type="button" class="kal-nav" data-p="-1">‹</button>';
            html += '<strong>' + meseci[mes] + ' ' + god + '</strong>';
            html += '<button type="button" class="kal-nav" data-p="1">›</button>';
            html += '</div><div class="kal-dani">';
            dani.forEach((d) => { html += '<span>' + d + '</span>'; });
            html += '</div><div class="kal-mreza">';
            for (let i = 0; i < startIdx; i++) html += '<span></span>';
            const danas = new Date();
            for (let dan = 1; dan <= brojDana; dan++) {
                const cls = (dan === danas.getDate() && mes === danas.getMonth() && god === danas.getFullYear()) ? ' danas' : '';
                html += '<button type="button" class="kal-dan' + cls + '" data-d="' + dan + '">' + dan + '</button>';
            }
            html += '</div>';
            box.innerHTML = html;
            box.querySelectorAll('.kal-nav').forEach((b) => {
                b.addEventListener('click', (e) => {
                    e.preventDefault();
                    mes += Number(b.getAttribute('data-p'));
                    if (mes < 0) { mes = 11; god -= 1; }
                    if (mes > 11) { mes = 0; god += 1; }
                    crtaj();
                });
            });
            box.querySelectorAll('.kal-dan').forEach((b) => {
                b.addEventListener('click', (e) => {
                    e.preventDefault();
                    polje.value = formatPrikaz(new Date(god, mes, Number(b.getAttribute('data-d'))));
                    box.remove();
                });
            });
        }

        const rect = polje.getBoundingClientRect();
        box.style.top = (window.scrollY + rect.bottom + 6) + 'px';
        box.style.left = (window.scrollX + rect.left) + 'px';
        document.body.appendChild(box);
        crtaj();

        setTimeout(() => {
            const zatvori = (ev) => {
                if (!box.contains(ev.target) && ev.target !== polje) {
                    box.remove();
                    document.removeEventListener('mousedown', zatvori);
                }
            };
            document.addEventListener('mousedown', zatvori);
        }, 0);
    }

    function povezi(polje) {
        polje.setAttribute('autocomplete', 'off');
        polje.addEventListener('focus', () => napraviKalendar(polje));
        polje.addEventListener('click', () => napraviKalendar(polje));
    }

    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('input.polje-datuma').forEach(povezi);
    });
})();
