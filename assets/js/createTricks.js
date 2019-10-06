var $collectionHolder;

// setup an "Ajouter une image" link
var $addImageButton = $('<button type="button" class="add_image_link">Ajouter une image</button>');
var $newLinkLi = $('<li></li>').append($addImageButton);

jQuery(document).ready(function() {
    // Get the ul that holds the collection of tags
    $collectionHolder = $('ul.images');

    // add the "add an image" anchor and li to the images ul
    $collectionHolder.append($newLinkLi);

    // count the current form inputs we have (e.g. 2), use that as the new
    // index when inserting a new item (e.g. 2)
    $collectionHolder.data('index', $collectionHolder.find(':input').length);


    $addImageButton.on('click', function(e) {
        // add a new image form (see next code block)
        addImageForm($collectionHolder, $newLinkLi);
    });
});

function addImageForm($collectionHolder, $newLinkLi) {
    // Get the data-prototype explained earlier
    var prototype = $collectionHolder.data('prototype');

    // get the new index
    var index = $collectionHolder.data('index');

    var newForm = prototype;
    // You need this only if you didn't set 'label' => false in your images field in TrickType
    newForm = newForm.replace(/__name__/g, index);

    // increase the index with one for the next item
    $collectionHolder.data('index', index + 2);

    // Display the form in the page in an li, before the "Add an image" link li
    var $newFormLi = $('<li></li>').append(newForm);

    // also add a remove button
    $newFormLi.append('<a href="#" class="remove-image">Supprimer</a>');

    $newLinkLi.before($newFormLi);

    // handle the removal
    $('.remove-image').click(function(e) {
        e.preventDefault();

        $(this).parent().remove();

        return false;
    });
}