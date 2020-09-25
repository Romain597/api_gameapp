<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\GameRepository;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Comment;
use App\Entity\Game;

class GameController extends AbstractController
{
    
    //  /game/{commentGameId}/comment/{commentAuthor}/{commentPublishedDate}/{commentText}
    //  /game/{commentGameId}/comment
    //   /game/comment
    /**
     * @Route("/game/comment", name="commentAdd")
    */
    public function createComment( ValidatorInterface $validator , GameRepository $GameRepository , Request $request ) : Response
    { // EntityManagerInterface $entityManager   ...$parameters    , $commentGameId, $commentAuthor , $commentPublishedDate , $commentText 
        //dd($commentGameId , $commentAuthor , $commentPublishedDate , $commentText);
        // In function call : decomposition input datas in a array named parameters
        // get variables passed by parameters array
        //list('gameId' => $commentGameId, 'author' => $commentAuthor, 'publishedDate' => $commentPublishedDate, 'comment' => $commentText) = $parameters;
        //$request = Request::createFromGlobals();
        $commentGameId = $request->request->get("gameId",null);
        $commentAuthor = $request->request->get("author",null);
        $commentPublishedDate = $request->request->get("publishedDate",null);
        $commentText = $request->request->get("comment",null);
        //$routeName = $request->attributes->get('_route');
        //$routeParameters = $request->attributes->get('_route_params');
        // use this to get all the available attributes (not only routing ones):
        //$allAttributes = $request->attributes->all();
        // test
        /*$testRe = [
            "server" => $request->server->all(),
            "files" => $request->files->all(),
            "attributes" => $request->attributes->all(),
            "cookies" => $request->cookies->all(),
            "query" => $request->query->all(),
            "headers" => $request->headers->all(),
            "request" => $request->request->all()
        ];*/
        /*$commentGameId = 2;
        $commentAuthor = "Romain";
        $commentPublishedDate = "2020_09_24_12_10_0";
        $commentText = "Super jeux de stratégie";*/
        //return new Response('Success : < '.serialize($testRe).' >');
        //return new Response('Success : < '.serialize($request->server->all()).' > < '.serialize($request->files->all()).' > < '.serialize($request->attributes->all()).' > < '.serialize($request->cookies->all()).' > < '.serialize($request->query->all()).' > < '.serialize($request->headers->all()).' > < '.serialize($request->request->all()).' >');
        //return new Response('Success : <'.$commentGameId.'><'.$commentAuthor.'><'.$commentPublishedDate.'><'.$commentText.'>');
        /*$response = new Response();
        $response->setContent('Success : <'.$commentGameId.'>'.'<'.$commentAuthor.'>'.'<'.$commentPublishedDate.'>'.'<'.$commentText.'>');
        $response->setStatusCode(Response::HTTP_OK);
        // sets a HTTP response header
        $response->headers->set('Content-Type', 'text/html');
        // prints the HTTP headers followed by the content
        return $response->send();
        //dd($commentGameId , $commentAuthor , $commentPublishedDate , $commentText);*/
        // create a new object Comment
        $comment = new Comment();
        // comment author
        $commentAuthor = filter_var($commentAuthor, FILTER_SANITIZE_STRING);
        if( !empty($commentAuthor) === true && trim($commentAuthor) != "" ) {
            $comment->setAuthor(trim($commentAuthor));
        }
        // comment add date
        //  /^2\d{3}\_\d{1,2}\_\d{1,2}\_\d{1,2}\_\d{1,2}\_\d{1,2}$/
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
            $commentGame = $GameRepository->find($commentGameId);
            if( ($commentGame instanceof \Game) === true ) {
                $comment->setGame($commentGame);
            }
        }
        //dd(get_class($commentGame));
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
            return new Response('Success : Saved the new comment.', 200);
            //return new Response('Success : Saved new comment with the identification number <'.$comment->getId().'>.', 200);
        }
    }

    /**
     * @Route("/game/{gameId}", name="gameView")
     */
    public function gameView(GameRepository $GameRepository , int $gameId ) : Response
    {
        /*$test = $GameRepository->find($gameId);
        dd($test->getStudios());*/
        /*$game = $GameRepository->find($gameId);
        if( ($game instanceof Game) === false ) {
            $game = "";
        }
        return $this->json( $game );*/
        return $this->json( $GameRepository->find($gameId) );
    }

    /**
     * @Route("/games", name="gamesList")
     */
    public function gamesList( GameRepository $GameRepository ) : Response
    {
        /*$test = $GameRepository->findAll();
        dd($test);*/
        /*$games = $GameRepository->findAll();
        if( gettype($games) !== "array" ) {
            $games = [];
        }
        return $this->json( $games );*/
        return $this->json( $GameRepository->findAll() );
    }

}
