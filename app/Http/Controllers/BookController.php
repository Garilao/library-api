<?php

namespace App\Http\Controllers;
use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return Book::paginated(15);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $this->authorizeAdmin($request->user());
        $data = $request->validate([
            'title'=>'required|string|max:255',
            'author'=>'nullable|string|max:255',
            'isbn'=>'nullable|string|unique:books,isbn',
            'genre'=>'nullable|string',
            'stock'=>'required|integer|min:0',
        ]);

        $data['available'] = $data['stock'];
        $book = Book::create($data);
        return response()->json($book,201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Book $book)
    {
        //
        return $book;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Book $book)
    {
        //
        $this->authorizeAdmin($request->user());
        $data = $request->validate([
            'title'=>'sometimes|required|string|max:255',
            'author'=>'nullable|string|max:255',
            'isbn'=>"nullable|string|unique:books,isbn,{$book->id}",
            'genre'=>'nullable|string',
            'stock'=>'sometimes|integer|min:0',
        ]);

        if(isset($data['stock'])){
            $diff = $data['stock'] - $book->stock;
            $book->available += $diff;
        }

        $book->update($data);
        return response()->json($book);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Book $book)
    {
        //
        $this->authorizeAdmin($request->user());
        $book->delete();
        return response()->json(null,204);

    }

    private function authorizeAdmin($user){
        if(!$user || $user->role !== 'admin'){
            abort(403, 'Unauthorize Access');
        }
    }
}
