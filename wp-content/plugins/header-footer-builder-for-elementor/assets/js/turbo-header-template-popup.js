jQuery(document).ready(function ($) {

    $('#tahefobu_display_targets').select2({
        placeholder: "Select Display Targets",
        width: '100%',
        closeOnSelect: false
    });

    $('#tahefobu_edit_display_targets').select2({
        placeholder: "Select Display Targets",
        width: '100%',
        closeOnSelect: false
    });
    
// Apply Select2 styling to both selects
    $('#tahefobu_include_pages, #tahefobu_exclude_pages').select2({
        width: '100%',
        placeholder: 'Select pages',
        allowClear: true,
        closeOnSelect: false,
        dropdownParent: $('#tahefobu-header-template-popup')
    });
    
    // 1. Intercept "Add New" button
    const addNewBtn = $('a.page-title-action');
    if (addNewBtn.length && window.location.href.includes('post_type=tahefobu_header')) {
        addNewBtn.on('click', function (e) {
            e.preventDefault();
            $('#tahefobu-header-template-popup').fadeIn();
        });
    }

    // 2. Cancel popup
    $('#tahefobu-cancel-template').on('click', function () {
        $('#tahefobu-header-template-popup').fadeOut();
    });

    // 3. Create template via AJAX
    $('#tahefobu-create-template').on('click', function () {
    const $button = $(this);
    const title = $('#tahefobu-header-template-title').val().trim();
    const includePages = $('#tahefobu_include_pages').val() || [];
    const excludePages = $('#tahefobu_exclude_pages').val() || [];
    const isSticky = $('#tahefobu_is_sticky').is(':checked') ? 1 : 0;
    const hasAnimation = $('#tahefobu_has_animation').is(':checked') ? 1 : 0;
    const displayTargets = $('#tahefobu_display_targets').val() || [];

    if (!title) {
        alert('Please enter a template name.');
        return;
    }

    // Disable button and show loading state
    $button.prop('disabled', true).text('Creating...');

    $.post(ajaxurl, {
        action: 'tahefobu_create_header_template',
        title: title,
        include_pages: includePages,
        exclude_pages: excludePages,
        is_sticky: isSticky,
        has_animation: hasAnimation,
        display_targets: displayTargets,
        _ajax_nonce: tahefobu_header_condition_nonce.nonce
    }, function (response) {
        if (response.success && response.data.edit_link) {
            // Show success state briefly before redirect
            $button.text('✓ Created').addClass('button-primary');
            setTimeout(function() {
                window.location.href = response.data.edit_link;
            }, 500);
        } else {
            alert(response.data.message || 'Something went wrong.');
            $button.prop('disabled', false).text('Create');
        }
    }).fail(function() {
        alert('Error creating template. Please try again.');
        $button.prop('disabled', false).text('Create');
    });
});



    // Select All Include
    $(document).on('change', '#select_all_include', function () {
        if ($(this).is(':checked')) {
            $('#tahefobu_include_pages > option').prop('selected', true);
        } else {
            $('#tahefobu_include_pages > option').prop('selected', false);
        }
        $('#tahefobu_include_pages').trigger('change');
    });

    // Select All Exclude
    $(document).on('change', '#select_all_exclude', function () {
        if ($(this).is(':checked')) {
            $('#tahefobu_exclude_pages > option').prop('selected', true);
        } else {
            $('#tahefobu_exclude_pages > option').prop('selected', false);
        }
        $('#tahefobu_exclude_pages').trigger('change');
    });

    // Edit Conditions Button Click
    $(document).on('click', '.tahefobu-edit-conditions-button', function () {
        const postId = $(this).data('post-id');
        const conditions = $(this).data('conditions');
        
        $('#tahefobu_conditions_post_id').val(postId);
        
        // Set values from cached data
        if (conditions) {
            // Set include pages
            $('#tahefobu_edit_include_pages').val(conditions.include || []).trigger('change');
            
            // Set exclude pages
            $('#tahefobu_edit_exclude_pages').val(conditions.exclude || []).trigger('change');
            
            // Set display targets
            $('#tahefobu_edit_display_targets').val(conditions.display_targets || []).trigger('change');
            
            // Set checkboxes
            $('#tahefobu_edit_is_sticky').prop('checked', conditions.is_sticky == 1);
            $('#tahefobu_edit_has_animation').prop('checked', conditions.has_animation == 1);
        }
        
        // Show modal immediately with data already loaded
        $('#tahefobu-conditions-modal').fadeIn();
    });

    // Cancel Edit Conditions
    $('#tahefobu-cancel-condition-edit').on('click', function () {
        $('#tahefobu-conditions-modal').fadeOut();
    });

    // Save Edit Conditions
    $('#tahefobu-save-condition-edit').on('click', function () {
        const $button = $(this);
        const postId = $('#tahefobu_conditions_post_id').val();
        const includePages = $('#tahefobu_edit_include_pages').val() || [];
        const excludePages = $('#tahefobu_edit_exclude_pages').val() || [];
        const isSticky = $('#tahefobu_edit_is_sticky').is(':checked') ? 1 : 0;
        const hasAnimation = $('#tahefobu_edit_has_animation').is(':checked') ? 1 : 0;
        const displayTargets = $('#tahefobu_edit_display_targets').val() || [];

        // Disable button and show loading state
        $button.prop('disabled', true).text('Saving...');

        $.post(ajaxurl, {
            action: 'tahefobu_save_header_conditions',
            post_id: postId,
            include_pages: includePages,
            exclude_pages: excludePages,
            is_sticky: isSticky,
            has_animation: hasAnimation,
            display_targets: displayTargets,
            _ajax_nonce: tahefobu_header_condition_nonce.nonce
        }, function (response) {
            if (response.success) {
                // Update the button's data attribute with new values
                const $editButton = $('.tahefobu-edit-conditions-button[data-post-id="' + postId + '"]');
                const newData = {
                    include: includePages.map(Number), // Convert to numbers
                    exclude: excludePages.map(Number), // Convert to numbers
                    is_sticky: isSticky,
                    has_animation: hasAnimation,
                    display_targets: displayTargets
                };
                
                // Update using jQuery data() method which updates the internal cache
                $editButton.data('conditions', newData);
                
                // Also update the attribute for persistence
                $editButton.attr('data-conditions', JSON.stringify(newData));
                
                // Show success feedback
                $button.text('✓ Saved').addClass('button-primary');
                
                // Close modal after short delay
                setTimeout(function() {
                    $('#tahefobu-conditions-modal').fadeOut();
                    $button.prop('disabled', false).text('Update').removeClass('button-primary');
                }, 800);
            } else {
                alert('Error saving conditions');
                $button.prop('disabled', false).text('Update');
            }
        }).fail(function() {
            alert('Error saving conditions');
            $button.prop('disabled', false).text('Update');
        });
    });

    // Apply Select2 to edit modal selects
    $('#tahefobu_edit_include_pages, #tahefobu_edit_exclude_pages').select2({
        width: '100%',
        placeholder: 'Select pages',
        allowClear: true,
        closeOnSelect: false,
        dropdownParent: $('#tahefobu-conditions-modal')
    });

});
