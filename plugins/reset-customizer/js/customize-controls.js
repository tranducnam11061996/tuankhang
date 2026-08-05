(function($) {
    wp.customize.bind('ready', function() {
        wp.customize.section.each(function(section) {
            if (!$('#sub-accordion-section-' + section.id + ' .customize-section-description').length) {
                $('#sub-accordion-section-' + section.id + ' .customize-section-description-container').append($('<div class="description customize-section-description"></div>'));
            }
            sectionOK = false;
            _.each(wp.customize.section(section.id).controls(), function (control) {
                if (typeof wp.customize(control.id) !== 'undefined') {
                    sectionOK = true;
                }
            });
            if (sectionOK && $('#sub-accordion-section-' + section.id + ' .customize-section-description').length) {
                var $button;
                if (section.id.startsWith('sidebar-widgets-')) {
                    $button = $('<input type="button" id="reset-' + section.id + '" class="button-secondary button" value="' + resetCustomizer.resetPrefix + $('#accordion-section-' + section.id + '>h3').clone().children().remove().end().text() + '">');
                } else {
                    $button = $('<input type="button" id="reset-' + section.id + '" class="button-secondary button" value="' + resetCustomizer.resetPrefix + $('#accordion-section-' + section.id + '>h3').clone().children().remove().end().text() + resetCustomizer.resetSuffix + '">');
                }
                $button.on('click', function () {
                    var controls = [];
                    _.each(wp.customize.section(section.id).controls(), function (control) {
                        if (typeof wp.customize(control.id) !== 'undefined' && !section.id.startsWith('sidebar-widgets-')) {
                            controls.push(control.id);
                        }
                    });
                    if (controls.length > 0) {
                        var data = {
                        	action: 'rc_get_control_defaults',
                        	_ajax_nonce: resetCustomizer.nonce,
                        	controls: controls,
                        	wp_customize: 'on'
                        };
                	    $.ajax({
                    	    url: ajaxurl,
                    	    data: data,
                            type: 'POST',
                            success: function(data) {
                                _.each(wp.customize.section(section.id).controls(), function (control) {
                                    if (typeof wp.customize(control.id) !== 'undefined' && section.id.startsWith('sidebar-widgets-')) {
                                        wp.customize(control.id).set([]);
                                    } else if (typeof wp.customize(control.id) !== 'undefined' && typeof data.data[control.id] != "undefined") {
                                        wp.customize(control.id).set(data.data[control.id]);
                                    }
                                });
                                wp.customize.previewer.refresh();
                            },
                            error: function() {
                                alert(resetCustomizer.errorNotice);
                            }
                	    });
                    }
                });
                if ($('#sub-accordion-section-' + section.id + ' .customize-section-description').text().length && !$('#sub-accordion-section-' + section.id + ' .customize-section-description p').length) {
                    $('#sub-accordion-section-' + section.id + ' .customize-section-description').append($('<br /><br />'));
                }
                $('#sub-accordion-section-' + section.id + ' .customize-section-description').append($button);
            }
        });
    });
})(jQuery);
