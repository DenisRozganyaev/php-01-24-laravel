import './app'

$(document).on('change', '#counter', function(e) {
    // e.preventDefault()
    $(this).parent().submit()
})
