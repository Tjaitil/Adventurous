import { ProfiencyStatusResponse } from './types/responses/ProfiencyStatus';
import { AdvApi } from './AdvApi.js';
import { ajaxJS } from './ajax.js';

window.addEventListener('DOMContentLoaded', () => {
  setTimeout(getProfienciesStatus, 120000);
  setTimeout(updateDiplomacyTab, 120000);
});

function getProfienciesStatus() {
  AdvApi.get<ProfiencyStatusResponse>('profiencystatus/get').then(data => {
    document.getElementById('tab_2').innerHTML =
      data.html.profiency_status_template;
  });
}

function updateCountdownTab() {
  const data = 'model=ProfiencyStatus' + '&method=get';
  ajaxJS(data, function (response) {
    if (response[0] !== false) {
      document.getElementById('tab_2').innerHTML = response[1].html;
    }
  });
  // setTimeout(updateCountdownTab, 120000);
}
function updateDiplomacyTab() {
  const data = 'model=Diplomacy' + '&method=get';
  ajaxJS(data, function (response) {
    if (response[0] !== false) {
      const responseText = response[1];
      const trs = document
        .getElementById('tab_3')
        .querySelectorAll('tbody')[0]
        .querySelectorAll('tr');
      for (let i = 0; i < trs.length; i++) {
        const td = trs[i].querySelectorAll('td')[1];
        td.classList.remove('negativeDiplomacy');
        td.classList.remove('positiveDiplomacy');
        const tdText = td.innerText;
        const diplomacy = responseText.diplomacy[i];
        if (diplomacy > 1) {
          td.classList.add('positiveDiplomacy');
        } else {
          td.classList.add('negativeDiplomacy');
        }
        const numberDifference = parseFloat(tdText) - parseFloat(diplomacy);
        trs[i].querySelectorAll('td')[1].innerHTML =
          diplomacy + ' (' + numberDifference + ')';
      }
    }
  });
}
