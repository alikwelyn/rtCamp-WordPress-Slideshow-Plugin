jQuery(document).ready(function($) {
    var slideIndex = 0;
    var slideCount = $('#slides-container .slide').length;

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

    if(slideCount === 0){
        var slideHtml = '';
        slideHtml += '<span>No images selected</span>';
        $('#slides-container').append(slideHtml);
    }

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
                slideHtml += '<button type="button" class="remove-slide button">Remove Slide</button>';
                slideHtml += '</div>';
            }
            $('#slides-container').html(slideHtml);
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

    $('form').submit(function() {
        var slideImages = [];

        $('.slide img').each(function() {
            slideImages.push($(this).attr('src'));
        });

        $('input[name="slider_images"]').val(JSON.stringify(slideImages));

        return true;
    });
});