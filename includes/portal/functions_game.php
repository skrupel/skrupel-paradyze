<?php
function get_goal_info($id) {
    
    $goals = array(
        array(
            'name' => 'Just for Fun',
            'desc' => 'Es wird gespielt, bis die Runde langweilig wird.'
        ),
        array(
            'name' => '&Uuml;berleben',
            'desc' => 'Spiel endet, sobald nur noch %d Spieler existiert/existieren.'
        ),
        array(
            'name' => 'Todfeind',
            'desc' => 'Jeder Spieler erh&auml;lt einen Todfeind, den es zu vernichten gilt.'
        ),
        array(
            'name' => 'Dominanz',
            'desc' => ''
        ),
        array(
            'name' => 'King of the Planet',
            'desc' => ''
        ),
        array(
            'name' => 'Spice',
            'desc' => '{1} KT Vormissan m&uuml;ssen im freien Raum an Board der eigenen Flotte gesichert werden'
        ),
        array(
            'name' => 'Team Todfeind',
            'desc' => 'Jedes Team aus 2 Spielern erh&auml;lt 2 Todfeinde, welche es zu vernichten gilt'
        ),
    );
    
    return $goals[$id];
}
