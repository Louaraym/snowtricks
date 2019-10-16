import Dropzone from 'dropzone'
import 'dropzone/dist/min/dropzone.min.css'

Dropzone.autoDiscover = false;

$(document).ready(function() {
    var trickImageList = new TrickImageList($('.js-trickImage-list'));

    initializeDropzone(trickImageList);

    var $locationSelect = $('.js-trick-form-location');
    var $specificLocationTarget = $('.js-specific-location-target');

    $locationSelect.on('change', function(e) {
        $.ajax({
            url: $locationSelect.data('specific-location-url'),
            data: {
                location: $locationSelect.val()
            },
            success: function (html) {
                if (!html) {
                    $specificLocationTarget.find('select').remove();
                    $specificLocationTarget.addClass('d-none');
                    return;
                }
                // Replace the current field and show
                $specificLocationTarget
                    .html(html)
                    .removeClass('d-none')
            }
        });
    });
});


class TrickImageList
{
    constructor($element) {

        this.$element = $element;
        this.trickImages = [];
        this.render();
        this.$element.on('click', '.js-trickImage-delete', (event) => {
            this.handleTrickImageDelete(event);
        });

        $.ajax({
            url: this.$element.data('url')
        }).then(data => {
            this.trickImages = data;
            this.render();
        })
    }

    addTrickImage(trickImage) {
        this.trickImages.push(trickImage);
        this.render();
    }

    handleTrickImageDelete(event) {

        const $li = $(event.currentTarget).closest('.list-group-item');
        const id = $li.data('id');
        $li.addClass('disabled');
        $.ajax({
            url: '/admin/trick/image/'+id,
            method: 'DELETE'
        }).then(() => {
            this.trickImages = this.trickImages.filter(trickImage => {
                return trickImage.id !== id;
            });
            this.render();
        });
    }

    render() {
        const itemsHtml = this.trickImages.map(trickImage => {
        return `
                <li class="list-group-item d-flex justify-content-between align-items-center" data-id="${trickImage.id}">
                    ${trickImage.originalFilename}
                    <span>
                        <button class="js-trickImage-delete btn btn-link btn-sm"><span class="fa fa-trash"></span></button>
                    </span>
                </li>
                `
        });
        this.$element.html(itemsHtml.join(''));
    }
}

/**
 * @param {TrickImageList} trickImageList
 */
function initializeDropzone(trickImageList) {

    var formElement = document.querySelector('.js-trickImage-dropzone');

    if (!formElement) {
        return;
    }

    var dropzone = new Dropzone(formElement, {

        paramName: 'trickImage',

        init: function() {
            this.on('success', function(file, data) {
                trickImageList.addTrickImage(data);
            });
            this.on('error', function(file, data) {
                if (data.detail) {
                    this.emit('error', file, data.detail);
                }
            });
        }
    });
}