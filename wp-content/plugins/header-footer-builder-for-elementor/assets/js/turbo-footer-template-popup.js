jQuery(document).ready(function ($) {

    $('#tahefobu_footer_display_targets').select2({
        placeholder: "Select Display Targets",
        width: '100%',
        closeOnSelect: false
    });

    $('#tahefobu_footer_edit_display_targets').select2({
        placeholder: "Select Display Targets",
        width: '100%',
        closeOnSelect: false
    });
    
    // Apply Select2 styling to both selects
    $('#tahefobu_footer_include_pages, #tahefobu_footer_exclude_pages').select2({
        width: '100%',
        placeholder: 'Select pages',
        allowClear: true
    });
    
    // 1. Intercept "Add New" button
    const addNewBtn = $('a.page-title-action');
    if (addNewBtn.length && window.location.href.includes('post_type=tahefobu_footer')) {
        addNewBtn.on('click', function (e) {
            e.preventDefault();
            $('#tahefobu-footer-template-popup').fadeIn();
        });
    }

    // 2. Cancel popup
    $('#tahefobu-cancel-footer-template').on('click', function () {
        $('#tahefobu-footer-template-popup').fadeOut();
    });

    // 3. Create template via AJAX
    $('#tahefobu-create-footer-template').on('click', function () {
        const title = $('#tahefobu-footer-template-title').val().trim();
        const includePages = $('#tahefobu_footer_include_pages').val() || [];
        const excludePages = $('#tahefobu_footer_exclude_pages').val() || [];
        const displayTargets = $('#tahefobu_footer_display_targets').val() || [];

        if (!title) {
            alert('Please enter a template name.');
            return;
        }

        $.post(ajaxurl, {
            action: 'tahefobu_create_footer_template',
            title: title,
            include_pages: includePages,
            exclude_pages: excludePages,
            display_targets: displayTargets,
            _ajax_nonce: tahefobu_footer_condition_nonce.nonce
        }, function (response) {
            if (response.success && response.data.edit_url) {
                window.location.href = response.data.edit_url;
            } else {
                alert(response.data.message || 'Something went wrong.');
            }
        });
    });

    // Select All Include Footer
    $(document).on('change', '#select_all_include_footer', function () {
        if ($(this).is(':checked')) {
            $('#tahefobu_footer_include_pages > option').prop('selected', true);
        } else {
            $('#tahefobu_footer_include_pages > option').prop('selected', false);
        }
        $('#tahefobu_footer_include_pages').trigger('change');
    });

    // Select All Exclude Footer
    $(document).on('change', '#select_all_exclude_footer', function () {
        if ($(this).is(':checked')) {
            $('#tahefobu_footer_exclude_pages > option').prop('selected', true);
        } else {
            $('#tahefobu_footer_exclude_pages > option').prop('selected', false);
        }
        $('#tahefobu_footer_exclude_pages').trigger('change');
    });

    // Edit Conditions Button Click
    $(document).on('click', '.tahefobu-footer-edit-conditions-button', function () {
        const postId = $(this).data('post-id');
        $('#tahefobu_footer_conditions_post_id').val(postId);
        
        // Load existing conditions
        $.post(ajaxurl, {
            action: 'tahefobu_get_footer_conditions_popup',
            post_id: postId,
            _ajax_nonce: tahefobu_footer_condition_nonce.nonce
        }, function (response) {
            if (response.success) {
                const data = response.data;
                
                // Set include pages
                $('#tahefobu_footer_edit_include_pages').val(data.include).trigger('change');
                
                // Set exclude pages
                $('#tahefobu_footer_edit_exclude_pages').val(data.exclude).trigger('change');
                
                // Set display targets
                $('#tahefobu_footer_edit_display_targets').val(data.targets).trigger('change');
                
                // Show modal
                $('#tahefobu-footer-conditions-modal').fadeIn();
            }
        });
    });

    // Cancel Edit Conditions
    $('#tahefobu-cancel-footer-condition-edit').on('click', function () {
        $('#tahefobu-footer-conditions-modal').fadeOut();
    });

    // Save Edit Conditions
    $('#tahefobu-save-footer-condition-edit').on('click', function () {
        const postId = $('#tahefobu_footer_conditions_post_id').val();
        const includePages = $('#tahefobu_footer_edit_include_pages').val() || [];
        const excludePages = $('#tahefobu_footer_edit_exclude_pages').val() || [];
        const displayTargets = $('#tahefobu_footer_edit_display_targets').val() || [];

        $.post(ajaxurl, {
            action: 'tahefobu_save_footer_conditions',
            post_id: postId,
            include_pages: includePages,
            exclude_pages: excludePages,
            display_targets: displayTargets,
            _ajax_nonce: tahefobu_footer_condition_nonce.nonce
        }, function (response) {
            if (response.success) {
                $('#tahefobu-footer-conditions-modal').fadeOut();
                location.reload(); // Refresh to show updated data
            } else {
                alert('Error saving conditions');
            }
        });
    });

    // Apply Select2 to edit modal selects
    $('#tahefobu_footer_edit_include_pages, #tahefobu_footer_edit_exclude_pages').select2({
        width: '100%',
        placeholder: 'Select pages',
        allowClear: true
    });

});
