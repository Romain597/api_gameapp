<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\StudioRepository;

class StudioController extends AbstractController
{
    /**
     * @Route("/studios", name="studiosList")
     */
    public function studiosList( StudioRepository $studioRepository ) : Response
    {
        /*$pageCount = intval($gameRepository->countAllGame(), 10);
        $page = $request->query->get("page",1);
        $limit = $this->gamesListPageLimitNb;
        $offset = ($limit * $page) - $limit;
        $pageMax =  ceil($pageCount / $limit);
        if( $page > 0 && $page <= $pageMax ) {
            $games = $gameRepository->findBy(
                [],
                [],
                $limit,
                $offset
            );
        } else {
            $games = $gameRepository->findAll();
        }
        if( gettype($games) !== "array" ) {
            $games = [];
        }
        return $this->json( $games );*/
        return $this->json( $studioRepository->findAll() );
    }

    /**
     * @Route("/studios-by-letter", name="studiosByLetter")
     */
    public function getStudiosByLetter( StudioRepository $studioRepository , Request $request ) : Response
    {
        $studios = [];

        $letterString = $request->query->get("letter",null);

        if( $letterString !== null && is_string( $letterString ) === true && !is_numeric( $letterString ) === true && strlen($letterString) === 1 ) {
            $letterString = strtolower($letterString);
            $studios = $studioRepository->findByFirstLetterName(
                $letterString,
                "name",
                "ASC"
            );
        }

        if( gettype($studios) !== "array" ) {
            $studios = [];
        }

        $jsonReady = [];

        if( count($studios) > 0 ) {

            foreach( $studios as $studio ) {
                
                $studioData = $studio->jsonSerialize();
                $studioData = $studioData + ["games"=>[]];
                $games = $studio->getGames()->toArray();

                foreach( $games as $game ) {
                    $gameData = $game->jsonSerialize();
                    unset($gameData["comments"]);
                    $studioData["games"][] = $gameData;
                }

                $jsonReady[] = $studioData;

            }

        }

        return $this->json( $jsonReady );
    }

    /**
     * @Route("/studios-games-count-by-letter", name="studiosGamesCountByLetter")
     */
    public function getStudiosGamesCountByLetter( StudioRepository $studioRepository ) : Response
    {
        $datas = $studioRepository->findAlphabeticListOfStudiosWithGamesRelatedCounter();
        return $this->json( $datas );
    }

}
