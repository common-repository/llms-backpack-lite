<?php
namespace BPKPFieldManager;

/**
 * Class for building field editor inside form editor or editor for shared fields.
 *
 * @author Dennis Hall
 */
class FieldBuilder
{

    /**
     *
     * @var type (int) : Field ID
     */
    private $id;

    /**
     *
     * @var type (string) : Field Type
     */
    private $type;

    /**
     *
     * @var type (array) : Field Type Data
     */
    private $typeData;

    /**
     *
     * @var type (array) : Field Data
     */
    private $data;

    /**
     *
     * @var type (array) : Inputs
     */
    private $inputs = array();

    /**
     *
     * @var (string) Editor name
     */
    private $editor;

    /**
     *
     * @var (array) Dropdown
     */
    public static $formFieldsDropdown = array();

    /**
     *
     * @param type $data            
     */
    function __construct($data = array())
    {
        global $bkpkFM;
        
        $this->data = $data;
        
        $this->id = ! empty($data['id']) ? $data['id'] : 0;
        $this->type = ! empty($data['field_type']) ? $data['field_type'] : '';
        $this->typeData = $bkpkFM->umFields($this->type);
        
        $this->populateInputs();
    }

    /**
     * Set editor name
     *
     * @param type $editor            
     */
    function setEditor($editor)
    {
        $this->editor = $editor;
    }

    /**
     * Set $this->inputs
     */
    private function populateInputs()
    {
        global $bkpkFM;
        
        $inputs = array(
            'field_title' => array(
                'label' => __('Field Label', $bkpkFM->name),
                'placeholder' => __('Field Label', $bkpkFM->name)
            ),
            'title_position' => array(
                'type' => 'select',
                'label' => __('Label Position', $bkpkFM->name),
                'options' => array(
                    'top' => __('Top', $bkpkFM->name),
                    'left' => __('Left', $bkpkFM->name),
                    'right' => __('Right', $bkpkFM->name),
                    'inline' => __('Inline', $bkpkFM->name),
                    // 'placeholder' => __( 'Placeholder', $bkpkFM->name ), // Commented since 1.1.8rc2
                    'hidden' => __('Hidden', $bkpkFM->name)
                )
            ),
            'placeholder' => array(
                'label' => 'Placeholder',
                'placeholder' => __('Placeholder', $bkpkFM->name)
            ),
            'description' => array(
                'type' => 'textarea',
                'label' => __('Description', $bkpkFM->name),
                'placeholder' => __("Field's Description", $bkpkFM->name)
            ),
            'meta_key' => array(
                'label' => 'Meta Key <span class="bkpk_required">*</span>',
                'placeholder' => __('Unique identification key for field', $bkpkFM->name),
                'info' => __('Unique identification key for field (required).', $bkpkFM->name)
            ),
            'default_value' => array(
                'label' => __('Default Value', $bkpkFM->name),
                'placeholder' => __('Default Value', $bkpkFM->name),
                'info' => __('Use this value when user doesn\'t have any stored value', $bkpkFM->name)
            ),
            'options' => array(
                'type' => 'textarea',
                'label' => __('Field Options', $bkpkFM->name) . ' <span class="bkpk_required">*</span>',
                'placeholder' => 'Available options. (e.g: Yes,No OR yes=Agree,no=Disagree'
            ),
            
            'field_class' => array(
                'label' => __('Input Class', $bkpkFM->name),
                'placeholder' => __('Specify custom class name for input', $bkpkFM->name)
            ),
            'css_class' => array(
                'label' => __('Field Container Class', $bkpkFM->name),
                'placeholder' => __('Custom class name for field container', $bkpkFM->name)
            ),
            'css_style' => array(
                'type' => 'textarea',
                'label' => __('Field Container Style', $bkpkFM->name),
                'placeholder' => __('Custom css style for field container', $bkpkFM->name)
            ),
            'field_size' => array(
                'label' => __('Field Size', $bkpkFM->name),
                'placeholder' => 'e.g. 200px;'
            ),
            'field_height' => array(
                'label' => __('Field Height', $bkpkFM->name),
                'placeholder' => 'e.g. 200px;'
            ),
            'max_char' => array(
                'label' => __('Max Char', $bkpkFM->name),
                'placeholder' => __('Maximum allowed character', $bkpkFM->name)
            ),
            
            'allowed_extension' => array(
                'label' => __('Allowed Extension', $bkpkFM->name),
                'placeholder' => 'Default: jpg,png,gif'
            ),
            'role_selection_type' => array(
                'type' => 'select',
                'label' => __('Role Selection Type', $bkpkFM->name),
                'options' => array(
                    'select' => 'Dropdown',
                    'radio' => 'Select One (radio)',
                    'hidden' => 'Hidden'
                )
            ),
            'datetime_selection' => array(
                'type' => 'select',
                'label' => __('Type Selection', $bkpkFM->name),
                'info' => 'Date, Time or Date & Time',
                'options' => array(
                    'date' => __('Date', $bkpkFM->name),
                    'time' => __('Time', $bkpkFM->name),
                    'datetime' => __('Date and Time', $bkpkFM->name)
                )
            ),
            'date_format' => array(
                'label' => __('Date Format', $bkpkFM->name),
                'placeholder' => 'Default: yy-mm-dd'
            ),
            'year_range' => array(
                'label' => __('Year Range', $bkpkFM->name),
                'placeholder' => 'Default: 1950:c'
            ),
            'country_selection_type' => array(
                'type' => 'select',
                'label' => __('Save meta value by', $bkpkFM->name),
                'options' => array(
                    'by_country_code' => __('Country Code', $bkpkFM->name),
                    'by_country_name' => __('Country Name', $bkpkFM->name)
                )
            ),
            
            'max_number' => array(
                'type' => 'number',
                'label' => __('Maximum Number', $bkpkFM->name)
            ),
            'min_number' => array(
                'type' => 'number',
                'label' => __('Minimum Number', $bkpkFM->name)
            ),
            'step' => array(
                'type' => 'number',
                'label' => 'Step',
                'info' => __('Intervals for number input', $bkpkFM->name)
            ),
            
            'max_file_size' => array(
                'type' => 'number',
                'min' => 0,
                'max' => File::getServerMaxSizeLimit(),
                'label' => __('Maximum File Size (KB)', $bkpkFM->name),
                'placeholder' => 'Default: 1024KB',
                'info' => 'According to your server settings, allowed maximum is ' . File::getServerMaxSizeLimit() . 'KB. ' . 'To increase the limit, increase value of post_max_size and upload_max_filesize on your server.'
            ),
            'image_width' => array(
                'type' => 'number',
                'min' => 0,
                'label' => 'Image Width (px)',
                'placeholder' => 'For Image Only. e.g. 640'
            ),
            'image_height' => array(
                'type' => 'number',
                'min' => 0,
                'label' => 'Image Height (px)',
                'placeholder' => 'For Image Only. e.g. 480'
            ),
            'image_size' => array(
                'type' => 'number',
                'min' => 0,
                'label' => 'Image Size (px) width/height',
                'placeholder' => 'Default: 96'
            ),
            'input_type' => array(
                'type' => 'select',
                'label' => 'HTML5 Input Type',
                'by_key' => true,
                'options' => array(
                    '' => '',
                    'email' => [
                        'Email',
                        'data-child' => 'retype_email,retype_label'
                    ],
                    'password' => [
                        'Password',
                        'data-child' => 'retype_password,retype_label'
                    ],
                    'tel' => 'Tel',
                    'month' => 'Month',
                    'week' => 'Week'
                )
            ),
            'regex' => array(
                'label' => 'Pattern',
                'placeholder' => 'e.g. (alpha-numeric): [a-zA-Z0-9]+'
            ),
            'error_text' => array(
                'label' => __('Error Text', $bkpkFM->name),
                'placeholder' => 'e.g. Invalid field'
            ),
            'retype_label' => array(
                'label' => __('Retype Label', $bkpkFM->name),
                'placeholder' => __('Label for retype field', $bkpkFM->name)
            ),
            'captcha_theme' => [
                'type' => 'select',
                'label' => __('reCaptcha Theme', $bkpkFM->name),
                'options' => [
                    '' => __('Light', $bkpkFM->name),
                    'dark' => __('Dark', $bkpkFM->name)
                ]
            ],
            'captcha_type' => [
                'type' => 'select',
                'label' => __('reCaptcha Type', $bkpkFM->name),
                'options' => [
                    '' => __('Image', $bkpkFM->name),
                    'audio' => __('Audio', $bkpkFM->name)
                ]
            ],
            'captcha_lang' => [
                'label' => __('reCaptcha Language', $bkpkFM->name),
                'placeholder' => __('(e.g. en) Leave blank for auto detection', $bkpkFM->name),
                'info' => __('(e.g. en) Leave blank for auto detection', $bkpkFM->name)
            ],
            'resize_image' => array(
                'type' => 'checkbox',
                'label' => __('Resize Image', $bkpkFM->name),
                'child' => 'crop_image'
            ),
            'retype_email' => array(
                'type' => 'checkbox',
                'label' => __('Retype Email', $bkpkFM->name),
                'child' => 'retype_label'
            ),
            'retype_password' => array(
                'type' => 'checkbox',
                'label' => __('Retype Password', $bkpkFM->name),
                'child' => 'retype_label'
            )
        );
        
        $checkboxes = array(
            'required' => __('Required', $bkpkFM->name),
            'admin_only' => __('Admin Only', $bkpkFM->name),
            'non_admin_only' => __('Non-Admin Only', $bkpkFM->name),
            'read_only' => __('Read-Only for all user', $bkpkFM->name),
            'read_only_non_admin' => __('Read-Only for non admin', $bkpkFM->name),
            'unique' => __('Unique', $bkpkFM->name),
            'registration_only' => __('Only on Registration Page', $bkpkFM->name),
			'bkpk_report_export' => __('Show in Report and Export', $bkpkFM->name),

            
            'disable_ajax' => __('Disable AJAX upload', $bkpkFM->name),
            'hide_default_avatar' => __('Hide default avatar', $bkpkFM->name),
            // 'resize_image' => __( 'Resize Image', $bkpkFM->name ),
            'crop_image' => __('Crop Image', $bkpkFM->name),
            
            'line_break' => __('Line Break', $bkpkFM->name),
            'integer_only' => __('Allow integer only', $bkpkFM->name),
            'as_range' => 'Use as range',
            
            'force_username' => __('Force to change username', $bkpkFM->name),
            // 'retype_email' => __( 'Retype Email', $bkpkFM->name ),
            // 'retype_password' => __( 'Retype Password', $bkpkFM->name ),
            'password_strength' => __('Show password strength meter', $bkpkFM->name),
            'required_current_password' => __('Current password is required', $bkpkFM->name),
            'show_divider' => __('Show Divider', $bkpkFM->name),
            'rich_text' => __('Use Rich Text', $bkpkFM->name),
            'make_field_shared' => __('Make this field as shared', $bkpkFM->name),
            'advanced_mode' => __('Advanced mode', $bkpkFM->name)
        );
        
        foreach ($checkboxes as $key => $val) {
            $inputs[$key] = array(
                'type' => 'checkbox',
                'label' => $val
            );
        }
        
        $this->inputs = $inputs;
    }

    /**
     * Run inside createElement() before executing field
     */
    private function _pre_field_title()
    {
        if (empty($this->data['is_new']))
            return;
        
        if (isset($this->typeData['field_group']) && 'wp_default' == $this->typeData['field_group']) {
            if (isset($this->typeData['title']))
                $this->data['field_title'] = $this->typeData['title'];
        }
    }

    /**
     * Backword comatibility for placeholder as title
     */
    private function _pre_title_position()
    {
        global $bkpkFM;
        if (! empty($this->data['title_position']) && 'placeholder' == $this->data['title_position']) {
            $this->inputs['title_position']['options']['placeholder'] = __('Placeholder', $bkpkFM->name);
        }
    }

    /**
     * Creating divider element
     * Run automaticallly inside createElement() method
     *
     * @return html
     */
    private function _element_divider()
    {
        return '<div class="pf_divider"></div>';
    }

    /**
     * Creating content element
     * Run automaticallly inside createElement() method
     *
     * @return html
     */
    private function _element_content($args)
    {
        global $bkpkFM;
        extract($this->data);
        
        $args['label'] = __('Content', $bkpkFM->name);
        $args['value'] = isset($default_value) ? $default_value : null;
        
        return $bkpkFM->createInput('default_value', 'textarea', $args);
    }

    /**
     * Creating value element
     * Run automaticallly inside createElement() method
     *
     * @return html
     */
    private function _element_value($args)
    {
        global $bkpkFM;
        extract($this->data);
        
        $args['label'] = __('Value', $bkpkFM->name);
        $args['value'] = isset($default_value) ? $default_value : null;
        
        return $bkpkFM->createInput('default_value', 'text', $args);
    }

    /**
     * Creating field_type element
     * Run automaticallly inside createElement() method
     *
     * @return html
     */
    private function _element_field_type()
    {
        global $bkpkFM;
        
        extract($this->data);
        
        $field_type_data = $bkpkFM->getFields('key', $field_type);
        $field_type_title = $field_type_data['title'];
        $field_group = $field_type_data['field_group'];
        $field_types_options = $bkpkFM->getFields('field_group', $field_group, 'title', ! $bkpkFM->isPro);
        
        return $bkpkFM->createInput('field_type', 'select', array(
            'label' => __('Field Type', $bkpkFM->name),
            'value' => isset($field_type) ? $field_type : null,
            'class' => 'form-control',
            'label_class' => 'col-sm-3 control-label',
            'field_enclose' => 'div class="col-sm-6"',
            'enclose' => 'div class="form-group bkpk_fb_field"',
            'by_key' => true
        ), $field_types_options);
    }

    /**
     * Creating checkbox_group element
     * Run automaticallly inside createElement() method
     *
     * @return html
     */
    private function _element_checkbox_group($args, $input)
    {
        global $bkpkFM;
        
        extract($this->data);
        
        array_shift($input);
        
        $html = '<div class="form-group"><label class="control-label col-sm-3">' . array_shift($input) . '</label>';
        
        $inputs = $this->inputs;
        
        $html .= '<div class="col-sm-8">';
        foreach ($input as $checkbox) {
            $data = $inputs[$checkbox];
            
            $inputArg = array(
                'value' => '1',
                'checked' => ! empty($$checkbox) ? true : false,
                'label' => $data['label'],
                'class' => 'form-control',
                'enclose' => 'p class="bkpk_fb_field"'
            );
            
            if (! empty($data['child'])) {
                $inputArg['class'] .= ' bkpk_parent';
                $inputArg['data-child'] = $data['child'];
            }
            
            $html .= $bkpkFM->createInput($checkbox, 'checkbox', $inputArg);
        }
        $html .= '</div></div>';
        
        return $html;
    }

    /**
     * Creating allowed_roles element
     * Run automaticallly inside createElement() method
     *
     * @return html
     */
    private function _element_allowed_roles()
    {
        global $bkpkFM;
        
        $roles = $bkpkFM->getRoleList(true);
        
        extract($this->data);
        
        return $bkpkFM->createInput('allowed_roles', 'multiselect', array(
            'label' => __('Allowed Roles', $bkpkFM->name),
            'value' => isset($allowed_roles) ? $allowed_roles : null,
            'class' => 'form-control bkpk_multiselect',
            'label_class' => 'col-sm-3 control-label',
            'field_enclose' => 'div class="col-sm-6"',
            'enclose' => 'div class="form-group bkpk_fb_field"',
            'by_key' => true
        ), $roles);
    }

    /**
     * Creating default_role element
     * Run automaticallly inside createElement() method
     *
     * @return html
     */
    private function _element_default_role()
    {
        global $bkpkFM;
        
        $roles = $bkpkFM->getRoleList(true);
        $emptyFirstRoles = $roles;
        array_unshift($emptyFirstRoles, null);
        
        extract($this->data);
        
        return $bkpkFM->createInput('default_value', 'select', array(
            'label' => __('Default Role', $bkpkFM->name),
            'value' => isset($default_value) ? $default_value : null,
            'after' => __('Should be one of the Allowed Roles', $bkpkFM->name),
            'class' => 'form-control',
            'label_class' => 'col-sm-3 control-label',
            'field_enclose' => 'div class="col-sm-6"',
            'enclose' => 'div class="form-group bkpk_fb_field"',
            'by_key' => true
        ), $emptyFirstRoles);
    }

    private function _element_options()
    {
        global $bkpkFM;
        
        return $bkpkFM->renderPro('optionsSelection', array(
            'data' => $this->data
        ), 'fields', true);
    }

    /**
     * Creating conditional_logic element
     * Run automaticallly inside createElement() method
     *
     * @return html
     */
    private function _element_conditional_logic()
    {
        global $bkpkFM;
        extract($this->data);
        
        return $html = $bkpkFM->renderPro('conditionalPanel', array(
            'id' => $this->id,
            'conditional' => ! empty($condition) && is_array($condition) ? $condition : array(),
            'fieldList' => self::$formFieldsDropdown
        ), 'forms', true);
    }

    /**
     * Temp implementation using umInput class
     * Creating divider element
     * Run automaticallly inside createElement() method
     *
     * @return html
     */
    private function _element_input_type($args)
    {
        $args['name'] = 'input_type';
        $args['class'] = $args['class'] . ' bkpk_parent';
        
        $attr = $args;
        $attr['_enclose'] = [
            'div',
            'class' => 'col-sm-6'
        ];
        unset($attr['type']);
        unset($attr['label']);
        unset($attr['label_class']);
        unset($attr['value']);
        unset($attr['field_enclose']);
        unset($attr['enclose']);
        unset($attr['by_key']);
        
        $options = $this->inputs['input_type']['options'];
        
        $input = new Html\Html('div', [
            'class' => 'form-group bkpk_fb_field'
        ]);
        $input->label($args['label'], [
            'class' => $args['label_class']
        ]);
        $input->select($args['value'], $attr, $options);
        
        return $input->render();
    }

    /**
     * Creating single field's element
     *
     * @param string $input            
     * @return type html input
     */
    function createElement($input)
    {
        global $bkpkFM;
        
        if (empty($input))
            return;
        
        $name = is_array($input) ? $input[0] : $input;
        
        if (method_exists($this, '_pre_' . $name)) {
            $methodName = '_pre_' . $name;
            $this->$methodName();
        }
        
        extract($this->data);
        
        $args = isset($this->inputs[$name]) ? $this->inputs[$name] : array();
        
        $args = wp_parse_args($args, array(
            'type' => 'text',
            'value' => isset($$name) ? $$name : null,
            'class' => 'form-control',
            'label_class' => 'col-sm-3 control-label',
            'field_enclose' => 'div class="col-sm-6"',
            'after' => $this->tooltip($input),
            'enclose' => 'div class="form-group bkpk_fb_field"'
        ));
        
        if ('checkbox' == $args['type']) {
            $args['label_class'] = 'col-sm-offset-3 col-sm-6';
            $args['field_enclose'] = '';
            $args['enclose'] = 'p class="form-group bkpk_fb_field"';
        }
        
        if ('select' == $args['type'] && ! isset($args['by_key']))
            $args['by_key'] = true;
        
        $options = array();
        if (isset($args['options'])) {
            $options = $args['options'];
            unset($args['options']);
        }
        
        if (method_exists($this, '_element_' . $name)) {
            $methodName = '_element_' . $name;
            return $this->$methodName($args, $input);
        }
        
        $type = $args['type'];
        unset($args['type']);
        
        return $bkpkFM->createInput($name, $type, $args, $options);
    }

    /**
     * Show tooltip
     *
     * @param string $input            
     */
    private function tooltip($input)
    {
        if (! (is_string($input) && isset($this->inputs[$input]) && isset($this->inputs[$input]['info'])))
            return;
        
        return Html\Html::i('', [
            'class' => 'fa fa-info-circle',
            'style' => 'margin:10px 0;font-size: 1.3em;',
            'data-toggle' => 'tooltip',
            'data-placement' => 'right',
            'title' => $this->inputs[$input]['info']
        ]);
    }

    /**
     * Run by $this->build()
     *
     * @return array Single field's conaining element array
     */
    function fieldSpecification()
    {
        global $bkpkFM;
        
        $start1 = array(
            'field_title',
            'title_position',
            'description'
        );
        $start2 = array(
            'field_title',
            'title_position',
            'placeholder',
            'description'
        );
        $start3 = array(
            'field_title',
            'title_position',
            'description',
            'meta_key',
            'default_value'
        );
        $start4 = array(
            'field_title',
            'title_position',
            'placeholder',
            'description',
            'meta_key',
            'default_value'
        );
        $checkbox1 = array(
            array(
                'checkbox_group',
                'Rules',
                'admin_only',
                'read_only',
                'read_only_non_admin',
				'bkpk_report_export',
            )
        );
        $checkbox2 = array(
            array(
                'checkbox_group',
                'Rules',
                'admin_only',
                'read_only',
                'read_only_non_admin',
                'unique',
				'bkpk_report_export'
            )
        );
        $checkbox3 = array(
            array(
                'checkbox_group',
                'Rules',
                'admin_only',
                'non_admin_only',
                'read_only',
                'read_only_non_admin',
				'bkpk_report_export'
            )
        );
        $style1 = array(
            'divider',
            'max_char',
            'field_size',
            'field_class',
            'css_class',
            'css_style'
        );
        $style2 = array(
            'divider',
            'max_char',
            'field_size',
            'field_height',
            'field_class',
            'css_class',
            'css_style'
        );
        $style3 = array(
            'divider',
            'field_size',
            'field_class',
            'css_class',
            'css_style'
        );
        
        $fields = array(
            'user_login' => array(
                'basic' => $start2,
                'advanced' => array_merge(array(
                    array(
                        'checkbox_group',
                        'Rule',
                        'admin_only'
                    )
                ), $style1)
            ),
            'user_email' => array(
                'basic' => array_merge($start2, array(
                    array(
                        'checkbox_group',
                        '',
                        'retype_email'
                    ),
                    'retype_label'
                )),
                'advanced' => array_merge($checkbox1, $style1)
            ),
            'user_pass' => array(
                'basic' => array_merge($start2, array(
                    'regex',
                    'error_text'
                ), array(
                    array(
                        'checkbox_group',
                        '',
                        'retype_password',
                        'password_strength',
                        'required_current_password'
                    ),
                    'retype_label'
                )),
                'advanced' => array_merge(array(
                    array(
                        'checkbox_group',
                        'Rule',
                        'admin_only'
                    )
                ), $style1)
            ),
            'description' => array(
                'basic' => array_merge($start2, array(
                    array(
                        'checkbox_group',
                        '',
                        'required',
                        'rich_text'
                    )
                )),
                'advanced' => array_merge($checkbox2, $style2)
            ),
            'role' => array(
                'basic' => array_merge($start1, array(
                    'allowed_roles',
                    'default_role',
                    'role_selection_type',
                    'required'
                )),
                'advanced' => array_merge($checkbox3, $style3)
            ),
            'user_avatar' => array(
                'basic' => array_merge($start1, array(
                    'allowed_extension',
                    'image_size',
                    'max_file_size'
                ), array(
                    array(
                        'checkbox_group',
                        '',
                        'required',
                        'hide_default_avatar',
                        'resize_image',
                        'crop_image',
                        'disable_ajax'
                    )
                )),
                'advanced' => array_merge($checkbox3, array(
                    'divider',
                    'field_class',
                    'css_class',
                    'css_style'
                ))
            ),
            'rich_text' => [
                'basic' => [
                    'field_title',
                    'title_position',
                    'description',
                    'meta_key',
                    'default_value',
                    'required'
                ],
                'advanced' => array_merge($checkbox2, $style2)
            ],
            'hidden' => array(
                'basic' => array(
                    'meta_key',
                    'default_value'
                ),
                'advanced' => array(
                    array(
                        'checkbox_group',
                        '',
                        'admin_only'
                    )
                )
            ),
            
            // select == multiselect radio == checkbox
            'select' => array(
                'basic' => array(
                    'field_title',
                    'title_position',
                    'description',
                    'meta_key',
                    'options',
                    array(
                        'checkbox_group',
                        '',
                        'advanced_mode',
                        'required'
                    )
                ),
                'advanced' => array_merge($checkbox1, $style3)
            ),
            'radio' => array(
                'basic' => array(
                    'field_title',
                    'title_position',
                    'description',
                    'meta_key',
                    'options',
                    array(
                        'checkbox_group',
                        '',
                        'advanced_mode',
                        'required',
                        'line_break'
                    )
                ),
                'advanced' => array_merge($checkbox1, $style3)
            ),
            'checkbox' => array(
                'basic' => array(
                    'field_title',
                    'title_position',
                    'description',
                    'meta_key',
                    'options',
                    array(
                        'checkbox_group',
                        '',
                        'advanced_mode',
                        'required',
                        'line_break'
                    )
                ),
                'advanced' => array_merge($checkbox1, $style3)
            ),
            'url' => array(
                'basic' => array(
                    'field_title',
                    'title_position',
                    'placeholder',
                    'description',
                    'meta_key',
                    'default_value',
                    'required'
                ),
                'advanced' => array_merge($checkbox2, $style3)
            ),
            'wp_default' => array(
                'basic' => array(
                    'field_title',
                    'title_position',
                    'placeholder',
                    'description',
                    'required'
                ),
                'advanced' => array_merge($checkbox2, $style1)
            ),
            'group_1' => array(
                'basic' => array(
                    'field_title',
                    'title_position',
                    'placeholder',
                    'description',
                    'meta_key',
                    'default_value',
                    'required'
                ),
                'advanced' => array_merge($checkbox2, $style2)
            ),
            'group_3' => array(
                'basic' => array_merge($start1, array(
                    array(
                        'checkbox_group',
                        '',
                        'show_divider'
                    )
                )),
                'advanced' => array(
                    'css_class',
                    'css_style'
                )
            )
        );
        
        if ($bkpkFM->isPro) {
            $fieldsPro = array(
                'multiselect' => array(
                    'basic' => array(
                        'field_title',
                        'title_position',
                        'description',
                        'meta_key',
                        'options',
                        array(
                            'checkbox_group',
                            '',
                            'advanced_mode',
                            'required'
                        )
                    ),
                    'advanced' => array_merge($checkbox1, $style3)
                ),
                'datetime' => array(
                    'basic' => array_merge($start4, array(
                        'datetime_selection',
                        'date_format',
                        'year_range',
                        'required'
                    )),
                    'advanced' => array_merge($checkbox2, $style3)
                ),
                'password' => array(
                    'basic' => array_merge($start4, array(
                        array(
                            'checkbox_group',
                            '',
                            'required',
                            'retype_password',
                            'password_strength'
                        )
                    )),
                    'advanced' => array_merge($checkbox1, $style1)
                ),
                'email' => array(
                    'basic' => array_merge($start4, array(
                        array(
                            'checkbox_group',
                            '',
                            'required',
                            'retype_email'
                        )
                    )),
                    'advanced' => array_merge($checkbox2, $style1)
                ),
                'file' => array(
                    'basic' => array_merge($start3, array(
                        'allowed_extension',
                        'image_width',
                        'image_height',
                        'max_file_size'
                    ), array(
                        array(
                            'checkbox_group',
                            '',
                            'required',
                            'resize_image',
                            'crop_image',
                            'disable_ajax'
                        )
                    )),
                    'advanced' => array_merge($checkbox1, array(
                        'divider',
                        'field_class',
                        'css_class',
                        'css_style'
                    ))
                ),
                'number' => array(
                    'basic' => array_merge($start4, array(
                        'min_number',
                        'max_number',
                        'step'
                    ), array(
                        array(
                            'checkbox_group',
                            '',
                            'required',
                            'integer_only',
                            'as_range'
                        )
                    )),
                    'advanced' => array_merge($checkbox2, $style3)
                ),
                'country' => array(
                    'basic' => array_merge($start3, array(
                        'country_selection_type',
                        'required'
                    )),
                    'advanced' => array_merge($checkbox2, $style3)
                ),
                'custom' => array(
                    'basic' => array_merge($start4, array(
                        'input_type',
                        'regex',
                        'error_text',
                        'retype_label',
                        'required',
                        'retype_email',
                        'retype_password'
                    )),
                    'advanced' => array_merge($checkbox2, $style3)
                ),
                
                'html' => array(
                    'basic' => array(
                        'field_title',
                        'title_position',
                        'content',
                        'description'
                    ),
                    'advanced' => array()
                ),
                'captcha' => array(
                    'basic' => array_merge($start1, array(
                        'captcha_theme',
                        'captcha_type',
                        'captcha_lang'
                    ), array(
                        array(
                            'checkbox_group',
                            '',
                            'registration_only'
                        )
                    )),
                    'advanced' => array()
                )
            );
            
            $fields = array_merge($fields, $fieldsPro);
        }
        
        $groups = array(
            'text' => 'group_1',
            'textarea' => 'group_1',
            // 'rich_text' => 'group_1',
            'image_url' => 'group_1',
            'phone' => 'group_1',
            'page_heading' => 'group_3',
            'section_heading' => 'group_3'
        );
        
        foreach ($groups as $key => $val)
            $fields[$key] = $fields[$val];
        
        $fieldType = isset($fields[$this->type]) ? $this->type : 'wp_default';
        $field = $fields[$fieldType];
        
        if ('fields_editor' == $this->editor) {
            array_unshift($field['advanced'], 'field_type');
        } elseif ('form_editor' == $this->editor) {
            if (! empty($this->data['is_shared'])) {
                $key = array_search('meta_key', $field['basic']);
                if ($key)
                    unset($field['basic'][$key]);
            } else {
                array_unshift($field['advanced'], 'field_type');
                array_push($field['advanced'], 'make_field_shared');
            }
        }
        
        $this->addConditionalLogic($field['advanced'], $fieldType);
        
        return $field;
    }

    /**
     * Add conditional_logic to end of provided array
     *
     * @param array $array            
     * @param unknown $fieldType            
     */
    private function addConditionalLogic(array &$array, $fieldType)
    {
        if ('form_editor' == $this->editor && ! in_array($fieldType, [
            'hidden',
            'page_heading',
            'captcha'
        ])) {
            array_push($array, 'conditional_logic');
        }
    }

    function build()
    {
        global $bkpkFM;
        $html = null;
        $field = $this->fieldSpecification();
        
        $tabsText = [
            'basic' => __('Basic', $bkpkFM->name),
            'advanced' => __('Advanced', $bkpkFM->name)
        ];
        $tabs = [];
        foreach ($field as $key => $group) {
            $inputs = null;
            $inputs .= '<br /><div class="form-horizontal" role="form">';
            foreach ($group as $input) {
                $inputs .= $this->createElement($input);
            }
            $inputs .= '</div>';
            $tabs[$tabsText[$key]] = $inputs;
        }
        
        return $bkpkFM->buildTabs('fields_tab_' . $this->id, $tabs);
    }

    /**
     * Building field panel
     *
     * @return html
     */
    function buildPanel()
    {
        $class = '';
        $panelClass = 'panel-info';
        
        if ('fields_editor' == $this->editor) {
            $class = ' in';
        } elseif ('form_editor' == $this->editor) {
            if (empty($this->data['is_shared'])) {
                $panelClass = 'panel-success';
                $class = ' in';
            }
        }
        
        return '<div id="bkpk_admin_field_' . $this->id . '" class="panel ' . $panelClass . ' bkpk_field_single">
            <div class="panel-heading">
                <h3 class="panel-title">
                    ' . $this->title() . '
                    <span class="bkpk_trash" title="Remove this field"><i style="margin-left:10px" class="fa fa-times"></i></span> 
                    <span title="Click to toggle"><i class="fa fa-caret-down"></i></span>
                </h3>
            </div>
            <div class="panel-collapse collapse' . $class . '">
                <div class="panel-body">
                ' . $this->build() . '
                </div>
            </div>
        </div>';
    }

    /**
     * Field panel title for field's editor
     *
     * @return html
     */
    function title()
    {
        $label = isset($this->data['field_title']) ? $this->data['field_title'] : '';
        $typeLabel = isset($this->typeData['title']) ? $this->typeData['title'] : '';
        return '<span class="bkpk_field_panel_title">ID:<span class="bkpk_field_id">' . $this->id . '</span>' . ' (<span>' . $typeLabel . '</span>) ' . '<span class="bkpk_field_label">' . $label . '</span></span>' . '<input type="hidden" class="bkpk_field_type" value="' . $this->type . '"/>';
    }
}