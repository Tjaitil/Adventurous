function show(element) {
  /*var div = event.target.id;*/
  var divs = ['password'];
  for (var i = 0; i < divs.length; i++) {
    if (divs[i] == element) {
      document.getElementById(divs[i]).style = 'display: inline';
    } else {
      document.getElementById(divs[i]).style = 'display: none';
    }
  }
}
