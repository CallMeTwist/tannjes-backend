<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use App\Models\Message;
use Illuminate\Http\Request;

class ConsultationController extends Controller
{
    public function index(Request $request)
    {
        $consultations = Consultation::query()
            ->where('patient_id', $request->user()->id)
            ->with('doctor:id,name,role')
            ->orderByDesc('last_message_at')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($c) => $this->summary($c));

        return ['data' => $consultations];
    }

    public function messages(Request $request, Consultation $consultation)
    {
        $this->authorizeOwner($request, $consultation);

        Message::where('consultation_id', $consultation->id)
            ->where('sender_type', 'staff')
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return [
            'consultation' => $this->summary($consultation->load('doctor:id,name,role')),
            'messages' => $consultation->messages()->get()->map(fn ($m) => $this->message($m)),
        ];
    }

    public function store(Request $request, Consultation $consultation)
    {
        $this->authorizeOwner($request, $consultation);

        $data = $request->validate([
            'body' => ['required', 'string', 'max:5000'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ]);

        $path = $request->hasFile('attachment')
            ? $request->file('attachment')->store('consultation-attachments', 'public')
            : null;

        $message = Message::create([
            'consultation_id' => $consultation->id,
            'sender_type' => 'patient',
            'sender_id' => $request->user()->id,
            'body' => $data['body'],
            'attachment_path' => $path,
        ]);

        $consultation->update(['last_message_at' => now()]);

        return response()->json(['message' => $this->message($message)], 201);
    }

    private function authorizeOwner(Request $request, Consultation $consultation): void
    {
        abort_unless($consultation->patient_id === $request->user()->id, 403);
    }

    private function summary(Consultation $c): array
    {
        return [
            'id' => $c->id,
            'subject' => $c->subject,
            'status' => $c->status,
            'doctor' => $c->doctor ? ['name' => $c->doctor->name, 'role' => $c->doctor->role] : null,
            'last_message_at' => optional($c->last_message_at)->toIso8601String(),
        ];
    }

    private function message(Message $m): array
    {
        return [
            'id' => $m->id,
            'sender_type' => $m->sender_type,
            'body' => $m->body,
            'attachment_url' => $m->attachment_path
                ? rtrim(config('app.url'), '/').'/storage/'.$m->attachment_path
                : null,
            'created_at' => $m->created_at->toIso8601String(),
        ];
    }
}
