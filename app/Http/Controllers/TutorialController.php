<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TutorialController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * User FAQ / Tutorial Page
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $kategori = $request->input('kategori');

        $faqs = Faq::where('is_active', true)
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('pertanyaan', 'ilike', '%' . $search . '%')
                      ->orWhere('jawaban', 'ilike', '%' . $search . '%');
                });
            })
            ->when($kategori, function ($query, $kategori) {
                $query->where('kategori', $kategori);
            })
            ->orderBy('kategori')
            ->orderBy('id', 'desc')
            ->get();

        $categories = Faq::where('is_active', true)
            ->whereNotNull('kategori')
            ->distinct()
            ->pluck('kategori');

        // AI Smart Answer (RAG)
        $aiAnswer = null;
        
        // Cek jika user meminta reset chat
        if ($request->has('reset_chat')) {
            session()->forget('faq_chat_history');
            return redirect()->route('tutorial.index');
        }

        if ($search && $request->has('chat')) {
            // Ambil semua FAQ aktif untuk dijadikan konteks
            $allFaqs = Faq::where('is_active', true)->get(['kategori', 'pertanyaan', 'jawaban']);
            
            $contextText = "";
            foreach ($allFaqs as $faq) {
                $contextText .= sprintf(
                    "Kategori: %s\nPertanyaan: %s\nJawaban: %s\n\n",
                    $faq->kategori,
                    $faq->pertanyaan,
                    $faq->jawaban
                );
            }

            $systemInstruction = "Nama Anda adalah BOT-ICELL, asisten pintar untuk sistem ICELL (Informasi Cepat Penyelidikan dan Penyidikan Laka Lantas). "
                . "Berperanlah sebagai seorang polisi yang mengayomi, ramah, tegas, dan komunikatif layaknya petugas Customer Service yang humanis, bukan seperti robot kaku. "
                . "Jawab pertanyaan pengguna dengan baik, jelas, dan santun berdasarkan data FAQ resmi yang disediakan. "
                . "PENTING: Jika pertanyaan pengguna di luar topik FAQ resmi atau jawaban tidak dapat ditemukan di data FAQ di bawah, Anda wajib menjawab persis seperti ini: "
                . "'Mohon maaf, belum ada jawaban resmi untuk pertanyaan Anda di sistem kami. Silakan hubungi tim helpdesk ICELL melalui WhatsApp di nomor [+62 851-3682-4141](https://wa.me/6285136824141)' "
                . "Tetap batasi jawaban Anda hanya pada informasi yang didukung oleh data FAQ resmi di bawah, jangan mengarang jawaban sendiri.\n\n"
                . "DATA FAQ RESMI:\n" . $contextText;

            $apiKey = config('services.openai.api_key');
            $baseUrl = config('services.openai.base_url');
            $model = config('services.openai.model');

            try {
                $response = Http::timeout(45)
                    ->withHeaders([
                        'Authorization' => 'Bearer ' . $apiKey,
                        'Content-Type' => 'application/json',
                    ])
                    ->post($baseUrl . '/chat/completions', [
                        'model' => $model,
                        'messages' => [
                            [
                                'role' => 'system',
                                'content' => $systemInstruction
                            ],
                            [
                                'role' => 'user',
                                'content' => $search
                            ]
                        ],
                        'temperature' => 0.1,
                    ]);

                if ($response->successful()) {
                    $aiAnswer = $response->json('choices.0.message.content');
                } else {
                    $errorCode = $response->status();
                    Log::error("AI API Error: " . $response->body());
                    if ($errorCode == 429) {
                        $aiAnswer = "Sistem gagal merespons. Batas kuota harian/menit server AI terlampaui (Error 429: Rate Limit Exceeded). Silakan coba lagi beberapa saat kemudian.";
                    } else {
                        $aiAnswer = "Sistem gagal merespons. Terjadi kesalahan pada server AI (HTTP Error {$errorCode}). Silakan hubungi administrator.";
                    }
                }
            } catch (\Exception $e) {
                Log::error('AI Tutorial RAG Search failed: ' . $e->getMessage());
                $aiAnswer = "Sistem gagal merespons. Gagal terhubung ke server AI (Koneksi Timeout/Error). Pastikan koneksi internet server stabil atau Ollama lokal Anda sudah aktif jika menggunakan offline.";
            }

            // Simpan percakapan ke dalam session history (SELALU SIMPAN agar tidak hilang)
            $history = session()->get('faq_chat_history', []);
            
            // Tambahkan pesan user
            $history[] = [
                'sender' => 'user',
                'text' => $search,
                'time' => now()->format('H:i')
            ];
            
            // Tambahkan jawaban bot
            $history[] = [
                'sender' => 'bot',
                'text' => $aiAnswer,
                'time' => now()->format('H:i')
            ];
            
            session()->put('faq_chat_history', $history);
        }

        return view('tutorial.index', compact('faqs', 'categories', 'search', 'kategori', 'aiAnswer'))
            ->with('_title', 'Tutorial & FAQ ICELL');
    }

    /**
     * CMS FAQ List
     */
    public function cmsIndex(Request $request)
    {
        // Simple permission check (admin or helpdesk)
        if (Auth::user()->role_id != 1 && Auth::user()->role_id != 2) {
            abort(403, 'Akses terbatas untuk Administrator.');
        }

        $faqs = Faq::orderBy('id', 'desc')->paginate(20);

        return view('cms.faq.index', compact('faqs'))
            ->with('_title', 'CMS - FAQ & Tutorial');
    }

    /**
     * Store new FAQ manually
     */
    public function store(Request $request)
    {
        if (Auth::user()->role_id != 1 && Auth::user()->role_id != 2) {
            abort(403);
        }

        $request->validate([
            'pertanyaan' => 'required|string',
            'jawaban' => 'required|string',
            'kategori' => 'nullable|string',
        ]);

        Faq::create([
            'pertanyaan' => $request->pertanyaan,
            'jawaban' => $request->jawaban,
            'kategori' => $request->kategori ?: 'Lain-lain',
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'FAQ berhasil ditambahkan secara manual.');
    }

    /**
     * Update FAQ
     */
    public function update(Request $request, $id)
    {
        if (Auth::user()->role_id != 1 && Auth::user()->role_id != 2) {
            abort(403);
        }

        $request->validate([
            'pertanyaan' => 'required|string',
            'jawaban' => 'required|string',
            'kategori' => 'nullable|string',
            'is_active' => 'required|boolean',
        ]);

        $faq = Faq::findOrFail($id);
        $faq->update([
            'pertanyaan' => $request->pertanyaan,
            'jawaban' => $request->jawaban,
            'kategori' => $request->kategori ?: 'Lain-lain',
            'is_active' => $request->is_active,
        ]);

        return redirect()->back()->with('success', 'FAQ berhasil diperbarui.');
    }

    /**
     * Delete FAQ
     */
    public function destroy($id)
    {
        if (Auth::user()->role_id != 1 && Auth::user()->role_id != 2) {
            abort(403);
        }

        $faq = Faq::findOrFail($id);
        $faq->delete();

        return redirect()->back()->with('success', 'FAQ berhasil dihapus.');
    }

    /**
     * Generate FAQ using AI based on solved tickets
     */
    public function generate(Request $request)
    {
        // Berikan waktu eksekusi PHP lebih panjang karena pemrosesan model AI lokal/online yang besar
        set_time_limit(180);

        if (Auth::user()->role_id != 1 && Auth::user()->role_id != 2) {
            abort(403);
        }

        $tickets = Ticket::where('status', 'solved')
            ->orderBy('updated_at', 'desc')
            ->take(15)
            ->get(['kategori', 'deskripsi_permasalahan', 'deskripsi_solusi']);

        if ($tickets->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada tiket bertatus "solved" yang dapat diolah oleh AI saat ini.');
        }

        $apiKey = config('services.openai.api_key');
        $baseUrl = config('services.openai.base_url');
        $model = config('services.openai.model');

        if (empty($apiKey)) {
            return redirect()->back()->with('error', 'API Key AI belum dikonfigurasi di file .env (OPENAI_API_KEY). Silakan hubungi Administrator.');
        }

        // Format data tiket untuk prompt
        $ticketListText = "";
        foreach ($tickets as $idx => $ticket) {
            $ticketListText .= sprintf(
                "Tiket #%d:\n- Kategori: %s\n- Masalah: %s\n- Solusi: %s\n\n",
                $idx + 1,
                $ticket->kategori ?: 'Umum',
                $ticket->deskripsi_permasalahan,
                $ticket->deskripsi_solusi
            );
        }

        $prompt = "Berikut adalah daftar tiket bantuan keluhan sistem ICELL (Informasi Cepat Penyelidikan dan Penyidikan Laka Lantas) yang telah terpecahkan:\n\n"
            . $ticketListText
            . "Tugas Anda:\n"
            . "1. Analisis tiket-tiket di atas.\n"
            . "2. Buatkan daftar Pertanyaan dan Jawaban (FAQ) yang mudah dipahami, berorientasi solusi, dan ramah pengguna untuk membantu polisi lapangan. KONSOLIDASIKAN dan gabungkan tiket-tiket yang memiliki permasalahan sejenis menjadi satu pertanyaan FAQ yang representatif agar tidak ada FAQ yang duplikat atau berulang.\n"
            . "3. Kelompokkan ke dalam kategori yang sesuai (misal: Login, Dokumen TTE, Sinkronisasi Data, Laporan Kasus).\n"
            . "4. WAJIB mengembalikan respons HANYA dalam format JSON Array dengan format objek berikut:\n"
            . "   [\n"
            . "     {\n"
            . "       \"kategori\": \"Kategori FAQ\",\n"
            . "       \"pertanyaan\": \"Pertanyaan FAQ?\",\n"
            . "       \"jawaban\": \"Jawaban FAQ detail.\"\n"
            . "     }\n"
            . "   ]\n"
            . "Jangan tambahkan teks pengantar atau penjelas lainnya di luar format JSON Array. Berikan hanya JSON valid.";

        try {
            $response = Http::timeout(120)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post($baseUrl . '/chat/completions', [
                    'model' => $model,
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'Anda adalah asisten AI profesional untuk sistem kepolisian ICELL. Anda bertugas menyusun dokumen FAQ teknis dari data log bantuan.'
                        ],
                        [
                            'role' => 'user',
                            'content' => $prompt
                        ]
                    ],
                    'temperature' => 0.3,
                ]);

            if ($response->failed()) {
                Log::error('AI FAQ Generation Failed: ' . $response->body());
                return redirect()->back()->with('error', 'API AI merespons dengan kesalahan: ' . $response->status() . ' - ' . $response->reason());
            }

            $content = $response->json('choices.0.message.content');
            
            // Clean up JSON block if AI wraps it in markdown (e.g. ```json ... ```)
            $cleanJson = trim($content);
            if (preg_match('/^```(?:json)?\s*(.*?)\s*```$/ms', $cleanJson, $matches)) {
                $cleanJson = $matches[1];
            }

            $faqItems = json_decode($cleanJson, true);

            if (!is_array($faqItems)) {
                Log::warning('AI Response is not valid JSON: ' . $content);
                return redirect()->back()->with('error', 'Respons AI tidak dapat dibaca sebagai format JSON valid. Silakan coba kembali.');
            }

            $count = 0;
            foreach ($faqItems as $item) {
                if (isset($item['pertanyaan']) && isset($item['jawaban'])) {
                    // Check if question already exists to avoid duplication
                    $exists = Faq::where('pertanyaan', $item['pertanyaan'])->exists();
                    if (!$exists) {
                        Faq::create([
                            'kategori' => $item['kategori'] ?? 'Umum',
                            'pertanyaan' => $item['pertanyaan'],
                            'jawaban' => $item['jawaban'],
                            'is_active' => true,
                        ]);
                        $count++;
                    }
                }
            }

            return redirect()->back()->with('success', "AI berhasil menganalisis tiket dan mengunggah {$count} FAQ baru ke sistem.");

        } catch (\Exception $e) {
            Log::error('AI FAQ Generation Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan koneksi saat menghubungi server AI: ' . $e->getMessage());
        }
    }
}
