<?php
namespace NObjects;

/**
 * Utility directory helper.
 *
 * @author Nesbert Hidalgo
 */
class Directory
{
    /**
     * Get an array of directory listings with $options for flexibility.
     * Options for $options array are as follows:
     *
     * 'recursive': default false
     * 'showDirs': default false, add directory to array
     * 'showInvisible': default false, add .* files to array
     * 'group': default false, Group files by directory
     * 'filter': default '', use a regex to filter array, example '/.php$/'
     *
     * @param string $path
     * @param array $options array of options
     * @return array
     * @link http://snippets.dzone.com/posts/show/155
     *
     */
    public static function ls($path, $options = array())
    {
        // set default options
        if (!is_array($options)) $options = array();
        if (!isset($options['recursive'])) $options['recursive'] = false;
        if (!isset($options['showDirs'])) $options['showDirs'] = false;
        if (!isset($options['showInvisible'])) $options['showInvisible'] = false;
        if (!isset($options['group'])) $options['group'] = false;
        if (!isset($options['filter'])) $options['filter'] = '';

        if (!is_dir($path)) return array();

        $array_items = array();
        if ($handle = opendir($path)) {
            while (false !== ($file = readdir($handle))) {

                if (!$options['showInvisible'] && $file{0} == '.') continue;

                if ($file != '.' && $file != '..') {

                    $filepath = $path . DIRECTORY_SEPARATOR . $file;

                    // be nice to non *nix machines
                    $dir_regex = DIRECTORY_SEPARATOR == '/' ? '/\/\//si' : '/\\\\/si';

                    if (is_dir($filepath)) {

                        if ($options['showDirs']) {
                            if ($options['group']) {
                                $array_items[dirname($filepath)][] = preg_replace($dir_regex, DIRECTORY_SEPARATOR, $filepath);
                            } else {
                                $array_items[] = preg_replace($dir_regex, DIRECTORY_SEPARATOR, $filepath);
                            }
                        }
                        if ($options['recursive']) {
                            $array_items = array_merge($array_items, self::ls($filepath, $options));
                        }

                    } else {

                        if ($options['filter'] && !preg_match($options['filter'], $file)) continue;

                        if ($options['group']) {
                            $array_items[dirname($filepath)][] = preg_replace($dir_regex, DIRECTORY_SEPARATOR, $filepath);
                        } else {
                            $array_items[] = preg_replace($dir_regex, DIRECTORY_SEPARATOR, $filepath);
                        }

                    }

                }
            }
            closedir($handle);
        }
        return $array_items;
    }

    /**
     * Gets a directories files in a directory by file type. Returns an
     * associative array with the file_name as key and file_path as value.
     *
     * @param string $path
     * @param string $file_type Optional default set to 'php'
     * @param bool $showInvisible
     * @return array
     */
    public static function lsWithFilename($path, $file_type = 'php', $showInvisible = false)
    {
        $files = self::ls($path, array(
            'filter' => '/.'.$file_type.'$/',
            'showInvisible' => $showInvisible
            ));
        foreach ($files as $k => $file) {
            if ( String::endsWith('.'.$file_type, $file) ) {
                $files[basename($file, '.'.$file_type)] = $path.DIRECTORY_SEPARATOR.$file;
            }
            unset($files[$k]);
        }
        asort($files);
        return $files;
    }
}
