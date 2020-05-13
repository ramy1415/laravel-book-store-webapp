<?php

namespace App\Http\Controllers;

use App\Book;
use App\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    // public function __construct()
    // {
    // $this->middleware('auth');
    // }

    private function addRate($books)
    {
        $rate_sum = 0;
        foreach ($books as $book) {
            foreach ($book->rate as $rate) {
                $rate_sum += $rate->rating;
            }
            if (count($book->rate)!= 0 ){
                $rate_sum = $rate_sum / count($book->rate);
                $book['rating'] = $rate_sum;
            }
            else {
                $book['rating'] = 0;
            }
            
            $rate_sum = 0;
        }
        return $books;
    }

    public function sort($sort_value)
    {
        $books = Book::paginate(8);
        $books = $this->addrate($books);
        $books->setCollection(
            $books->sortByDesc($sort_value)
        );
        return view('home.home', ['books' => $books]);
    }

    public function index()
    {
        $categories = Category::all();
        $books = Book::paginate(8);
        if (Auth::check()) {
            $books = $this->addrate($books);
            return view('home.home', [
                'books' => $books,
                'categories' => $categories
            ]);
        } else {
            return view('home.landing', [
                'books' => $books,
                'categories' => $categories
            ]);
        }
    }
    
    public function category($category){
        $categories = Category::all();
        $current_category = Category::find($category);
        $books = Category::find($category)->books()->paginate(8);
        // dd(count($books));
        if (Auth::check()) {
            $books = $this->addrate($books);
            return view('home.home', compact('books','categories', 'current_category'));
        } else {
            return view('home.landing', compact('books','categories', 'current_category'));
        }
    }
}
