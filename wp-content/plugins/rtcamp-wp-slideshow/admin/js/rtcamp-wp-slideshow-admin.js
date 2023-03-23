"use strict";
jQuery().ready(function($) {
    var wp = window.wp || {};
    var slideIndex = $('#slides-container .slide').length;

    function updateSlideNames() {
        // loop through all slides and update slide names
        $('#slides-container .slide').each(function(index) {
            $(this).find('h4').text('Slide ' + (index + 1));
            $(this).attr('slider-id', 'slider_order_' + (index + 1)); // Add slider-id attribute
        });
        slideIndex = $('#slides-container .slide').length;
        if (slideIndex === 0) {
            var slideHtml = '';
            slideHtml += 'No images selected';
            $('#slides-container').append(slideHtml);
        }
    }

    updateSlideNames();

    $('#slides-container').sortable({
        update: function(event, ui) {
            // Get the new order of the slide items
            var slideOrder = $('#slides-container .slide').map(function() {
                return $(this).attr('slider-id');
            }).get();

            // Update the hidden input field with the new slide order
            $('#slider_order').remove(); // Remove existing input field
            $('<input>').attr({
                type: 'hidden',
                id: 'slider_order',
                name: 'slider_order',
                value: slideOrder.join(',')
            }).appendTo('form'); // Add new input field with updated slide order
        }
    });

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
        mediaUploader.on('select', function() {
          var attachments = mediaUploader.state().get('selection').toJSON();
          var slideHtml = '';
        
          function createSlideHtml(attachment, index) {
            var sliderId = 'slider_order_' + (slideIndex + index);
            return '<div class="slide" slider-id="' + sliderId + '">' +
            '<h4>Slide ' + (slideIndex + index) + '</h4>' +
            '<img src="' + attachment.url + '" alt="' + attachment.title + '" ' +
            'height="96" width="96" loading="lazy">' +
            '<input type="hidden" name="slider_images[]" value="' + attachment.url + '">' +
            '<button type="button" class="remove-slide">Remove Slide</button>' +
            '</div>';
          }
        
          for (var i = 0; i < attachments.length; i++) {
            slideHtml += createSlideHtml(attachments[i], i);
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
        
          // Reorder the slider order based on the new slide positions
          var slideOrder = [];
          $('#slides-container .slide').each(function() {
            var sliderId = $(this).attr('slider-id');
            slideOrder.push(sliderId);
          });
          $("#slider_order").val(slideOrder.join());
        });
        mediaUploader.open();
    });

    $().on('click', '.remove-slide', function() {
        $(this).closest('.slide').remove();
        updateSlideNames();
    });

    $('.remove-slide').on('click', function(){
        // Find the parent slide and remove it
        $(this).closest('.slide').remove();
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