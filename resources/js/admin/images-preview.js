const selectors = {
    thumbnailPreview: '#thumbnail-preview',
    thumbnailInput: '#thumbnail',
    imagesPreview: '#images-wrapper',
    imagesInput: '#images',
};

$(document).ready(function() {
    if (window.FileReader) {
        $(selectors.imagesInput).on('change', function() {
            let counter = 0, file;
            const template = "<div class='mb-4'><img src='_url_' style='width: 100%' /></div>";

            $(selectors.imagesPreview).html('');

            while(file = this.files[counter++]) {
                const reader = new FileReader()

                reader.onloadend = (() => {
                    return (e) => {
                        const img = template.replace('_url_', e.target.result)
                        $(selectors.imagesPreview).append(img)
                    }
                })(file)
                reader.readAsDataURL(file)
            }
        })

        $(selectors.thumbnailInput).on('change', function() {
            const reader = new FileReader()
            reader.onloadend = (e) => {
                $(selectors.thumbnailPreview).attr('src', e.target.result).show()
            }
            reader.readAsDataURL(this.files[0])
        })
    }
})
