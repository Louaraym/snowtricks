var $collectionHolder;

// setup an "add a video" link
var $addVideoButton = $('<button type="button" class="add_video_link">Ajouter une vidéo</button>');
var $newLink = $('<div></div>').append($addVideoButton);

$(document).ready(function() {
    // Get the ul that holds the collection of videos
    $collectionHolder = $('div#trick_videos');

    // add the "add a video" anchor and li to the tags ul
    $collectionHolder.append($newLink);

    // count the current form inputs we have (e.g. 2), use that as the new
    // index when inserting a new item (e.g. 2)
    $collectionHolder.data('index', $collectionHolder.find(':input').length);

    $addVideoButton.on('click', function(e) {
        // add a new video form (see next code block)
        addVideoForm($collectionHolder, $newLink);
    });
});

function addVideoForm($collectionHolder, $newLinkdiv) {
    var prototype = $collectionHolder.data('prototype');
    var index = $collectionHolder.data('index');

    var newForm = prototype;
    // You need this only if you didn't set 'label' => false in your videos field in TrickType
    // Replace '__name__label__' in the prototype's HTML to
    // instead be a number based on how many items we have
    newForm = newForm.replace(/__name__label__/g, index);

    // Replace '__name__' in the prototype's HTML to
    // instead be a number based on how many items we have
    newForm = newForm.replace(/__name__/g, index);

    // increase the index with one for the next item
    $collectionHolder.data('index', index + 1);

    // Display the form in the page in an div, before the "Add a video" link div
    var $newFormdiv = $('<div></div>').append(newForm);
    $newLinkdiv.before($newFormdiv);

    addVideoFormDeleteLink($newFormdiv);

}

function addVideoFormDeleteLink($videoFormdiv) {
    var $removeFormButton = $('<button type="button" class="delete_video_link mb-4">Supprimer</button>');
    $videoFormdiv.append($removeFormButton);

    $removeFormButton.on('click', function(e) {
        // remove the div for the video form
        $videoFormdiv.remove();
    });
}