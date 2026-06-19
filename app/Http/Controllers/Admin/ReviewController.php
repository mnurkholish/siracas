<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search'));
        $replyStatus = (string) $request->query('reply_status');
        $rating = (string) $request->query('rating');

        $reviews = Review::query()
            ->with(['user', 'product', 'adminReplier'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query
                        ->where('isi', 'like', "%{$search}%")
                        ->orWhere('admin_reply', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($query) use ($search) {
                            $query->where('username', 'like', "%{$search}%");
                        })
                        ->orWhereHas('product', function ($query) use ($search) {
                            $query->where('nama_produk', 'like', "%{$search}%");
                        });
                });
            })
            ->when($replyStatus === 'replied', fn ($query) => $query->whereNotNull('admin_reply'))
            ->when($replyStatus === 'unreplied', fn ($query) => $query->whereNull('admin_reply'))
            ->when(in_array($rating, ['1', '2', '3', '4', '5'], true), fn ($query) => $query->where('rating', $rating))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.reviews.index', [
            'reviews' => $reviews,
            'replyStatusOptions' => [
                'replied' => 'Sudah dibalas',
                'unreplied' => 'Belum dibalas',
            ],
        ]);
    }

    public function create(Review $review)
    {
        $review->load(['user', 'product', 'adminReplier']);

        return view('admin.reviews.create', compact('review'));
    }

    public function reply(Request $request, Review $review)
    {
        if ($request->input('reply_action') === 'delete') {
            if (blank($review->admin_reply)) {
                return $this->noChangesResponse();
            }

            $review->fill([
                'admin_reply' => null,
                'admin_replied_at' => null,
                'admin_replied_by' => null,
            ])->save();

            return redirect()
                ->route('admin.reviews.index', $request->query())
                ->with('success', 'Balasan review berhasil dihapus.');
        }

        $validated = Validator::make($request->all(), [
            'admin_reply' => ['required', 'string', 'max:2000'],
        ], [
            'admin_reply.required' => 'Balasan admin wajib diisi.',
            'admin_reply.max' => 'Balasan maksimal 2000 karakter.',
        ])->validate();

        $reply = trim((string) ($validated['admin_reply'] ?? ''));

        if ($reply === '') {
            throw ValidationException::withMessages([
                'admin_reply' => 'Balasan admin wajib diisi.',
            ]);
        }

        $currentReply = trim((string) ($review->admin_reply ?? ''));

        if ($currentReply === $reply) {
            return $this->noChangesResponse();
        }

        $review->fill([
            'admin_reply' => $reply !== '' ? $reply : null,
            'admin_replied_at' => $reply !== '' ? now() : null,
            'admin_replied_by' => $reply !== '' ? Auth::id() : null,
        ]);

        $review->save();

        return redirect()
            ->route('admin.reviews.index', $request->query())
            ->with('success', 'Balasan review berhasil disimpan.');
    }
}
