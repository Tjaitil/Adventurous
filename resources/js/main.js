var c = 0;

var timer = 0;

function chat() {
  console.log('chat');
  var input = document.getElementById('text');
  var text = input.value;
  input.value = '';
  if (text.length == 0) {
    return false;
  }
  if (text.search(/(<([^>]+)>)/gi) != -1) {
    return false;
  }
  timer = 1;
  var data = 'model=Main' + '&method=Chat' + '&message=' + text;
  ajaxP(data, function (response) {
    if (response[0] !== false) {
      updateScroll(response[1]);
    }
  });
  timer = 0;
}
function getNews() {
  ajaxRequest = new XMLHttpRequest();
  ajaxRequest.onload = function () {
    if (this.readyState == 4 && this.status == 200) {
      console.log(this.responseText);
      if (this.responseText.indexOf('ERROR:') != -1) {
        if (log == true) {
          gameLogger.addMessage('getNews error');
          gameLogger.logMessages();
        }
        callback([false, this.responseText]);
      } else {
        callback([true, this.responseText]);
      }
    }
  };
  ajaxRequest.open('GET', 'views/news.php');
  ajaxRequest.send();
}
function getChat() {
  var chat = document.getElementById('chat');
  chat.scrollTop = chat.scrollHeight - chat.clientHeight;
  if (timer != 0) {
    return false;
  }
  let id;
  if (document.getElementById('chat').children[0].lastElementChild) {
    id = document
      .getElementById('chat')
      .children[0].lastElementChild.dataset.message_id.trim();
  } else {
    id = false;
  }
  var data = 'model=Main' + '&method=getChat' + '&id=' + id;
  ajaxG(data, function (response) {
    updateScroll(response[1]);
  });
}

function updateScroll(messages) {
  var chat = document.getElementById('chat');
  var isScrolledToBottom =
    chat.scrollHeight - chat.clientHeight <= chat.scrollTop + 1;
  messages = messages.split('|');
  messages.pop();
  let liElement;
  for (let i = 0; i < messages.length; i++) {
    liElement = document.createElement('li');
    liData = messages[i].split('*^%');
    liElement.innerText = liData[0].replace(/(\r\n|\n|\r)/gm, '');
    liElement.setAttribute('data-message_id', liData[1]);
    document.getElementById('chat').children[0].appendChild(liElement);
  }

  // scroll to bottom if isScrolledToBotto
  if (isScrolledToBottom) {
    chat.scrollTop = chat.scrollHeight - chat.clientHeight;
  }
}
/*setInterval(getChat, 2000);*/
window.onload = function () {
  getChat();
  getXP();
  // Check for browser, and return error message for not optimized browser
  if (/Safari|Chrome|Firefox/i.test(navigator.userAgent) == false) {
    alert(
      'WARNING! \n This game is not tested and optimized for the browser you are using.' +
        ' Recommended browsers are Safari, Chrome or Firefox',
    );
  }
};
function getXP() {
  var data = '&model=gamedata' + '&method=getXP';
  ajaxG(data, function (response) {
    if (response[0] != false) {
      var data = response[1].split('|');
      var x_value1 = Number(data[0]);
      var x_value2 = Number(data[1]);
      var width = (x_value1 / x_value2) * 100;
      document.getElementById('skill_bar2').style.width = width + '%';
    }
  });
}
var data = [];

function add() {
  console.log('ADD');
  data.push(number1);
  data.push(number2);
  console.log(data);
}

function callFunction() {
  for (let i = 0; i < data.length; i++) {
    console.log(data[i]);
    data[i]();
  }
}
function zoom() {
  let images = document.getElementById('map').querySelectorAll('.map_img');
  console.log(images);
  for (var i = 0; i < images.length; i++) {
    console.log(images[i].style.width);
    if (images[i].style.width === '') {
      images[i].style.width = '400px';
      images[i].style.height = '400px';
    } else {
      images[i].style.width = 400;
      images[i].style.height = 400;
    }
    console.log(images[i].style.width);
  }
  document.getElementById('map').style.width = 9 * 400 + 'px';
}

function number1() {
  console.log(1);
}
function number2() {
  console.log(2);
}
function number3() {
  console.log(3);
}
