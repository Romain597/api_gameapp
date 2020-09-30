<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\GameRepository;
use App\Repository\CategoryRepository;
use App\Repository\StudioRepository;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Comment;
use App\Entity\Game;

class GameController extends AbstractController
{
    /**
     * @var int Limit number of games display in a page
     */
    private $gamesListPageLimitNb = 6;

    /**
     * @Route("/game/comment", name="commentAdd")
    */
    public function createComment( ValidatorInterface $validator , GameRepository $gameRepository , Request $request ) : Response
    {
        // get request parameters
        $commentGameId = $request->request->get("gameId",null);
        $commentAuthor = $request->request->get("author",null);
        $commentPublishedDate = $request->request->get("publishedDate",null);
        $commentText = $request->request->get("comment",null);
        // create a new object Comment
        $comment = new Comment();
        // comment author
        $commentAuthor = filter_var($commentAuthor, FILTER_SANITIZE_STRING);
        if( !empty($commentAuthor) === true && trim($commentAuthor) != "" ) {
            $comment->setAuthor(trim($commentAuthor));
        }
        // comment add date
        if( preg_match('/^2\d{3}\_[0-1]*\d\_[0-3]*\d\_[0-2]*\d\_[0-5]*\d\_[0-5]*\d$/',$commentPublishedDate) === 1 ) {
            $publishedDateArray = explode("_",$commentPublishedDate);
            $publishedDate = new \DateTime("now", new \DateTimeZone("UTC"));
            if( intval($publishedDateArray[1], 10) <= 12 ) {
                $dayOfPublishedDate = cal_days_in_month(CAL_GREGORIAN, intval($publishedDateArray[1], 10), intval($publishedDateArray[0], 10));
                if( intval($publishedDateArray[2], 10) <= $dayOfPublishedDate ) {
                    $publishedDate->setDate(intval($publishedDateArray[0], 10), intval($publishedDateArray[1], 10), intval($publishedDateArray[2], 10));
                    if( intval($publishedDateArray[3], 10) <= 23 && intval($publishedDateArray[4], 10) <= 59 && intval($publishedDateArray[5], 10) <= 59 ) {
                        $publishedDate->setTime(intval($publishedDateArray[3], 10), intval($publishedDateArray[4], 10), intval($publishedDateArray[5], 10));
                        $comment->setPublishedAt($publishedDate);
                    }
                }
            }
        }
        // comment text
        $commentText = filter_var($commentText, FILTER_SANITIZE_STRING);
        if( !empty($commentText) === true && trim($commentText) != "" ) {
            $comment->setText(trim($commentText));
        }
        // game related
        $commentGame = null;
        $commentGameId = filter_var($commentGameId, FILTER_VALIDATE_INT, array('options' => array( 'min_range' => 0 )));
        if( $commentGameId !== false ) {
            $commentGame = $gameRepository->find($commentGameId);
            if( ($commentGame instanceof \Game) === true ) {
                $comment->setGame($commentGame);
            }
        }
        // validation
        $errors = $validator->validate($comment);
        if(count($errors) > 0) {
            // errors in validation
            // return bad request response
            return new Response((string) $errors, 400);
        }
        else {
            // validation ok
            if( ($commentGame instanceof Game) === true ) {
                $commentGame->addComment($comment);
            }
            // create entity manager ( ou via param EntityManagerInterface $entityManager )
            $entityManager = $this->getDoctrine()->getManager();
            // tell Doctrine you want to (eventually) save the Comment (no queries yet)
            $entityManager->persist($comment);
            // actually executes the queries (i.e. the INSERT query)
            $entityManager->flush();
            // return success response
            return new Response('Success : Saved the new comment.', 200);
            //return new Response('Success : Saved new comment with the identification number <'.$comment->getId().'>.', 200);
        }
    }

    /**
     * @Route("/game/{gameId}", name="gameView")
     */
    public function gameView(GameRepository $gameRepository , int $gameId ) : Response
    {
        /*$test = $gameRepository->find($gameId);
        dd($test->getStudios());*/
        /*$game = $gameRepository->find($gameId);
        if( ($game instanceof Game) === false ) {
            $game = "";
        }
        return $this->json( $game );*/
        return $this->json( $gameRepository->find($gameId) );

    }

    /**
     * @Route("/games/count", name="gamesCount")
     */
    public function gamesCount(GameRepository $gameRepository ) : Response
    {
        
        $count = intval($gameRepository->countAllGame(), 10);

        return new Response($count);
        //return $this->json( $count );
    }

    /**
     * @Route("/games/by-categories", name="gamesByCategories")
     */
    public function gamesByCategories( GameRepository $gameRepository , CategoryRepository $categoryRepository , Request $request ) : Response
    {
        /*$categoryId = 1;
        $sortField = 'name';
        $order = "ASC";
        $page = 1;*/
        $categoryId = $request->query->get("category",null);
        $category = $categoryRepository->find($categoryId);
        $fieldParam = $request->query->get("field","");
        $sortField = '';
        switch($fieldParam) {
            case 'released':
                $sortField = 'releasedAt';
            break;
            case 'name':
                $sortField = 'name';
            break;
        }
        $order = mb_strtoupper($request->query->get("order","ASC"));
        $page = $request->query->get("page",1);
        // sort
        $allCategoryGames = $category->getGames()->toArray();
        $methodName = 'get'.ucfirst($sortField);
        $result = usort($allCategoryGames, function($a, $b) {
            global $order;
            if( $order == "ASC" ) {
                if( $a->$methodName() === $a->$methodName()  ) {
                    return 0;
                }
                return ( $a->$methodName() < $a->$methodName() ) ? -1 : 1;
            } else if( $order == "DESC" ) {
                if( $a->$methodName() === $a->$methodName()  ) {
                    return 0;
                }
                return ( $a->$methodName() > $a->$methodName() ) ? -1 : 1;
            }
        });
        $gameCount = count($allCategoryGames);
        $limit = $this->gamesListPageLimitNb;
        $offsetBegin = ($limit * $page) - $limit;
        $pageMax =  ceil($gameCount / $limit);
        $offsetEnd = ( ( $offsetBegin + $limit - 1 ) < $gameCount ) ? ( $offsetBegin + $limit - 1 ) : $gameCount ;
        if( $page > 0 && $page <= $pageMax ) {
            $games = [];
            for( $i = $offsetBegin; $i <= $offsetEnd; $i++ ) {
                if( isset( $allCategoryGames[$i] ) === true ) {
                    $games[] = $allCategoryGames[$i];
                }
            }

        } else {
            $games = $allCategoryGames;
        }

        if( gettype($games) !== "array" ) {
            $games = [];
        }

        return $this->json( $games );
    }

    /**
     * @Route("/games/by-studios", name="gamesByStudios")
     */
    public function gamesByStudios( GameRepository $gameRepository , StudioRepository $studioRepository , Request $request ) : Response
    {
        /*$studioId = 1;
        $sortField = 'name';
        $order = "ASC";
        $page = 1;*/
        $studioId = $request->query->get("studio",null);
        $studio = $studioRepository->find($studioId);
        $fieldParam = $request->query->get("field","");
        $sortField = '';
        switch($fieldParam) {
            case 'released':
                $sortField = 'releasedAt';
            break;
            case 'name':
                $sortField = 'name';
            break;
        }
        $order = mb_strtoupper($request->query->get("order","ASC"));
        $page = $request->query->get("page",1);
        // sort
        $allStudioGames = $studio->getGames()->toArray();
        $methodName = 'get'.ucfirst($sortField);
        $result = usort($allStudioGames, function($a, $b) {
            global $order;
            if( $order == "ASC" ) {
                if( $a->$methodName() === $a->$methodName()  ) {
                    return 0;
                }
                return ( $a->$methodName() < $a->$methodName() ) ? -1 : 1;
            } else if( $order == "DESC" ) {
                if( $a->$methodName() === $a->$methodName()  ) {
                    return 0;
                }
                return ( $a->$methodName() > $a->$methodName() ) ? -1 : 1;
            }
        });
        $gameCount = count($allStudioGames);
        $limit = $this->gamesListPageLimitNb;
        $offsetBegin = ($limit * $page) - $limit;
        $pageMax =  ceil($gameCount / $limit);
        $offsetEnd = ( ( $offsetBegin + $limit - 1 ) < $gameCount ) ? ( $offsetBegin + $limit - 1 ) : $gameCount ;
        if( $page > 0 && $page <= $pageMax ) {
            $games = [];
            for( $i = $offsetBegin; $i <= $offsetEnd; $i++ ) {
                if( isset( $allStudioGames[$i] ) === true ) {
                    $games[] = $allStudioGames[$i];
                }
            }

        } else {
            $games = $allStudioGames;
        }

        if( gettype($games) !== "array" ) {
            $games = [];
        }

        return $this->json( $games );
    }

    /**
     * @Route("/games/sort-by-released-date", name="gamesListByReleasedDate")
     */
    public function gamesListByReleasedDate( GameRepository $gameRepository , Request $request ) : Response
    {
        $sortField = 'releasedAt';
        $order = mb_strtoupper($request->query->get("order","ASC"));
        $pageCount = intval($gameRepository->countAllGame(), 10);
        $page = $request->query->get("page",1);
        $limit = $this->gamesListPageLimitNb;
        $offset = ($limit * $page) - $limit;
        $pageMax =  ceil($pageCount / $limit);
        if( $page > 0 && $page <= $pageMax ) {
            $games = $gameRepository->findBy(
                [],
                [$sortField => $order],
                $limit,
                $offset
            );
        } else {
            $games = $gameRepository->findAll();
        }
        if( gettype($games) !== "array" ) {
            $games = [];
        }
        return $this->json( $games );
    }

    /**
     * @Route("/games/sort-by-name", name="gamesListByName")
     */
    public function gamesListByName( GameRepository $gameRepository , Request $request ) : Response
    {
        $sortField = 'name';
        $order = mb_strtoupper($request->query->get("order","ASC"));
        $pageCount = intval($gameRepository->countAllGame(), 10);
        $page = $request->query->get("page",1);
        $limit = $this->gamesListPageLimitNb;
        $offset = ($limit * $page) - $limit;
        $pageMax =  ceil($pageCount / $limit);
        if( $page > 0 && $page <= $pageMax ) {
            $games = $gameRepository->findBy(
                [],
                [$sortField => $order],
                $limit,
                $offset
            );
        } else {
            $games = $gameRepository->findAll();
        }
        if( gettype($games) !== "array" ) {
            $games = [];
        }
        return $this->json( $games );
    }

    /**
     * @Route("/games", name="gamesList")
     */
    public function gamesList( GameRepository $gameRepository , Request $request ) : Response
    {
        $pageCount = intval($gameRepository->countAllGame(), 10);
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
        return $this->json( $games );
        //return $this->json( $gameRepository->findAll() );
    }

    /**
     * @Route("/games-by-letter", name="gamesByLetter")
     */
    public function getGamesByLetter( GameRepository $gameRepository , Request $request ) : Response
    {
        $games = [];

        $letterString = $request->query->get("letter",null);
        
        if( $letterString !== null && is_string( $letterString ) === true && !is_numeric( $letterString ) === true && strlen($letterString) === 1 ) {
            $letterString = strtolower($letterString);
            $games = $gameRepository->findByFirstLetterName(
                $letterString,
                "name",
                "ASC"
            );
        }

        if( gettype($games) !== "array" ) {
            $games = [];
        }

        $jsonReady = [];

        if( count($games) > 0 ) {

            foreach( $games as $game ) {
                
                $gameData = $game->jsonSerialize();
                unset($gameData["comments"]);
                $jsonReady[] = $gameData;

            }

        }

        return $this->json( $jsonReady );
    }

    /**
     * @Route("/games-count-by-letter", name="gamesCountByLetter")
     */
    public function getGamesCountByLetter( GameRepository $gameRepository ) : Response
    {
        $datas = $gameRepository->findAlphabeticListOfGamesRelatedCounter();
        return $this->json( $datas );
    }

}
