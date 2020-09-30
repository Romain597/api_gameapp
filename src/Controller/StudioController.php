<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\StudioRepository;

use Symfony\Component\Serializer\SerializerInterface;

// serialisation
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;

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
    public function getStudiosByLetter( StudioRepository $studioRepository , Request $request , SerializerInterface $serializer ) : Response
    {
        $studios = [];
        //$letterString = $request->query->get("letter",null);
        $letterString = "C";
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
            //dd($jsonReady);
            //return $this->json( $jsonReady );

            /*$json = [];
            foreach( $studios as $studio ) {

                $json[] = $serializer->normalize($studio, 'json', ['groups' => 'classify']);;

            }
            return new Response($json);*/

            /*$classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
            $normalizer = new ObjectNormalizer($classMetadataFactory);
            $serializer = new Serializer([$normalizer]);

            foreach( $studios as $studio ) {

                $studio = $serializer->normalize($studio, null, ['groups' => 'classify']);;

            }*/

           /* $obj2 = $serializer->denormalize(
                ['foo' => 'foo', 'bar' => 'bar'],
                'MyObj',
                null,
                ['groups' => ['group1', 'group3']]
            );*/
            
            /*$classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));

            // all callback parameters are optional (you can omit the ones you don't use)
            $maxDepthHandler = function ($innerObject, $outerObject, string $attributeName, string $format = null, array $context = []) {
                return '/Studio/'.$innerObject->id;
            };

            $defaultContext = [
                AbstractObjectNormalizer::MAX_DEPTH_HANDLER => $maxDepthHandler,
            ];
            $normalizer = new ObjectNormalizer($classMetadataFactory, null, null, null, null, null, $defaultContext);

            $serializer = new Serializer([$normalizer]);

            foreach( $studios as $studio ) {

                $studio = $serializer->normalize($studio, null, [AbstractObjectNormalizer::ENABLE_MAX_DEPTH => true]);

            }*/

        }
        //return $this->json( $studios );
        return $this->json( $jsonReady );
    }

    /**
     * @Route("/studios-games-count-by-letter", name="studiosGamesCountByLetter")
     */
    public function getStudiosGamesCountByLetter( StudioRepository $studioRepository ) : Response
    {
        $datas = $studioRepository->findAlphabeticListOfStudioWithGamesRelatedCounter();
        return $this->json( $datas );
    }

    /**
     * @Route("/studio-games", name="studioGamesList")
     */
    public function getStudioGamesList( StudioRepository $studioRepository ) : Response
    {
        
        if( true ) {
            $studios = $studioRepository->findBy(
                [],
                ["name" => "ASC"]
            );
            /*$studios = $studioRepository->findAllWithGamesRelatedCounter(
                "name",
                "ASC"
            );*/
        } else {
            $studios = $studioRepository->findBy(
                [],
                ["name" => "ASC"]
            );
        }
        if( gettype($studios) !== "array" ) {
            $studios = [];
        }
        return $this->json( $studios );
        //return $this->json( $studioRepository->findAll() );
    }

}
