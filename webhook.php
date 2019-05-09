<?php

// TEST TECHNIQUE PROBOT \\


/*

Il faut que tu conçoives un Chatbot Facebook Messenger avec un menu, qui, quand tu l’interroges, te permette de récupérer des résultats sportifs des matchs de foot de la ligue 1. 
Tu peux aller chercher les données où tu veux, mais des données à jour. 

Techno de base : Php, frameworks autorisés. 

Livrables : code source + url du bot fonctionnel

*/

// Utilisation de WebHooks via la platforme Application de Facebook Developper \\
//                        API -> https://apifootball.com                       \\ 


/************************ GESTION SU WEBHOOK ************************/

/*************** Les fonctions sont en bas du fichier ***************/

/* J'include l'API */
include 'api.php';



/* J'appel ma fonction qui permet de valider le Token et la bonne configuration du WebHook */
checkToken();



/*********** Traitement de reception et envoie de message ***********/

/* Ce code est dans la doc de Messenger Developper */
/*          Je l'ai adapté selon mes besoins       */

$input = json_decode(file_get_contents('php://input'), true);

if (isset($input['entry'][0]['messaging'][0]['sender']['id'])) {


    $sender = $input['entry'][0]['messaging'][0]['sender']['id'];
    $message = $input['entry'][0]['messaging'][0]['message']['text'];

    /* J'utilise $postback_ pour savoir quel choix l'utilisateur a fait */
    $postback_ = isset($input['entry'][0]['messaging'][0]['postback']['payload']) ? $input['entry'][0]['messaging'][0]['postback']['payload']: '' ;

    /* */
    $url = 'https://graph.facebook.com/v2.6/me/messages?access_token=EAAGCdLTQMjUBAP69gCkNE18oECIdRhoBbcHMPbYMkKbpVyJv1f2vzEKMoRloN76AAMHUrp3ocomr4W4sJixOsKLcnMIbcvjShKHZAkZALQr9Xk1VlZBp3GeSW6zZAtmmdBS7yJpOwdw9KZADFHxoZCiaAfHvkMeTS67SZAv4vHb2vG7Gzi40Iw5';

    /* Je declare $message_to_reply vide */
    $message_to_reply = '';

    /* La variable $TEAM contient la valeur du choix de l'utilisateur grace a la fonction checkTeam() */
    $TEAM = checkTeam($postback_);


    if (preg_match(".$TEAM.", $TEAM)) {

        /* Cette boucle me permet de parcourir le tableau de mon API ($tab) */ 
        for ($i = 0; $i <= count($tab); $i++) {

            /* Si la valeur de $TEAM est dans mon tableau de rempli $message_to_reply */
            if (in_array($TEAM, $tab[$i]) == true) {

                /* Je crée ma réponse en donnant le resultat du dernier match qu'a réalisé $TEAM */
                $message_to_reply = "Le dernier match " . $tab[$i]['awayTeamName'] . " etait contre " . $tab[$i]['homeTeamName'] . " avec un score de " . $tab[$i]['awayTeamScore'] . " : " . $tab[$i]['homeTeamScore'] . "";
            }
        }

    /* Cette condition n'est pas utile, mais je préfère la laisser en cas de problème */
    } else {
        $message_to_reply = 'Désolé je n\'ai pas compris votre requete';
    }
}


/******************************* JSON *******************************/

/***** Code fourni dans la documentation mais inutile dans ce programme *****/

/* Configuration de reponse */
$jsonData = '{
    "recipient":{
        "id":"' . $sender . '"
        },
        "message":{
            "text":"' . $message_to_reply . '" 
        }
    }';

/* J'initialise curl */
$ch = curl_init($url);

/* Configuration des options de curl pour l'envoie des reponses en POST JSON DATA */
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
if (!empty($message_to_reply)) {
    $result = curl_exec($ch);
}

/* Fonction qui permet de valider le Token et la bonne configuration du WebHook */
function checkToken()
{
/* Code fourni dans la documentation */
if (isset($_GET['hub_verify_token'])) {
    if ($_GET['hub_verify_token'] === 'helloprorobot') {
        echo $_GET['hub_challenge'];
        return;
    } else {
        echo 'Invalid Verify Token';
        return;
    }
}
}

/* Fonction qui permet de determiner quelle valeur contient $postback_ */
function checkTeam($postback_)
{
    /* Switch Case qui permet de return la valeur dans la variable $TEAM*/
    switch ($postback_) {

        case 'Marseille':
            $TEAM = 'Marseille';
            break;

        case 'Amiens':
            $TEAM = 'Amiens';
            break;

        case 'Bordeaux':
            $TEAM = 'Bordeaux';
            break;

        case 'Rennes':
            $TEAM = 'Rennes';
            break;

        case 'Dijon':
            $TEAM = 'Dijon';
            break;
    }
    return $TEAM;
}



/*
Je suis passé par mon invité de commande Git Bash car il contient le package CURL
J'ai donc configuré un bouton "get_started"


curl -X POST -H "Content-Type: application/json" -d '{ 
  "get_started":{
    "payload":"<GET_STARTED_PAYLOAD>"
  }
}' 
"https://graph.facebook.com/v2.6/me/messenger_profile?access_token=EAAGCdLTQMjUBAP69gCkNE18oECIdRhoBbcHMPbYMkKbpVyJv1f2vzEKMoRloN76AAMHUrp3ocomr4W4sJixOsKLcnMIbcvjShKHZAkZALQr9Xk1VlZBp3GeSW6zZAtmmdBS7yJpOwdw9KZADFHxoZCiaAfHvkMeTS67SZAv4vHb2vG7Gzi40Iw5"


Puis j'ai créé le Persitent Menu qui permet a l'utilisateur de faire son choix sur un volet d'équipe

//////// Je n'ai pas reussi a mettre plus de 5 postback en m'y penchant plus longtemps je pense que le probleme pourrait etre réglé \\\\\\\\

curl -X POST -H "Content-Type: application/json" -d '{
  "persistent_menu":[
    {
      "locale":"default",
      "composer_input_disabled": true,
      "call_to_actions":[
        {
          "title":"Choisir une equipe",
          "type":"nested",
          "call_to_actions":[
            {
              "title":"Marseille",
              "type":"postback",
              "payload":"Marseille"
            },
	    {
              "title":"Amiens",
              "type":"postback",
              "payload":"Amiens"
            },
	    {
              "title":"Bordeaux",
              "type":"postback",
              "payload":"Bordeaux"
            },
        {
              "title":"Rennes",
              "type":"postback",
              "payload":"Rennes"
            },
        {
              "title":"Dijon",
              "type":"postback",
              "payload":"Dijon"
            }
          ]
        }
      ]
    }
  ]
}' "https://graph.facebook.com/v2.6/me/messenger_profile?access_token=EAAGCdLTQMjUBAP69gCkNE18oECIdRhoBbcHMPbYMkKbpVyJv1f2vzEKMoRloN76AAMHUrp3ocomr4W4sJixOsKLcnMIbcvjShKHZAkZALQr9Xk1VlZBp3GeSW6zZAtmmdBS7yJpOwdw9KZADFHxoZCiaAfHvkMeTS67SZAv4vHb2vG7Gzi40Iw5"


*/