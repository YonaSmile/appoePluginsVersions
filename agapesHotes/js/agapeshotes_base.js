function disabledAllFields(inputExlude) {
    $('input.sensibleField').not(inputExlude).attr('disabled', 'disabled');
}

function activateAllFields() {
    $('input.sensibleField').attr('disabled', false);
}
