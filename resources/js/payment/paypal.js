import '../bootstrap.js'

const selectors = {
    form: '#checkout-form'
}

const errorTemplate = `<span class="invalid-feedback" role="alert">
    <strong>__</strong>
</span>`;

function getFields() {
    return $(selectors.form).serializeArray()
        .reduce((obj, item) => {
            obj[item.name] = item.value
            return obj
        }, {})
}

function isEmptyFields() {
    const fields = getFields()

    return Object.values(fields).some((val) => val.length < 1)
}

// Render the PayPal button into #paypal-button-container
paypal.Buttons({
    onInit: function (data, actions) {
        actions.disable()

        $(selectors.form).change(() => {
            if (!isEmptyFields()) {
                actions.enable()
            }
        })
    },

    onClick: function (data, actions) {
        if (isEmptyFields()) {
            iziToast.warning({
                title: 'Please fill the form',
                position: 'topRight'
            })
        }

        $(selectors.form).find('.is-invalid').removeClass('is-invalid')
        $(selectors.form).find('.invalid-feedback').remove()
    },

    // Call your server to set up the transaction
    createOrder: function (data, actions) {
        return axios.post('/ajax/paypal/order/create', getFields())
            .then((response) => {
                console.log('success', response)
                return response.data.vendor_order_id
            })
            .catch((error) => {
                const response = error.response.data
                if (response.errors) {
                    const keys = Object.keys(response.errors)

                    keys.map((key) => {
                        let $field = $(`input[name="${key}"]`)
                        $field.addClass('is-invalid')
                        $field.parent().append(
                            errorTemplate.replace(
                                '__',
                                response.errors[key][0]
                            )
                        )
                    })
                }
            })
    },
    // ''
    // php analog: ".... $variable ....."
    // `.... ${ variable_name } .....`
    // Call your server to finalize the transaction
    onApprove: function (data, actions) {
        return axios.post(`/ajax/paypal/order/${data.orderID}/capture`)
        .then(function (res) {
            return res.data;
        }).then(function (orderData) {
            // Three cases to handle:
            //   (1) Recoverable INSTRUMENT_DECLINED -> call actions.restart()
            //   (2) Other non-recoverable errors -> Show a failure message
            //   (3) Successful transaction -> Show confirmation or thank you

            // This example reads a v2/checkout/orders capture response, propagated from the server
            // You could use a different API or structure for your 'orderData'
            var errorDetail = Array.isArray(orderData.details) && orderData.details[0];

            if (errorDetail && errorDetail.issue === 'INSTRUMENT_DECLINED') {
                return actions.restart(); // Recoverable state, per:
                // https://developer.paypal.com/docs/checkout/integration-features/funding-failure/
            }

            if (errorDetail) {
                var msg = 'Sorry, your transaction could not be processed.';
                if (errorDetail.description) msg += '\n\n' + errorDetail.description;
                if (orderData.debug_id) msg += ' (' + orderData.debug_id + ')';
                return alert(msg); // Show a failure message (try to avoid alerts in production environments)
            }

            // Successful capture! For demo purposes:
            console.log('Capture result', orderData, JSON.stringify(orderData, null, 2));
            var transaction = orderData.purchase_units[0].payments.captures[0];
            alert('Transaction ' + transaction.status + ': ' + transaction.id + '\n\nSee console for all available details');

            // Replace the above to show a success message within this page, e.g.
            // const element = document.getElementById('paypal-button-container');
            // element.innerHTML = '';
            // element.innerHTML = '<h3>Thank you for your payment!</h3>';
            // Or go to another URL:  actions.redirect('thank_you.html');
        });
    }

}).render('#paypal-button-container');
