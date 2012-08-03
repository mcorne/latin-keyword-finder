<?php
/**
 * Latin keyword finder
 *
 * Command to count the frequency of headwords in a text
 *
 * Ex. php count-headwords.php gospel-of-john
 *
 * @author    Michel Corne <mcorne@yahoo.com>
 * @copyright 2012 Michel Corne
 * @license   http://opensource.org/licenses/MIT MIT License
 */

require_once 'common.php';

/**
 * Calculates the ratio (percentage) of the "count" row cell
 *
 * @param array $rows the array of rows
 * @param int $total  the total to calculate the ratio against
 * @return array      the array of rows updated with each ratio
 */
function calculate_ratio($rows, $total)
{
    foreach($rows as &$row){
        $ratio = round($row['count'] / $total * 100, 1);
        $row['%'] = sprintf('%01.1f', $ratio);
    }

    return $rows;
}

/**
 * Calculates the total of all row "count" cell
 *
 * @param array $rows the array of rows
 * @return int        the total
 */
function calculate_total($rows)
{
    $total = 0;

    foreach($rows as $row){
        $total += $row['count'];
    }

    return $total;
}

/**
 * Counts the occurrences of each headword
 *
 * Adds the count of the words (lexeme) based on a given headword.
 *
 * @param array $words_count the words counts
 * @param array $headwords   the list of headwords
 * @throws Exception
 * @return array             the list of headwords with their part of speech, count, ratio, and word list
 */
function count_headwords($words_count, $headwords)
{
    $headwords_count = array();

    foreach($words_count as $word => $word_count) {
        if (! isset($headwords[$word])) {
            throw new Exception("missing headword for $word");
        }

        $headword = $headwords[$word];
        $lemma = $headword['headword'];

        if (! isset($headwords_count[$lemma])) {
            $headwords_count[$lemma] = array(
                'headword'  => $lemma,
                'pos'       => $headword['pos'],
                'count'     => 0,
                '%'         => null,
                'words'     => '',
            );
        }

        $headwords_count[$lemma]['count'] += $word_count['count'];

        if (! empty($headwords_count[$lemma]['words'])) {
            // there is a word list started, adds word separator
            $headwords_count[$lemma]['words'] .= WORD_SEPARATOR;
        }

        $headwords_count[$lemma]['words'] .= $word;
    }

    return $headwords_count;
}

/**
 * Main function to count the headwords
 *
 * @param string $text_title the title of the text to process
 * @throws Exception
 */
function exec_count_headwords($text_title)
{
    echo "counting headwords...\n";

    $words_count = read_csv(__DIR__ . "/../data/$text_title/generated/words-count.csv", INDEX_ROWS);
    $headwords = read_csv(__DIR__ . "/../data/$text_title/generated/headwords.csv", INDEX_ROWS);

    $headwords_count = count_headwords($words_count, $headwords);
    $headwords_total = calculate_total($headwords_count);
    $words_total = calculate_total($words_count);

    if ($headwords_total != $words_total) {
        throw new Exception("headwords total ($headwords_total) different from words total ($words_total)");
    }

    $headwords_count = calculate_ratio($headwords_count, $headwords_total);
    $headwords_count = sort_rows($headwords_count, 'count', SORT_DESC);

    $has_content_changed = write_csv(__DIR__ . "/../data/$text_title/generated/headwords-count.csv", $headwords_count);
    echo count($headwords_count) . ' headwords ';
    echo $has_content_changed? '(content has changed)' : '(content has not changed)';
}

// runs the main function if this is the file of the command being run
exec_if_command(__FILE__);
