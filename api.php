<?php

/**************************** API ****************************/

/* J'ai cherché une API qui pouvait me donner les les informations necessaire (Resultats de la Ligue 1)*/
$uri = 'https://apifootball.com/api/?action=get_events&from=2019-01-01&to=2019-12-31&league_id=127&APIkey=15d71d37cbf5eb83992ee2e160879af9de0e77af1337ae3f94ec6da08e76583d';
$reqPrefs['http']['method'] = 'GET';
$reqPrefs['http']['header'] = 'X-Auth-Token: 15d71d37cbf5eb83992ee2e160879af9de0e77af1337ae3f94ec6da08e76583d';
$stream_context = stream_context_create($reqPrefs);
$response = file_get_contents($uri, false, $stream_context);
$matches = json_decode($response, true);

/* Je declare un tableau vide */
$tab = [];

/* Je fais une boucle qui me permet de récupérer les infos dont j'ai besoin afin de les push dans mon tableau ci-dessus */
foreach($matches as $detailMatches)
{

    /* Tableau pour chaque tour de boucle */
    $detailMatchesTab = [
        'date'           => $detailMatches['match_date'],

        'homeTeamName'   => $detailMatches['match_hometeam_name'],
        'homeTeamScore'  => $detailMatches['match_hometeam_score'],

        'awayTeamName'   => $detailMatches['match_awayteam_name'],
        'awayTeamScore'  => $detailMatches['match_awayteam_score']
    ];

    /* L'API n'etant pas complete sur tous les matchs, j'ai préféré push uniquement les matchs qui avaient un score différent de null */ 
    if($detailMatchesTab['homeTeamScore'] != null || $detailMatchesTab['awayTeamScore'] != null)
    {
        /* Je push */
        array_push($tab, $detailMatchesTab);
    }
}