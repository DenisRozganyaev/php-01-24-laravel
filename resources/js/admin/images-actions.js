import '../bootstrap.js'

const selectors = {
    wrapper: '#images-wrapper',
    removeBtn: '.image-remove',
    imageItem: '.image-item',
    input: '#images-upload',
    addBtn: '.image-add',
    addWrapper: '.add-btn-wrapper',
    spinner: '.spinner-border'
}

const template = `
<div class="row flex-row mb-4 align-items-center justify-content-center image-item">
    <div class="col-8 col-md-10">
        <img src="_url_" style="width: 100%" />
    </div>
    <div class="col-4 col-md-2">
        <button class="btn btn-danger image-remove" data-url="/ajax/images/_id_"><i class="fa-solid fa-trash-can"></i></button>
    </div>
</div>
`;

$(document).ready(function() {

    $(document).on('click', selectors.addBtn, function(e) {
        e.preventDefault()
        $(this).parents(selectors.addWrapper).find(selectors.input).click()
    })

    $(document).on('change', selectors.input, function(e) {
        e.preventDefault()

        const uploadUrl = $(selectors.wrapper).data('url')
        const data = new FormData()

        $(selectors.spinner).show()
        $(this).addClass('disabled')

        for(let i = 0; i < this.files.length; i++) {
            data.append(`images[${i}]`, this.files[i], this.files[i].name)
        }


        axios.post(uploadUrl, data, {
            headers: { "Content-Type": "multipart/form-data" }
        }).then((response) => {
            if (response.data.length > 0) {
                for(const key in response.data) {
                    const imageBlock = template
                        .replace('_url_', response.data[key].url)
                        .replace('_id_', response.data[key].id)

                    $(selectors.wrapper).append(imageBlock)
                }
            }
            iziToast.success({
                message: "Image(s) was/were added",
                position: 'topRight'
            });
        }).catch((error) => {
            console.error(error)
            iziToast.warning({
                message: error.data.message,
                position: 'topRight'
            });
        }).finally(() => {
            $(selectors.addBtn).removeClass('disabled')
            $(selectors.spinner).hide()
        })
    })

    $(document).on('click', selectors.removeBtn, function(e) {
        e.preventDefault()

        const $btn = $(this)

        $(this).addClass('disabled')

        axios.delete($btn.data('url'), {
            responseType: 'json'
        }).then((response) => {
            iziToast.success({
                message: response.data.message,
                position: 'topRight'
            });
            $btn.parents(selectors.imageItem).remove()
        }).catch((error) => {
            console.error(error)
            iziToast.warning({
                message: error.data.message,
                position: 'topRight'
            });
            $btn.removeClass('disabled')
        })
    })
})
