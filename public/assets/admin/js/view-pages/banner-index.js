"use strict";

function readURL(input) {
    if (input.files && input.files[0]) {
        let reader = new FileReader();

        reader.onload = function (e) {
            $('#viewer').attr('src', e.target.result);
        }
        reader.readAsDataURL(input.files[0]);
    }
}

$("#customFileEg1").change(function () {
    readURL(this);
});

$(document).ready(function() {
    $('#video-input').on('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const url = URL.createObjectURL(file);
            const $previewVideo = $(this).siblings('label').find('.preview-video');
            const $uploadIcon = $(this).siblings('label').find('.upload-icon, .upload-text');
            const $deleteBtn = $(this).siblings('.delete_video');

            $previewVideo.attr('src', url).show();
            $uploadIcon.hide();
            $deleteBtn.show();
        }
    });

    $('.delete_video').on('click', function(e) {
        e.preventDefault();
        const $videoInput = $(this).siblings('input[type="file"]');
        const $previewVideo = $(this).siblings('label').find('.preview-video');
        const $uploadIcon = $(this).siblings('label').find('.upload-icon, .upload-text');

        $videoInput.val('');
        $previewVideo.attr('src', '').hide();
        $uploadIcon.show();
        $(this).hide();
    });
});
