<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BorrowRecord;
use App\Models\Book;
use Carbon\Carbon;

/**
 * @OA\Tag(
 *     name="Borrow",
 *     description="Borrow and return book endpoints"
 * )
 */

class BorrowController extends Controller
{
/**
 * @OA\Post(
 *     path="/api/borrow",
 *     summary="Borrow a book",
 *     tags={"Borrow"},
 *     security={{"sanctum":{}}},
 *     @OA\RequestBody(
 *          required=true,
 *          @OA\JsonContent(
 *              required={"book_id"},
 *              @OA\Property(property="book_id", type="integer", example=1),
 *              @OA\Property(property="days", type="integer", example=14)
 *          )
 *     ),
 *     @OA\Response(response=201, description="Book borrowed successfully")
 * )
 */

    public function borrow(Request $request)
    {
        $data = $request->validate([
            'book_id'=>'required|exists:books,id',
            'days'=>'nullable|integer|min:1|max:30'
        ]);

        $book = Book::findOrFail($data['book_id']);
        if ($book->available <= 0) {
            return response()->json(['message'=>'Book not available'], 400);
        }

        $book->available -= 1;
        $book->save();

        $borrowedAt = Carbon::now();
        $dueAt = $borrowedAt->copy()->addDays($data['days'] ?? 14);

        $record = BorrowRecord::create([
            'user_id' => $request->user()->id,
            'book_id' => $book->id,
            'borrowed_at' => $borrowedAt,
            'due_at' => $dueAt
        ]);

        return response()->json($record,201);
    }
/**
 * @OA\Post(
 *     path="/api/return",
 *     summary="Return a borrowed book",
 *     tags={"Borrow"},
 *     security={{"sanctum":{}}},
 *     @OA\RequestBody(
 *          required=true,
 *          @OA\JsonContent(
 *              required={"borrow_id"},
 *              @OA\Property(property="borrow_id", type="integer", example=1)
 *          )
 *     ),
 *     @OA\Response(response=200, description="Book returned successfully")
 * )
 */

    public function return(Request $request)
    {
        $data = $request->validate([
            'borrow_id'=>'required|exists:borrow_records,id'
        ]);

        $record = BorrowRecord::findOrFail($data['borrow_id']);
        if ($record->returned_at) {
            return response()->json(['message'=>'Already returned'],400);
        }

        // authorize owner or admin
        if ($request->user()->id !== $record->user_id && $request->user()->role !== 'admin') {
            abort(403);
        }

        $record->returned_at = Carbon::now();
        $record->save();

        $book = $record->book;
        $book->available += 1;
        $book->save();

        return response()->json($record);
    }
    /**
     * @OA\Get(
     *     path="/api/borrow/history",
     *     summary="Get borrow history (admin only)",
     *     tags={"Borrow"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=200, description="Borrow history retrieved")
     * )
     */
    public function history(Request $request)
    {
        $user = $request->user();
        if ($user->role === 'admin') {
            return BorrowRecord::with(['book','user'])->latest()->paginate(20);
        }
        return $user->borrowRecords()->with('book')->latest()->paginate(20);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
