<?php
/**
 * Latin keyword finder
 *
 * Command to count the frequency of words in a text
 *
 * @author    Michel Corne <mcorne@yahoo.com>
 * @copyright 2012 Michel Corne
 * @license   http://opensource.org/licenses/MIT MIT License
 */

require_once 'common.php';

/**
 * Adds the occurrence count of a word
 *
 * @param string $word        the word
 * @param string $number      the verse number
 * @param array  $words_count the current list of words counts
 * @return                    the updated list of words counts,
 *                            each word has a count and a list of verse numbers
 */
function add_word_count($word, $number, $words_count)
{
    if (! isset($words_count[$word])) {
        $words_count[$word] = array('word' => $word, 'count' => 0, 'numbers' => null);
    }

    $words_count[$word]['count']++;

    if (empty($words_count[$word]['numbers'])) {
        // this is a new word, sets verse number
        $words_count[$word]['numbers'] = $number;

    } else if (strpos($words_count[$word]['numbers'], $number) === false) {
        // there is no verse number for this word yet, adds verse number
        $words_count[$word]['numbers'] .= ';' .  $number;
    }

    return $words_count;
}

/**
 * Counts the occurrences of each word
 *
 * @param array $rows the rows (lines) of the text
 * @throws Exception
 * @return array      the list of words counts, each word has a count and a list of verse numbers
 */
function count_words($rows)
{
    $words_count = array();

    foreach($rows as $row) {
        if (empty($row['verse'])) {
            // the verse is empty, ex. verse 5:4
            continue;
        }

        if (! preg_match_all('~[a-z]+~', $row['verse'], $matches)) {
            throw new Exception('cannot parse verse ' .  $row['number']);
        }

        $words = fix_words($matches[0]);

        foreach($words as $word) {
            $words_count = add_word_count($word, $row['number'], $words_count);
        }
    }

    return $words_count;
}

/**
 * Main function to count the words
 *
 * @param string $text_title the title of the text to process
 */
function exec_count_words($text_title)
{
    echo "counting words...\n";

    $rows = read_csv(__DIR__ . "/../data/$text_title/source/latin.csv", CONTENT_TO_LOWER);
    $words_count = count_words($rows);
    $words_count = sort_rows($words_count, 'count', SORT_DESC);
    $has_content_changed = write_csv(__DIR__ . "/../data/$text_title/generated/words-count.csv", $words_count);

    $words = get_words($words_count);
    write_file(__DIR__ . "/../data/$text_title/generated/words.txt", $words);

    echo count($words_count) . ' words ';
    echo $has_content_changed? '(content has changed)' : '(content has not changed)';
}

/**
 * Fixes words
 *
 * Splits compound words, ex. "mecum" = "me" + "cum"
 *
 * @param array $words the list of words
 * @return             the fixed list of words
 */
function fix_words($words)
{
    static $fixes;

    if (! isset($fixes)) {
        $fixes = require __DIR__ . '/whitaker/word-fixes.php';
    }

    $fixed = array();
    foreach($words as $word) {
        if (isset($fixes[$word])) {
            $fixed = array_merge($fixed, $fixes[$word]);
        } else {
            $fixed[] = $word;
        }
    }

    return $fixed;
}

/**
 * Returns the list of words from the list of words counts
 *
 * @param array $words_count the list of words counts
 * @return                   the list of words
 */
function get_words($words_count)
{
    $words = array_keys($words_count);
    sort($words);

    return $words;
}

// runs the main function if this is the file of the command being run
exec_if_command(__FILE__);
