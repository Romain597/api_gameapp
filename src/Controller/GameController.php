<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\GameRepository;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Comment;

class GameController extends AbstractController
{
    /**
     * @Route("/games", name="gamesList")
     */
    public function gamesList( GameRepository $GameRepository ) : Response
    {
        /*$test = $GameRepository->findAll();
        dd($test);*/
        $games = $GameRepository->findAll();
        if( gettype($game) !== "array" ) {
            $games = [];
        }
        return $this->json( $games );
    }

    /**
     * @Route("/game/{gameId}", name="gameView")
     */
    public function gameView(GameRepository $GameRepository , int $gameId ) : Response
    {
        /*$test = $GameRepository->find($gameId);
        dd($test->getStudios());*/
        $game = $GameRepository->find($gameId);
        if( ($game instanceof Game) === false ) {
            $game = [];
        }
        return $this->json( $game );
    }

    /**
     * @Route("/game/{gameId}/comment/{author}/{publishedDate}/{comment}", name="commentAdd")
    */
    public function createComment( ValidatorInterface $validator , GameRepository $GameRepository , ...$parameters ) : Response
    { // EntityManagerInterface $entityManager
        // In function call : decomposition input datas in a array named parameters
        // get variables passed by parameters array
        list('gameId' => $commentGameId, 'author' => $commentAuthor, 'publisheDate' => $commentPublishedDate, 'comment' => $commentText) = $parameters;
        // create a new object Comment
        $comment = new Comment();
        // comment author
        $commentAuthor = filter_var($commentAuthor, FILTER_SANITIZE_STRING);
        if( !empty($commentAuthor) === true && trim($commentAuthor) != "" ) {
            $comment->setAuthor(trim($commentAuthor));
        }
        // comment add date
        if( preg_match('/^\d{4}\-\d{1,2}\-\d{1,2}\-\d{1,2}\-\d{1,2}\-\d{1,2}$/',$commentPublishedDate) === 1 ) {
            $publishedDateArray = explode("-",$commentPublishedDate);
            $publishedDate = new \DateTime("now", new DateTimeZone("UTC"));
            $publishedDate->setDate(intval($publishedDateArray[0], 10), intval($publishedDateArray[1], 10), intval($publishedDateArray[2], 10));
            $publishedDate->setTime(intval($publishedDateArray[3], 10), intval($publishedDateArray[4], 10), intval($publishedDateArray[5], 10));
            $comment->setPublishedAt($publishedDate);
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
            $commentGame = $GameRepository->find($commentGameId);
            if( ($commentGame instanceof Game) === true ) {
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
            // create entity manager
            $entityManager = $this->getDoctrine()->getManager();
            // tell Doctrine you want to (eventually) save the Comment (no queries yet)
            $entityManager->persist($comment);
            // actually executes the queries (i.e. the INSERT query)
            $entityManager->flush();
            // return success response
            return new Response('Success : Saved new comment with id '.$comment->getId(), 200);
        }
    }

}
