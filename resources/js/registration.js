function showinfo() {
    document.getElementById('show_info').style.visibility = 'visible';
}

function hideinfo() {
    document.getElementById('show_info').style.visibility = 'hidden';
}

function showterms() {
    document.getElementById('terms').style.visibility = 'visible';
    document.getElementById('body_head').style.opacity = '0.1';
    var h3 = document.getElementsByTagName('h3')[1];
    h3.style.opacity = '0.1';
    document.getElementById('body_aside').style.opacity = '0.1';
    document.getElementById('body_foot').style.opacity = ' 0.1';
    document.getElementById('regi').style.opacity = '0.1';
}

function hideterms() {
    document.getElementById('terms').style.visibility = 'hidden';
    document.getElementById('body_head').style.opacity = '1';
    var h3 = document.getElementsByTagName('h3')[1];
    h3.style.opacity = '1';
    document.getElementById('body_aside').style.opacity = '1';
    document.getElementById('body_foot').style.opacity = ' 1';
    document.getElementById('regi').style.opacity = '1';
}

function standardterms() {
    document.getElementById('terms').style.visibility = 'hidden';
}
