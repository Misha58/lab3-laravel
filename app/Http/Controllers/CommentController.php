<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

// Отзывы о городах: добавить и удалить
class CommentController extends Controller
{
    // POST /cities/{city}/comments — добавить отзыв к любому городу (своему или чужому)
    public function store(Request $request, City $city)
    {
        $data = $request->validate(
            ['body' => ['required', 'string', 'min:3', 'max:1000', 'not_regex:/<[^>]*>/']],
            [
                'body.required'  => 'Напишите текст отзыва.',
                'body.min'       => 'Отзыв слишком короткий: минимум :min символа.',
                'body.max'       => 'Отзыв слишком длинный: максимум :max символов.',
                'body.not_regex' => 'В отзыве нельзя использовать HTML-теги.',
            ]
        );

        // город берём из адреса, автора записывает модель Comment (событие creating)
        $city->comments()->create($data);

        return redirect(route('cities.show', $city) . '#comments')
            ->with('status', 'Отзыв добавлен.');
    }

    // DELETE /comments/{comment} — удалить отзыв (автор отзыва или администратор)
    public function destroy(Comment $comment)
    {
        Gate::authorize('delete-comment', $comment); // иначе ошибка 403

        $comment->delete();

        return redirect(route('cities.show', $comment->city_id) . '#comments')
            ->with('status', 'Отзыв удалён.');
    }
}
