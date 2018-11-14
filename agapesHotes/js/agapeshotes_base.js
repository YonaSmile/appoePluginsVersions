function disabledAllFields(inputExlude) {
    $('input.mainCourantInput').not(inputExlude).attr('disabled', 'disabled');
}

function activateAllFields() {
    $('input.mainCourantInput').attr('disabled', false);
}