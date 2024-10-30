<?php
namespace BPKPFieldManager;

class SupportHtmlModel
{

    function boxHowToUse()
    {
        global $bkpkFM;
        $html = null;
        $html .= sprintf('<p>' . __('<strong>Step 1.</strong> Create a form and populate it with fields by %s page.', $bkpkFM->name) . '</p>', $bkpkFM->adminPageUrl('forms'));
        $html .= sprintf('<p>' . __('<strong>Step 2.</strong> Write shortcode to your page or post. Shortcode (e.g.): %s', $bkpkFM->name) . '</p>', '[llms-bkpk-profile form="Form_Name"]');
        $html .= "<div><center><a class=\"button-primary\" href=\"" . $bkpkFM->website . "\">" . __('Visit Plugin Site', $bkpkFM->name) . "</a></center></div>";
        return $html;
    }

    function boxGetPro()
    {
        global $bkpkFM;
        $html = null;
        $html .= "<div style='padding-left: 10px'>";
        $html .= "<p><strong>" . __('Get pro version for:', $bkpkFM->name) . "</strong></p>";
        $html .= "<li>" . __('Login, registration and profile widget.', $bkpkFM->name) . "</li>";
        $html .= "<li>" . __('Add extra fields to backend profile.', $bkpkFM->name) . "</li>";
        $html .= "<li>" . __('Role based user redirection on login, logout and registratioin.', $bkpkFM->name) . "</li>";
        $html .= "<li>" . __('User activatation/deactivation, Admin approval on new user registration.', $bkpkFM->name) . "</li>";
        $html .= "<li>" . __('Customize email notification with including extra field\'s data.', $bkpkFM->name) . "</li>";
        $html .= "<p></p>";
        $html .= "<li>" . __('Advanced fields for creating profile/registration form.', $bkpkFM->name) . "</li>";
        $html .= "<li>" . __('Fight against spam by Captcha.', $bkpkFM->name) . "</li>";
        $html .= "<li>" . __('Split your form into multiple page by using Page Heading.', $bkpkFM->name) . "</li>";
        $html .= "<li>" . __('Group fields using Section Heading.', $bkpkFM->name) . "</li>";
        $html .= "<li>" . __('Allow user to upload their file by File Upload.', $bkpkFM->name) . "</li>";
        $html .= "<li>" . __('Country Dropdown for country selection.', $bkpkFM->name) . "</li>";
        $html .= "<li>" . __('Use Custom Field to build custom input field.', $bkpkFM->name) . "</li>";
        $html .= "<li>" . __('Use free add-ons.', $bkpkFM->name) . "</li>";
        $html .= "<br />";
        $html .= "<center><a class='button-primary' href='http://llms-bkpk.com'>" . sprintf(__('Get %s', $bkpkFM->name), 'lifterlms Back-Pack Pro') . "</a></center>";
        $html .= "</div>";
        
        return $html;
    }

    function boxLiveDemo()
    {
        global $bkpkFM;
        $html = null;
        $html .= "<div style='padding-left: 10px'>";
        $html .= "<p>" . sprintf(__('See live demo of %s', $bkpkFM->name), '<strong>lifterlms Back-Pack Pro</strong>') . "</p>";
        $html .= "<center><a class='button-primary' href='http://demo.llms-bkpk.com/'>" . __('Live Demo', $bkpkFM->name) . "</a></center>";
        $html .= "</div>";
        return $html;
    }

    function boxShortcodesDocs()
    {
        global $bkpkFM;
        $html = null;
        $html .= "<div style='padding-left: 10px'>";
        $html .= '<p><div><strong>' . __('Profile shortcode', $bkpkFM->name) . '</div></strong>[llms-bkpk-profile form="Form_Name"]</p>';
        $html .= '<p><div><strong>' . __('Registration shortcode', $bkpkFM->name) . '</strong></div>[llms-bkpk-registration form="Form_Name"]</p>';
        $html .= '<p><div><strong>' . __('Profile / Registration', $bkpkFM->name) . '</strong></div><div>[llms-bkpk type=profile-registration form="Form_Name"]</div><div><em>(To show user profile if user logged in, or showing registration form, if user not logged in.)</em></div></p>';
        $html .= '<p><div><strong>' . __('Public profile', $bkpkFM->name) . '</strong></div><div>[llms-bkpk type=public form="Form_Name"]</div><div><em>(To show public profile if user_id parameter provided as GET request.)</em></div></p>';
        $html .= '<p><div><strong>' . __('Login shortcode', $bkpkFM->name) . '</strong></div>[llms-bkpk-login] OR [llms-bkpk-login form="Form_Name"]</p>';
        if ($bkpkFM->isPro()) {
            $html .= '<p><div><strong>' . __('Field shortcode', $bkpkFM->name) . '</strong></div>[llms-bkpk-field id=Field_ID]</p>';
            $html .= '<p><div><strong>' . __('Field content shortcode', $bkpkFM->name) . '</strong></div>[llms-bkpk-field-value id=Field_ID] OR [llms-bkpk-field-value key=meta_key]</p>';
        }
        $html .= "<p></p>";
        $html .= "<center><a class='button-primary' href='http://llms-bkpk.com/documentation/shortcodes/'>" . __('Read More', $bkpkFM->name) . "</a></center>";
        $html .= "</div>";
        return $html;
    }

    function boxLinks()
    {
        global $bkpkFM;
        $html = "<div style='padding-left: 10px'>";
        $html .= '<li><a href="http://llms-bkpk.com/documentation/">' . __('Documentation', $bkpkFM->name) . '</a></li>';
        $html .= '<li><a href="http://llms-bkpk.com/videos/">' . __('Video Tutorials', $bkpkFM->name) . '</a></li>';
        if ($bkpkFM->isPro()) {
            $html .= '<li><a href="http://llms-bkpk.com/forums/">' . __('Forums', $bkpkFM->name) . '</a></li>';
            $html .= '<li><a href="http://llms-bkpk.com/add-ons/">' . __('Add Ons', $bkpkFM->name) . '</a></li>';
        }
        $html .= '<li><a href="http://demo.llms-bkpk.com/">' . __('Live Demo', $bkpkFM->name) . '</a></li>';
        $html .= "</div>";
        return $html;
    }

    function boxTips()
    {
        global $bkpkFM;
        $html = "<div style='padding-left: 10px'>";
        $html .= "</div>";
        return $html;
    }

    function getProLink($label = null)
    {
        global $bkpkFM;
        $label = $label ? $label : $bkpkFM->website;
        
        return "<a href=\"{$bkpkFM->website}\">$label</a>";
    }

    function proDemoImage($img = null)
    {
        global $bkpkFM;
        if ($bkpkFM->isPro)
            $html = adminNotice("Please validate your license to use this feature.");
        else
            $html = adminNotice("This feature is only supported in Pro version. Get <a href=\"{$bkpkFM->website}\">lifterlms Back-Pack Pro</a>");
        
        if ($img)
            $html .= "<img src=\"https://s3.amazonaws.com/llms-bkpk/public/plugin/images/{$img}?ver={$bkpkFM->version}\" width=\"100%\" onclick=\"umGetProMessage(this)\" />";
        
        return $html;
    }

    function showInfo($data, $title = '', $icon = true)
    {
        $iconHtml = "<span style=\"display: inline-block;\" class=\"ui-icon ui-icon-info\"></span>";
        
        if ($icon)
            $title .= $iconHtml;
        
        $title = $title ? $title : $iconHtml;
        
        return "<p data-ot='$data' class='my-element' >$title</p>";
    }

    function buildPanel($title, $body)
    {
        ?>
<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title">
                    <?php echo $title; ?> <i class="fa fa-caret-down"></i>
		</h3>
	</div>
	<div class="panel-collapse collapse">
		<div class="panel-body"><?php echo $body; ?>
                </div>
	</div>
</div>
<?php
    }

    function buildTabs($name = null, $tabs = array())
    {
        $li = null;
        $tabContent = null;
        $active = 'active';
        
        foreach ($tabs as $title => $content) {
            $id = str_replace(' ', '_', strtolower($title));
            if (! empty($name))
                $id = "{$name}_{$id}";
            
            $li .= "<li class=\"nav $active\"><a href=\"#{$id}\" data-toggle=\"tab\">$title</a></li>";
            
            if ($active)
                $active = 'in active';
            $tabContent .= "<div class=\"tab-pane fade $active\" id=\"$id\">$content</div>";
            
            if ($active)
                $active = null;
        }
        
        $html = '<ul class="nav nav-tabs">' . $li . '</ul>';
        $html .= '<div class="tab-content">' . $tabContent . '</div>';
        
        return $html;
    }
}