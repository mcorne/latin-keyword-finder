<?php
/**
 * Latin keyword finder
 *
 * Command to get the keywords of a text
 *
 * @author    Michel Corne <mcorne@yahoo.com>
 * @copyright 2012 Michel Corne
 * @license   http://opensource.org/licenses/MIT MIT License
 */

require_once 'common.php';

/**
 * Main function to get the keywords
 *
 * @param string $text_title   the title of the text to process
 * @param array  $selected_pos the parts of speeches to filter
 * @param int    $max_keywords the maximum number of keywords to return
 */
function exec_get_keywords($text_title, $selected_pos = null, $max_keywords = null)
{
    echo_command_title('getting keywords');

    $headwords_count = read_csv(__DIR__ . "/../data/$text_title/generated/headwords-count.csv");
    list($keywords, $count) = get_keywords($headwords_count, $selected_pos, $max_keywords);
    $has_content_changed = write_csv(__DIR__ . "/../data/$text_title/generated/keywords.csv", $keywords);

    echo $count . ' keywords ';
    echo_has_content_changed($has_content_changed);
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
        $selected_pos = DEFAULT_POS;
    }

    if (! is_array($selected_pos)) {
        $selected_pos = explode(',', $selected_pos);
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
