function hideElementById(elementId) {
    document.getElementById(elementId).classList.add('d-none');
}

function showElementById(elementId) {
    document.getElementById(elementId).classList.remove('d-none');
}

function disableButtonById(buttonId) {
    document.getElementById(buttonId).setAttribute('disabled', '');
}

function enableButtonById(buttonId) {
    document.getElementById(buttonId).removeAttribute('disabled');
}

function buildMessageDisplay(message) {
    return '<p><strong>' + message + '</strong></p>';
}
