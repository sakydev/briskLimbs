function hideElement(element) {
    $(element).addClass('d-none');
}

function showElement(element) {
    $(element).removeClass('d-none');
}

function disableButton(button) {
    $(button).prop('disabled', 'disabled');
}

function enableButton(button) {
    $(button).prop('disabled', false);
}

function buildMessageDisplay(message) {
    return '<p><strong>' + message + '</strong></p>';
}
