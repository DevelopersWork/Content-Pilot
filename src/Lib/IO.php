<?php
/**
 * @package DWContentPilot
 */
namespace DW\ContentPilot\Lib;

class IO
{
    public static function read_asset_file( $file )
    {

        $extensions = explode('.', $file);
        $extension = end($extensions);

        $path = dw_cp_plugin_dir_path . 'assets/' . $extension . '/' . $file;

        $html = file_get_contents($path);

        return $html;
    }
}