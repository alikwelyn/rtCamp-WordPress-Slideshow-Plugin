jQuery(document).ready(function($) {
    var slideIndex = $('#slides-container .slide').length;

    function updateSlideNames() {
        // loop through all slides and update slide names
        $('#slides-container .slide').each(function(index) {
            $(this).find('h4').text('Slide ' + (index + 1));
        });
        slideIndex = $('#slides-container .slide').length;
        if(slideIndex === 0){
            var slideHtml = '';
            slideHtml += 'No images selected';
            $('#slides-container').append(slideHtml);
        }
    }

    updateSlideNames();

    $('#add-slide').click(function() {
        var mediaUploader;
        slideIndex++;
        if ( mediaUploader ) {
            mediaUploader.open();
            return;
        }
        mediaUploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Image',
            button: {
                text: 'Use this image'
            },
            multiple: true // enable multiple image selection
        });
        mediaUploader.on( 'select', function() {
            var attachments = mediaUploader.state().get('selection').toJSON();
            var slideHtml = '';
            for (var i = 0; i < attachments.length; i++) {
                var attachment = attachments[i];
                slideHtml += '<div class="slide">';
                slideHtml += '<h4>Slide ' + (slideIndex + i) + '</h4>';
                slideHtml += '<img src="' + attachment.url + '" alt="' + attachment.title + '" height="96" width="96" loading="lazy">';
                slideHtml += '<input type="hidden" name="slider_images[]" value="' + attachment.url + '">';
                slideHtml += '<button type="button" class="remove-slide">Remove Slide</button>';
                slideHtml += '</div>';
            }
            if (slideIndex === 1 && $('#slides-container').text() === 'No images selected') {
                $('#slides-container').html(slideHtml);
            } else {
                $('#slides-container').append(slideHtml);
            }
            slideIndex += attachments.length;

            // Remove "No images selected" text if it's present
            if ($('#slides-container').text() === 'No images selected') {
                $('#slides-container').empty();
            }
        });
        mediaUploader.open();
    });

    $(document).on('click', '.remove-slide', function() {
        $(this).closest('.slide').remove();
        updateSlideNames();
    });

    $('.remove-slide').on('click', function(){
        // Find the parent slide and remove it
        $(this).closest('.slide').remove();
        // Get the index of the removed slide
        var slideIndex = $(this).closest('.slide').index();
        // Create a hidden input field to mark the image as removed
        var inputName = 'removed_slider_images[]';
        var inputValue = $(this).siblings('input').val();
        $('<input>').attr({
            type: 'hidden',
            name: inputName,
            value: inputValue
        }).appendTo('form');
    });

    $('form').submit(function() {
        var slideImages = [];

        $('.slide img').each(function() {
            slideImages.push($(this).attr('src'));
        });

        $('input[name="slider_images"]').val(JSON.stringify(slideImages));

        return true;
    });
});