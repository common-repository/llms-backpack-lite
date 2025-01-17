<?php
global $bkpkFM;
// Expected: $text

$html = null;

$msgs = $bkpkFM->msgs();

$html .= '<h4>' . __('Custom text for front-end', $bkpkFM->name) . '</h4>';

$html .= '<em>' . __('(Leave blank to use default)', $bkpkFM->name) . '</em>';

// $html .= "<div class='pf_divider'></div>";

foreach ($msgs as $key => $msg) {
    
    if (strpos($key, 'group_') !== false) {
        $html .= "<div class='pf_divider'></div><h4>$msg</h4>";
        continue;
    }
    
    $html .= $bkpkFM->createInput("text[$key]", 'text', array(
        'id' => "text_$key",
        'label' => $key,
        'value' => ! empty($text[$key]) ? $text[$key] : $msg,
        "label_class" => "pf_label",
        "class" => "bkpk_input",
        "style" => "width: 600px;",
        'enclose' => 'p'
    )
    );
}

?>
