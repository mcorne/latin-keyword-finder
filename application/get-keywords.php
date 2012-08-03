<?php
/**
 * Latin keyword finder
 *
 * Command to get the keywords of a text
 *
 * Ex. php get-keywords.php gospel-of-john
 *
 * @author    Michel Corne <mcorne@yahoo.com>
 * @copyright 2012 Michel Corne
 * @license   http://opensource.org/licenses/MIT MIT License
 */

require_once 'common.php';

define('MAX_KEYWORDS', 50);

/**
 * Main function to get the keywords
 *
 * @param string $text_title the title of the text to process
 */
function exec_get_keywords($text_title)
{
    echo "getting keywords...\n";

    $headwords_count = read_csv(__DIR__ . "/../data/$text_title/generated/headwords-count.csv");
    list($keywords, $count) = get_keywords($headwords_count);
    $has_content_changed = write_csv(__DIR__ . "/../data/$text_title/generated/keywords.csv", $keywords);

    echo $count . ' keywords ';
    echo $has_content_changed? '(content has changed)' : '(content has not changed)';
}

/**
 * Returns the keywords (the most frequent headwords)
 *
 * @param array $headwords_count the counts of headwords
 * @param array $selected_pos    the parts of speeches to filter
 * @param int   $max_keywords    the maximum number of keywords to return
 * @return array                 the keywords
 */
function get_keywords($headwords_count, $selected_pos = null, $max_keywords = null)
{
    if (is_null($selected_pos)) {
        $selected_pos = array('ADJ', 'N', 'V');
    }

    if (is_null($max_keywords)) {
        $max_keywords = MAX_KEYWORDS;
    }

    $keywords = array();
    $count = 0;

    foreach($headwords_count as $headword_count) {
        if (in_array($headword_count['pos'], $selected_pos)) {
            if ($max_keywords-- == 0) {
                break;
            }

            $count++;
            $keywords[] = array('#' => $count) + $headword_count;
        }
    }

    return array($keywords, $count);
}

// runs the main function if this is the file of the command being run
exec_if_command(__FILE__);
