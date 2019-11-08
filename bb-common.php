<?


function returnIfExists($json, $setting) {
    if (array_key_exists($setting, $json)) {
        return $json[$setting];
    }
    return "";
}

function convertAndGetSettings() {
    global $settings;
        
    $cfgFile = $settings['configDirectory'] . "/plugin.fpp-BigButtons";
    if (file_exists($cfgFile)) {
        $pluginSettings = parse_ini_file($cfgFile);
        $json = array();
        for ($x = 1; $x <= 20; $x++) {
            $buttonName = "button" . sprintf('%02d', $x);
            $color = returnIfExists($pluginSettings, $buttonName . "color");
            $desc = returnIfExists($pluginSettings, $buttonName . "desc");
            $script = returnIfExists($pluginSettings, $buttonName . "script");
            
            if ($color != "" || $desc != "" || $script != "") {
                $json["buttons"][$x]["description"] = $desc;
                $json["buttons"][$x]["color"] = $color;
                if ($script != "" && $script != null) {
                    $json["buttons"][$x]["command"] = "Run Script";
                    $json["buttons"][$x]["args"][] = $script;
                } else {
                    $json["buttons"][$x]["command"] = "";
                }
            }
        }
        $fontsize = returnIfExists($pluginSettings, "buttonFontSize");
        if ($fontsize != "" && $fontsize != null) {
            $json["fontSize"] = (int)$fontsize;
        }
        $title = returnIfExists($pluginSettings, "buttonTitle");
        if ($title != "" && $title != null) {
            $json["title"] = $title;
        }

        file_put_contents($cfgFile . ".json", json_encode($json, JSON_PRETTY_PRINT));
        unlink($cfgFile);
        return $json;
    }
    $j = file_get_contents($cfgFile . ".json");
    $json = json_decode($j, true);
    return $json;
}


?>
