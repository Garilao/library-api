<?php

namespace App\Http\Controllers;
use App\Models\Book;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Books",
 *     description="Book management endpoints"
 * )
 */

class BookController extends Controller
{

/**
 * @OA\Get(
 *     path="/api/books",
 *     summary="Get all books",
 *     tags={"Books"},
 *     @OA\Response(response=200, description="List of books")
 * )
 */

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return Book::paginated(15);
    }

/**
 * @OA\Post(
 *     path="/api/books",
 *     summary="Create a new book",
 *     tags={"Books"},
 *     security={{"sanctum":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"title","stock"},
 *             @OA\Property(property="title", type="string", example="Harry Potter"),
 *             @OA\Property(property="author", type="string", example="J.K. Rowling"),
 *             @OA\Property(property="isbn", type="string", example="9780747532743"),
 *             @OA\Property(property="genre", type="string", example="Fantasy"),
 *             @OA\Property(property="stock", type="integer", example=5)
 *         )
 *     ),
 *     @OA\Response(response=201, description="Book created")
 * )
 */

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
 * @OA\Get(
 *     path="/api/books/{id}",
 *     summary="Get book by ID",
 *     tags={"Books"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Book ID"
 *     ),
 *     @OA\Response(response=200, description="Book details")
 * )
 */

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
