<?php
/**
 * Latin keyword finder
 *
 * Command to find the keywords (most frequent headwords) in a text
 *
 * @author    Michel Corne <mcorne@yahoo.com>
 * @copyright 2012 Michel Corne
 * @license   http://opensource.org/licenses/MIT MIT License
 */

require_once 'common.php';
require_once 'count-headwords.php';
require_once 'count-words.php';
require_once 'get-headwords.php';
require_once 'get-keywords.php';
require_once 'get-words-info.php';

/**
 * Main function to find keywords
 *
 * @param string $text_title the title of the text to process
 */
function exec_find_keywords($text_title)
{
    exec_count_words($text_title);
    echo "\n";
    exec_get_words_info($text_title);
    echo "\n";
    exec_get_headwords($text_title);
    echo "\n";
    exec_count_headwords($text_title);
    echo "\n";
    exec_get_keywords($text_title);
}

// runs the main function if this is the file of the command being run
exec_if_command(__FILE__);
