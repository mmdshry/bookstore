<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class Book extends Model
{
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'isbn',
        'title',
        'description',
    ];

    /**
     * Resolves authors related to this book
     *
     * @return BelongsToMany
     */
    public function authors(): BelongsToMany
    {
        return $this->belongsToMany(Author::class, 'book_author');
    }

    /**
     * Resolves reviews related to this book
     *
     * @return HasMany
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(BookReview::class);
    }

    /**
     * Search for given title
     *
     * @param  Builder  $query
     * @param  string  $title
     * @return void
     */
    public function scopeSearchTitle(Builder $query, string $title): void
    {
        $query->where('title', 'like', '%'.$title.'%');
    }

    /**
     * Search for given authors
     *
     * @param  Builder  $query
     * @param  string  $authors
     * @return void
     */
    public function scopeSearchAuthors(Builder $query, string $authors): void
    {
        $authors = explode(',', $authors);

        $query->whereHas('authors', function ($query) use ($authors) {
            $query->whereIn('author_id', $authors);
        });
    }

    /**
     * Sorts query by given query string
     *
     * @param  Builder  $query
     * @param  string  $sortColumn
     * @param  string|null  $sortDirection
     * @return void
     */
    public function scopeSorting(Builder $query, string $sortColumn, ?string $sortDirection): void
    {
        $sortDirection = ($sortDirection && strtoupper($sortDirection) === 'DESC') ? 'DESC' : 'asc';

        if ($sortColumn === 'avg_review') {
            $query->withCount([
                'reviews as review_average' => function ($query) {
                    $query->select(DB::raw('coalesce(avg(review),0)'));
                }
            ])->orderBy('review_average', $sortDirection);
        } elseif ($sortColumn === 'title') {
            $query->OrderBy($sortColumn, $sortDirection);
        }
    }
}
