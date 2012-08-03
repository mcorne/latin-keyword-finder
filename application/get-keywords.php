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

define('MAX_KEYWORDS', 50);
define('VERSES_PER_KEYWORD', 10);

/**
 * Main function to get the keywords
 *
 * @param string $text_title the title of the text to process
 */
function exec_get_keywords($text_title)
{
    echo "getting keywords...\n";

    $headwords_count = read_csv(__DIR__ . "/../data/$text_title/generated/headwords-count.csv");
    $latin_text = read_csv(__DIR__ . "/../data/$text_title/source/latin.csv");
    $english_text = read_csv(__DIR__ . "/../data/$text_title/source/english.csv", INDEX_ROWS, 'number');
    list($keywords, $count) = get_keywords($headwords_count, $latin_text, $english_text);

    $has_content_changed = write_csv(__DIR__ . "/../data/$text_title/generated/keywords.csv", $keywords);
    echo $count . ' keywords ';
    echo $has_content_changed? '(content has changed)' : '(content has not changed)';
}

/**
 * Returns the keywords (the most frequent headwords)
 *
 * @param array $headwords_count the counts of headwords
 * @param array $latin_text      the lines of the Latin text
 * @param array $english_text    the lines of the English text
 * @param array $selected_pos    the parts of speeches to filter
 * @param int   $max_keywords    the maximum number of keywords to return
 * @return array                 the keywords
 */
function get_keywords($headwords_count, $latin_text, $english_text, $selected_pos = null, $max_keywords = null)
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

            $keywords += get_verses($headword_count, $latin_text, $english_text);
            $count++;
        }
    }

    return array($keywords, $count);
}

/**
 * Returs the Latin and English verse containing a headword
 *
 * @param array $headword_count  the headword count details
 * @param array $latin_text      the lines of the Latin text
 * @param array $english_text    the lines of the English text
 * @throws Exception
 * @return multitype:
 */
function get_verses($headword_count, $latin_text, $english_text)
{
    static $already_cited = array();

    // extracts the inflected words (lexeme) corresponding to the headword
    $words = str_replace(WORD_SEPARATOR, '|', $headword_count['words']);
    unset($headword_count['words']);

    $verses = array();

    foreach($latin_text as $row) {
        if (preg_match("~\b($words)\b~i", $row['verse'])) {
            // one of the inflected word is used in the Latin verse, captures the verse number
            $number = $row['number'];

            if (! isset($english_text[$number])) {
                throw new Exception("missing English verse $number");
            }

            if (! isset($already_cited[$number])) {
                // the verse is not already used for another headword
                // adds the verse number, and the Latin verse and the English verse
                $verses[$number] = $headword_count + array(
                    'number' => $number,
                    'verses' => sprintf("la : %s\nen: %s", $row['verse'], $english_text[$number]['verse']));
            }
        }
    }

    // keeps a limited number of verses
    $verses = slice_verses($verses);
    $already_cited += $verses;

    return $verses;
}

/**
 * Slices the verses
 *
 * Keeps a limited number of verses. Picks the verse accross the text.
 * (every N verses, N = number of verses / max number of verses)
 *
 * @param array $verses the verses
 * @return array        the selected verses
 */
function slice_verses($verses)
{
    $count = count($verses);
    $modulus = floor($count / VERSES_PER_KEYWORD);
    $sliced = array();

    foreach($verses as $number => $verse) {
        if ($modulus == 0 or $count-- % $modulus == 0) {
            $sliced[$number] = $verse;
        }
    }

    return array_slice($sliced, 0, VERSES_PER_KEYWORD, true);
}

// runs the main function if this is the file of the command being run
exec_if_command(__FILE__);
