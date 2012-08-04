<?php
/**
 * Latin keyword finder
 *
 * Command line to find the keywords (most frequent headwords) in a Latin text
 *
 * @author    Michel Corne <mcorne@yahoo.com>
 * @copyright 2012 Michel Corne
 * @license   http://opensource.org/licenses/MIT MIT License
 */

require_once 'common.php';

define('OPTION_A', '-W -i -l -L -k -y');

/**
 * The command help
 */
$help =
'Usage:
-a        Options: %1$s.
-d        Display the title of the available texts.
-l        Get the headwords (lemma) of a text.
-L        Count the headwords (lemma) of a text.
-i        Get the words information.
-k        Get the keywords of a text.
-m        Maximum number of keywords to return, default: %2$s.
          Used with option -k only.
-p        Selected parts of speech, default: %3$s.
          Available: ADJ,ADV,CONJ,INTERJ,N,NUM,PACK,PREFIX,
                     PREP,PRON,SUFFIX,SUPINE,TACKON,V,VPAR.
          Used with option -k only.
-t title  The title of the text to process.
-v        Maximum number of verses per keyword to return, default: %4$s.
          Used with option -y only.
-W        Count the words of a text.
-y        Make the analysis template for the keywords.

Example:
findlatinkeywords -a -t gospel-of-john
';

/**
 * Returns the text titles
 *
 * A text title is the name of the sub-directory in the "data" directory containing the files related to that text.
 *
 * @return string the list of text titles
 */
function get_text_titles()
{
    $text_directories = glob(__DIR__ . '/../data/*', GLOB_ONLYDIR);

    return array_map('basename', $text_directories);
}

try {
    if (! $options = getopt("hadlLikm:p:t:v:Wy")) {
        throw new Exception('invalid or missing option(s)');
    }

    if (isset($options['h'])) {
        // displays the command usage (help)
        exit(sprintf($help, OPTION_A, MAX_KEYWORDS, DEFAULT_POS, VERSES_PER_KEYWORD));
    }

    $text_titles = get_text_titles();
    if (isset($options['d'])) {
        // displays the title of the available texts
        exit(implode("\n", $text_titles));
    }

    if (! isset($options['t'])) {
        // the text title is missing
        throw new Exception('missing text title');
    }
    $text_title = $options['t'];
    unset($options['t']);

    if (! in_array($text_title, $text_titles)) {
        throw new Exception('invalid text title');
    }

    if (isset($options['a'])) {
        // this is the (combined) option A, adds the options
        preg_match_all('~\w~', (string)OPTION_A, $matches);
        $options += array_fill_keys($matches[0], false);
        unset($options['a']);
    }

    foreach(array_keys($options) as $option) {
        switch($option) {
            case 'l':
                require_once 'get-headwords.php';
                exec_get_headwords($text_title);
                break;

            case 'L':
                require_once 'count-headwords.php';
                exec_count_headwords($text_title);
                break;

            case 'i':
                require_once 'get-words-info.php';
                exec_get_words_info($text_title);
                break;

            case 'k':
                require_once 'get-keywords.php';
                $selected_pos = isset($options['p'])? $options['p'] : null;
                $max_keywords = isset($options['m'])? $options['m'] : null;
                exec_get_keywords($text_title, $selected_pos, $max_keywords);
                break;

            case 'W':
                require_once 'count-words.php';
                exec_count_words($text_title);
                break;

            case 'y':
                require_once 'make-analysis-template.php';
                $verses_per_keyword = isset($options['v'])? $options['v'] : null;
                exec_make_analysis_template($text_title, $verses_per_keyword);
                break;
        }
    }

} catch(Exception $e) {
    echo($e->getMessage());
}
