<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\CategoryRepository;

class CategoryController extends AbstractController
{
    /**
     * @Route("/categories", name="categoriesList")
     */
    public function categoriesList( CategoryRepository $categoryRepository , Request $request ) : Response
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
        return $this->json( $categoryRepository->findAll() );
    }

    /**
     * @Route("/categories-by-letter", name="categoriesByLetter")
     */
    public function getCategoriesByLetter( CategoryRepository $categoryRepository , Request $request ) : Response
    {
        $categories = [];
        $letterString = $request->query->get("letter",null);
        if( $letterString !== null && is_string( $letterString ) === true && !is_numeric( $letterString ) === true && strlen($letterString) === 1 ) {
            $letterString = strtolower($letterString);
            $categories = $categoryRepository->findByFirstLetterName(
                $letterString,
                "name",
                "ASC"
            );
        }
        if( gettype($categories) !== "array" ) {
            $categories = [];
        }
        return $this->json( $categories );
    }

}
