<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\GameRepository;

class GameController extends AbstractController
{
    /**
     * @Route("/games", name="gamesList")
     */
    public function gamesList( GameRepository $GameRepository ) : Response
    {
        return $this->json( $GameRepository->findAll() );
    }

    /**
     * @Route("/game/{gameId}", name="gameView")
     */
    public function gameView( int $gameId , GameRepository $GameRepository ) : Response
    {
        //$test = $GameRepository->find($gameId);
        //dd($test->getStudios());
        return $this->json( $GameRepository->find($gameId) );
    }

}
