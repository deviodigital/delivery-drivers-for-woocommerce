jQuery(document).ready(function ($) {
    // Initialize SignaturePad.
    var canvas = document.getElementById('signature-canvas');
    var signaturePad = new SignaturePad(canvas);

    // Clear Signature Button.
    $('#clear-signature').on('click', function (e) {
        e.preventDefault();
        signaturePad.clear();
    });

    // Save Signature Data on Custom Button Click.
    $('#save-signature-button').on('click', function (e) {
        e.preventDefault();
        var signatureData = signaturePad.toDataURL();
        saveSignatureToOrder(signatureData);
    });

    // Load Saved Signature on Order Details Page.
    if (script.order_id && $('#customer-signature').val()) {
        var savedSignature = $('#customer-signature').val();
        signaturePad.fromDataURL(savedSignature);
    }
});

// Function to Save Signature to Order.
function saveSignatureToOrder(signatureData) {
    var orderId = script.order_id;
    var data = {
        action: 'save_customer_signature',
        order_id: orderId,
        customer_signature: signatureData,
    };

    $.post(script.ajax_url, data, function (response) {
        console.log(response);
    });
}
