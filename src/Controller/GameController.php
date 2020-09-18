<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class GameController extends AbstractController
{
    /**
     * @Route("/games", name="gamesList")
     */
    public function gamesList()
    {
        return $this->json(
            [
                [
                    "id" => 1,
                    "title" => "Assassin's Creed Valhalla",
                    "releaseDate" => "2020-11-10",
                    "studio" => "Ubisoft",
                    "category" => "Action,Aventure",
                    "poster" => "poster-ascv.png",
                    "comments" => [
                        [ "id" => 1 , "addDate" => "2020-09-08" , "author" => "Romain" , "text" => "Vivement la sortie !" ],
                        [ "id" => 2 , "addDate" => "2020-09-02" , "author" => "Romain" , "text" => "Super impatient !!!" ]
                    ]
                ],
                [
                    "id" => 2,
                    "title" => "Total War : Three Kingdoms",
                    "releaseDate" => "2019-05-23",
                    "studio" => "Creative Assembly",
                    "category" => "Stratégie",
                    "poster" => "poster-tw3k.png",
                    "comments" => []
                ],
                [
                    "id" => 3,
                    "title" => "Detroit : Become Human",
                    "releaseDate" => "2019-12-12",
                    "studio" => "Quantic Dream",
                    "category" => "Aventure",
                    "poster" => "poster-dbh.png",
                    "comments" => []
                ],
                [
                    "id" => 4,
                    "title" => "Star Wars : Squadrons",
                    "releaseDate" => "2020-10-02",
                    "studio" => "Motive Studios,Electronic Arts",
                    "category" => "Shooter",
                    "poster" => "poster-sws.png",
                    "comments" => []
                ],
                [
                    "id" => 5,
                    "title" => "Watch Dogs Legion",
                    "releaseDate" => "2020-10-29",
                    "studio" => "Ubisoft",
                    "category" => "Action,Aventure",
                    "poster" => "", //poster-wdl.png
                    "comments" => []
                ]
            ]
        );
    }

    /**
     * @Route("/game/", name="gameView")
     */
    public function gameView()
    {
        return $this->json(
            [
                "id" => 1,
                "title" => "Assassin's Creed Valhalla",
                "releaseDate" => "2020-11-10",
                "studio" => "Ubisoft",
                "category" => "Aventure",
                "poster" => "poster-ascv.png",
                "comments" => [
                    [ "id" => 1 , "addDate" => "2020-09-08" , "author" => "Romain" , "text" => "Vivement la sortie !" ],
                    [ "id" => 2 , "addDate" => "2020-09-02" , "author" => "Romain" , "text" => "Super impatient !!!" ]
                ]
            ]
        );
    }

}
