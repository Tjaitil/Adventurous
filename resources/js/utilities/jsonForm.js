function JSONForm(form) {
    // Returns JSON object from a form
    var form_data = {};
    for (var i = 0; i < form.length; i++) {
        switch (form[i].tagName) {
            case 'DIV':
                for (var x = 0; x < form[i].children.length; x++) {
                    if (form[i].children[x].tagName != 'INPUT') {
                        continue;
                    }
                    form_data[form[i].children[x].name] =
                        form[i].children[x].value;
                }
                break;
            case 'INPUT':
                if (form[i].name.length == 0) {
                    break;
                }
                if (form[i].type == 'checkbox' && form[i].checked === true) {
                    form_data[form[i].name] = form[i].value;
                }
                form_data[form[i].name] = form[i].value;
                break;
            case 'SELECT':
                if (form[i].name.length > 0) {
                    form_data[form[i].name] =
                        form[i].children[form[i].selectedIndex].value;
                    break;
                }
                break;
            default:
                break;
        }
    }
    return JSON.stringify(form_data);
}
