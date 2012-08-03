<?php
/**
 * Latin keyword finder
 *
 * List of words missing information (headword and part of speech) extracted from a Whitaker WORD.OUT formatted file
 *
 * @author    Michel Corne <mcorne@yahoo.com>
 * @copyright 2012 Michel Corne
 * @license   http://opensource.org/licenses/MIT MIT License
 */

$missing_info = array(
    'aliquis' => array('aliquis, aliqua, aliquid', 'PRON'),
    'ego'     => array('ego, mei',                 'PRON'),
    'nos'     => array('nos, nostri, nostrum',     'PRON'),
    'qui'     => array('qui, quae, quod',          'PRON'),
    'quis'    => array('quis, quae, quid',         'PRON'),
    'se'      => array('sui, sibi, se',            'PRON'),
    'tu'      => array('tu, tui',                  'PRON'),
    'vos'     => array('vos, vostri, vostrum',     'PRON'),
);

$missing_info += array(
    'aliquid' => $missing_info['aliquis'],
    'cui'     => $missing_info['qui'],
    'cuius'   => $missing_info['qui'],
    'me'      => $missing_info['ego'],
    'mei'     => $missing_info['ego'],
    'mihi'    => $missing_info['ego'],
    'nobis'   => $missing_info['nos'],
    'nostri'  => $missing_info['nos'],
    'nostrum' => $missing_info['nos'],
    'qua'     => $missing_info['qui'],
    'quae'    => $missing_info['qui'],
    'quam'    => $missing_info['qui'],
    'quem'    => $missing_info['qui'],
    'quid'    => $missing_info['quis'],
    'quo'     => $missing_info['qui'],
    'quod'    => $missing_info['qui'],
    'quorum'  => $missing_info['qui'],
    'quos'    => $missing_info['qui'],
    'sibi'    => $missing_info['se'],
    'sui'     => $missing_info['se'],
    'te'      => $missing_info['tu'],
    'tibi'    => $missing_info['tu'],
    'tui'     => $missing_info['tu'],
    'vestri'  => $missing_info['vos'],
    'vestrum' => $missing_info['vos'],
    'vobis'   => $missing_info['vos'],
);

return $missing_info;
