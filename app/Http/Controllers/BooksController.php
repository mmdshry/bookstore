<?php

declare (strict_types=1);

namespace App\Http\Controllers;

use App\Book;
use App\Http\Requests\PostBookRequest;
use App\Http\Requests\PostBookReviewRequest;
use App\Http\Resources\BookResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BooksController extends Controller
{
    /**
     * Returns book collection
     *
     * @param  Request  $request
     * @return AnonymousResourceCollection
     */
    public function getCollection(Request $request): AnonymousResourceCollection
    {
        $books = Book::query();

        $this->searchTitle($request, $books);

        $this->searchAuthors($request, $books);

        $this->sorting($request, $books);

        return BookResource::collection($books->paginate());
    }

    /**
     * Creates a new book
     *
     * @param  PostBookRequest  $request
     * @return BookResource
     */
    public function post(PostBookRequest $request): BookResource
    {
        $book              = new Book();
        $book->title       = $request['title'];
        $book->isbn        = $request['isbn'];
        $book->description = $request['description'];
        $book->save();

        $book->authors()->attach($request['authors']);

        return new BookResource($book);
    }

    public function postReview(Book $book, PostBookReviewRequest $request)
    {
        //@todo code here
    }

    /**
     * Performs title search if title attribute has filled
     *
     * @param  Request  $request
     * @param  Builder  $books
     */
    private function searchTitle(Request $request, Builder $books): void
    {
        if ($request->filled('title')) {
            $books->searchTitle($request['title']);
        }
    }

    /**
     * Performs author search if title attribute has filled
     *
     * @param  Request  $request
     * @param  Builder  $books
     */
    private function searchAuthors(Request $request, Builder $books): void
    {
        if ($request->filled('authors')) {
            $books->searchAuthors($request['authors']);
        }
    }

    /**
     * Performs sorting if sortColumn attribute has filled
     *
     * @param  Request  $request
     * @param  Builder  $books
     */
    private function sorting(Request $request, Builder $books): void
    {
        if ($request->filled('sortColumn')) {
            $books->sorting($request['sortColumn'], $request['sortDirection']);
        }
    }
}
