<?php
/**
 * Latin keyword finder
 *
 * List of words returned as "unknown" in a Whitaker WORD.OUT formatted file
 *
 * @author    Michel Corne <mcorne@yahoo.com>
 * @copyright 2012 Michel Corne
 * @license   http://opensource.org/licenses/MIT MIT License
 */

$unknow_words = array(
    'Abraham'      => array('Abraham, Abrahae', 'N', 'Abraham'),
    'Annam'        => array('Annas, Annae', 'N', 'Annas, father-in-law of Caiaphas'),
    'Arimathaea'   => array('Arimathaea (Arimathia), Arimathaeae', 'N', 'Arimathea, a city in Palestine'),
    'Barabbas'     => array('Barabbas, Barabbae', 'N', 'Barabbas'),
    'Bethania'     => array('Bethania, Bethaniae', 'N', 'Bethany, a city in Judea'),
    'Bethlehem'    => array('Bethlehem', 'N', 'Bethlehem, a city in Judea'),
    'Bethsaida'    => array('Bethsaida, Bethsaidae', 'N', 'Bethsaida, a city in Galilee'),
    'Bethsatha'    => array('Bethsatha, Bethsathae', 'N', 'Bethesda, a pool of water in Jerusalem'),
    'Caiphas'      => array('Caiphas, Caiphae', 'N', 'Caiaphas, the Roman-appointed Jewish high priest at the time'),
    'Capharnaum'   => array('Capharnaum, Capharnai', 'N', 'Capernaum, a city in Galilee'),
    'Cedron'       => array('Cedron', 'N', 'Kidron Valley, near Jerusalem'),
    'Cleopae'      => array('Cleopas, Cleopae', 'N', "Clopas, the husband of Mary, Jesus' mother's sister"),
    'Elias'        => array('Elias, Eliae', 'N', 'Elijah or Elias, a Hebrew prophet'),
    'Enon'         => array('Enon', 'N', 'Aenon or Enon, a place where John the Baptist performed baptisms in the River Jordan'),
    'Ephraim'      => array('Ephraim', 'N', 'Ephraim, a city in Israel'),
    'Gabbatha'     => array('Gabbatha', 'N', 'Gabbatha, a place in Jerusalem, where Pilate had his judicial seat'),
    'gazophylacio' => array('gazophylacium, gazophylacii', 'N', 'treasure room'),
    'Golgotha'     => array('Golgotha', 'N', 'Golgotha, a site outside of Jerusalem where Jesus was crucified'),
    'haurierant'   => array('haurio, haurire, hausi, haustus', 'V', 'draw up/out'), // unidentified conjugation by whitaker
    'inconsutilis' => array('inconsutilis, inconsutilis, inconsutile', 'ADJ', 'seamless'),
    'Iordanem'     => array('Iordanes, Iordanis', 'N', 'the Jordan river, in West Asia flowing to the Dead Sea'),
    'Ioseph'       => array('Ioseph', 'N', 'Joseph, the husband of Mary, mother of Jesus'),
    'Isaias'       => array('Isaias, Isaiae', 'N', 'a prophet'),
    'Iscariotes'   => array('Iscariotes, Iscariotae', 'N', 'Iscariot, refers to Judas'),
    'Lazarus'      => array('Lazarus, Lazari', 'N', 'Lazarus of Bethany, raised by Jesus from the dead'),
    'Magdalene'    => array('Magdalene, Magdalenes', 'N', "Mary Magdalene, one of Jesus' disciples"),
    'Malchus'      => array('Malchus', 'N', 'Malchus, the slave of a high priest'),
    'Martha'       => array('Martha, Marthae', 'N', "Martha of Bethany, Lazarus' sister"),
    'Messias'      => array('Messias, Messiae', 'N', 'the Messiah'),
    'natatoria'    => array('natatoria, natatoriae', 'N', 'pool, swimming pool'),
    'Nathanael'    => array('Nathanael', 'N', 'Nathanael,  one of the Twelve Apostles aka Bartholomew'),
    'Nicodemus'    => array('Nicodemus', 'N', 'Nicodemus,  a Pharisee and a member of the Sanhedrin'),
    'parascevem'   => array('parasceve, parasceves', 'N', 'day of preparation, day before the Sabbath'), // unidentified inflection by whitaker
    'pharisaei'    => array('pharisaei, pharisaeorum', 'N', 'Pharisees, a school of thought among Jews'),
    'probatica'    => array('probaticus, probatica, probaticum', 'ADJ', 'related to cattle'),
    'quatriduanus' => array('quatriduanus, quatriduana, quatriduanum', 'ADJ', 'period of four days'),
    'rabbuni'      => array('rabbuni', 'N', 'rabbouni, master'),
    'Salim'        => array('Salim', 'N', 'Salim, an uncertain site in the upper Jordan valley or in Samaria'),
    'Sichar'       => array('Sichar', 'N', 'Sychar or Shechem, a Canaanite city'),
    'Siloa'        => array('Siloa, Siloae', 'N', 'Siloam, an ancient site in Jerusalem'),
    'Simon'        => array('Simon, Simonis', 'N', 'Simon'),
    'Sion'         => array('Sion', 'N', 'Zion, a place name often used as a synonym for Jerusalem'),
    'temet'        => array('tumet, tuimet', 'PRON', 'yourself (sing.)'),
    'Thomas'       => array('Thomas, Thomae', 'N', 'Thomas'),
    'Tiberias'     => array('Tiberias, Tiberiadis', 'N', 'Tiberias, a city in Galilee'),
    'vobismet'     => array('vosmet, vostrimet', 'PRON', 'yourself (plur.)'),
    'Zebedaei'     => array('Zebedaeus, Zebedaei', 'N', 'Zebedee, father of James and John, two disciples of Jesus'),
);

$unknow_words += array(
    'Abrahae'    => $unknow_words['Abraham'],
    'Barabbam'   => $unknow_words['Barabbas'],
    'Bethaniam'  => $unknow_words['Bethania'],
    'Caipha'     => $unknow_words['Caiphas'],
    'Caiphae'    => $unknow_words['Caiphas'],
    'Caipham'    => $unknow_words['Caiphas'],
    'Isaiae'     => $unknow_words['Isaias'],
    'Iscariotis' => $unknow_words['Iscariotes'],
    'Lazare'     => $unknow_words['Lazarus'],
    'Lazarum'    => $unknow_words['Lazarus'],
    'Marthae'    => $unknow_words['Martha'],
    'Martham'    => $unknow_words['Martha'],
    'Messiam'    => $unknow_words['Messias'],
    'pharisaeis' => $unknow_words['pharisaei'],
    'pharisaeos' => $unknow_words['pharisaei'],
    'Siloae'     => $unknow_words['Siloa'],
    'Siloam'     => $unknow_words['Siloa'],
    'Simonem'    => $unknow_words['Simon'],
    'Simoni'     => $unknow_words['Simon'],
    'Simonis'    => $unknow_words['Simon'],
    'Thomae'     => $unknow_words['Thomas'],
    'Tiberiade'  => $unknow_words['Tiberias'],
    'Tiberiadis' => $unknow_words['Tiberias'],
);

return $unknow_words;
