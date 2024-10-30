(function($) {

    var bkpkFM = bkpkFM || {};

    bkpkFM.admin = {

        init: function() {
            this.events();
            this.initMultiselect();
        },

        events: function() {
            $(document).on('click', '.panel .panel-heading', this.togglePanel);
        },

        togglePanel: function() {
            $(this).closest(".panel").find(".collapse").slideToggle();
        },

        initMultiselect: function() {
            if ($.isFunction($.fn.multiselect)) {
                $('.bkpk_multiselect').multiselect({
                    includeSelectAllOption: true
                });
            }
        },

        saveButton: function(arg) {
            $.ajax({
                type: "post",
                url: ajaxurl,
                data: arg,
                beforeSend: function() {
                    $(".bkpk_save_button").html('<i class="fa fa-spin fa-circle-o-notch"></i> ' + llms_bkpk.saving);
                },
                success: function(data) {

                    var config = "";
                    try {
                        config = JSON.parse(data);
                        if (config.redirect_to) {
                            window.location.replace(config.redirect_to);
                        }
                    } catch (err) {}


                    if (data == '1' || (config && typeof config == 'object')) {
                        $(".bkpk_save_button").removeClass('btn-primary').addClass('btn-success');
                        $(".bkpk_save_button").html('<i class="fa fa-check"></i> ' + llms_bkpk.saved);
                        $(".bkpk_error_msg").html("");
                    } else {
                        $(".bkpk_save_button").removeClass('btn-primary').addClass('btn-danger');
                        $(".bkpk_save_button").html(llms_bkpk.not_saved + ' <i class="fa fa-exclamation-triangle"></i>');
                        $(".bkpk_error_msg").html('<span class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> ' + data + '</span>');
                    }

                    setTimeout(function() {
                        $(".bkpk_save_button").removeClass('btn-success').removeClass('btn-danger').addClass('btn-primary');
                        $(".bkpk_save_button").html('Save Changes');
                    }, 3000);

                }
            });
        }
    };

    bkpkFM.formEditor = {

        init: function() {
            if ($('#bkpk_fields_editor').length) {
                this.name = 'fields_editor';
                this.editor = $('#bkpk_fields_editor');
                this.fieldsLoad();
                this.fieldsEvents();

            } else if ($('#bkpk_form_editor').length) {
                this.name = 'form_editor';
                this.editor = $('#bkpk_form_editor');
                this.formLoad();
                this.formEvents();
            }
        },

        fieldsLoad: function() {
            this.expandFirstField();

            this.load();
        },

        formLoad: function() {
            this.collapseAll();
            this.sanitizeSelectors();

            this.load();
        },

        load: function() {
            $('#bkpk_fields_container').sortable({handle: ".panel-heading"});

            $(window).scroll(this.steadySidebar);
            $(window).resize(this.steadySidebar);
            $(window).load(this.steadySidebar);

            //this.loadConditionalConfig();
            //$(this.editor).find('.bkpk_selector_options .bkpk_plusminus_rows_holder').sortable();
            this.triggerLoadMethods();
            
            bkpkFM.optionsSelection.init(this.editor);
            
        },
        
        /**
         * Common load method for load, add-new-field and change-field
         */
        triggerLoadMethods: function() {
        	bkpkFM.admin.initMultiselect();
        	bkpkFM.formEditor.loadConditionalConfig();
        	$(this.editor).find('.bkpk_selector_options .bkpk_plusminus_rows_holder').sortable();
        	$('[data-toggle="tooltip"]').tooltip();
        },

        fieldsEvents: function() {
            this.editor.on('click', '.bkpk_save_button', this.updateFields);
            this.editor.on('click', '#bkpk_fields_selectors .panel-heading', this.toggleSelectorPanel);

            this.editor.on('click', '.bkpk_field_selecor', this.addNewField);

            this.events();
        },

        formEvents: function() {
            this.editor.on('click', '.bkpk_field_selecor', this.addNewFormField);

            this.editor.on('click', '#bkpk_fields_selectors .panel-heading', this.toggleSelectorPanel);
            this.editor.on('change', '.bkpk_enable_conditional_logic', this.toggleConditionalPanel);

            this.editor.on('click', '.bkpk_conditional_plus', this.conditionalPlus);
            this.editor.on('click', '.bkpk_conditional_minus', this.conditionalMinus);

            this.editor.on('click', '.bkpk_save_button', this.updateForm);

            this.events();
        },

        events: function() {
            this.editor.on('change', 'select[name=field_type]', this.changeField);

            this.editor.on('keyup', 'input[name=field_title]', this.changeTitle);
            this.editor.on('blur', 'input[name=field_title]', this.updateMetaKey);
            this.editor.on('blur', 'input[name=meta_key]', this.updateMetaKey);

            this.editor.on('click', '.panel .panel-heading .bkpk_trash', this.removePanel);

            this.editor.on('change', '.bkpk_parent', this.toggleConditionalConfig);
        },

        expandFirstField: function() {
            $('#bkpk_fields_container .panel-collapse').removeClass('in');
            $('#bkpk_fields_container .panel-collapse').first().addClass('in');

            $('#bkpk_fields_selectors .panel-collapse').first().addClass('in');
        },

        collapseAll: function() {
            $('#bkpk_fields_container .panel-collapse').removeClass('in');
        },

        sanitizeSelectors: function() {
            var first = $('#bkpk_fields_selectors .panel-collapse').first();
            first.addClass('in');
            first.css('max-height', '300px');
            first.css('overflow', 'auto');
        },

        toggleSelectorPanel: function() {
            self = $(this).closest(".panel").find(".collapse");
            $(this).closest(".panel-group").find(".collapse").not(self).slideUp();
        },

        removePanel: function() {
            if (confirm('Confirm to remove?')) {
                if (bkpkFM.formEditor.name == 'form_editor') {
                    var fieldID = $(this).closest(".panel").find('.bkpk_field_id').text();
                    $('#bkpk_fields_selectors button[data-field-id="' + fieldID + '"]').show();

                    bkpkFM.formEditor.removeOptionFromConditions(fieldID);
                }

                $(this).closest(".panel").remove();
            }
        },
        
        addNewField: function() {
            var self = $(this);
            if (self.attr("class").indexOf("pf_blure") >= 0) return;
            
            var label = self.text();
            var newID = parseInt($('#bkpk_max_id').val()) + 1;

            var arg = 'id=' + newID + '&field_type=' + $(this).attr('data-field-type');
            arg = arg + '&action=bkpk_add_field&_wpnonce=' + $(this).attr('data-nonce');

            $.ajax({
                type: 'post',
                url: ajaxurl,
                data: arg,
                beforeSend: function() {
                    self.html('<i class="fa fa-spin fa-circle-o-notch"></i> ' + label);
                },
                success: function(data) {
                    self.html(label);
                    $('#bkpk_fields_container').append(data);
                    $('#bkpk_max_id').val(newID);

                    $('html, body').animate({
                        scrollTop: $('#bkpk_admin_field_' + newID).offset().top
                    });

                    bkpkFM.formEditor.triggerLoadMethods();
                }
            });
        },

        addNewFormField: function() {
            var self = $(this);
            if (self.attr("class").indexOf("pf_blure") >= 0) return;
            
            var label = self.text();
            var newID = parseInt($("#bkpk_max_id").val()) + 1;

            var isShared = 0;

            if ($(this).attr('data-is-shared') && parseInt($(this).attr('data-field-id')) > 0) {
                isShared = 1;
                newID = parseInt($(this).attr('data-field-id'));
            }

            var arg = 'id=' + newID + '&field_type=' + $(this).attr('data-field-type');
            arg = arg + '&action=bkpk_add_form_field&is_shared=' + isShared + '&_wpnonce=' + $(this).attr('data-nonce');

            $.ajax({
                type: "post",
                url: ajaxurl,
                data: arg,
                beforeSend: function() {
                    self.html('<i class="fa fa-spin fa-circle-o-notch"></i> ' + label);
                },
                success: function(data) {
                    if (isShared) {
                        self.hide();
                    } else {
                        $('#bkpk_max_id').val(newID);
                    }

                    self.html(label);
                    $('#bkpk_fields_container').append(data);

                    $('html, body').animate({
                        scrollTop: $('#bkpk_admin_field_' + newID).offset().top
                    });

                    bkpkFM.formEditor.triggerLoadMethods();

                    bkpkFM.formEditor.addOptionToConditions(newID);
                }
            });
        },

        changeField: function() {
            var field = $(this).closest('.panel');
            var id = $(field).find('.bkpk_field_id').text();

            var fieldObj = $(this).closest('.panel-body').find('input, textarea, select').serializeArray();
            var arg = {};
            for (var i = 0; i < fieldObj.length; i++) {
                if (fieldObj[i].value) {
                    arg[fieldObj[i].name] = fieldObj[i].value;
                }
            }
            arg.id = id;
            arg.editor = $('#bkpk_editor').val();

            var result = bkpkFM.optionsSelection.getSelectionOptions(field);
            if (result.options) {
                arg.options = result.options;
                arg.default_value = result.defaultOpt;
            }

            pfAjaxCall(this, "bkpk_change_field", $.param(arg), function(data) {
                field.replaceWith(data);
                bkpkFM.formEditor.triggerLoadMethods();
                bkpkFM.formEditor.addOptionToConditions(id);
            });
        },

        changeTitle: function() {
            title = $(this).val();
            //if (!title){ title = 'Untitled'; }
            $(this).closest(".panel").find("h3 .bkpk_field_label").text(title);
        },

        updateMetaKey: function() {
            self = $(this).closest('.panel');
            if (self.find('input[name=meta_key]').length && !self.find('input[name=meta_key]').val()) {
                title = self.find('input[name=field_title]').val();
                meta_key = title.trim().toLowerCase().replace(/[^a-z0-9 ]/g, '').replace(/\s+/g, '_');
                self.find('input[name=meta_key]').val(meta_key);
            }
        },

        steadySidebar: function() {
            var windowWidth = $(window).width();
            var windowHeight = $(window).height();
            var windowTop = $(window).scrollTop();

            var containerTop = $("#wpbody").offset().top + 5;
            //var containerTop = $(".wrap").offset().top;
            var FieldsContainerTop = $("#bkpk_fields_container").offset().top;
            var FieldsContainerHeight = parseInt($("#bkpk_fields_container").css("height"));
            var holderTop = $("#bkpk_steady_sidebar_holder").offset().top;
            var sidebarTop = $("#bkpk_steady_sidebar").offset().top;
            var sidebarHeight = parseInt($("#bkpk_steady_sidebar").css("height"));

            if (FieldsContainerHeight < windowHeight /*|| sidebarHeight > windowHeight*/ ) {
                $('#bkpk_steady_sidebar').css({
                    position: 'relative',
                    top: 0,
                    width: '100%'
                });
                return;
            }

            //var footerTop = $("#wpfooter").offset().top;
            //var footerHeight = parseInt($("#wpfooter").css("height")) ;
            //var sidebarHeight = windowHeight - containerTop;

            var adminbarHeight = parseInt($("#wpadminbar").css("height"));
            //var sidebarHeight = parseInt($("#bkpk_steady_sidebar").css("height")) ;
            var frameTop = windowTop + containerTop;
            var footerScrollTop = $("#wpfooter").offset().top - windowHeight;

            if (windowWidth >= 790) { //Standard: 767
                if (frameTop >= sidebarTop) {
                    if (windowTop >= footerScrollTop) {
                        $('#bkpk_steady_sidebar').css({
                            position: 'relative',
                            top: (footerScrollTop - holderTop + containerTop),
                            width: '100%'
                        });
                    } else if (FieldsContainerTop > sidebarTop) {
                        $('#bkpk_steady_sidebar').css({
                            position: 'relative',
                            top: 0,
                            width: '100%'
                        });
                    } else {
                        $('#bkpk_steady_sidebar').css({
                            position: 'fixed',
                            top: containerTop,
                            width: '26.5%'
                        });
                    }

                } else {
                    $('#bkpk_steady_sidebar').css({
                        position: 'relative',
                        top: 0,
                        width: '100%'
                    });
                }

            } else {
                $('#bkpk_steady_sidebar').css({
                    position: 'relative',
                    top: 0,
                    width: '100%'
                });
            }

            //if ( sidebarHeight > windowHeight ) {
            $('#bkpk_steady_sidebar').css({
                height: windowHeight - adminbarHeight,
                overflow: 'hidden'
            });
            //}
            //console.log( FieldsContainerTop + ', ' + frameTop + ', ' + sidebarTop + ', ' + windowWidth  );
        },

        loadConditionalConfig: function() {
            this.editor.find('.panel-body .bkpk_parent').each(function() {
                bkpkFM.formEditor.toggleConditionalConfig(this, $(this)); // First argument is for default event
            });
        },

        /**
         * Implemented for select and checkbox
         */
        toggleConditionalConfig: function(event, input) {
            if (!input) {
                input = $(this);
            }

            var panel = input.closest('.panel-body');
            var tagName = input.prop("tagName").toLowerCase();

            if (tagName == 'select') {
                var allChild = [];
                input.find('option').each(function() {
                    if ($(this).data('child')) {
                        child = $(this).data('child').split(',');
                        $.merge(allChild, child);
                    }
                });
                allChild = $.unique(allChild); //console.log(allChild);

                // Hide all child first
                $(allChild).each(function() { //console.log(panel.find( 'input[name='+ this +']' ).closest('p'));
                    panel.find('input[name=' + this + ']').closest('.bkpk_fb_field').slideUp();
                });

                // Show relevent child
                if (input.find(':selected').data('child')) {
                    targetChild = input.find(':selected').data('child').split(',');
                    $(targetChild).each(function() {
                        panel.find('input[name=' + this + ']').closest('.bkpk_fb_field').slideDown();
                    });
                }

            } else if (tagName == 'input' && input.attr('type') == 'checkbox') {
                targetChild = input.data('child').split(',');
                $(targetChild).each(function() { //panel.find( 'input[name='+ this +']' ).hide();
                    if (input.is(":checked")) {
                        panel.find('input[name=' + this + ']').closest('.bkpk_fb_field').slideDown();
                    } else {
                        panel.find('input[name=' + this + ']').closest('.bkpk_fb_field').slideUp();
                    }
                });
            }
        },

        toggleConditionalPanel: function() {
            var panel = $(this).closest('.panel-body').find('.bkpk_conditional_details');
            if ($(this).is(":checked")) {
                panel.slideDown();
            } else {
                panel.slideUp();
            }

            bkpkFM.formEditor.conditionsCountsEvent(panel);
        },

        conditionalPlus: function() {
            var panel = $(this).closest('.bkpk_conditional_details');

            var row = $(this).closest('.form-group');
            var clone = row.clone();

            clone.find('.bkpk_conditional_value').val('');

            clone.insertAfter(row);

            bkpkFM.formEditor.conditionsCountsEvent(panel);
        },

        conditionalMinus: function(e) {
            e.preventDefault();

            var panel = $(this).closest('.bkpk_conditional_details');

            rows = $(this).closest('.bkpk_conditions').find('.form-group').length;
            if (rows > 1) {
                $(this).closest('.form-group').remove();
            }

            bkpkFM.formEditor.conditionsCountsEvent(panel);
        },

        conditionsCountsEvent: function(panel) {
            var rows = panel.find('.bkpk_conditions .form-group').length;
            if (rows > 1) {
                panel.find('.bkpk_conditional_relation_div').slideDown();
                panel.find('.bkpk_conditional_minus').show();
            } else {
                panel.find('.bkpk_conditional_relation_div').slideUp();
                panel.find('.bkpk_conditional_minus').hide();
            }
        },

        addOptionToConditions: function(id) {
            var field = $('#bkpk_admin_field_' + id + ' .panel-title');

            var optionLabel = $('#bkpk_admin_field_' + id + ' .panel-title .bkpk_field_panel_title').text();

            this.editor.find('.bkpk_conditional_field_id').each(function() {
                $(this).append('<option value="' + id + '">' + optionLabel + '</option>');
            });

            // Copy populated select
            first = this.editor.find('.bkpk_conditional_field_id').first().html();
            $('#bkpk_admin_field_' + id + ' .bkpk_conditional_field_id').html(first);
        },

        removeOptionFromConditions: function(id) {
            this.editor.find('.bkpk_conditional_field_id option[value="' + id + '"]').remove();
        },

        getConditionalLogic: function(element) {
            var condition = {},
                rules = [];

            if (element.find('.bkpk_enable_conditional_logic').is(':checked')) {
                condition.visibility = element.find('.bkpk_conditional_visibility').val();
                condition.relation = element.find('.bkpk_conditional_relation').val();

                $(element).find('.bkpk_conditional_details .bkpk_conditions .form-group').each(function() {
                    var rule = {};
                    rule.field_id = $(this).find('.bkpk_conditional_field_id').val();
                    rule.condition = $(this).find('.bkpk_conditional_condition').val();
                    rule.value = $(this).find('.bkpk_conditional_value').val();
                    rules.push(rule);
                });

                condition.rules = rules;
            }

            return condition;
        },

        updateFields: function() {
            var fields = [];
            $(".bkpk_field_single").each(function(index) {
                fieldID = $(this).find(".bkpk_field_id").text();
                field = {
                    'id': fieldID
                };
                fieldObj = $(this).find('input, textarea, select').serializeArray();
                for (var i = 0; i < fieldObj.length; i++) {
                    if (fieldObj[i].value) {
                        field[fieldObj[i].name] = fieldObj[i].value;
                    }
                }

                // Multiselect
                $(this).find('.bkpk_multiselect').each(function() {
                    name = $(this).attr('name');
                    if (name == 'undefined') return;

                    delete field[name];
                    name = name.replace('[]', '');
                    multiselectVal = [];
                    $(this).parent().find('.multiselect-container li.active input').each(function() {
                        var val = $(this).val();
                        if (val && val != 'multiselect-all') {
                            multiselectVal.push($(this).val());
                        }
                    });
                    field[name] = multiselectVal;
                });

                field_type = $(this).find('.bkpk_field_type').val();
                if ($.inArray(field_type, ['select', 'radio', 'checkbox', 'multiselect']) > -1) {
                    result = bkpkFM.optionsSelection.getSelectionOptions(this);
                    if (result.options) {
                        field.options = result.options;
                    }
                    field.default_value = result.defaultOpt;
                }

                //console.log(field_type);
                fields[index] = field;
            });

            var arg = {
                "action": "bkpk_update_field",
                "fields": fields
            };

            var input = $('#bkpk_additional_input').find('input').serializeArray();
            for (var i = 0; i < input.length; i++) {
                arg[input[i].name] = input[i].value;
            }

            bkpkFM.admin.saveButton(arg);
        },

        updateForm: function() {
            var fields = [];
            $(".bkpk_field_single").each(function(index) {
                fieldID = $(this).find(".bkpk_field_id").text();
                field = {
                    'id': fieldID
                };
                fieldObj = $(this).find('input, textarea, select').serializeArray();
                for (var i = 0; i < fieldObj.length; i++) {
                    field[fieldObj[i].name] = fieldObj[i].value;
                }

                // Multiselect
                $(this).find('.bkpk_multiselect').each(function() {
                    name = $(this).attr('name');
                    if (name == 'undefined') return;

                    delete field[name];
                    name = name.replace('[]', '');
                    multiselectVal = [];
                    $(this).parent().find('.multiselect-container li.active input').each(function() {
                        var val = $(this).val();
                        if (val && val != 'multiselect-all') {
                            multiselectVal.push($(this).val());
                        }
                    });
                    field[name] = multiselectVal;
                });

                $(this).find('input[type="checkbox"]').each(function() {
                    name = $(this).attr('name');
                    if (name && name != 'undefined') {
                        if ($(this).is(':checked')) {
                            field[name] = 1;
                        } else {
                            field[name] = 0;
                        }
                    }
                });

                condition = bkpkFM.formEditor.getConditionalLogic($(this));
                if (condition) {
                    field.condition = condition;
                }

                field_type = $(this).find('.bkpk_field_type').val();
                if ($.inArray(field_type, ['select', 'radio', 'checkbox', 'multiselect']) > -1) {
                    result = bkpkFM.optionsSelection.getSelectionOptions(this);
                    if (result.options) {
                        field.options = result.options;
                    }
                    field.default_value = result.defaultOpt;
                }

                fields[index] = field;
            });

            var arg = {
                "action": "bkpk_update_forms"
            };

            arg.form_key = $('input[name="form_key"]').val();
            arg.fields = fields;

            var i = 0;
            input = $('#bkpk_form_settings_tab').find('input, textarea, select').serializeArray();
            for (i = 0; i < input.length; i++) {
                arg[input[i].name] = input[i].value;
            }

            input = $('#bkpk_additional_input').find('input').serializeArray();
            for (i = 0; i < input.length; i++) {
                arg[input[i].name] = input[i].value;
            }

            //console.log(arg);

            bkpkFM.admin.saveButton(arg);
        }

    };

    bkpkFM.optionsSelection = {

            init: function(editor) {
                this.editor = editor;
                this.editor.on('change', 'input[name=advanced_mode]', this.toggleAdvancedMode);
                this.editor.on('click', '.bkpk_selector_options .bkpk_row_button_plus', this.rowPlus);
                this.editor.on('click', '.bkpk_selector_options .bkpk_row_button_minus', this.rowMinus);
                this.editor.on('click', '.bkpk_selector_options .bkpk_plusminus_row input[type=radio]', this.radioGroupWithoutName);
                this.editor.on('keyup', '.bkpk_selector_options .bkpk_option_label', this.updateValue);
            },

            toggleAdvancedMode: function() {
                var panel = $(this).closest('.panel-body');
                if (panel.find('.bkpk_selector_options').length > 0) {
                    panel = panel.find('.bkpk_selector_options');

                    if ($(this).is(":checked")) {
                        panel.find('.bkpk_advanced').show('slow');
                    } else {
                        panel.find('.bkpk_advanced').hide('slow');
                    }
                }
            },

            rowPlus: function() {
                var row = $(this).closest('.bkpk_plusminus_row');
                var clone = row.clone();
                clone.find('input[type="text"]').val('');
                clone.find('input[type="radio"]').prop('checked', false);
                clone.find('input[type="checkbox"]').prop('checked', false);
                clone.insertAfter(row);
            },

            rowMinus: function() {
                var count = 0;

                if ($(this).closest('.bkpk_plusminus_row.bkpk_option').length > 0) {
                    count = $(this).closest('.bkpk_plusminus_rows_holder').find('.bkpk_plusminus_row.bkpk_option').length;
                    if (count > 1) {
                        $(this).closest('.bkpk_plusminus_row').remove();
                    }
                }

                if ($(this).closest('.bkpk_plusminus_row.bkpk_optgroup').length > 0) {
                    count = $(this).closest('.bkpk_plusminus_rows_holder').find('.bkpk_plusminus_row.bkpk_optgroup').length;
                    if (count > 1) {
                        $(this).closest('.bkpk_plusminus_row').remove();
                    }
                }
            },

            radioGroupWithoutName: function() {
                $(this).closest('.bkpk_plusminus_rows_holder').find('.bkpk_option_default').each(function() {
                    this.checked = false;
                });
                this.checked = true;
            },

            updateValue: function() {
                row = $(this).closest('.bkpk_plusminus_row');
                val = this.value;
                row.find('.bkpk_option_value').val(val);

                row.find('.bkpk_option_default').val(row.find('.bkpk_option_value').val());
            },

            getSelectionOptions: function(elem) {
                var options = [];
                var defaultOpt = [];
                $(elem).find('.bkpk_selector_options .bkpk_plusminus_row').each(function() {
                    var option = {};

                    if ($(this).find('.bkpk_option_group').length > 0) {
                        if ($(this).find('.bkpk_option_group').val()) {
                            option.type = 'optgroup';
                            option.label = $(this).find('.bkpk_option_group').val();
                        }
                    } else {
                        option.value = $(this).find('.bkpk_option_value').val();
                        option.label = $(this).find('.bkpk_option_label').val();
                    }

                    if ($(this).find('.bkpk_option_default').is(":checked")) {
                        defaultOpt.push($(this).find('.bkpk_option_default').val());
                    }

                    if (!$.isEmptyObject(option)) {
                        options.push(option);
                    }
                });

                var result = [];
                result.options = options;
                result.defaultOpt = defaultOpt;
                //console.log(result);
                return result;
            },
        },

        bkpkFM.advanced = {

            init: function() {
                if ($('#bkpk_advanced_settings').length) {
                    this.editor = $('#bkpk_advanced_settings');
                    this.events();
                }
            },

            events: function() {
                this.editor.on('click', '.bkpk_generate_wpml_config', this.wpmlConfig);
            },

            wpmlConfig: function() {
                bindElement = $(this);
                pfAjaxCall(bindElement, 'bkpk_generate_wpml_config', '', function(data) {
                    bindElement.after("<div class='pf_ajax_result'>" + data + "</div>");
                });
            }

        };

    $(function() {
        bkpkFM.admin.init();
        bkpkFM.formEditor.init();
        bkpkFM.advanced.init();
    });

})(jQuery);


function pfToggleMetaBox(toggleIcon) {
    jQuery(toggleIcon).parents('.postbox').children('.inside').toggle();

    if (jQuery(toggleIcon).parents('.postbox').hasClass('closed')) {
        jQuery(toggleIcon).parents('.postbox').removeClass("closed");
    } else {
        jQuery(toggleIcon).parents('.postbox').addClass("closed");
    }
}

function pfRemoveMetaBox(removeIcon) {
    if (confirm('Confirm to remove?')) {
        jQuery(removeIcon).parents('.postbox').parents('.meta-box-sortables').remove();
    }
}

function umNewField(element) {
    newID = parseInt(jQuery("#last_id").val()) + 1;
    arg = 'id=' + newID + '&field_type=' + jQuery(element).attr('field_type');
    pfAjaxCall(element, 'bkpk_add_field', arg, function(data) {
        jQuery("#bkpk_fields_container").append(data);
        jQuery("#last_id").val(newID);
    });
}

function umChangeFieldTitle(element) {
    title = jQuery(element).val();
    if (!title) {
        title = 'Untitled Field';
    }
    jQuery(element).parents(".postbox").children("h3").children(".bkpk_admin_field_title").text(title);
}

function umUpdateMetaKey(element) {
    if (jQuery(element).parents(".postbox").find(".bkpk_meta_key_editor").length) {
        if (!jQuery(element).parents(".postbox").find(".bkpk_meta_key_editor").val()) {
            title = jQuery(element).parents(".postbox").find(".bkpk_field_title_editor").val();
            meta_key = title.trim().toLowerCase().replace(/[^a-z0-9 ]/g, '').replace(/\s+/g, '_');
            jQuery(element).parents(".postbox").find(".bkpk_meta_key_editor").val(meta_key);
        }
    }
}

function umNewForm(element) {
    newID = parseInt(jQuery("#form_count").val()) + 1;
    pfAjaxCall(element, 'bkpk_add_form', 'id=' + newID, function(data) {
        jQuery("#bkpk_fields_container").append(data);
        jQuery("#form_count").val(newID);

        jQuery('.bkpk_dropme').sortable({
            connectWith: '.bkpk_dropme',
            cursor: 'pointer'
        }).droppable({
            accept: '.postbox',
            activeClass: 'bkpk_highlight'
        });
    });
}

function umUpdateForms(element) {
    if (!jQuery(element).validationEngine("validate")) return;

    jQuery(".bkpk_selected_fields").each(function(index) {
        var length = jQuery(this).children(".postbox").size();
        n = index + 1;
        jQuery("#field_count_" + n).val(length);
    });

    bindElement = jQuery(".pf_save_button");
    bindElement.parent().children(".pf_ajax_result").remove();
    arg = jQuery(element).serialize();
    pfAjaxCall(bindElement, 'bkpk_update_forms', arg, function(data) {
        bindElement.after("<div class='pf_ajax_result'>" + data + "</div>");
    });
}

function umChangeFormTitle(element) {
    title = jQuery(element).val();
    if (!title) {
        title = 'Untitled Form';
    }
    jQuery(element).parents(".postbox").children("h3").text(title);
}

function umAuthorizePro(element) {
    if (!jQuery(element).validationEngine("validate")) return;

    arg = jQuery(element).serialize();
    bindElement = jQuery("#authorize_pro");
    pfAjaxCall(bindElement, 'bkpk_update_settings', arg, function(data) {
        bindElement.parent().children(".pf_ajax_result").remove();
        bindElement.after("<div class='pf_ajax_result'>" + data + "</div>");
    });
}

function umWithdrawLicense(element) {
    bindElement = jQuery(element);
    arg = "method_name=withdrawLicense";
    bindElement.parent().children(".pf_ajax_result").remove();
    pfAjaxCall(bindElement, 'pf_ajax_request', arg, function(data) {
        bindElement.after("<div class='pf_ajax_result'>" + data + "</div>");
    });
}

function umUpdateSettings(element) {
    bindElement = jQuery("#update_settings");

    jQuery(".bkpk_selected_fields").each(function(index) {
        var length = jQuery(this).children(".postbox").size();
        n = index + 1;
        jQuery("#field_count_" + n).val(length);

    });

    arg = jQuery(element).serialize();
    pfAjaxCall(bindElement, 'bkpk_update_settings', arg, function(data) {
        bindElement.parent().children(".pf_ajax_result").remove();
        bindElement.after("<div class='pf_ajax_result'>" + data + "</div>");
    });
}

// Get Pro Message in admin section
function umGetProMessage(element) {
    alert(llms_bkpk.get_link);
}

// Toggle custom field in Admin Import Page
function umToggleCustomField(element) {
    if (jQuery(element).val() == 'custom_field')
        jQuery(element).parent().children(".bkpk_custom_field").fadeIn();
    else
        jQuery(element).parent().children(".bkpk_custom_field").fadeOut();
}

/**
 * Export and Import
 */

var umAjaxRequest;

function umUserImportDialog(element) {
    jQuery("#import_user_dialog").html('<center>' + llms_bkpk.please_wait + '</center>');
    jQuery("#dialog:ui-dialog").dialog("destroy");
    jQuery("#import_user_dialog").dialog({
        modal: true,
        beforeClose: function(event, ui) {
            umAjaxRequest.abort();
            jQuery(".pf_loading").remove();
        },
        buttons: {
            Close: function() {
                jQuery(this).dialog("close");
            }
        }
    });
    umUserImport(element, 0, 1);
}

function umUserImport(element, file_pointer, init) {
    arg = jQuery(element).serialize();
    arg = arg + '&step=import&file_pointer=' + file_pointer;
    if (init) arg = arg + '&init=1';
    pfAjaxCall(element, 'bkpk_user_import', arg, function(data) {
        jQuery("#import_user_dialog").html(data);
        if (jQuery(data).attr('do_loop') == 'do_loop') {
            umUserImport(element, jQuery(data).attr('file_pointer'));
        }
    });
}

function umUserExport(element, type) {
    var arg = jQuery(element).parent("form").serialize();
    arg = arg.replace(/\(/g, "%28").replace(/\)/g, "%29"); //Replace "()"
    var field_count = jQuery(element).parent("form").children(".bkpk_selected_fields").children(".postbox").size();

    arg = arg + "&action_type=" + type + "&field_count=" + field_count;

    if (type == 'export' || type == 'save_export') {
        document.location.href = ajaxurl + "?action=pf_ajax_request&" + arg;
    } else if (type == 'save') {
        pfAjaxCall(element, 'pf_ajax_request', arg, function(data) {
            alert('Form saved');
        });
    }
}

function umNewUserExportForm(element) {
    var formID = jQuery("#new_user_export_form_id").val();
    incID = formID + 1;
    jQuery("#new_user_export_form_id").val(parseInt(formID) + 1);

    arg = 'method_name=userExportForm&form_id=' + formID;

    pfAjaxCall(element, 'pf_ajax_request', arg, function(data) {
        jQuery(element).before(data);

        jQuery('.bkpk_dropme').sortable({
            connectWith: '.bkpk_dropme',
            cursor: 'pointer'
        }).droppable({
            accept: '.postbox',
            activeClass: 'bkpk_highlight'
        });
        jQuery(".bkpk_date").datepicker({
            dateFormat: 'yy-mm-dd',
            changeYear: true
        });
    });
}

function umAddFieldToExport(element) {
    var metaKey = jQuery(element).parent().children(".bkpk_add_export_meta_key").val();
    if (metaKey) {
        var button = '<div class="postbox">Title:<input type="text" style="width:50%" name="fields[' + metaKey + ']" value="' + metaKey + '" /> (' + metaKey + ')</div>';
        jQuery(element).parents("form").children(".bkpk_selected_fields").append(button);
        jQuery(element).parent().children(".bkpk_add_export_meta_key").val("").focus();
    } else {
        alert('Please provide Meta Key.');
    }
}

function umDragAllFieldToExport(element) {
    jQuery(element).parents("form").children(".bkpk_selected_fields").append(
        jQuery(element).parents("form").children(".bkpk_availabele_fields").html()
    );
    jQuery(element).parents("form").children(".bkpk_availabele_fields").html("");
}

function umRemoveFieldToExport(element, formID) {
    if (confirm("This form will removed permanantly. Confirm to Remove?")) {
        var arg = 'method_name=RemoveExportForm&form_id=' + formID;
        pfAjaxCall(element, 'pf_ajax_request', arg, function(data) {

        });
        jQuery(element).parents(".panel").hide('slow').empty();
    }
}

function umToggleVisibility(condition, result, reverse) {
    reverse = typeof reverse == 'undefined' ? true : false;
    val = jQuery(condition).val();
    val = reverse ? !val : val;
    val ? jQuery(result).fadeIn() : jQuery(result).fadeOut();
}

function umSettingsRegistratioUserActivationChange() {
    var userActivationType = jQuery('.bkpk_registration_user_activation:checked').val();
    if (userActivationType == 'auto_active') {
        jQuery('#bkpk_settings_registration_block_2').hide();
        jQuery('#bkpk_settings_registration_block_1').fadeIn();
    } else if (userActivationType == 'email_verification') {
        jQuery('#bkpk_settings_registration_block_1').hide();
        jQuery('#bkpk_settings_registration_block_2').fadeIn();
    } else if (userActivationType == 'admin_approval') {
        jQuery('#bkpk_settings_registration_block_1').hide();
        jQuery('#bkpk_settings_registration_block_2').hide();
    } else if (userActivationType == 'both_email_admin') {
        jQuery('#bkpk_settings_registration_block_1').hide();
        jQuery('#bkpk_settings_registration_block_2').fadeIn();
    }
}

function umSettingsToggleCreatePage() {
    umToggleVisibility('#bkpk_login_login_page', '#bkpk_login_login_page_create');
    umToggleVisibility('#bkpk_login_login_page', '#bkpk_login_disable_wp_login_php_block', false);

    umToggleVisibility('#bkpk_registration_email_verification_page', '#bkpk_registration_email_verification_page_create');
    umToggleVisibility('#bkpk_login_resetpass_page', '#bkpk_login_resetpass_page_create');
}

function umSettingsToggleError() {
    umToggleVisibility('#bkpk_registration_email_verification_page', '.bkpk_required_email_verification_page');

    showError = false;
    if (jQuery('#bkpk_login_disable_wp_login_php:checked').val()) {
        if (!jQuery('#bkpk_login_resetpass_page').val())
            showError = true;
    }
    if (showError)
        jQuery('.bkpk_required_resetpass_page_page').fadeIn();
    else
        jQuery('.bkpk_required_resetpass_page_page').fadeOut();
}
