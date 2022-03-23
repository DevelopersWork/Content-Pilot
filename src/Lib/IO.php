<?php
/**
 * @package DWContentPilot
 */
namespace DW\ContentPilot\Lib;

class IO
{

    public static function read_asset_file($plugin, $file)
    {

        $plugin_path = plugin_dir_path($plugin);
        
        $assets_path = $plugin_path . 'assets/';

        $extensions = explode('.', $file);
        $extension = end($extensions);

        $path = $assets_path . $extension . '/' . $file;

        $html = file_get_contents($path);

        return $html;
    }
}
