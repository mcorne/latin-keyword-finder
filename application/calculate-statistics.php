<?php
/**
 * Latin keyword finder
 *
 * Command to calculate the statistics of a text
 *
 * @author    Michel Corne <mcorne@yahoo.com>
 * @copyright 2012 Michel Corne
 * @license   http://opensource.org/licenses/MIT MIT License
 */

require_once 'common.php';

/**
 * Calculates the number of unique words and the total number of words in a file
 *
 * @param string $word_type       the type of word
 * @param string $filename        the file name
 * @param bool   $calculate_total calculates the total if true, not applicable otherwise
 * @return array                  the number of unique words and the total number of words
 */
function calculate_statistics($word_type, $filename, $calculate_total)
{
    $rows = read_csv($filename);

    return array(
        'type'   => $word_type,
        'unique' => count($rows),
        'all'    => $calculate_total? calculate_total($rows) : 'n/a',
    );
}

/**
 * Main function to calculate the statistics of a text
 *
 * @param string $text_title the title of the text to process
 */
function exec_calculate_statistics($text_title)
{
    echo_command_title('calculate the statistics');

    $latin_text = read_csv(__DIR__ . "/../data/$text_title/source/latin.csv");

    $statistics = array(
        calculate_statistics('words',     __DIR__ . "/../data/$text_title/generated/words-count.csv", true),
        calculate_statistics('headwords', __DIR__ . "/../data/$text_title/generated/headwords-count.csv", true),
        calculate_statistics('keywords',  __DIR__ . "/../data/$text_title/generated/keywords.csv", true),
        calculate_statistics('verses',    __DIR__ . "/../data/$text_title/source/latin.csv", false),
    );

    $has_content_changed = write_csv(__DIR__ . "/../data/$text_title/generated/statistics.csv", $statistics);

    echo count($statistics) . ' files ';
    echo_has_content_changed($has_content_changed);
}
