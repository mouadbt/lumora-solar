/* global thfbDash, thfbTemplates, thfbTagLabels, ajaxurl */
jQuery( function ( $ ) {
    'use strict';

    var nonce    = thfbDash.nonce;
    var pages    = thfbDash.pages;   // [{id, title}, ...]
    var str      = thfbDash.strings;
    var tagLabels = window.thfbTagLabels || {};

    /* ── Populate page selects ─────────────────────────────── */
    function buildPageOptions( selectedIds ) {
        selectedIds = selectedIds || [];
        return pages.map( function ( p ) {
            var sel = selectedIds.indexOf( String( p.id ) ) !== -1 ? ' selected' : '';
            return '<option value="' + p.id + '"' + sel + '>' + escHtml( p.title ) + '</option>';
        } ).join( '' );
    }

    function initPageSelect( $el, selectedIds ) {
        $el.html( buildPageOptions( selectedIds ) );
        if ( $el.data( 'select2' ) ) { $el.select2( 'destroy' ); }
        $el.select2( {
            width: '100%',
            placeholder: 'Select pages…',
            allowClear: true,
            closeOnSelect: false,
            dropdownParent: $el.closest( '.thfb-modal' )
        } );
    }

    function initTargetSelect( $el, selectedVals ) {
        selectedVals = selectedVals || [];
        if ( $el.data( 'select2' ) ) { $el.select2( 'destroy' ); }
        $el.select2( {
            width: '100%',
            placeholder: 'Select conditions…',
            allowClear: true,
            closeOnSelect: false,
            dropdownParent: $el.closest( '.thfb-modal' )
        } );
        $el.val( selectedVals ).trigger( 'change' );
    }

    /* ── Escape helper ─────────────────────────────────────── */
    function escHtml( s ) {
        return String( s )
            .replace( /&/g, '&amp;' )
            .replace( /</g, '&lt;' )
            .replace( />/g, '&gt;' )
            .replace( /"/g, '&quot;' );
    }

    /* ── Condition tag HTML ────────────────────────────────── */
    function conditionTagsHtml( targets ) {
        if ( ! targets || ! targets.length ) {
            return '<span class="thfb-condition-none">Not set</span>';
        }
        return targets.map( function ( t ) {
            var lbl = tagLabels[ t ] || t;
            return '<span class="thfb-condition-tag">' + escHtml( lbl ) + '</span>';
        } ).join( '' );
    }

    /* ── Build a table row HTML ────────────────────────────── */
    function buildRow( tpl ) {
        var isHeader  = tpl.type === 'tahefobu_header';
        var isActive  = tpl.status === 'publish';
        var icon      = isHeader ? 'dashicons-layout' : 'dashicons-align-center';
        var badgeCls  = isActive ? 'thfb-badge-active' : 'thfb-badge-draft';
        var badgeLbl  = isActive ? 'Active' : 'Draft';
        var stickyBadge = ( isHeader && tpl.is_sticky )
            ? '<span class="thfb-badge" style="background:#f3f0ff;color:#7c3aed;font-size:10px;padding:2px 7px;">Sticky</span>' : '';
        var animBadge = ( isHeader && tpl.has_animation )
            ? '<span class="thfb-badge" style="background:#fff8ee;color:#e67e22;font-size:10px;padding:2px 7px;">Animated</span>' : '';

        return '<tr data-id="' + tpl.id + '" data-type="' + escHtml( tpl.type ) + '">' +
            '<td>' +
                '<div class="thfb-template-name">' +
                    '<span class="dashicons ' + icon + '"></span>' +
                    '<div>' +
                        '<strong>' + escHtml( tpl.title ) + '</strong>' +
                        stickyBadge + animBadge +
                        '<div class="thfb-template-meta">Modified: ' + escHtml( tpl.modified ) + '</div>' +
                    '</div>' +
                '</div>' +
            '</td>' +
            '<td>' +
                '<button class="thfb-badge ' + badgeCls + ' thfb-toggle-status" data-id="' + tpl.id + '" title="Click to toggle" style="cursor:pointer;border:none;background:none;padding:0;">' +
                    badgeLbl +
                '</button>' +
            '</td>' +
            '<td class="thfb-conditions-cell">' + conditionTagsHtml( tpl.targets ) + '</td>' +
            '<td>' +
                '<div class="thfb-row-actions">' +
                    '<a href="' + escHtml( tpl.edit_url ) + '" class="thfb-action-btn thfb-action-edit">' +
                        '<span class="dashicons dashicons-edit"></span>Edit' +
                    '</a>' +
                    '<button class="thfb-action-btn thfb-action-conditions thfb-open-conditions" data-id="' + tpl.id + '">' +
                        '<span class="dashicons dashicons-admin-settings"></span>Conditions' +
                    '</button>' +

                    '<button class="thfb-action-btn thfb-action-delete thfb-delete-tpl" data-id="' + tpl.id + '">' +
                        '<span class="dashicons dashicons-trash"></span>Delete' +
                    '</button>' +
                '</div>' +
            '</td>' +
        '</tr>';
    }

    /* ── Ensure table exists in a wrap ─────────────────────── */
    function ensureTable( $wrap ) {
        if ( $wrap.find( 'table.thfb-template-table' ).length ) return;
        $wrap.html(
            '<table class="thfb-template-table">' +
                '<thead><tr>' +
                    '<th>Name</th><th>Status</th><th>Display Conditions</th><th>Actions</th>' +
                '</tr></thead>' +
                '<tbody></tbody>' +
            '</table>'
        );
    }

    /* ── Open Create Modal ─────────────────────────────────── */
    function openCreateModal( type ) {
        var $m = $( '#thfb-create-modal' );
        $( '#thfb-create-type' ).val( type );
        $( '#thfb-create-modal-title' ).text( type === 'footer' ? 'Create New Footer' : 'Create New Header' );
        $( '#thfb-create-name-label' ).contents().first()[0].textContent = type === 'footer' ? 'Footer Name ' : 'Header Name ';
        $( '#thfb-create-title' )
            .val( '' )
            .attr( 'placeholder', type === 'footer' ? 'e.g. Main Footer' : 'e.g. Main Header' );
        $( '#thfb-create-sticky' ).prop( 'checked', false );
        $( '#thfb-create-animation' ).prop( 'checked', false );
        $( '#thfb-create-header-opts' ).toggle( type === 'header' );

        initTargetSelect( $( '#thfb-create-targets' ), [] );
        initPageSelect( $( '#thfb-create-include' ), [] );
        initPageSelect( $( '#thfb-create-exclude' ), [] );

        $m.fadeIn( 150 );
        setTimeout( function () { $( '#thfb-create-title' ).focus(); }, 200 );
    }

    function closeCreateModal() { $( '#thfb-create-modal' ).fadeOut( 150 ); }

    /* ── Open Conditions Modal ─────────────────────────────── */
    function openConditionsModal( postId ) {
        var tpl = null;
        ( window.thfbTemplates || [] ).forEach( function ( t ) {
            if ( String( t.id ) === String( postId ) ) tpl = t;
        } );
        if ( ! tpl ) return;

        var isHeader = tpl.type === 'tahefobu_header';
        $( '#thfb-cond-post-id' ).val( postId );
        $( '#thfb-cond-post-type' ).val( tpl.type );
        $( '#thfb-cond-header-opts' ).toggle( isHeader );
        $( '#thfb-cond-sticky' ).prop( 'checked', !! tpl.is_sticky );
        $( '#thfb-cond-animation' ).prop( 'checked', !! tpl.has_animation );

        initTargetSelect( $( '#thfb-cond-targets' ), tpl.targets || [] );
        initPageSelect( $( '#thfb-cond-include' ), tpl.include || [] );
        initPageSelect( $( '#thfb-cond-exclude' ), tpl.exclude || [] );

        $( '#thfb-conditions-modal' ).fadeIn( 150 );
    }

    function closeConditionsModal() { $( '#thfb-conditions-modal' ).fadeOut( 150 ); }

    /* ── Update in-memory template store ───────────────────── */
    function updateTemplateStore( tpl ) {
        var found = false;
        window.thfbTemplates = ( window.thfbTemplates || [] ).map( function ( t ) {
            if ( String( t.id ) === String( tpl.id ) ) { found = true; return tpl; }
            return t;
        } );
        if ( ! found ) window.thfbTemplates.push( tpl );
    }

    function removeFromStore( postId ) {
        window.thfbTemplates = ( window.thfbTemplates || [] ).filter( function ( t ) {
            return String( t.id ) !== String( postId );
        } );
    }

    /* ── Button: open create modal ─────────────────────────── */
    $( '#thfb-new-header-btn, #thfb-new-header-btn2, #thfb-qa-new-header' ).on( 'click', function () { openCreateModal( 'header' ); } );
    $( '#thfb-new-footer-btn, #thfb-new-footer-btn2, #thfb-qa-new-footer' ).on( 'click', function () { openCreateModal( 'footer' ); } );



    /* ── Close modals ──────────────────────────────────────── */
    $( '#thfb-create-close, #thfb-create-cancel' ).on( 'click', closeCreateModal );
    $( '#thfb-cond-close,   #thfb-cond-cancel'   ).on( 'click', closeConditionsModal );
    $( '.thfb-modal-overlay' ).on( 'click', function ( e ) {
        if ( $( e.target ).hasClass( 'thfb-modal-overlay' ) ) {
            closeCreateModal(); closeConditionsModal();
        }
    } );
    $( document ).on( 'keydown', function ( e ) {
        if ( e.key === 'Escape' ) { closeCreateModal(); closeConditionsModal(); }
    } );

    /* ── Submit: Create template ───────────────────────────── */
    $( '#thfb-create-submit' ).on( 'click', function () {
        var $btn  = $( this );
        var title = $.trim( $( '#thfb-create-title' ).val() );
        var type  = $( '#thfb-create-type' ).val();

        if ( ! title ) {
            $( '#thfb-create-title' ).focus().css( 'border-color', '#e53e3e' );
            return;
        }
        $( '#thfb-create-title' ).css( 'border-color', '' );

        $btn.prop( 'disabled', true ).text( str.creating );

        $.post( thfbDash.ajaxurl, {
            action:          'tahefobu_dashboard_create',
            nonce:           nonce,
            type:            type,
            title:           title,
            display_targets: $( '#thfb-create-targets' ).val() || [],
            include_pages:   $( '#thfb-create-include' ).val()  || [],
            exclude_pages:   $( '#thfb-create-exclude' ).val()  || [],
            is_sticky:       $( '#thfb-create-sticky' ).is( ':checked' ) ? 1 : 0,
            has_animation:   $( '#thfb-create-animation' ).is( ':checked' ) ? 1 : 0,
        } )
        .done( function ( res ) {
            if ( res.success ) {
                var tpl  = res.data.template;
                var wrap = type === 'footer' ? '#thfb-footer-table-wrap' : '#thfb-header-table-wrap';
                var $w   = $( wrap );
                $w.find( '.thfb-empty-state' ).remove();
                ensureTable( $w );
                $w.find( 'tbody' ).prepend( buildRow( tpl ) );
                updateTemplateStore( tpl );
                closeCreateModal();
                // Redirect to Elementor editor
                window.location.href = res.data.edit_url;
            } else {
                alert( res.data.message || str.error );
                $btn.prop( 'disabled', false ).html( '<span class="dashicons dashicons-plus-alt2"></span> Create &amp; Edit with Elementor' );
            }
        } )
        .fail( function () {
            alert( str.error );
            $btn.prop( 'disabled', false ).html( '<span class="dashicons dashicons-plus-alt2"></span> Create &amp; Edit with Elementor' );
        } );
    } );

    /* ── Click: open conditions modal ─────────────────────── */
    $( document ).on( 'click', '.thfb-open-conditions', function () {
        openConditionsModal( $( this ).data( 'id' ) );
    } );

    /* ── Submit: save conditions ───────────────────────────── */
    $( '#thfb-cond-save' ).on( 'click', function () {
        var $btn   = $( this );
        var postId = $( '#thfb-cond-post-id' ).val();
        $btn.prop( 'disabled', true ).text( str.saving );

        $.post( thfbDash.ajaxurl, {
            action:          'tahefobu_dashboard_save_conditions',
            nonce:           nonce,
            post_id:         postId,
            display_targets: $( '#thfb-cond-targets' ).val() || [],
            include_pages:   $( '#thfb-cond-include' ).val()  || [],
            exclude_pages:   $( '#thfb-cond-exclude' ).val()  || [],
            is_sticky:       $( '#thfb-cond-sticky' ).is( ':checked' ) ? 1 : 0,
            has_animation:   $( '#thfb-cond-animation' ).is( ':checked' ) ? 1 : 0,
        } )
        .done( function ( res ) {
            if ( res.success ) {
                var tpl = res.data.template;
                updateTemplateStore( tpl );
                // Update row in table
                var $row = $( 'tr[data-id="' + postId + '"]' );
                $row.find( '.thfb-conditions-cell' ).html( conditionTagsHtml( tpl.targets ) );
                // Update sticky/animated badges
                var $name = $row.find( '.thfb-template-name strong' );
                $row.find( '.thfb-badge[style*="7c3aed"]' ).remove();
                $row.find( '.thfb-badge[style*="e67e22"]' ).remove();
                if ( tpl.is_sticky )     $name.after( '<span class="thfb-badge" style="background:#f3f0ff;color:#7c3aed;font-size:10px;padding:2px 7px;">Sticky</span>' );
                if ( tpl.has_animation ) $name.after( '<span class="thfb-badge" style="background:#fff8ee;color:#e67e22;font-size:10px;padding:2px 7px;">Animated</span>' );
                $btn.text( str.saved ).addClass( 'thfb-btn-success' ).removeClass( 'thfb-btn-primary' );
                setTimeout( function () {
                    closeConditionsModal();
                    $btn.prop( 'disabled', false ).text( 'Save Conditions' ).addClass( 'thfb-btn-primary' ).removeClass( 'thfb-btn-success' );
                }, 900 );
            } else {
                alert( res.data.message || str.error );
                $btn.prop( 'disabled', false ).text( 'Save Conditions' );
            }
        } )
        .fail( function () {
            alert( str.error );
            $btn.prop( 'disabled', false ).text( 'Save Conditions' );
        } );
    } );

    /* ── Click: toggle active/draft ───────────────────────── */
    $( document ).on( 'click', '.thfb-toggle-status', function () {
        var $btn   = $( this );
        var postId = $btn.data( 'id' );
        $btn.prop( 'disabled', true ).css( 'opacity', '.5' );

        $.post( thfbDash.ajaxurl, {
            action:  'tahefobu_dashboard_toggle_status',
            nonce:   nonce,
            post_id: postId,
        } )
        .done( function ( res ) {
            if ( res.success ) {
                var isActive = res.data.new_status === 'publish';
                $btn.removeClass( 'thfb-badge-active thfb-badge-draft' )
                    .addClass( isActive ? 'thfb-badge-active' : 'thfb-badge-draft' )
                    .text( isActive ? 'Active' : 'Draft' );
                // Update store
                ( window.thfbTemplates || [] ).forEach( function ( t ) {
                    if ( String( t.id ) === String( postId ) ) t.status = res.data.new_status;
                } );
            }
        } )
        .always( function () { $btn.prop( 'disabled', false ).css( 'opacity', '' ); } );
    } );

    /* ── Click: delete template ────────────────────────────── */
    $( document ).on( 'click', '.thfb-delete-tpl', function () {
        if ( ! window.confirm( str.confirm_delete ) ) return;
        var $btn   = $( this );
        var postId = $btn.data( 'id' );
        $btn.prop( 'disabled', true ).text( str.deleting );

        $.post( thfbDash.ajaxurl, {
            action:  'tahefobu_dashboard_delete',
            nonce:   nonce,
            post_id: postId,
        } )
        .done( function ( res ) {
            if ( res.success ) {
                var $row  = $( 'tr[data-id="' + postId + '"]' );
                var $wrap = $row.closest( '.thfb-table-wrap' );
                $row.fadeOut( 200, function () {
                    $( this ).remove();
                    if ( $wrap.find( 'tbody tr' ).length === 0 ) {
                        $wrap.html(
                            '<div class="thfb-empty-state">' +
                                '<div class="thfb-empty-icon"><span class="dashicons dashicons-layout"></span></div>' +
                                '<h3>No templates yet</h3>' +
                                '<p>Click "Add New" above to create your first template.</p>' +
                            '</div>'
                        );
                    }
                } );
                removeFromStore( postId );
            } else {
                alert( res.data.message || str.error );
                $btn.prop( 'disabled', false ).html( '<span class="dashicons dashicons-trash"></span>Delete' );
            }
        } )
        .fail( function () {
            alert( str.error );
            $btn.prop( 'disabled', false ).html( '<span class="dashicons dashicons-trash"></span>Delete' );
        } );
    } );

} );



/* ── Select All / Deselect All toggle for include/exclude page selects ── */
jQuery( function ( $ ) {
    $( document ).on( 'click', '.thfb-select-all-btn', function () {
        var $btn      = $( this );
        var targetId  = $btn.data( 'target' );
        var $select   = $( '#' + targetId );
        var isAll     = $btn.data( 'deselect' ) === 1;

        if ( isAll ) {
            $select.val( [] ).trigger( 'change' );
            $btn.data( 'deselect', 0 ).text( thfbDash.strings.select_all || 'Select All' );
            $btn.removeClass( 'thfb-select-all-active' );
        } else {
            var allVals = $select.find( 'option' ).map( function () {
                return $( this ).val();
            } ).get();
            $select.val( allVals ).trigger( 'change' );
            $btn.data( 'deselect', 1 ).text( thfbDash.strings.deselect_all || 'Deselect All' );
            $btn.addClass( 'thfb-select-all-active' );
        }
    } );

    $( document ).on( 'click', '#thfb-create-close, #thfb-create-cancel, #thfb-cond-close, #thfb-cond-cancel' , function () {
        $( '.thfb-select-all-btn' ).data( 'deselect', 0 )
            .text( thfbDash.strings.select_all || 'Select All' )
            .removeClass( 'thfb-select-all-active' );
    } );
} );
