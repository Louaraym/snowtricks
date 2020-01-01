$(document).ready(function() {

    $('.js-like-trick').on('click', function(e) {
        e.preventDefault();

        var $link = $(e.currentTarget);
        $link.toggleClass('far fa-thumbs-up').toggleClass('far fa-thumbs-up');

        $.ajax({
            method: 'POST',
            url: $link.attr('href')
        }).done(function(data) {
            $('.js-like-trick-count').html(data.likes);
        })
    });

    $('.js-dislike-trick').on('click', function(e) {
        e.preventDefault();

        var $link = $(e.currentTarget);
        $link.toggleClass('far fa-thumbs-down').toggleClass('far fa-thumbs-down');

        $.ajax({
            method: 'POST',
            url: $link.attr('href')
        }).done(function(data) {
            $('.js-dislike-trick-count').html(data.likes);
        })
    });

});