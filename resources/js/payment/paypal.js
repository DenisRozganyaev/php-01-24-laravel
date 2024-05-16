import '../bootstrap.js'

const selectors = {
    form: '#checkout-form'
}

const errorTemplate = `<span class="invalid-feedback" role="alert">
    <strong>__</strong>
</span>`

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
    onApprove: function (data, actions) {
        return axios.post(`/ajax/paypal/order/${data.orderID}/capture`)
            .then(function (res) {
                return res.data
            }).then(function(orderData) {
                console.log('orderData', orderData)

                iziToast.success({
                    title: 'Transactions success!',
                    position: 'topRight',
                    onClosing: () => {
                        window.location.href = `/orders/${orderData['vendorOrderId']}/thank-you`
                    }
                })

            }).catch(function (orderData) {
                const errorDetail = Array.isArray(orderData.details) && orderData.details[0]

                if (errorDetail && errorDetail.issue === 'INSTRUMENT_DECLINED') {
                    return actions.restart()
                }

                if (errorDetail) {
                    let msg = 'Sorry, your transaction could not be processed.'
                    if (errorDetail.description) {
                        msg += '\n\n' + errorDetail.description
                    }
                    if (orderData.debug_id) {
                        msg += ' (' + orderData.debug_id + ')'
                    }

                    iziToast.danger({
                        title: 'Error',
                        message: msg,
                        position: 'topRight'
                    })
                }
            })
    }

}).render('#paypal-button-container')
