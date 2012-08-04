<?php
/**
 * Latin keyword finder
 *
 * Command to make the analysis template for the keywords
 *
 * @author    Michel Corne <mcorne@yahoo.com>
 * @copyright 2012 Michel Corne
 * @license   http://opensource.org/licenses/MIT MIT License
 */

require_once 'common.php';

/**
 * Main function to make the analysis template
 *
 * @param string $text_title         the title of the text to process
 * @param int    $verses_per_keyword maximum number of verses per keyword
 */
function exec_make_analysis_template($text_title, $verses_per_keyword = null)
{
    echo_command_title('making analysis template');

    $keywords = read_csv(__DIR__ . "/../data/$text_title/generated/keywords.csv");
    $latin_text = read_csv(__DIR__ . "/../data/$text_title/source/latin.csv");
    $english_text = read_csv(__DIR__ . "/../data/$text_title/source/english.csv", INDEX_ROWS, 'number');
    $analysis_template = make_analysis_template($keywords, $latin_text, $english_text, $verses_per_keyword);

    $has_content_changed = write_csv(__DIR__ . "/../data/$text_title/generated/analysis-template.csv", $analysis_template);

    echo count($analysis_template) . ' verses ';
    echo_has_content_changed($has_content_changed);
}

/**
 * Makes the analysis template
 *
 * @param array $keywords           the keywords
 * @param array $latin_text         the lines of the Latin text
 * @param array $english_text       the lines of the English text
 * @param int   $verses_per_keyword maximum number of verses per keyword
 * @return array                    the analysis template
 */
function make_analysis_template($keywords, $latin_text, $english_text, $verses_per_keyword = null)
{
    $analysis_template = array();

    foreach($keywords as $keyword) {
        $analysis_template += get_verses($keyword, $latin_text, $english_text, $verses_per_keyword);
    }

    return $analysis_template;
}

/**
 * Returns the Latin and English verses containing a keyword
 *
 * @param array $keyword            the keyword details
 * @param array $latin_text         the lines of the Latin text
 * @param array $english_text       the lines of the English text
 * @param int   $verses_per_keyword maximum number of verses per keyword
 * @throws Exception
 * @return                          the keyword and a set of Latin and English verses
 */
function get_verses($keyword, $latin_text, $english_text, $verses_per_keyword = null)
{
    static $already_cited = array();

    // extracts the inflected words (lexeme) corresponding to the keyword
    $words = str_replace(WORD_SEPARATOR, '|', $keyword['words']);
    unset($keyword['words']);

    $verses = array();

    foreach($latin_text as $row) {
        if (preg_match("~\b($words)\b~i", $row['verse'])) {
            // one of the inflected word is used in the Latin verse, captures the verse number
            $number = $row['number'];

            if (! isset($english_text[$number])) {
                throw new Exception("missing English verse $number");
            }

            if (! isset($already_cited[$number])) {
                // the verse is not already used for another keyword
                // adds the verse number, and the Latin verse and the English verse
                $verses[$number] = array(
                    'headword' => $keyword['headword'],
                    'type'     => null,
                    'comment'  => null,
                    'number'   => $number,
                    'verses'   => sprintf("la : %s\nen: %s", $row['verse'], $english_text[$number]['verse']));
            }
        }
    }

    // keeps a limited number of verses
    $verses = slice_verses($verses, $verses_per_keyword);
    $already_cited += $verses;

    return $verses;
}

/**
 * Slices the verses
 *
 * Keeps a limited number of verses. Picks the verse accross the text.
 * (every N verses, N = number of verses / max number of verses)
 *
 * @param array $verses             the verses
 * @param int   $verses_per_keyword maximum number of verses per keyword
 * @return array                    the selected verses
 */
function slice_verses($verses, $verses_per_keyword = null)
{
    if (is_null($verses_per_keyword)) {
        $verses_per_keyword = VERSES_PER_KEYWORD;
    }

    $count = count($verses);
    $modulus = floor($count / $verses_per_keyword);
    $sliced = array();

    foreach($verses as $number => $verse) {
        if ($modulus == 0 or $count-- % $modulus == 0) {
            $sliced[$number] = $verse;
        }
    }

    return array_slice($sliced, 0, $verses_per_keyword, true);
}
